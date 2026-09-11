<?php
/**
 * Permission Index - 权限分配(四级树形勾选)
 * @var array $tree 权限树(分组→目录→菜单/操作)
 * @var array $role
 * @var bool $is_super
 * @var array $role_permission_ids
 * @var bool $saved
 * @var string $error
 * @var array $urls (save, list)
 */
$title = '权限分配';

if (!function_exists('renderPermNode')) {
    /**
     * 递归渲染权限树节点(checkbox)
     */
    function renderPermNode($node, $checkedIds, $disabled) {
        $id = (int)$node['id'];
        $typeLabels = [0 => '目录', 1 => '菜单', 2 => '操作'];
        $typeColors = [0 => 'secondary', 1 => 'primary', 2 => 'info'];
        $type = (int)($node['type'] ?? 1);
        $checked = in_array($id, $checkedIds, true) ? ' checked' : '';
        $dis = $disabled ? ' disabled' : '';

        echo '<li class="perm-li mb-1">';
        echo '<div class="form-check">';
        echo '<input class="form-check-input perm-cb" type="checkbox" name="permission_ids[]" value="' . $id . '"'
            . ' id="perm_' . $id . '"' . $checked . $dis . ' onchange="cascadePerm(this)">';
        echo '<label class="form-check-label" for="perm_' . $id . '">'
            . __h($node['name'])
            . ' <span class="badge bg-' . ($typeColors[$type] ?? 'secondary') . '">' . ($typeLabels[$type] ?? '?') . '</span>';
        if (!empty($node['url'])) {
            echo ' <small class="text-muted"><code>' . __h($node['url']) . '</code></small>';
        }
        echo '</label></div>';
        if (!empty($node['children'])) {
            echo '<ul class="list-unstyled ms-4">';
            foreach ($node['children'] as $child) {
                renderPermNode($child, $checkedIds, $disabled);
            }
            echo '</ul>';
        }
        echo '</li>';
    }
}
?>
<div class="page-header">
    <h4>权限分配 - <?= __h($role['name']) ?>
        <?php if ($is_super): ?>
            <span class="badge bg-danger">超管</span>
        <?php endif; ?>
    </h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">权限分配</a></li>
            <li class="breadcrumb-item active"><?= __h($role['name']) ?></li>
        </ol>
    </nav>
</div>

<?php if ($saved): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> 保存成功</div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= __h($error) ?></div>
<?php endif; ?>

<?php if ($is_super): ?>
    <div class="alert alert-info"><i class="bi bi-info-circle"></i> 超级管理员职位拥有所有权限，无需分配（以下权限为只读展示）。</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= $urls['save'] ?>?id=<?= (int)$role['id'] ?>">
            <?php if (empty($tree)): ?>
                <p class="text-muted">暂无可用权限</p>
            <?php else: ?>
                <?php if (!$is_super): ?>
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAllPerms(true)">全选</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAllPerms(false)">全不选</button>
                    </div>
                <?php endif; ?>
                <ul class="list-unstyled">
                    <?php foreach ($tree as $node): ?>
                        <?php renderPermNode($node, array_map('intval', $role_permission_ids ?? []), $is_super); ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!$is_super): ?>
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> 保存权限
                </button>
                <a href="<?= $urls['list'] ?>" class="btn btn-outline-secondary">返回</a>
            <?php else: ?>
                <hr>
                <a href="<?= $urls['list'] ?>" class="btn btn-outline-secondary">返回</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
// 勾选父节点 => 级联所有子孙
function cascadePerm(cb) {
    var li = cb.closest('.perm-li');
    if (!li) return;
    li.querySelectorAll('ul .perm-cb').forEach(function(child) {
        child.checked = cb.checked;
    });
}
function checkAllPerms(v) {
    document.querySelectorAll('.perm-cb').forEach(function(cb) { cb.checked = v; });
}
</script>
