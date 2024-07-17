<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>浏览页面</title>
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <body class="pear-container">   
        <!-- 数据表格 -->
        <div class="layui-card">
            <div class="layui-card-body">
切换到现实全部
切换到正常
<table width="100%" border="1">
<tr>
	<th>ID</th>
	<th>用户名</th>
	<th>禁用/启用</th>
</tr>
<?php
foreach ($list as $v) {
?>
<tr>
	<td><?=$v['id']?></td>
	<td><?=$v['username']?></td>
	<td><a href="<?=$v['url_delete']?>">删除</a></td>
</tr>
<?php }?>
</table>
            </div>
        </div>
        <div>分页:<?=$pager?></div>
    </body>
</html>
