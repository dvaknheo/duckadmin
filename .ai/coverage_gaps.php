<?php declare(strict_types=1);
/**
 * 从 duckcoverage 的 report.jsonl 里列出「还没覆盖、而且值得补」的行。
 *
 * 字段来源（duckcoverage 本次补丁之后）：
 *   file.todo[]   真正还能补的行（= unc - sig）
 *   file.unc[]    未执行的行。本次补丁把签名行从统计口径里剔除了，所以通常 todo == unc
 *   file.sig[]    被剔除的签名行（多行函数签名里的声明/默认值行）。覆盖率驱动永远不会
 *                 把它们标成已执行，剔除之后文件才可能真正到 100%
 *   meta.warning  只有在「一份 dump 都没合并」时才出现，值是 "no dumps merged"
 *
 * 兼容：老版本没有 todo 字段时退化成 unc - sig。
 *
 * 用法：
 *   php .ai/coverage_gaps.php                  默认读 runtime/DuckCoverage/report.jsonl，列缺口 <=3 行的文件
 *   php .ai/coverage_gaps.php --max=10         缺口 <=10 行
 *   php .ai/coverage_gaps.php --top            按缺口从多到少排（找收益最大的）
 *   php .ai/coverage_gaps.php --app=User       只看某个子应用（path 的第一段）
 *   php .ai/coverage_gaps.php --file=src/User/view/install.php
 *   php .ai/coverage_gaps.php --jsonl=PATH     指定 jsonl 路径
 *   php .ai/coverage_gaps.php --with-sig       不扣签名行（看原始 unc；老版本才有意义）
 *
 * 注意：report.jsonl 由 `php bin/cli.php cover --report <组...> --jsonl=...` 产出；
 * 每份报告目录里也会自动有一份同源的 report.json（那边字段叫 todo_lines）。
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

// 同一个信息在两份输出里名字不同：report.json 顶层叫 dumps_merged，jsonl 的 meta 里叫 dumps。
$dumps = $meta['dumps_merged'] ?? $meta['dumps'] ?? null;
$warning = $meta['warning'] ?? null;

if (!$files) {
    fwrite(STDERR, "报告里没有任何 file 记录");
    fwrite(STDERR, $dumps === 0 || $warning ? "（dumps=" . ($dumps ?? '?') . "，warning=" . var_export($warning, true) . "）\n" : "\n");
    exit(1);
}

$rows = [];
$sigOnly = [];   // 已经 100%，但有被剔除的签名行 —— 说明口径里少了什么，不用再补
foreach ($files as $path => $rec) {
    if ($opt['app'] !== '' && strpos($path, $opt['app'] . '/') !== 0) {
        continue;
    }
    if ($opt['file'] !== '' && $path !== $opt['file']) {
        continue;
    }
    $unc = array_map('intval', (array)($rec['unc'] ?? []));
    $sig = array_map('intval', (array)($rec['sig'] ?? []));
    $lines = $rec['lines'] ?? ['executable' => 0, 'executed' => 0];
    // 新版本直接给 todo；老版本退化成 unc - sig
    $todo = isset($rec['todo'])
        ? array_map('intval', (array)$rec['todo'])
        : ($opt['with_sig'] ? $unc : array_values(array_diff($unc, $sig)));
    if (!$todo) {
        if ($sig) {
            $sigOnly[] = [$path, $lines, $sig];
        }
        continue;
    }
    $rows[] = [$path, $lines, $todo, $sig];
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

echo "=== 还值得补的缺口（未覆盖 <= {$opt['max']} 行）===\n";
if (!$shown) {
    echo "  （没有；可以放宽 --max，或用 --top 看收益最大的）\n";
}
foreach ($shown as $r) {
    [$path, $lines, $todo, $sig] = $r;
    $pct = $lines['executable'] > 0 ? sprintf('%.2f%%', $lines['executed'] * 100 / $lines['executable']) : '-';
    printf(
        "  %2d行  %-52s %3d/%3d %7s  todo=[%s]%s\n",
        count($todo),
        $path,
        $lines['executed'],
        $lines['executable'],
        $pct,
        implode(',', $todo),
        $sig ? '  (另剔除了签名行 ' . implode(',', $sig) . ')' : ''
    );
}

if (!$opt['file']) {
    echo "\n=== 已达 100%（其中剔除了签名行，无需再补）===\n";
    if (!$sigOnly) {
        echo "  （无）\n";
    }
    foreach ($sigOnly as $c) {
        printf("  %-52s %3d/%3d  剔除=[%s]\n", $c[0], $c[1]['executed'], $c[1]['executable'], implode(',', $c[2]));
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
    $dumps === null ? '' : "，dumps={$dumps}"
);
if ($warning || $dumps === 0) {
    echo "⚠️  这份报告没有采集到数据（warning=" . var_export($warning, true) . "，dumps=" . var_export($dumps, true) . "）：\n";
    echo "    百分比全是 0 不代表真的没覆盖。\n";
}
echo "候选文件（<= {$opt['max']} 行）：" . count($shown) . " 个。\n";

// 别让 --max 把"好看的小数字"变成误导：永远报出缺口更大、因而没被列出来的文件。
// （踩过：--max=4 时 AdminController 的 7 行缺口被静默过滤，被读成"已覆盖"。）
$bigger = array_values(array_filter($rows, function ($r) use ($opt) {
    return count($r[2]) > $opt['max'];
}));
if ($bigger) {
    $bigLines = 0;
    $maxRow = null;
    foreach ($bigger as $r) {
        $n = count($r[2]);
        $bigLines += $n;
        if ($maxRow === null || $n > count($maxRow[2])) {
            $maxRow = $r;
        }
    }
    printf(
        "⚠️  另有 %d 个文件缺口超过 %d 行（合计 %d 行）没列出来，最大的是 %s（%d 行）—— 用 --max=%d 或 --top 看。\n",
        count($bigger),
        $opt['max'],
        $bigLines,
        $maxRow[0],
        count($maxRow[2]),
        count($maxRow[2])
    );
}
