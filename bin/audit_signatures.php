<?php declare(strict_types=1);
/**
 * DuckAdmin 契约签名审计
 *
 * 用途:上游 duckphp 调整接口方法签名(例如 canAccess 的参数顺序把 $url 挪到第二位)后,
 *      用来一次性检查本工程所有"隐式实现"是否与上游契约一致。
 *
 * 用法: php bin/audit_signatures.php
 * 退出码: 0 = 全部一致; 1 = 发现漂移
 *
 * 说明:
 *  - 本工程不少类并没有写 implements(靠 App 选项里的 local_service/login_service/login_session
 *    绑定成上游服务),PHP 不会替我们检查,所以只能按"角色表"显式比对。
 *  - 由上游 trait 提供的方法(SessionTrait/AdminSessionTrait 等)不算本工程的漂移,已跳过。
 *  - 参数类型"放宽"(如 ?string 对应接口的 string)是 LSP 允许的,单独归类为 WARN 而不是 FAIL。
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$src = $root . '/src';

/** 上游契约接口 */
$IFACE = [
    'AdminServiceInterface'      => \DuckPhp\GlobalAdmin\AdminServiceInterface::class,
    'AdminLoginServiceInterface' => \DuckPhp\GlobalAdmin\AdminLoginServiceInterface::class,
    'AdminSessionInterface'      => \DuckPhp\GlobalAdmin\AdminSessionInterface::class,
    'UserServiceInterface'       => \DuckPhp\GlobalUser\UserServiceInterface::class,
    'UserLoginServiceInterface'  => \DuckPhp\GlobalUser\UserLoginServiceInterface::class,
    'UserSessionInterface'       => \DuckPhp\GlobalUser\UserSessionInterface::class,
];

/**
 * 角色表:本工程哪些类实际承担哪个上游契约
 * 依据:各 App 的 options 里 globaladmin_ 与 globaluser_ 开头的
 *      local_service / login_service / login_session 绑定
 * 新增子应用/换绑定时,请同步维护这里。
 */
$ROLE = [
    'DuckAdmin\Admin\Business\AppBusiness'         => ['AdminServiceInterface', 'AdminLoginServiceInterface'],
    'DuckAdmin\SingleAdmin\Business\AdminBusiness' => ['AdminServiceInterface', 'AdminLoginServiceInterface'],
    'DuckAdmin\DemoUsers\Business\UserBusiness'    => ['UserServiceInterface', 'UserLoginServiceInterface'],
    'DuckAdmin\User\Business\UserBusiness'         => ['UserServiceInterface', 'UserLoginServiceInterface'],
    'DuckAdmin\Admin\Controller\Session'           => ['AdminSessionInterface'],
    'DuckAdmin\SingleAdmin\Controller\Session'     => ['AdminSessionInterface'],
    'DuckAdmin\DemoUsers\Controller\Session'       => ['UserSessionInterface'],
    'DuckAdmin\User\Controller\Session'            => ['UserSessionInterface'],
];

// 再自动补上"显式 implements 了上述接口"的类
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src));
foreach ($it as $f) {
    if ($f->getExtension() !== 'php') continue;
    if (strpos($f->getPathname(), DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR) !== false) continue;
    $rel = substr($f->getPathname(), strlen($src) + 1, -4);
    $cls = 'DuckAdmin\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $rel);
    if (!class_exists($cls)) continue;
    $rc = new ReflectionClass($cls);
    if ($rc->isAbstract()) continue;
    foreach ($rc->getInterfaceNames() as $i) {
        foreach ($IFACE as $short => $fq) {
            if ($i === $fq) {
                $ROLE[$cls] = array_values(array_unique(array_merge($ROLE[$cls] ?? [], [$short])));
            }
        }
    }
}

function param_spec(ReflectionMethod $m): string
{
    $parts = [];
    foreach ($m->getParameters() as $p) {
        $t = $p->getType();
        $parts[] = ($t ? (string)$t . ' ' : '') . '$' . $p->getName() . ($p->isOptional() ? ' = …' : '');
    }
    $rt = $m->getReturnType();
    return '(' . implode(', ', $parts) . ')' . ($rt ? ': ' . (string)$rt : '');
}

$fail = 0;
$warn = 0;
$pass = 0;

