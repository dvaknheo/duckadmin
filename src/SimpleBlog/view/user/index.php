<?php declare(strict_types=1);
// userController::index() 的视图。
// 原来这里缺文件：Helper::Show($data) 会去找 view/user/index.php，
// 而它不存在，于是 /blog/user/index 直接 500（只是平时没人访问到）。
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>我的评论</title>
</head>
<body>
<h1>我的评论</h1>
<p>这里是当前用户的评论列表。</p>
<p class="todo">TODO：显示我的评论，并支持在此删除评论。</p>
<hr>
<p><a href="<?= __h(__url('')) ?>">返回首页</a></p>
</body>
</html>
