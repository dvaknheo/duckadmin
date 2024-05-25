<table border="1">
<tr>
	<th>ID</th>
	<th>文章标题</th>
	<th>评论内容</th>
	<th>用户</th>
	<th>删除</th>
</tr>
<?php
foreach ($list as $v) {?>
<tr>
	<td><?=$v['id']?></td>
	<td><?=$v['title']?></td>
	<td><?=$v['content']?></td>
	<td><?=$v['username']?>(<?=$v['user_id']?>)</td>
	<td><a href="<?=$v['url_delete']?>"><?=$v['id']?></a></td>
</tr>
<?php }?>
</table>