<?php
/**
 * Role Form (Create / Edit)
 */
$isEdit = isset($role) && !empty($role);
$data = $isEdit ? $role : ($input ?? []);
$title = $isEdit ? '编辑角色' : '创建角色';
$current_route = 'role';
?>

<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item"><a href="<?= __url('role/index') ?>">角色管理</a></li>
            <li class="breadcrumb-item active"><?= __h($title) ?></li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= __h($error) ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?= __url($isEdit ? 'role/update' : 'role/save') ?>" class="row g-3">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
            <?php endif; ?>
            
            <div class="col-md-6">
                <label class="form-label">角色名称 <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                       value="<?= __h($data['name'] ?? '') ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">描述</label>
                <textarea name="description" class="form-control" rows="3"><?= __h($data['description'] ?? '') ?></textarea>
            </div>
            
            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $isEdit ? '保存修改' : '创建角色' ?>
                </button>
                <a href="<?= __url('role/index') ?>" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
