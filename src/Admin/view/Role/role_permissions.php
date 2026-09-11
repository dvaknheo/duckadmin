<?php
/**
 * Role - Permission Assignment
 */
$title = '角色权限分配';
$current_route = 'role';
?>

<div class="page-header">
    <h4>权限分配 - <?= __h($role['name']) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item"><a href="<?= __url('role/index') ?>">角色管理</a></li>
            <li class="breadcrumb-item active">权限分配</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= __url('role/permissions?id=' . $role['id']) ?>">
            <div class="row">
                <?php if (empty($permissions)): ?>
                    <p class="text-muted">暂无可用权限，请先 <a href="<?= __url('permission/create') ?>">创建权限</a></p>
                <?php else: ?>
                    <?php
                    $topPermissions = array_filter($permissions, function($p) { return $p['parent_id'] == 0; });
                    $childPermissions = array_filter($permissions, function($p) { return $p['parent_id'] > 0; });
                    ?>
                    
                    <?php foreach ($topPermissions as $top): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header" style="padding: 10px 16px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               value="<?= (int)$top['id'] ?>"
                                               id="perm_<?= (int)$top['id'] ?>"
                                               name="permission_ids[]"
                                               onchange="toggleChildren(this, <?= (int)$top['id'] ?>)"
                                               <?= in_array($top['id'], $role_permission_ids ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="perm_<?= (int)$top['id'] ?>">
                                            <?= __h($top['name']) ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" style="padding: 10px 16px;">
                                    <?php 
                                    $children = array_filter($childPermissions, function($c) use ($top) { 
                                        return $c['parent_id'] == $top['id']; 
                                    });
                                    ?>
                                    
                                    <?php if (empty($children)): ?>
                                        <small class="text-muted"><?= __h($top['url'] ?? '') ?></small>
                                    <?php else: ?>
                                        <?php foreach ($children as $child): ?>
                                            <div class="form-check">
                                                <input class="form-check-input child-perm child-of-<?= (int)$top['id'] ?>" 
                                                       type="checkbox" 
                                                       value="<?= (int)$child['id'] ?>"
                                                       id="perm_<?= (int)$child['id'] ?>"
                                                       name="permission_ids[]"
                                                       <?= in_array($child['id'], $role_permission_ids ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="perm_<?= (int)$child['id'] ?>">
                                                    <?= __h($child['name']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <hr>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> 保存权限
            </button>
            <a href="<?= __url('role/index') ?>" class="btn btn-outline-secondary">取消</a>
        </form>
    </div>
</div>

<script>
function toggleChildren(parentCheckbox, parentId) {
    var children = document.querySelectorAll('.child-of-' + parentId);
    children.forEach(function(child) {
        child.checked = parentCheckbox.checked;
    });
}
</script>
