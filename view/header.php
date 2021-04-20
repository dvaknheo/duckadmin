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
  
  <link rel="stylesheet" href="/layui/css/layui.css"  media="all">
</head>
<body>
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo">DuckPhp管理后台</div>
    <!-- 头部区域（可配合layui 已有的水平导航） -->
    <ul class="layui-nav layui-layout-left">
      <li class="layui-nav-item"><a href="">nav 1</a></li>
      <li class="layui-nav-item"><a href="">nav 2</a></li>
      <li class="layui-nav-item"><a href="">nav 3</a></li>
      <li class="layui-nav-item">
        <a href="javascript:;">nav groups</a>
        <dl class="layui-nav-child">
          <dd><a href="">menu 11</a></dd>
          <dd><a href="">menu 22</a></dd>
          <dd><a href="">menu 33</a></dd>
        </dl>
      </li>
    </ul>
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item">
        <a href="javascript:;">
          <img src="http://tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg" class="layui-nav-img">
          admin
        </a>
        <dl class="layui-nav-child">
          <dd><a href="">set 1</a></dd>
          <dd><a href="">set 2</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item"><a href="">登出</a></li>
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