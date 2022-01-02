# DuckAdmin\
## 简介

`DuckAdmin` 是二次开发的基线库，注意，是基线库，而不是基线工程。

`DuckAdmin` 是个使用 `DuckPhp` 框架的库，你的工程使用`DuckPhp` 的所有功能，不需要魔改。

`DuckAdmin` 只做了公司员工结构的基本代码，并没有其他业务

## 运行

最简单的方式：

```php
composer create-project  dvaknheo/duckadmin myporject
cd myproject
php duckphp-project run
# 首次运行会安装填写数据库，密码

# 打开 127.0.0.1

```
这模式为允许 demo 目录下的代码。

有一个用户管理系统。用于管理外部注册是用户。

## 高级问题

### 前置知识

你需要了解 DuckPhp 的插件机制。

### 静态资源外放

为性能你需要修改 DuckAdmin\Api\DuckAdminPlugin 类的选项 duckadmin_res

### 接管视图

作为 DuckPhp 的插件。

### 使用 API
你的控制器可能会用到 DuckAdmin\Api\DuckAdminAction，的方法


你的业务代码，可能会用到 DuckAdmin\Api\DuckAdminService ，的方法。
### 修改实现

DuckAdmin\Api\DuckAdminPlugin::G(MyDuckAdminPlugin::G())