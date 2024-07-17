# DuckAdmin
## 简介

你一定会见过很多后台系统，都是在基线工程之上做二次开发。但是，这不符合`DuckPhp`的思维。

`DuckAdmin` 是个使用 `DuckPhp` 框架的库，你的工程使用`DuckPhp` 的功能，不需要魔改。

`DuckAdmin` 是二次开发的基线库，注意，是基线库，而不是基线工程。

`DuckAdmin` 只做了公司员工结构的基本代码，通用业务。你可以在这之上添加更多符合公司业务的功能。 甚至还带有 覆盖测试的库。

## DuckAdmin 要解决什么问题
1. 现在的管理后台，二次开发，都是在别家管理后台上改代码，但是 DuckAdmin 独树一帜的是基线库，不是基线工程
2. 静态资源 和代码分离 ，可部署在不同机器
3. 这个composer 库里，不仅仅包括 DuckAdmin 还包括了 DuckUser， DuckUserManager ,SimpleBlog 四个独立工程，以及一个独立演示的 DuckAdminDemo 工程。
  1. DuckUser 是个用户基线库，你也可以类似 DuckAdmin 那样在外面修改它 
  2. DuckUserManger 则是 用户管理系统
  3. SimpleBlog SimpleBlog 演示了用以上管理系统，用户系统的博客系统。
  4. 甚至还包括了覆盖测试的库。  DuckCoverage ，用于做覆盖测试

## 演示 demo
扩展需求：  sqlite

duckadmin 支持 mysql，只需要改配置，后面再演示

```
composer require dvaknheo/duckadmin
cd ./vendor/dvaknheo/duckadmin/demo
php cli.php run
```

访问 `http://127.0.0.1:8080/` 打开 Demo

### 重新安装

demo 里

你的数据会保存在 `vendor/dvaknheo/duckadmin/demo/runtime/database.db`

动态配置文件保存在 `vendor/dvaknheo/duckadmin/demo/config/DuckPhpApps.config.php`

删除这两个，可重新执行安装程序。

```
php cli.php install ## --force 可以强制执行 --help 查看参数
```


## 实际应用

### 复制 demo 的效果

```
composer require dvaknheo/duckphp
./vendor/bin/duckphp install
composer require dvaknheo/duckadmin
php cli.php require DuckAdmin/System/DuckAdminApp
php cli.php require DuckUser/System/DuckUserApp
php cli.php require SimpleBlog/System/SimpleBlogApp
php cli.php require DuckUserMangager/System/DuckUserMangagerApp

```
零代码虽然舒服，但不是最后解决方案


### 第二种方案

```
修改你的 System/App.php

```php
    public $options = [
        //...
        // 'allow_require_ext_app' => true,
        'app' => [
                \DuckAdmin\System\DuckAdminApp::class => [      // 后台管理系统
                    'controller_url_prefix' => 'app/admin/',    // 访问路径
                    // 'database_driver' =>'mysql',  // 如果你想改为 mysql 驱动
                    //... 其他配置
                    
                ],
            ],
        ];
```
运行
```
php cli.php require DuckAdmin/System/DuckAdminApp
```

## 安装程序
运行
```
php cli.php run
```
访问 http://127.0.0.1:8080/app/admin/index 打开管理后台。

这里只是 DuckAdmin 后台，不包括demo里的用户系统，用户管理，而博客例子

## 二次开发

### 前置知识

一些 duckphp 框架的基础知识不再这里重复

### 调整选项

DuckAdmin 支持 sqlite /mysql 两种模式的数据库 你可以在选项里切换 `database_driver`

希望几个版本之后 我们可以在安装程序里切换（目前只能手动切换

### 静态资源

原生时代，我们直接解压文件在web目录就行。

但是我们要做动态和静态资源分离。减缓服务器压力。

所以我们有了`controller_resource_prefix` 选项。

那资源文件在哪里呢？  `src/DuckAdmin/res` 目录之下。

你可以设置这个选项为完整的 http 路径，而不使用默认的

也可以设置成 / 开始的绝对路径。 默认是 'res/'， 安装程序会把他们复制到相应目录。

资源文件安装要点

因为扯到安装 ，我们设置 `controller_resource_prefix` ，克隆文件的必要性

### 接管视图

`demo/view/DuckAdmin` 目录 就是demo工程调整后的视图

### 使用 API

在你的工程里

使用 `DuckPhp\Foundation\Helper::Admin()` 获得 Admin 对象，
`AdminId($check_login)` , `AdminName($check_login)`  则是获取当前Id, 管理员名称
具体看 DuckPhp的文档， `DuckPhp\GlobalAdmin\GlobalAdmin` 的类介绍。

使用 `DuckPhp\Foundation\Helper::User()` 获得 User 通用对象，
具体看 DuckPhp的文档， `DuckPhp\GlobalUser\GlobalUser` 的类介绍。

### 修改现有实现

在 duckphp 框架的基础知识，不再阐述。

### 高级

菜单和权限。 如何在我的控制器里，加入菜单。 参考 demo 里 用户管理系统 的实现。


### 覆盖测试测试

```
cd demo
php cli.php duckcover  --watch testname --replay --report
```
testname 会在 demo/runtime 目录底下生成 test_coverage 生成报告
 
### 其他非 DuckPhp 框架代码如何整合 DuckAdmin 的管理后台系统

DuckAdminDemoApp::init([]); 之后

Helper::Admin() 就是管理员对象，
Helper::AdminId(); Helper::AdminName();

### 登录事件管理
暂缺

## TODO


缺乏 valido 验证系统 本来用 thinkphp 的应该可以，可惜太过复杂，后面版本又专一

缺乏 表单生成器， 这其实不重要。


## Demo 的文件结构

```
├── System
│   └── DemoApp.php
├── Test
│   └── MyTester.php
├── cli.php
├── config
│   ├── DuckPhpApps.config.php
│   └── DuckPhpSettings.config.php.sample
├── public
│   └── index.php
├── runtime
│   └── database.db
└── view
    ├── DuckAdmin
    │   └── account
    │       └── login.php
    ├── DuckUser
    │   ├── Home
    │   │   ├── inc-foot.php
    │   │   ├── inc-head.php
    │   │   ├── index.php
    │   │   └── password.php
    │   ├── inc-foot.php
    │   ├── inc-head.php
    │   ├── login.php
    │   ├── main.php
    │   └── register.php
    └── main.php

```
和典型的 DuckPhp 工程一样 System/DemoApp 是入口文件。

我们把主页嵌入到 DemoApp 类里。从而少了控制器目录

View 下是各种对 DuckAdmin 的视图的覆盖

Test 是测试覆盖

runtime 是数据库文件


