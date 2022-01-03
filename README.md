# DuckAdmin\
## 简介

`DuckAdmin` 是二次开发的基线库，注意，是基线库，而不是基线工程。

`DuckAdmin` 是个使用 `DuckPhp` 框架的库，你的工程使用`DuckPhp` 的所有功能，不需要魔改。

`DuckAdmin` 只做了公司员工结构的基本代码，通用业务。你可以在这之上添加更多符合公司业务的功能。


## 运行

最简单的方式：

```php
composer create-project  dvaknheo/duckadmin myporject
cd myproject
php duckphp-project run
# 首次运行会安装填写数据库，密码
# 打开 127.0.0.1

```
这模式为允许 demo 目录下的代码。 会有一个用户管理系统。用于管理外部注册用户。


这只是DEMO， 正常模式下：

```php
composer require dvaknheo/duckadmin

```


## 高级问题

### 前置知识

你需要了解 DuckPhp 的插件机制。

### 静态资源外放

为性能你需要修改 DuckAdmin\Api\DuckAdminPlugin 类的选项 duckadmin_res


### 调整选项
```
    $this->options['ext'][\DuckAdmin\Api\DuckAdminPlugin::class]=[
        'plugin_url_prefix' => 'admin/',
        'table_prefix' => '',
        'session_prefix' => '',
        
    ];
```
### 接管视图

作为 DuckPhp 的插件。

demo/view/DuckUser 就是调整后的视图

### 使用 API
你的控制器可能会用到 DuckAdmin\Api\DuckAdminAction，的方法


你的业务代码，可能会用到 DuckAdmin\Api\DuckAdminService ，的方法。

不过，为了给你的应用工程师（你的小弟）使用，你应该自己封装入  DuckAdminDemo\System\ProjectController 和 DuckAdminDemo\System\ProjectBusiness 里

### 修改实现

遵从 DuckPhp 应用的可变单例模式，你要在想修改的类前面放这么一句：

DuckAdmin\Api\DuckAdminPlugin::G(MyDuckAdminPlugin::G())

### 在 DuckAdmin 的 url 中修改东西



###
难题，在 duckadmin 的项目中，相对 duckadmin 的 url 和相对 duckadmindemo 的  url
