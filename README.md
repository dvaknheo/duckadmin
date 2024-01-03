# DuckAdmin
正在不断开发中，暂时没稳定可运行版本。
说明不一定对
## 简介

`DuckAdmin` 是二次开发的基线库，注意，是基线库，而不是基线工程。

`DuckAdmin` 是个使用 `DuckPhp` 框架的库，你的工程使用`DuckPhp` 的所有功能，不需要魔改。

`DuckAdmin` 只做了公司员工结构的基本代码，通用业务。你可以在这之上添加更多符合公司业务的功能。


## 运行

最简单的方式：

```php
composer require dvaknheo/duckadmin
cd demo
php go.php run
# 打开 127.0.0.1
# 进入后台管理系统，为系统安装


根据 demo 目录，酌情修改你的代码 。

目前 duckphp 系统还没有自动发现插件机制扩展。(我们做个自动发现插件的机制扩展？)

## 高级问题

### 前置知识

### 调整选项
### 静态资源外放
### 接管视图

作为 DuckPhp 的插件。

`demo/view/DuckUser` 目录 就是demo工程调整后的视图

### 使用 API


### 修改实现

遵从 `DuckPhp` 应用的可变单例模式，你要在想修改的类前面放这么一句：

```
DuckAdmin\Api\DuckAdminPlugin::G(MyDuckAdminPlugin::G())
``
