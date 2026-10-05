<?php declare(strict_types=1);
/**
 * 从 duckcoverage 的 report.jsonl 里列出「还没覆盖、而且值得补」的行。
 *
 * 背景（见 duckcoverage 的 docs 与 README）：
 *   report.jsonl 的 file 记录里有
 *     - unc[]  未执行的行号
 *     - sig[]  签名行：被静态分析算成可执行、但驱动永远不会标成执行的行
 *              （例如多行函数签名里的默认值）。这类行是「天花板」，补测试也补不出来。
 *   所以本工具默认把 sig 从 unc 里扣掉，只留真正能追的行；
 *   扣完为空的文件单独列出来，提醒「别再花时间」。
 *
 * 用法：
 *   php bin/coverage_gaps.php                     默认读 runtime/DuckCoverage/report.jsonl，列缺口 <=3 行的文件
 *   php bin/coverage_gaps.php --max=10            缺口 <=10 行
 *   php bin/coverage_gaps.php --top               按缺口行数从多到少排（找收益最大的）
 *   php bin/coverage_gaps.php --app=User          只看某个子应用（path 的第一段）
 *   php bin/coverage_gaps.php --file=src/User/view/install.php
 *   php bin/coverage_gaps.php --with-sig          不扣签名行（看原始 unc）
 *   php bin/coverage_gaps.php --jsonl=PATH        指定 jsonl 路径
 *
 * 注意：report.jsonl 由 `php bin/cli.php cover --report <组...> --jsonl=...` 产出；
 * 每份报告目录里也会自动有一份同源的 report.json。
 */

$opt = [
    'jsonl' => 'runtime/DuckCoverage/report.jsonl',
    'max' => 3,
    'app' => '',
    'file' => '',
    'with_sig' => false,
    'top' => false,
];
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--jsonl=(.*)$/', $arg, $m)) {
        $opt['jsonl'] = $m[1];
    } elseif (preg_match('/^--max=(\d+)$/', $arg, $m)) {
        $opt['max'] = (int)$m[1];
    } elseif (preg_match('/^--app=(.*)$/', $arg, $m)) {
        $opt['app'] = $m[1];
    } elseif (preg_match('/^--file=(.*)$/', $arg, $m)) {
        $opt['file'] = $m[1];
    } elseif ($arg === '--with-sig') {
        $opt['with_sig'] = true;
    } elseif ($arg === '--top') {
        $opt['top'] = true;
    } elseif ($arg === '--help' || $arg === '-h') {
        echo "用法见本文件头部注释。\n";
        exit(0);
    } else {
        fwrite(STDERR, "未知参数: {$arg}（--help 看用法）\n");
        exit(2);
    }
}

if (!is_file($opt['jsonl'])) {
    fwrite(STDERR, "找不到 {$opt['jsonl']}。\n");
    fwrite(STDERR, "先生成：php bin/cli.php cover --report <组...> --jsonl={$opt['jsonl']} --jsonl-no-timestamp\n");
    exit(1);
}

$files = [];
$meta = null;
$total = null;
$handle = fopen($opt['jsonl'], 'r');
while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $rec = json_decode($line, true);
    if (!is_array($rec)) {
        continue;   // 坏行/未知记录一律忽略，保证向前兼容
    }
    $t = $rec['t'] ?? '';
    if ($t === 'meta') {
        $meta = $rec;
    } elseif ($t === 'total') {
        $total = $rec;
    } elseif ($t === 'file') {
        $files[$rec['path']] = $rec;
    }
}
fclose($handle);

// 注意：同一个信息在两份输出里名字不同 —— report.json 顶层叫 dumps_merged，
// report.jsonl 的 meta 里叫 dumps。两个都认。
$dumps = $meta['dumps_merged'] ?? $meta['dumps'] ?? null;

if (!$files) {
    $merged = $total['records']['file'] ?? 0;
    fwrite(STDERR, "报告里没有任何 file 记录");
    fwrite(STDERR, $merged === 0 ? "（dumps=" . ($dumps ?? '?') . "，很可能这次没采到数据）\n" : "\n");
    exit(1);
}
$rows = [];
$ceil = [];
foreach ($files as $path => $rec) {
    if ($opt['app'] !== '' && strpos($path, $opt['app'] . '/') !== 0) {
        continue;
    }
    if ($opt['file'] !== '' && $path !== $opt['file']) {
        continue;
    }
    $unc = array_map('intval', (array)($rec['unc'] ?? []));
    $sig = array_map('intval', (array)($rec['sig'] ?? []));
    $real = $opt['with_sig'] ? $unc : array_values(array_diff($unc, $sig));
    $ceilSig = array_values(array_intersect($unc, $sig));
    $lines = $rec['lines'] ?? ['executable' => 0, 'executed' => 0];
    if (!$unc) {
        continue;
    }
    if (!$real) {
        $ceil[] = [$path, $lines, $ceilSig];
        continue;
    }
    $rows[] = [$path, $lines, $real, $ceilSig];
}

usort($rows, function ($a, $b) use ($opt) {
    $na = count($a[2]);
    $nb = count($b[2]);
    if ($na !== $nb) {
        return $opt['top'] ? $nb <=> $na : $na <=> $nb;
    }
    return strcmp($a[0], $b[0]);
});

$shown = array_values(array_filter($rows, function ($r) use ($opt) {
    return count($r[2]) <= $opt['max'];
}));

echo "=== 还值得补的缺口（未覆盖 <= {$opt['max']} 行，已扣掉签名行）===\n";
if (!$shown) {
    echo "  （没有；可以放宽 --max，或用 --top 看收益最大的）\n";
}
foreach ($shown as $r) {
    [$path, $lines, $real, $ceilSig] = $r;
    $pct = $lines['executable'] > 0 ? sprintf('%.2f%%', $lines['executed'] * 100 / $lines['executable']) : '-';
    printf(
        "  %2d行  %-52s %3d/%3d %7s  unc=[%s]%s\n",
        count($real),
        $path,
        $lines['executed'],
        $lines['executable'],
        $pct,
        implode(',', $real),
        $ceilSig ? '  (另有签名行 ' . implode(',', $ceilSig) . '，别追)' : ''
    );
}

if (!$opt['file']) {
    echo "\n=== 已经是天花板（缺口全是签名行，别再花时间）===\n";
    if (!$ceil) {
        echo "  （无）\n";
    }
    foreach ($ceil as $c) {
        printf("  %-52s %3d/%3d  sig=[%s]\n", $c[0], $c[1]['executed'], $c[1]['executable'], implode(',', $c[2]));
    }
}

$sumExec = 0;
$sumTot = 0;
foreach ($files as $rec) {
    $sumExec += (int)($rec['lines']['executed'] ?? 0);
    $sumTot += (int)($rec['lines']['executable'] ?? 0);
}
printf(
    "\n合计：%d 个文件，%d/%d 行（%.2f%%）%s\n",
    count($files),
    $sumExec,
    $sumTot,
    $sumTot > 0 ? $sumExec * 100 / $sumTot : 0,
    $dumps === null ? '' : "，dumps_merged={$dumps}"
);
if ($dumps === 0) {
    echo "⚠️  dumps_merged=0：这份报告没采到数据，百分比全 0 不代表真的没覆盖。\n";
}
echo "候选文件（<= {$opt['max']} 行）：" . count($shown) . " 个；天花板：" . count($ceil) . " 个。\n";
