<?php return
array (
  'logo' => 
  array (
    'title' => 'DuckPhp Admin',
    'image' => '@/logo.png', // 这里我们要换成资源里的
  ),
  'menu' => 
  array (
    'data' => 'rule/get',
    'method' => 'GET',
    'accordion' => true,
    'collapse' => false,
    'control' => false,
    'controlWidth' => 500,
    'select' => '0',
    'async' => true,
  ),
  'tab' => 
  array (
    'enable' => true,
    'keepState' => true,
    'session' => true,
    'preload' => false,
    'max' => '30',
    'index' => 
    array (
      'id' => '0',
      'href' => 'account/dashboard',
      'title' => '仪表盘',
    ),
  ),
  'theme' => 
  array (
    'defaultColor' => '2',
    'defaultMenu' => 'light-theme',
    'defaultHeader' => 'light-theme',
    'allowCustom' => true,
    'banner' => false,
  ),
  'colors' => 
  array (
    0 => 
    array (
      'id' => '1',
      'color' => '#36b368',
      'second' => '#f0f9eb',
    ),
    1 => 
    array (
      'id' => '2',
      'color' => '#2d8cf0',
      'second' => '#ecf5ff',
    ),
    2 => 
    array (
      'id' => '3',
      'color' => '#f6ad55',
      'second' => '#fdf6ec',
    ),
    3 => 
    array (
      'id' => '4',
      'color' => '#f56c6c',
      'second' => '#fef0f0',
    ),
    4 => 
    array (
      'id' => '5',
      'color' => '#3963bc',
      'second' => '#ecf5ff',
    ),
  ),
  'other' => 
  array (
    'keepLoad' => '500',
    'autoHead' => false,
    'footer' => false,
  ),
  'header' => 
  array (
    'message' => false,
  ),
);