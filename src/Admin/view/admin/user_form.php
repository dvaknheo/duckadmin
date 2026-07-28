<?php
/**
 * User Form (Create / Edit)
 * @var array $user  (edit mode)
 * @var array $input (create mode with validation errors)
 * @var array $roles
 * @var array $user_role_ids
 * @var string $error
 */
$isEdit = isset($user) && !empty($user);
$data = $isEdit ? $user : ($input ?? []);
$title = $isEdit ? '编辑用户' : '创建用户';
$current_route = 'user';
?>

<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item"><a href="<?= __url('user/index') ?>">用户管理</a></li>
            <li class="breadcrumb-item active"><?= __h($title) ?></li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= __h($error) ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?= __url($isEdit ? 'user/update' : 'user/save') ?>" class="row g-3">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
            <?php endif; ?>
            
            <div class="col-md-6">
                <label class="form-label">用户名 <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" required
                       value="<?= __h($data['username'] ?? '') ?>" 
                       <?= $isEdit ? 'readonly' : '' ?>>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">密码 <?= $isEdit ? '<small class="text-muted">(留空不修改)</small>' : '<span class="text-danger">*</span>' ?></label>
                <input type="password" name="password" class="form-control" 
                       <?= $isEdit ? '' : 'required' ?>
                       minlength="6" placeholder="<?= $isEdit ? '留空则不修改密码' : '至少6位密码' ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">姓名</label>
                <input type="text" name="realname" class="form-control" value="<?= __h($data['realname'] ?? '') ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">邮箱</label>
                <input type="email" name="email" class="form-control" value="<?= __h($data['email'] ?? '') ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">状态</label>
                <select name="status" class="form-select">
                    <option value="1" <?= (($data['status'] ?? 1) == 1) ? 'selected' : '' ?>>启用</option>
                    <option value="0" <?= (($data['status'] ?? 1) == 0) ? 'selected' : '' ?>>禁用</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">角色</label>
                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                    <?php foreach ($roles as $role): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="role_ids[]" 
                                   value="<?= (int)$role['id'] ?>"
                                   id="role_<?= (int)$role['id'] ?>"
                                   <?= in_array((string)$role['id'], array_map('strval', $user_role_ids ?? []), true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="role_<?= (int)$role['id'] ?>">
                                <?= __h($role['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $isEdit ? '保存修改' : '创建用户' ?>
                </button>
                <a href="<?= __url('user/index') ?>" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
