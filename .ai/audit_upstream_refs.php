<?php declare(strict_types=1);
/**
 * DuckAdmin 上游引用审计
 *
 * 用途:上游 duckphp / duckcoverage 删除或改名类与方法(例如
 *      DuckPhp\Foundation\ExceptionTrait、DuckPhp\Foundation\Model\Helper、
 *      \DuckPhp\DuckPhp::Global())之后,src/ 里可能留下"引用已不存在的东西"——
 *      这类问题不会在 php -l 阶段暴露,只在运行到那一行时才致命。
 *
 * 用法: php .ai/audit_upstream_refs.php
 * 退出码: 0 = 未发现问题; 1 = 有问题
 *
 * 做法:逐个 php 文件解析 use 导入与 ::method() / ::_()->method() 调用,
 *      用反射确认存在的类/接口/trait 与存在的方法(先剥掉注释,避免误报)。
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$src = $root . '/src';

/** 只报告这些上游命名空间下的问题(DuckAdmin\ 自己的引用也查,便于发现死导入) */
$CHECK_PREFIX = ['DuckPhp\\', 'DuckCoverage\\', 'DuckAdmin\\', 'Illuminate\\', 'think\\', 'Topthink\\'];

function strip_comments(string $code): string
{
    $out = '';
    foreach (token_get_all($code) as $t) {
        if (is_array($t)) {
            if ($t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT) {
                $out .= str_repeat("\n", substr_count($t[1], "\n")); // 保留行号
                continue;
            }
            $out .= $t[1];
        } else {
            $out .= $t;
        }
    }
    return $out;
}

function need_check(string $fqcn, array $prefixes): bool
{
    foreach ($prefixes as $p) {
        if (strpos($fqcn, $p) === 0) {
            return true;
        }
    }
    return false;
}

$problems = [];
$files = 0;
$checkedUse = 0;
$checkedCall = 0;

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src));
foreach ($it as $f) {
    if ($f->getExtension() !== 'php') {
        continue;
    }
    $file = $f->getPathname();
    $rel = 'src/' . substr($file, strlen($src) + 1);
    $files++;
    $code = strip_comments((string)file_get_contents($file));

    // ---------- 1. use 导入是否还存在 ----------
    $alias = [];
    if (preg_match_all('/^use\s+([A-Za-z_][\w\\\\]*)(?:\s+as\s+(\w+))?\s*;/m', $code, $ms, PREG_SET_ORDER)) {
        foreach ($ms as $one) {
            $fq = ltrim($one[1], '\\');
            if (!need_check($fq, $CHECK_PREFIX)) {
                continue;
            }
            $pos = strrpos($fq, '\\');
            $alias[$one[2] ?? ($pos === false ? $fq : substr($fq, $pos + 1))] = $fq;
            $checkedUse++;
            if (!class_exists($fq) && !interface_exists($fq) && !trait_exists($fq)) {
                $problems[] = "$rel\n  [导入不存在] use $fq  (类/接口/trait 都没有)";
            }
        }
    }

    // ---------- 2. 别名上的静态/单例调用 ----------
    foreach ($alias as $short => $fq) {
        if (!class_exists($fq) && !interface_exists($fq)) {
            continue;
        }
        if (!preg_match_all('/\b' . preg_quote($short, '/') . '::(?:_\(\)->)?(\w+)\s*\(/', $code, $mm)) {
            continue;
        }
        try {
            $rc = new ReflectionClass($fq);
        } catch (\Throwable $e) {
            continue;
        }
        foreach (array_unique($mm[1]) as $method) {
            if ($method === '_') {
                continue;
            }
            $checkedCall++;
            if ($rc->hasMethod($method) || $rc->hasMethod('__callStatic') || $rc->hasMethod('__call')) {
                continue;
            }
            $line = 0;
            if (preg_match('/' . preg_quote($short, '/') . '::(?:_\(\)->)?' . preg_quote($method, '/') . '\s*\(/', $code, $l, PREG_OFFSET_CAPTURE)) {
                $line = substr_count(substr($code, 0, $l[0][1]), "\n") + 1;
            }
            $problems[] = "$rel:$line\n  [方法不存在] $fq::$method()";
        }
    }

    // ---------- 3. 内联全限定名调用 \Vendor\Ns\Class::method( ----------
    if (preg_match_all('/\\\\([A-Z][\w\\\\]+)::(?:_\(\)->)?(\w+)\s*\(/', $code, $fm, PREG_SET_ORDER)) {
        foreach ($fm as $one) {
            $fq = ltrim($one[1], '\\');
            $method = $one[2];
            if ($method === '_' || !need_check($fq, $CHECK_PREFIX)) {
                continue;
            }
            $checkedCall++;
            if (!class_exists($fq) && !interface_exists($fq)) {
                $problems[] = "$rel\n  [类不存在] \\$fq::$method()";
                continue;
            }
            try {
                $rc = new ReflectionClass($fq);
            } catch (\Throwable $e) {
                continue;
            }
            if ($rc->hasMethod($method) || $rc->hasMethod('__callStatic') || $rc->hasMethod('__call')) {
                continue;
            }
            $off = strpos($code, $one[0]);
            $line = $off === false ? 0 : substr_count(substr($code, 0, $off), "\n") + 1;
            $problems[] = "$rel:$line\n  [方法不存在] \\$fq::$method()";
        }
    }

    // ---------- 4. 未导入的类名静态调用(拼写错误 / 忘了 use) ----------
    $ns = '';
    if (preg_match('/^\s*namespace\s+([\w\\\\]+)\s*;/m', $code, $nm)) {
        $ns = $nm[1];
    }
    if (preg_match_all('/(?<![\\\\\w$>])([A-Z]\w*)::(\w+)\s*\(/', $code, $qm, PREG_SET_ORDER)) {
        foreach ($qm as $one) {
            $name = $one[1];
            $method = $one[2];
            if (in_array($name, ['self', 'static', 'parent'], true)) {
                continue;
            }
            if (isset($alias[$name])) {
                continue; // 已在第 1/2 步检查
            }
            $local = $ns === '' ? $name : $ns . '\\' . $name;
            if (class_exists($local) || interface_exists($local) || trait_exists($local)) {
                continue;
            }
            if (class_exists($name) || interface_exists($name)) {
                continue; // 全局类(如 PDO/DateTime)
            }
            $problems[] = "$rel\n  [类无法解析] $name::$method()  (既不是 $local,也不是全局类)";
        }
    }
}

echo "扫描 {$files} 个 php 文件; 检查导入 {$checkedUse} 处、上游方法调用 {$checkedCall} 处\n";
echo str_repeat('=', 72), "\n";
if (!$problems) {
    echo "src 中引用的上游类与方法全部存在\n";
    exit(0);
}
echo '发现 ' . count($problems) . " 个问题:\n\n";
foreach ($problems as $p) {
    echo $p, "\n\n";
}
exit(1);
