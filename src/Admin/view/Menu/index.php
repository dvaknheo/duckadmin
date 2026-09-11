<?php
/**
 * Menu Index - Menu/
 * @var array $tree
 * @var array $urls (list, create, edit, delete, scan)
 */
$title = '权限和菜单管理';
$current_route = 'system';

if (!function_exists('renderMenuNode')) {
    /**
     * 递归渲染树节点
     */
    function renderMenuNode($item, $urls, $level) {
        $typeLabels = [0 => '目录', 1 => '菜单', 2 => '操作'];
        $typeColors = [0 => 'secondary', 1 => 'primary', 2 => 'info'];
        $typeLabel = $typeLabels[(int)($item['type'] ?? 1)] ?? '未知';
        $typeColor = $typeColors[(int)($item['type'] ?? 1)] ?? 'secondary';

        $indent = $level > 0
            ? str_repeat('<span class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;</span>', $level - 1) . '<span class="text-muted">├─ </span>'
            : '';
        $name = htmlspecialchars($item['name']);
        $url = htmlspecialchars($item['url'] ?? '-');
        $weight = (int)($item['weight'] ?? 0);
        $id = (int)$item['id'];

        $html = "<tr>";
        $html .= "<td>{$id}</td>";
        $html .= "<td>{$indent}<strong>{$name}</strong></td>";
        $html .= "<td><code>{$url}</code></td>";
        $html .= "<td><span class=\"badge bg-{$typeColor}\">{$typeLabel}</span></td>";
        $html .= "<td>{$weight}</td>";
        $html .= "<td>";
        $html .= "<a href=\"{$urls['edit']}?id={$id}\" class=\"btn btn-sm btn-outline-primary\"><i class=\"bi bi-pencil\"></i></a> ";
        $html .= "<a href=\"{$urls['delete']}?id={$id}\" class=\"btn btn-sm btn-outline-danger\" onclick=\"return confirm('确定删除「{$name}」？')\"><i class=\"bi bi-trash\"></i></a>";
        $html .= "</td>";
        $html .= "</tr>";

        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                $html .= renderMenuNode($child, $urls, $level + 1);
            }
        }

        return $html;
    }
}
?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>权限和菜单管理</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">首页</a></li>
                <li class="breadcrumb-item active">权限和菜单管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= $urls['scan'] ?>" class="btn btn-outline-success">
            <i class="bi bi-search"></i> 一键扫描
        </a>
        <a href="<?= $urls['create'] ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> 新增菜单
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($tree)): ?>
            <div class="text-center text-muted py-5">暂无数据</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px">ID</th>
                            <th>权限名称</th>
                            <th>URL</th>
                            <th>类型</th>
                            <th>排序</th>
                            <th style="width:120px">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tree as $item): ?>
                            <?= renderMenuNode($item, $urls, 0) ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
