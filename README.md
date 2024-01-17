# DuckAdmin
正在不断开发中，暂时没稳定可运行版本。
说明不一定对
## 简介

`DuckAdmin` 是二次开发的基线库，注意，是基线库，而不是基线工程。

`DuckAdmin` 是个使用 `DuckPhp` 框架的库，你的工程使用`DuckPhp` 的所有功能，不需要魔改。

`DuckAdmin` 只做了公司员工结构的基本代码，通用业务。你可以在这之上添加更多符合公司业务的功能。

## 安装
1. 修改数据库

`demo/config/DuckPhpApps.config.php`

```
      'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=webman_admin;charset=utf8mb4;',
```

2. 导入
`demo/demo.sql` 这是我正在用的 sql

管理员 `admin1` 密码  `123456`
用户 `user1` 密码 `123456`

## 运行

最简单的方式，克隆工程

```php
composer install
cd demo
php cli.php run
# 打开 127.0.0.1:8080

```
如果你要使用 workerman 做 server 

```php
composer install
cd demo
php start.php
# 打开 127.0.0.1:8080

```

## 高级问题

### 前置知识

### 调整选项
### 静态资源外放

### 接管视图
`demo/view/DuckUser` 目录 就是demo工程调整后的视图

### 使用 API

### 修改实现


## TODO
需要覆盖测试。