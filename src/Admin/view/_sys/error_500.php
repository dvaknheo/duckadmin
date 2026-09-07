<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>500 - 服务器错误</title>
    <style>
        body { font-family: sans-serif; text-align: left; padding: 80px 20px; color: #333; }
        h1 { font-size: 72px; margin: 0; color: #e74c3c; }
        p { font-size: 18px; margin: 20px 0; color: #666; }
        a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <h1>500</h1>
    <p>服务器内部错误，请稍后再试。</p>
    <p><a href="<?= __url('') ?>">返回首页</a></p>
<pre>
<?php var_dump($ex); debug_print_backtrace(2)?>
</pre>
</body>
</html>
