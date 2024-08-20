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
<?php
    if($is_all){
?>
        当前显示未删除用户<a href="">切换到显示全部</a>
<?php
    }else{
?>
        当前显示所有用户<a href="">切换到正常模式</a>
<?php
    }
?>


<table width="100%" border="1">
<tr>
	<th>ID</th>
	<th>用户名</th>
	<th>禁用/启用</th>
</tr>
<?php
foreach ($users as $v) {
?>
<tr>
	<td><?=$v['id']?></td>
	<td><?=$v['username']?></td>
	<td>
<?php
    if($v['is_deleted']){
?>
        <a href="<?=$v['url_delete']?>">删除</a>
<?php
    }else{
?>
        <a href="<?=$v['url_undelete']?>">还原</a>
<?php
    }
?>
    </td>
</tr>
<?php
}
?>
</table>
            </div>
        </div>
        <div>分页:<?=$pager?></div>
    </body>
</html>
