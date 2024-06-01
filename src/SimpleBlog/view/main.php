<!doctype html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<body>
<h1>SimpleBlog 的默认界面太难看了，需要重载</h1>
<?php if ($user_id) { ?>
	欢迎你 <?=$user_name?> 
	<a href="<?=$url_logout?>">登出</a>
<?php } else {?>
	<a href="<?=$url_login?>">登录</a>
	<a href="<?=$url_reg?>">注册</a>
<?php } ?>
[<a href="<?=$url_admin?>">管理</a>]
<h1></h1>
<fieldset>
<legend>最近文章</legend>
<ul>
<?php foreach ($articles as $v) {?>
	<li><a href="<?=$v['url']?>"><?=$v['title']?></a></li>
<?php }?>
</ul>
<?=$pager?>
</fieldset>
</body>
</html>