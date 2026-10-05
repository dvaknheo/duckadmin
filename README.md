# DuckAdmin
## 安全（部署前必读）

**DuckAdmin 默认不提供服务。** 必须在各环境的 `config/DuckPhpSettings.config.php` 里显式开启：

```php
return [
    // ...
    'duckadmin_enable' => true,
];
```

- 没有这个键、或写成 `false`：所有网页请求都会被挡下，只显示一个提示页（HTTP **503**），
  提示你到这里开启。**连安装页也挡**，所以不存在「忘了开就被别人装走」的情况。
- 默认关是有意的：这是「确认你有意把后台暴露出去」的开关，少一个配置项就少一份风险。
- 命令行不受影响：`php bin/cli.php`（含覆盖测试的 `cover`）照常可用。
- 改完若提示页还在，等一两秒再刷新（PHP 的 `opcache.revalidate_freq` 默认 2 秒，
  该配置文件会被缓存），必要时重启 php-fpm。

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

`DuckAdmin` 支持 mysql，只需改配置，后面再演示

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

删除这两个，可重新执行下列安装程序。

```
php cli.php install ## --force 可以强制执行 --help 查看参数
```

### Web 安装器重新安装（改数据库配置）

子应用已安装后，直接访问 `<前缀>/install` 会 302 回首页（框架行为，防止误入安装页）。
需要重装（例如换数据库文件、换管理员/用户提供者）时，显式带上 `force`：

```
GET  /<前缀>/install?force=1       # 显示安装表单
POST /<前缀>/install  force=1 ...  # 重装（force 会先执行 {driver}.clean.sql 再建表）
```

不带 `force` 时行为与框架原版一致。实现见 `src/System/WebInstaller.php`。

管理员 / 用户提供者在根应用首页的单选框中切换（写入 `runtime/DuckPhpData*.config.json`），
切换后请对对应子应用执行一次上述重装，让它的数据库配置重新初始化。

## 正常使用模式
### 第一种模式：快速 duckphp 工程并应用。
当你想复制 demo 例子，不想做任何操作，执行以下命令

```
composer require dvaknheo/duckadmin
composer exec duckphp new
php cli.php require DuckAdmin/System/DuckAdminApp
# 一路回车
#php cli.php require DuckUser/System/DuckUserApp
#php cli.php require SimpleBlog/System/SimpleBlogApp
#php cli.php require DuckUserMangager/System/DuckUserMangagerApp

php cli.php run

```
访问 http://127.0.0.1:8080/duck-admin/ 打开管理后台。


### 第二种模式：独立工程嵌入

当你要给个没有后台系统的  php-fpm 工程里使用。 demo 里的 `另一个简洁版本` 指向的 `main.php` 演示的这种情况

 `composer require dvaknheo/duckadmin`

核心功能代码如下
```
<?php
//我们演示在其他框架系统中嵌入 duckadmin
$options=[
    'path' => __DIR__.'/../', // 指定路径
    'skip_404' => true,      // 跳过内部 404处理。
    'app' => [
        DuckAdminApp::class => [      // 后台管理系统
            'controller_url_prefix' => 'app/admin/',    // 访问路径
            'installed' => '2024-06-10T22:12:01+08:00', // 安装标志
            'database_list' =>  //指定数据库
            [
                'dsn' => 'sqlite:demodb.db',
                'username' => '',
                'password' => '',
            ],
        ],
    ],    
];

$flag = DuckPhp::RunQuickly($options);
if ($flag) {
    return; // 后台管理系统完毕
}

//////////////////////////

// you code here.
$url_admin = __url('app/admin/index'); // 后台地址，和设置的 controller_url_prefix 一样

$admin_id = Helper::AdminId(false);     // 管理员ID
$admin_name = $admin_id ? Helper::AdminName() : ''; // 管理员名字
$url_logout = Helper::Admin()->urlForLogout();

```
可以看出，多了 skip_404 选项。 当返回404 的时候，自行处理其他业务
这里得到的 admin_id 就是登录后获得的id.


### 第三种模式，手动修改 duckphp工程嵌入

零代码虽然舒服，但不是最后解决方案

当你熟悉了 duckphp 工程结构之后。

在你的duckphp工程里修改你的 System/App.php 相关 配置

```php
    public $options = [
        //...

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
php cli.php run
```
访问 http://127.0.0.1:8080/app/admin/index 打开管理后台。

这里只是 DuckAdmin 后台，不包括demo里的用户系统，用户管理，而博客例子


## 二次开发

### 上游升级后的自检（重要）

`DuckPhp` 的上游接口改过签名（例如 `canAccess()` 的参数顺序把 `$url` 挪到第二位），
而本工程不少类**没有写 `implements`**（靠 App 选项里的 `local_service` / `login_service` /
`login_session` 绑定成上游服务），PHP 不会替我们检查 —— 这类问题只会在运行到那一行时才爆。

上游更新后请跑这两个脚本：

```
php bin/audit_signatures.php      # 契约签名审计:本工程实现与上游接口逐参数比对
php bin/audit_upstream_refs.php   # 上游引用审计:src 里引用的类/方法是否还存在
```

