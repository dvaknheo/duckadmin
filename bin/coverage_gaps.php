<?php declare(strict_types=1);
/**
 * 列出覆盖测试里「真正没执行到的行」,按文件汇总 —— 用来规划下一步该补什么测试步骤。
 *
 * 用法:
 *   php bin/coverage_gaps.php src/Admin              # 看某个目录
 *   php bin/coverage_gaps.php src/Admin/Business/RoleBusiness.php
 *   php bin/coverage_gaps.php src/ --group=e_user    # 只看某一组
 *
 * 读的是 php-code-coverage 自己的行分析结果(getReport() -> Node\File::lineCoverageData())。
 *
 * 注意:它与项目里 libcoverage 生成的 HTML 报告**口径不同** —— 报告会把一些
 * php-code-coverage 不认为是语句的行(例如带默认参数的方法签名、闭合括号)也算进分母,
 * 所以报告百分比普遍低于这里的判断。两边都要看:
 *   - 这里说「没有未执行的行」→ 按 php-code-coverage 的口径,该文件没有漏测的语句;
 *   - 报告仍显示 <100% → 差额来自报告侧的行计数,不是漏测。
 */
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

use SebastianBergmann\CodeCoverage\CodeCoverage;

$prefix = 'src/';
$group = '';
foreach (array_slice($argv, 1) as $a) {
    if (str_starts_with($a, '--group=')) {
        $group = substr($a, 8);
    } else {
        $prefix = $a;
    }
}

$pattern = $root . '/runtime/DuckCoverage/' . ($group !== '' ? $group : '*') . '/*.php';
$dumps = glob($pattern) ?: [];
if (!$dumps) {
    fwrite(STDERR, "没找到覆盖数据($pattern),先跑 bin/cover_groups.sh\n");
    exit(1);
}

$merged = null;
foreach ($dumps as $f) {
    $cov = @include $f;
    if (!($cov instanceof CodeCoverage)) {
        continue;
    }
    if ($merged === null) {
        $merged = $cov;
    } else {
        $merged->merge($cov);
    }
}

// 注意:不能用 getData()->lineCoverage() —— 那是驱动(xdebug)的原始记录,只包含「执行过的行」,
// 未执行的行压根不在表里,于是每个文件都会显示"0 行未覆盖"。
// 必须走 getReport():它会对每个文件做静态分析,列出「可执行但没执行到」的行(-1)。
$prefix_abs = $root . '/' . ltrim($prefix, '/');

$rows = [];
$collect = function ($node) use (&$collect, $prefix_abs, &$rows) {
    if ($node instanceof \SebastianBergmann\CodeCoverage\Node\Directory) {
        foreach ($node->directories() as $d) {
            $collect($d);
        }
        foreach ($node->files() as $f) {
            $collect($f);
        }
        return;
    }
    if (!($node instanceof \SebastianBergmann\CodeCoverage\Node\File)) {
        return;
    }
    $path = $node->pathAsString();
    if (strpos($path, $prefix_abs) !== 0) {
        return;
    }
    $hit = 0;
    $miss = [];
    // php-code-coverage 9.2 里 Node\File 的 API 是 lineCoverageData()(不是 lineCoverage())
    foreach ($node->lineCoverageData() as $n => $st) {
        if ($st >= 1) {
            $hit++;
        } elseif ($st === -1) {
            $miss[] = $n;
        }
    }
    if ($miss) {
        $rows[$path] = [$hit, $miss, $node->numberOfExecutableLines(), $node->numberOfExecutedLines()];
    }
};
$collect($merged->getReport());

uksort($rows, fn($a, $b) => count($rows[$b][1]) <=> count($rows[$a][1]));

$total_miss = 0;
foreach ($rows as $file => [$hit, $miss, $executable, $executed]) {
    $total_miss += count($miss);
    printf(
        "\n%s  (%d/%d 行,未执行 %d)\n",
        str_replace($root . '/', '', $file),
        $executed,
        $executable,
        count($miss)
    );
    $src = file($file) ?: [];
    foreach ($miss as $n) {
        printf("   %4d: %s\n", $n, rtrim((string)($src[$n - 1] ?? '')));
    }
}
printf("\n合计未执行 %d 行,分布在 %d 个文件(数据源 %d 份采集)\n", $total_miss, count($rows), count($dumps));
