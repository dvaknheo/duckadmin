<?php
/**
 * Menu Index - Menu/
 * @var array $tree
 * @var string $show ('all'|'menu')
 * @var array $urls (list, create, edit, delete, scan)
 */
$title = '权限和菜单管理';

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
<?php if (($_GET['error'] ?? '') === 'has_children'): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> 该节点包含子节点，请先删除其下所有子节点。</div>
<?php endif; ?>
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
        <div class="btn-group me-2" role="group" aria-label="查看方式">
            <a href="<?= $urls['list'] ?>?show=all" class="btn btn-sm <?= ($show ?? 'all') === 'all' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                <i class="bi bi-list-ul"></i> 全部
            </a>
            <a href="<?= $urls['list'] ?>?show=menu" class="btn btn-sm <?= ($show ?? 'all') === 'menu' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                <i class="bi bi-diagram-3"></i> 不看操作
            </a>
        </div>
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