两者都是以退出码表示结果（0 通过 / 1 有问题），可以直接挂到 CI 或 git hook 上。

- `audit_signatures.php` 里的**角色表**（哪个类承担哪个契约）需要在新加子应用或换绑定时同步维护。
- 参数类型"放宽"（`?string` 对接口的 `string`）是 LSP 允许的，会报 warn 而不是 fail。
- 覆盖率 DSL（`CALL Class@method k=v`）是**按参数名传参**的，所以参数名也必须和契约一致。



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


### 覆盖测试

依赖 xdebug 的 coverage 模式（CLI 与 FPM 都要开）：`xdebug.mode=develop,coverage`。
开关与站点基址见 `config/DuckPhpSettings.config.php.sample`。

**按提供者分组建档**（同一时刻只能有一个管理员提供者 + 一个用户提供者生效，所以一个提供者一组）：

```bash
bash bin/cover_groups.sh               # 跑全部组并合并报告
bash bin/cover_groups.sh e_user f_blog # 只跑指定组
```

报告在 `runtime/DuckCoverage/AAAAA.report/`（多组合并）或 `<组名>.report/`（单组）。
手工等价于 `cover --watch <组名>` → POST 选提供者 → `cover --play` → `cover --report 组名...`；
**不要用 `cover --go`**，它的行为与手工四步有差异。

#### 行计数有两个口径，以 libcoverage 报告为准

- `runtime/DuckCoverage/*.report/index.html` —— 对外唯一口径。
- `php bin/coverage_gaps.php src/Admin` —— 按 php-code-coverage 自己的语句分析列出
  「真正没执行到的行」，用来找漏测点。它说「没有未执行的行」时报告仍可能 <100%，
  差额来自报告侧的行计数。

**已知的报告侧误报**：方法声明的参数里带默认值（尤其是数组默认值，例如
`public function log($user_id, string $string, ?string $type = null, array $ext = [])`）时，
**那一行声明会被报告算成可执行行，但覆盖驱动永远不会把它记为已执行**，该文件因此到不了 100%。
`src/DemoUsers/Business/UserBusiness.php` 就是例子：驱动数据 29/29 全命中，
报告固定 29/30 = 96.67%。这类差额不必再补测试。

#### 补覆盖的手段

1. 能用 HTTP 表达的加 `WEB` 步骤。uri 写「应用相对」的（框架会补 `controller_url_prefix`），
   跨应用才用绝对 uri。
2. HTTP 表达不了的（纯业务/模型方法、异常分支）用宏，它们会展开成
   `CALL <phase>!<namespace>\<层>\<类>@<方法> 参数`：
   - `#BUSINESS <类>@<方法> 参数` → 当前应用的 `Business\<类>`
   - `#MODEL <类>@<方法> 参数` → `Model\<类>`
   - `#ACTION <类>@<方法> 参数` → `Controller\<类>`
   - `#INCLUDE_CALL <调用>` → 直接展开成 `CALL`

   ⚠️ `CALL` 抛异常会中断整场 play：要踩错误分支请用 `WEB`（HTTP 请求不会中断 play），
   或像 `src/DemoUsers/System/TestLister.php` 的 `moreTest2()` 那样把每步各自 try。

当前进度（6 组、374 个 HTTP 请求、0 条应用错误日志）：Total 68.29% ——
Admin 69.18%、Admin_LaravelModel 61.85%、Admin_ThinkPHPModel 62.23%、DemoUsers 99.01%、
SimpleBlog 90.20%、SingleAdmin 100.00%、System 65.15%、User 45.03%。

### 多语言（安装向导等文案）

文案走 DuckPhp 的 Lang 组件，各子应用的语言文件放在自己的 `config/`：

```
src/Admin/config/lang-zh_CN.php        # Admin（Admin_LaravelModel / Admin_ThinkPHPModel 共用）
src/Admin/config/lang-en.php
src/User/config/lang-zh_CN.php         src/User/config/lang-en.php
src/SimpleBlog/config/lang-zh_CN.php   src/SimpleBlog/config/lang-en.php
```

- 默认语言是根应用 `DuckAdminApp` 的 `lang_default`（`zh_CN`）；子应用的 Lang
  通过 `lang_follow_root` 跟随根应用，所以只在根应用设一处。
- 切换方式：`?lang=en`、`lang` cookie，或浏览器的 `Accept-Language`。
- **主语言文件优先于 frag**（`Lang` 的注释：the main language file always wins,
  a frag only fills in the missing keys）——要改安装页文案就改 `lang-*.php`，
  不要再另建 `lang-*-for_webinstaller.php`，否则改了不生效。
- key 是扁平的（点号只是名字的一部分）；缺 key 会回落到框架内置英文，不会显示成裸 key。
- 安装器的 key 是 `webinstaller.*`；Admin 另有自定义字段的
  `Admin Name` / `Admin Password` / `Admin Password Confirm`。

### 其他非 DuckPhp 框架代码如何整合 DuckAdmin 的管理后台系统

### 登录事件管理
暂缺



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


## TODO


缺乏 validor 验证系统 本来用 thinkphp 的应该可以，可惜太过复杂，后面版本又专一

缺乏 表单生成器， 这其实不重要。
