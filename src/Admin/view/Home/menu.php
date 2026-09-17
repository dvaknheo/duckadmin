<?php
/**
 * Home Menu - 查看我的菜单
 * @var array $tree 菜单树
 * @var bool $is_super 是否超管
 */
$title = '我的菜单';

if (!function_exists('renderMenuNode')) {
    /**
     * 递归渲染菜单树节点
     */
    function renderMenuNode($node) {
        $type = (int)($node['type'] ?? 1);
        $typeLabels = [0 => '目录', 1 => '菜单'];
        $typeColors = [0 => 'secondary', 1 => 'primary'];
        $typeLabel = $typeLabels[$type] ?? '菜单';
        $typeColor = $typeColors[$type] ?? 'primary';

        $hasChildren = !empty($node['children']);
        $url = !empty($node['url']) ? $node['url'] : '';

        echo '<li class="menu-li mb-1">';
        echo '<div class="menu-item">';
        echo '<span class="badge bg-' . $typeColor . '">' . $typeLabel . '</span> ';
        if ($url) {
            echo '<a href="' . __h($url) . '" class="menu-link">' . __h($node['name']) . '</a>';
        } else {
            echo '<span class="menu-name">' . __h($node['name']) . '</span>';
        }
        echo '</div>';

        if ($hasChildren) {
            echo '<ul class="list-unstyled ms-3">';
            foreach ($node['children'] as $child) {
                renderMenuNode($child);
            }
            echo '</ul>';
        }
        echo '</li>';
    }
}
?>
<style>
.menu-li { line-height: 1.8; }
.menu-link { text-decoration: none; color: inherit; }
.menu-link:hover { text-decoration: underline; color: #0d6efd; }
</style>
<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item active">我的菜单</li>
        </ol>
    </nav>
</div>

<?php if ($is_super): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> 您是超级管理员，可以访问所有菜单。
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($tree)): ?>
            <p class="text-muted">暂无菜单</p>
        <?php else: ?>
            <ul class="list-unstyled">
                <?php foreach ($tree as $node): ?>
                    <?php renderMenuNode($node); ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
