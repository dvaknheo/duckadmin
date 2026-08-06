<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>安装 - DuckUser</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
            color: #1a1a1a;
        }
        .card {
            max-width: 640px;
            margin: 40px auto;
            background: #fff;
            padding: 32px 36px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .card h1 { margin: 0 0 6px; font-size: 24px; }
        .card .subtitle { margin: 0 0 24px; color: #666; font-size: 14px; }
        h2 {
            font-size: 16px;
            margin: 24px 0 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .check-list { list-style: none; margin: 0; padding: 0; }
        .check-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
        }
        .check-list li:nth-child(odd) { background: #fafbfc; }
        .check-list .badge {
            flex: 0 0 auto;
            width: 22px; height: 22px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }
        .badge.ok { background: #22a06b; }
        .badge.fail { background: #d64545; }
        .check-list .detail { margin-left: auto; color: #888; font-size: 12px; }
        .btn {
            display: inline-block;
            padding: 10px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            background: #4a90d9;
            color: #fff;
            transition: background 0.2s;
        }
        .btn:hover:not(:disabled) { background: #3a7cc4; }
        .btn:disabled { background: #b8c4d0; cursor: not-allowed; }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 18px;
        }
        .alert.error { background: #fdecec; color: #b33; border: 1px solid #f5c2c2; }
        .state-box { text-align: center; padding: 24px 0 8px; }
        .state-box .icon { font-size: 48px; margin-bottom: 10px; }
        .state-box .desc { color: #666; font-size: 14px; margin: 8px 0 20px; }
        .foot { margin-top: 24px; text-align: center; color: #aaa; font-size: 12px; }
    </style>
</head>
<body>
<?php
$esc = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$installed = $installed ?? false;
$done = $done ?? false;
$checks = $checks ?? [];
$all_ok = $all_ok ?? true;
$error = $error ?? '';
?>
<div class="card">
    <h1>DuckUser 安装</h1>
    <p class="subtitle">用户中心子应用 · 首次运行请初始化数据库</p>

    <?php if ($installed): ?>
        <div class="state-box">
            <div class="icon">✅</div>
            <h2>DuckUser 已安装</h2>
            <p class="desc">用户中心已完成安装，无需重复执行。如需重装，请删除 runtime/DuckPhpData.config.json 中的 DuckUser 配置后重新访问。</p>
            <a class="btn" href="<?= $esc($url_home ?? '') ?>">进入用户中心</a>
        </div>
    <?php elseif ($done): ?>
        <div class="state-box">
            <div class="icon">🎉</div>
            <h2>安装完成</h2>
            <p class="desc">用户数据库已初始化（Users 表），现在可以注册与登录了。</p>
            <a class="btn" href="<?= $esc($url_home ?? '') ?>">进入用户中心</a>
        </div>
    <?php else: ?>

        <?php if ($error !== ''): ?>
            <div class="alert error"><?= $esc($error) ?></div>
        <?php endif; ?>

        <h2>环境自检</h2>
        <ul class="check-list">
            <?php foreach ($checks as $check): ?>
                <li>
                    <span class="badge <?= !empty($check['ok']) ? 'ok' : 'fail' ?>"><?= !empty($check['ok']) ? '✓' : '✗' ?></span>
                    <span><?= $esc($check['name']) ?></span>
                    <span class="detail"><?= $esc($check['detail']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (!$all_ok): ?>
            <div class="alert error">环境自检未通过，请先解决上述问题后再安装。</div>
        <?php endif; ?>
        <form method="post" action="">
            <button class="btn" type="submit" <?= $all_ok ? '' : 'disabled' ?>>开始安装</button>
        </form>

    <?php endif; ?>

    <div class="foot">DuckUser · DuckPhp</div>
</div>
</body>
</html>
