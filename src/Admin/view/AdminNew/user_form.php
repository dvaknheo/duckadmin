<?php
/**
 * User Form (Create / Edit) - AdminNew
 * @var array $user  (edit mode)
 * @var array $input (create mode with validation errors)
 * @var string $error
 * @var array $urls  (save/update, list)
 * @var bool $is_edit
 */
$data = $is_edit ? $user : ($input ?? []);
$title = $is_edit ? '编辑人员' : '新增人员';
$form_url = $is_edit ? ($urls['update'] ?? '') : ($urls['save'] ?? '');
?>

<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">首页</a></li>
            <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">人员管理</a></li>
            <li class="breadcrumb-item active"><?= __h($title) ?></li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= __h($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= $form_url ?>" class="row g-3">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
            <?php endif; ?>

            <div class="col-md-6">
                <label class="form-label">用户名 <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" required
                       value="<?= __h($data['username'] ?? '') ?>"
                       <?= $is_edit ? 'readonly' : '' ?>>
            </div>

            <div class="col-md-6">
                <label class="form-label">密码 <?= $is_edit ? '<small class="text-muted">(留空不修改)</small>' : '<span class="text-danger">*</span>' ?></label>
                <input type="password" name="password" class="form-control"
                       <?= $is_edit ? '' : 'required' ?>
                       minlength="6" placeholder="<?= $is_edit ? '留空则不修改密码' : '至少6位密码' ?>">
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

            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $is_edit ? '保存修改' : '新增人员' ?>
                </button>
                <a href="<?= $urls['list'] ?>" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
