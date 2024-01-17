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
<table width="100%">
<tr>
	<th>ID</th>
	<th>用户名</th>
	<th>禁用/启用</th>
</tr>
<?php
    $list =[];
foreach ($list as $v) {?>
<tr>
	<td><?=$v['id']?></td>
	<td><?=$v['title']?></td>
	<td><a href="<?=$v['url_edit']?>">编辑</a></td>
</tr>
<?php }?>
</table>
            </div>
        </div>
    </body>
</html>