foreach ($ROLE as $cls => $shorts) {
    if (!class_exists($cls)) {
        echo "[FAIL] 角色表里的类不存在: $cls\n";
        $fail++;
        continue;
    }
    $rc = new ReflectionClass($cls);
    // 上游 trait 提供的方法不算本工程漂移
    $traitMethods = [];
    foreach ($rc->getTraits() as $tr) {
        foreach ($tr->getMethods() as $tm) {
            $traitMethods[$tm->getName()] = true;
        }
    }

    $printed = false;
    foreach ($shorts as $short) {
        $ri = new ReflectionClass($IFACE[$short]);
        foreach ($ri->getMethods() as $im) {
            $name = $im->getName();
            if (isset($traitMethods[$name])) {
                $pass++;
                continue;
            }
            if (!$rc->hasMethod($name)) {
                if (!$printed) { echo "### $cls\n"; $printed = true; }
                echo "  [FAIL] 缺少方法 $name" . param_spec($im) . "   (契约: $short)\n";
                $fail++;
                continue;
            }
            $cm = $rc->getMethod($name);
            $fails = [];
            $warns = [];
            $ip = $im->getParameters();
            $cp = $cm->getParameters();
            if (count($cp) < count($ip)) {
                $fails[] = '参数个数少于契约(' . count($cp) . '<' . count($ip) . ')';
            }
            $seenOpt = false;
            $ireq = 0;
            $creq = 0;
            foreach ($ip as $p) {
                if (!$p->isOptional()) $ireq++;
            }
            foreach ($cp as $p) {
                if ($p->isOptional()) {
                    $seenOpt = true;
                } else {
                    $creq++;
                    if ($seenOpt) $fails[] = '必需参数出现在可选参数之后(PHP8 弃用)';
                }
            }
            if ($creq > $ireq) {
                $fails[] = "必需参数多于契约($creq>$ireq)";
            }
            for ($i = 0; $i < min(count($ip), count($cp)); $i++) {
                $a = $ip[$i]->getType();
                $b = $cp[$i]->getType();
                $as = $a ? (string)$a : '(none)';
                $bs = $b ? (string)$b : '(none)';
                if ($as !== $bs) {
                    // ?string vs string 属于"放宽约束",LSP 允许
                    if ($bs === '?string' && $as === 'string') {
                        $warns[] = '第' . ($i + 1) . '位类型放宽 ?string(契约 string)';
                    } elseif ($as === '(none)' && $bs !== '(none)') {
                        $warns[] = '第' . ($i + 1) . '位类型收紧 ' . $bs . '(契约无类型)';
                    } else {
                        $fails[] = '第' . ($i + 1) . "位类型 {$bs} != 契约 {$as}";
                    }
                }
                if ($ip[$i]->getName() !== $cp[$i]->getName()) {
                    // 覆盖率 DSL / 命名实参都按参数名传参,名字不一致会取不到值
                    $fails[] = '第' . ($i + 1) . "位参数名 \${$cp[$i]->getName()} != \${$ip[$i]->getName()}(命名传参会失配)";
                }
            }
            $irt = $im->getReturnType();
            $crt = $cm->getReturnType();
            $irs = $irt ? (string)$irt : '(none)';
            $crs = $crt ? (string)$crt : '(none)';
            if ($irs !== $crs) {
                if ($irs === '(none)' && $crs !== '(none)') {
                    $warns[] = "补充了返回类型 {$crs}(契约未声明)";
                } elseif ($irs !== '(none)' && $crs === '(none)') {
                    $fails[] = "丢失返回类型(契约 {$irs})";
                } else {
                    $fails[] = "返回类型 {$crs} != 契约 {$irs}";
                }
            }

            if ($fails) {
                if (!$printed) { echo "### $cls\n"; $printed = true; }
                echo "  [FAIL] $name " . param_spec($cm) . "\n";
                echo "         契约 " . $short . ' ' . param_spec($im) . "\n";
                echo "         -> " . implode('; ', $fails) . "\n";
                $fail++;
            } elseif ($warns) {
                if (!$printed) { echo "### $cls\n"; $printed = true; }
                echo "  [warn] $name " . param_spec($cm) . "  -> " . implode('; ', $warns) . "\n";
                $warn++;
            } else {
                $pass++;
            }
        }
    }
}

echo str_repeat('=', 72), "\n";
echo "一致: {$pass} 处,  警告: {$warn} 处,  漂移: {$fail} 处\n";
exit($fail > 0 ? 1 : 0);
