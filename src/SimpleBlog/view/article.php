<!doctype html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<body>
<h1>SimpleBlog 的默认界面太难看了，需要重载</h1>
<fieldset>
	<legend><?=$article['title']?></legend>
	<div>
		<?=$article['content']?>
	</div>
</fieldset>
<fieldset>
	<legend>评论列表</legend>
	<ul>
<?php foreach ($article['comments'] as $v) {?>
		<li><?=$v['content']?> (<?=$v['username']?> |<?=$v['created_at']?>)</li>
<?php }?>
	</ul>
	<?=$html_pager?>
</fieldset>
<fieldset>
	<legend>添加评论</legend>
<?php if ($user_id) {?>
	<form method="post" action="<?=$url_add_comment?>">
		<input name="article_id" type="hidden"  value="<?=$article['id']?>">
		<textarea name="content"></textarea>
		<input type="submit" value="提交">
	</form>
<?php } else { ?>
	<a href="<?=$url_login_to_commment?>">登录以添加评论</a>
<?php }?>
</fieldset>
</body>
</html>