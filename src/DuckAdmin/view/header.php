<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>DuckAdmin 管理后台</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="apple-mobile-web-app-status-bar-style" content="black"> 
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="format-detection" content="telephone=no">
  
  <link href="<?=\DuckAdmin\__res("layui/css/layui.css")?>" rel="stylesheet" media="all">

</head>
<body>
  <script src="<?=\DuckAdmin\__res("layui/layui.js")?>"></script>
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo">DuckPhp管理后台</div>
    <!-- 头部区域（可配合layui 已有的水平导航） -->
    <div>

    </div>
    <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item">
        <a href="<?=\DuckAdmin\__url('Permission/show')?>">
          <i class="layui-icon layui-icon-group" style="color:red;"></i>
          <?=__h($admin['role'])?>
        </a>
        </li>
      <li class="layui-nav-item">
        <a href="javascript:;">
          <i class="layui-icon layui-icon-friends" style="color: #1E9FFF;"></i>
          <?=__h($admin['nickname'])?>(<?=__h($admin['username'])?>)
        </a>
        <dl class="layui-nav-child">
          <dd><a href="<?=\DuckAdmin\__url('Profile/index')?>">工作台首页</a></dd>
          <dd><a href="<?=\DuckAdmin\__url('Admin/password')?>">修改密码</a></dd>
          <dd><a href="<?=\DuckAdmin\__url('logout')?>">登出</a></dd>
        </dl>
      </li>
    </ul>
  </div>
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
    
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree">
        <li class="layui-nav-item">
            <?php // __display('inc_side_menu',['nodes' => $tree]);?>
        </li>
        <li class="layui-nav-item">
          <a href="javascript:;">----分割线----</a>
        </li>
        <li class="layui-nav-item">
        <a href="javascript:;">超级管理员专属</a>
        <dl class="layui-nav-child">
            <dd><a href="<?=\DuckAdmin\__url('Admin/index')?>">管理员管理</a></dd>
            <dd><a href="<?=\DuckAdmin\__url('Role/index')?>">职务(角色)管理</a></dd>
            <dd><a href="<?=\DuckAdmin\__url('Menu/index')?>">菜单和权限</a></dd>
            <dd><a href="/<?=\DuckAdmin\__url('Admin/log')?>">操作日志</a></dd>
        <dl>
        </li>

      </ul>
    </div>
  </div>

  <div class="layui-body" style="padding: 15px;">
<!-- head end -->