<?php
/**
 * DuckPhp Admin System - Profile
 */
$title = '个人信息';
?>
<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item active">个人信息</li>
        </ol>
    </nav>
</div>

<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success">保存成功</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= __h($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">个人信息</h5>
    </div>
    <div class="card-body">
        <form method="post" action="<?= __url('Home/profile') ?>">
            <div class="mb-3">
                <label class="form-label">用户名</label>
                <input type="text" class="form-control" value="<?= __h($user['username'] ?? '') ?>" readonly>
                <small class="text-muted">用户名不可修改</small>
            </div>

            <div class="mb-3">
                <label class="form-label">姓名</label>
                <input type="text" name="realname" class="form-control" value="<?= __h($user['realname'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">邮箱</label>
                <input type="email" name="email" class="form-control" value="<?= __h($user['email'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">新密码</label>
                <input type="password" name="password" class="form-control" minlength="6" placeholder="留空表示不修改密码">
                <small class="text-muted">如需修改密码请填写，最少6位</small>
            </div>

            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
</div>
