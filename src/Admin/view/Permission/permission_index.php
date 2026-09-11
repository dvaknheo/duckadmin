<?php
/**
 * Permission Index - 权限分配(顶部选择职位 + 四级树形勾选)
 * @var array $roles 全部职位(顶部下拉)
 * @var array|null $role 当前选中职位
 * @var bool $is_super
 * @var array $tree 权限树(分组→目录→菜单/操作)
 * @var array $role_permission_ids
 * @var bool $saved
 * @var string $error
 * @var array $urls (self)
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
    <h4>权限分配</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">权限分配</li>
        </ol>
    </nav>
</div>

<div class="card mb-3">
    <div class="card-body d-flex align-items-center gap-3">
        <label class="form-label mb-0 fw-bold" for="role_switcher">选择职位：</label>
        <select id="role_switcher" class="form-select" style="max-width:320px"
                onchange="if(this.value) location.href='<?= $urls['self'] ?>?id='+this.value;">
            <option value="">-- 请选择职位 --</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= (int)$r['id'] ?>" <?= ($role && (int)$role['id'] === (int)$r['id']) ? 'selected' : '' ?>>
                    <?= __h($r['name']) ?><?= (int)($r['is_super'] ?? 0) === 1 ? '（超管）' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($role && $is_super): ?>
            <span class="badge bg-danger">超管</span>
        <?php endif; ?>
    </div>
</div>

<?php if ($saved): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> 保存成功</div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= __h($error) ?></div>
<?php endif; ?>

<?php if (!$role): ?>
    <div class="card"><div class="card-body text-center text-muted py-5">请先在上方选择要分配权限的职位</div></div>
<?php else: ?>
    <?php if ($is_super): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle"></i> 超级管理员职位拥有所有权限，无需分配（以下权限为只读展示）。</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= $urls['self'] ?>?id=<?= (int)$role['id'] ?>">
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

                <hr>
                <?php if (!$is_super): ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> 保存权限
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php endif; ?>

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
