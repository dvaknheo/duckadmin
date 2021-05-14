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
<?php
$tree = [
    'a1'=>[
        'name'=>'a1',
        'href'=>'javascript:;',

    ],
    'a2'=>[
        'name'=>'a2',
        'href'=>'javascript:;',
    ],
    'a3'=>[
        'name'=>'a3',
        'href'=>'javascript:;',
    ],
    'a4'=>[
        'name'=>'a4',
        'href' => 'abc',
        'children'=>[
            'a41'=>[
                'name'=>'a4-1',
                'href'=>'javascript:;',
            ],
            'a42'=>[
                'name'=>'a4-2',
                'href'=>'javascript:;',
            ],
            'a43'=>[
                'name'=>'a4-3',
                'href' => 'abc',
                'children'=>[
                    'a431'=>[
                        'name'=>'a4-3-1',
                        'href'=>'#4ab',
                    ],
                    'a432'=>[
                        'name'=>'a4-3-2',
                        'href'=>'#ddd',
                    ],
                    'a433'=>[
                        'name'=>'a4-3-3',
                        'href' => '#abc',
                    ],
                ],
            ],
        ],
    ],
];
?>
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
    
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree">
        <li class="layui-nav-item">
            <?php __display('inc_side_menu',['nodes' => $tree]);?>
        </li>
        <li class="layui-nav-item">
          <a href="javascript:;">----分割线----</a>
        </li>
        <li class="layui-nav-item">
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
<!-- head end -->
