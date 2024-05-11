# DuckAdmin
正在不断开发中，暂时没稳定可运行版本。
说明不一定对
## 简介

`DuckAdmin` 是二次开发的基线库，注意，是基线库，而不是基线工程。

`DuckAdmin` 是个使用 `DuckPhp` 框架的库，你的工程使用`DuckPhp` 的所有功能，不需要魔改。

`DuckAdmin` 只做了公司员工结构的基本代码，通用业务。你可以在这之上添加更多符合公司业务的功能。

## 快速安装

基本需求： mysql sqlite

### demo 的安装

```
composer install dvaknheo/duckamin 
cd demo
php cli.php run
```
按照提示设置数据库
```
Database Setting:

```
### 正常作为库引入

```
composer install dvaknheo/duckamin 
```

根据 demo 的 DemoApp.php 填写相关代码
```php

class MyApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'path' => __DIR__.'/',
        
        'cli_command_with_fast_installer' => true,
        'namespace_controller' => '\\',
        'app' => [
            \DuckAdmin\System\DuckAdminApp::class => [      // 后台管理系统
                'controller_url_prefix' => 'app/admin/',    // 访问路径
                'controller_resource_prefix' => 'res/',     // 资源文件前缀
                
            ],
        ],
    ];
    public function __construct()
    {
    }
}
```
安装程序
```
php cli.php install
```
运行
```
php cli.php run
```
访问 127.0.0.1:8080

## 高级问题

### 前置知识

### 调整选项

### 静态资源外放

### 接管视图

`demo/view/DuckUser` 目录 就是demo工程调整后的视图

### 使用 API

### 修改实现


## TODO
