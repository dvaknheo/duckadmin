<?php
/**
 * Menu Scan - Menu/
 * @var array $added
 * @var array $menuTree
 * @var array $urls (list)
 */
$title = '一键扫描结果';

if (!function_exists('renderScanTree')) {
    function renderScanTree($nodes, $level = 0) {
        $html = '';
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
        foreach ($nodes as $n) {
            $typeLabels = [0 => '目录', 1 => '菜单', 2 => '动作', 3 => '特殊'];
            $typeColors = [0 => 'secondary', 1 => 'primary', 2 => 'info', 3 => 'warning'];
            $type = (int)($n['type'] ?? 1);
            $typeLabel = $typeLabels[$type] ?? '未知';
            $typeColor = $typeColors[$type] ?? 'secondary';
            $name = htmlspecialchars($n['name']);
            $url = htmlspecialchars($n['url'] ?? '-');
            $html .= "<div>{$indent}<span class=\"badge bg-{$typeColor}\">{$typeLabel}</span> <strong>{$name}</strong> <code>{$url}</code></div>";
            if (!empty($n['children'])) {
                $html .= renderScanTree($n['children'], $level + 1);
            }
        }
        return $html;
    }
}
?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>一键扫描结果</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">首页</a></li>
                <li class="breadcrumb-item active">一键扫描</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= $urls['list'] ?>" class="btn btn-outline-secondary">返回列表</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <?php if (empty($added)): ?>
            <p class="text-success mb-0">扫描完成,没有新增权限/菜单(所有路由均已入库)。</p>
        <?php else: ?>
            <p>扫描完成,新增 <?= count($added) ?> 条权限/菜单:</p>
            <ul>
                <?php foreach ($added as $url): ?>
                    <li><code><?= __h($url) ?></code></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-diagram-3"></i> 扫描结构预览（已同步到 config/scanned_menu.php）
    </div>
    <div class="card-body">
        <?php if (empty($menuTree)): ?>
            <div class="text-muted">无扫描结果</div>
        <?php else: ?>
            <?= renderScanTree($menuTree) ?>
        <?php endif; ?>
    </div>
</div>
