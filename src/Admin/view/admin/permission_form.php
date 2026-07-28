<?php
/**
 * Permission Form (Create / Edit)
 */
$isEdit = isset($perm) && !empty($perm);
$data = $isEdit ? $perm : ($input ?? []);
$title = $isEdit ? '编辑权限' : '创建权限';
$current_route = 'permission';
?>

<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item"><a href="<?= __url('permission/index') ?>">权限管理</a></li>
            <li class="breadcrumb-item active"><?= __h($title) ?></li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= __h($error) ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?= __url($isEdit ? 'permission/update' : 'permission/save') ?>" class="row g-3">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
            <?php endif; ?>
            
            <div class="col-md-6">
                <label class="form-label">权限名称 <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                       value="<?= __h($data['name'] ?? '') ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">权限标识 <span class="text-danger">*</span></label>
                <input type="text" name="key" class="form-control" required
                       value="<?= __h($data['key'] ?? '') ?>"
                       placeholder="如: system.user.create">
                <small class="text-muted">用点号分隔的层级标识，如 system.user.list</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">上级权限</label>
                <select name="parent_id" class="form-select">
                    <option value="0">顶级权限</option>
                    <?php foreach ($permissions as $p): ?>
                        <?php if ($p['parent_id'] == 0): ?>
                            <option value="<?= (int)$p['id'] ?>" 
                                <?= (($data['parent_id'] ?? 0) == $p['id']) ? 'selected' : '' ?>>
                                <?= __h($p['name']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">排序</label>
                <input type="number" name="sort_order" class="form-control" 
                       value="<?= (int)($data['sort_order'] ?? 0) ?>">
            </div>
            
            <div class="col-12">
                <label class="form-label">描述</label>
                <textarea name="description" class="form-control" rows="2"><?= __h($data['description'] ?? '') ?></textarea>
            </div>
            
            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $isEdit ? '保存修改' : '创建权限' ?>
                </button>
                <a href="<?= __url('permission/index') ?>" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
