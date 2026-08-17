<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>DemoUsers 登录</title>
<style>
body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f5f5f5; }
.login-box { background: #fff; padding: 2em 3em; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.15); width: 300px; }
h1 { font-size: 1.3em; margin-top: 0; }
label { display: block; margin: .8em 0 .3em; }
input { width: 100%; padding: .5em; box-sizing: border-box; }
button { width: 100%; margin-top: 1.2em; padding: .6em; background: #48a; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
.error { color: #c00; font-size: .9em; }
</style>
</head>
<body>
<div class="login-box">
    <h1>DemoUsers 登录</h1>
<?php if (!empty($error)) { ?>
    <p class="error"><?=__h($error)?></p>
<?php } ?>
    <form method="post">
        <label>用户名</label>
        <input type="text" name="username">
        <label>密码</label>
        <input type="password" name="password">
        <button type="submit">登录</button>
    </form>
</div>
</body>
</html>
