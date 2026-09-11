<?php
/**
 * Role Form (Create / Edit) - 职位表单
 */
$isEdit = isset($role) && !empty($role);
$data = $isEdit ? $role : ($input ?? []);
$title = $isEdit ? '编辑职位' : '新增职位';
?>
<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item"><a href="<?= __url('role/index') ?>">职位管理</a></li>
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
                <label class="form-label">职位名称 <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                       value="<?= __h($data['name'] ?? '') ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">上级职位</label>
                <select name="pid" class="form-select">
                    <option value="0">根职位(顶级)</option>
                    <?php foreach ($roles ?? [] as $r): ?>
                        <?php if (($data['pid'] ?? 0) != $r['id']): ?>
                            <option value="<?= (int)$r['id'] ?>" <?= (($data['pid'] ?? 0) == $r['id']) ? 'selected' : '' ?>>
                                <?= __h($r['name']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">只能在你的管理范围内选择上级职位</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">描述</label>
                <textarea name="description" class="form-control" rows="3"><?= __h($data['description'] ?? '') ?></textarea>
            </div>
            
            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $isEdit ? '保存修改' : '新增职位' ?>
                </button>
                <a href="<?= __url('role/index') ?>" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
