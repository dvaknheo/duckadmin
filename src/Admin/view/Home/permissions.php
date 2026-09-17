<?php
/**
 * Home Permissions - 查看我的权限
 * @var array $tree 权限树
 * @var array $permission_ids 用户拥有的权限ID
 * @var bool $is_super 是否超管
 */
$title = '我的权限';

if (!function_exists('renderPermNode')) {
    /**
     * 递归渲染权限树节点
     */
    function renderPermNode($node, $checkedIds) {
        $id = (int)$node['id'];
        $typeLabels = [0 => '目录', 1 => '菜单', 2 => '操作'];
        $type = (int)($node['type'] ?? 1);
        $checked = in_array($id, $checkedIds, true);

        if (!$checked) {
            return;
        }

        $typeColors = [0 => 'secondary', 1 => 'primary', 2 => 'warning'];
        $typeLabel = $typeLabels[$type] ?? '?';
        $typeColor = $typeColors[$type] ?? 'secondary';
        $showUrl = !empty($node['url']) ? ' <code class="perm-url">' . __h($node['url']) . '</code>' : '';

        echo '<li class="perm-li mb-1">';
        echo '<div class="perm-item">';
        echo '<span class="perm-name">';
        if ($type === 0) {
            echo '<strong>' . __h($node['name']) . '</strong>';
        } else {
            echo '<span class="badge bg-' . $typeColor . '">' . $typeLabel . '</span> ' . __h($node['name']) . $showUrl;
        }
        echo '</span>';
        echo '</div>';
        if (!empty($node['children'])) {
            echo '<ul class="list-unstyled ms-3">';
            foreach ($node['children'] as $child) {
                renderPermNode($child, $checkedIds);
            }
            echo '</ul>';
        }
        echo '</li>';
    }
}
?>
<style>
.perm-li { line-height: 1.8; }
.perm-item { display: inline-block; }
.perm-url { display: none; font-size: 12px; color: #888; }
.perm-item:hover .perm-url { display: inline; }
</style>
<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item active">我的权限</li>
        </ol>
    </nav>
</div>

<?php if ($is_super): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> 您是超级管理员，拥有所有权限。
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($tree)): ?>
            <p class="text-muted">暂无可用权限</p>
        <?php else: ?>
            <ul class="list-unstyled">
                <?php foreach ($tree as $node): ?>
                    <?php renderPermNode($node, array_map('intval', $permission_ids ?? [])); ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
