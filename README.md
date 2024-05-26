# DuckAdmin
正在不断开发中，暂时没稳定可运行版本。
说明不一定对
## 简介

`DuckAdmin` 是二次开发的基线库，注意，是基线库，而不是基线工程。

`DuckAdmin` 是个使用 `DuckPhp` 框架的库，你的工程使用`DuckPhp` 的所有功能，不需要魔改。

`DuckAdmin` 只做了公司员工结构的基本代码，通用业务。你可以在这之上添加更多符合公司业务的功能。
## 演示
演示需求需求： mysql sqlite

```
composer install dvaknheo/duckamin 
cd demo
php cli.php run
```
得到欢迎页面，
```
php cli.php install # 安装数据库等完善功能
```
`cli_dev.php` `index_dev.php` 是开发测试的版本

### 正常作为库引入


```
composer require dvaknheo/duckphp
composer require dvaknheo/duckamin 
./vendor/bin/duckphp new   # 创建你的工程
```

模仿 demo 的 DemoApp.php 填写 `src/System\App.php`

```php

class App extends DuckPhp
{
    public $options = [
        'path' => __DIR__.'/',
        
        'cli_command_with_fast_installer' => true,
        'app' => [
            \DuckAdmin\System\DuckAdminApp::class => [      // 后台管理系统
                'controller_url_prefix' => 'app/admin/',    // 访问路径
                'controller_resource_prefix' => 'res/',     // 资源文件前缀
                
            ],
        ],
    ];
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
访问 127.0.0.1:8080/app/admin/ 打开管理后台。

## 高级问题

### 前置知识

### 调整选项

### 静态资源外放

### 接管视图

`demo/view/DuckUser` 目录 就是demo工程调整后的视图

### 使用 API
使用 `DuckPhp\Foundation\Helper::Admin()` 获得 Admin 对象，

使用 `DuckPhp\Foundation\Helper::User()` 获得 User 通用对象，

### 修改实现

## 测试
进入 demo 目录
```
php cli_dev.php testgroup --watch w1 --replay --report
```
demo/runtime 里查看结果
## 问答

问：DuckAdmin 为什么采用命令行方式安装，而不是 web 安装？

答：因为命令行权限> Web 权限，用这等方法可以避免 web 用户获取不必要的权限

问： 有没有手动加子应用的的方法：

比如我想在我的工程里

`php cli.php require DuckAdmin/System/DuckAdminApp`

这样把 DuckAdmin 加进去

答：考虑过，不过这容易出现： 这东西是从哪里来的。 所以还是手动配置你的 app 吧（考虑 选项里加一句 allow_install_sub_app_quickly

## TODO



