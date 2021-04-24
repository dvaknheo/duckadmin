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
  
  <link href="/layui/css/layui.css" rel="stylesheet" media="all">
  <script src="/layui/layui.js?t=1617720346170" charset="utf-8"></script>
</head>
<body>
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo">DuckPhp管理后台</div>
    <!-- 头部区域（可配合layui 已有的水平导航） -->
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item">
        <a href="javascript:;">
          <img src="" class="layui-nav-img">
          <?= '【admin】'?>（<?= '【超级管理员】'?>）
        </a>
        <dl class="layui-nav-child">
          <dd><a href="">修改密码</a></dd>
          <dd><a href="<?=$url_logout?>">登出</a></dd>
        </dl>
      </li>
    </ul>
  </div>
  
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
    
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree" lay-filter="test">

        <li class="layui-nav-item layui-nav-itemed">
        <a href="javascript:;">超级管理员专属</a>
        <dl class="layui-nav-child">
            <dd><a href="javascript:;">管理员管理</a></dd>
            <dd><a href="javascript:;">职务(角色)管理</a></dd>
            <dd><a href="javascript:;">菜单和权限</a></dd>
            <dd><a href="javascript:;">操作日志</a></dd>
        <dl>
        </li>
      </ul>
    </div>
  </div>
  
  <div class="layui-body" style="padding: 15px;">
<!-- -->
<?php
/*
        <li class="layui-nav-item">
          <a class="" href="javascript:;">1</a>
          <dl class="layui-nav-child">
            <dd><a href="javascript:;">2</a></dd>
            <dd><a href="javascript:;">3</a></dd>
            <dd class="layui-nav-itemed">
                <a href="javascript:;" class="layui-nav-child-itemed">4</a>
                <dl >
                  <dd><a href="javascript:;" >4-1</a></dd>
                  <dd><a href="javascript:;">4-2</a></dd>
                  <dd class="layui-nav-itemed">
                      <a href="javascript:;">4-3</a>
                      <dl class="layui-nav-child">
                        <dd><a href="javascript:;" class="layui-this">4-3-1</a></dd>
                        <dd><a href="javascript:;" >4-3-2</a></dd>
                      </dl>
                      
                  
                  </dd>
                  <dd><a href="javascript:;">4-4</a></dd>
                </dl>
            </dd>
          </dl>
        </li>

//*/