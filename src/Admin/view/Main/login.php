<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>后台登录 - <?= __h($app_name ?? 'Admin System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .login-card h3 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .login-card .form-control {
            border-radius: 8px;
            padding: 12px 16px;
        }
        .login-card .btn-primary {
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            width: 100%;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 13px;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h3>管理员登录</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= __h($error ?? '') ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?= __url('login') ?>">
            <div class="mb-3">
                <label class="form-label">用户名</label>
                <input type="text" name="username" class="form-control" placeholder="请输入用户名" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">密码</label>
                <input type="password" name="password" class="form-control" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn btn-primary">登 录</button>
        </form>
        
        <div class="login-footer">
            <p class="mb-0">DuckPhp Admin System</p>
        </div>
    </div>
</body>
</html>
