<?php //regenerate by DuckPhp\Component\ExtOptionsLoader->saveExtOptions at 2024-05-11T17:46:16+08:00
return array (
  'DemoApp' => 
  array (
    'install' => '2024-04-11T21:15:26+08:00',
    'database_list' => 
    array (
      0 => 
      array (
        'username' => 'user1',
        'password' => '123456',
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=DuckAdminDb;charset=utf8mb4;',
      ),
    ),
    'redis_list' => 
    array (
      0 => 
      array (
        'host' => '127.0.0.1',
        'port' => '6379',
        'auth' => 'password1',
        'select' => '1',
      ),
    ),
    'is_debug' => true,
    'app_a' => '111',
    'app_b' => '33',
    'controller_url_prefix' => '',
    'controller_resource_prefix' => '',
  ),
  'DuckAdmin\\System\\DuckAdminApp' => 
  array (
    'abc' => 'def',
    'install' => '2024-05-08T12:57:14+08:00',
    'database_list' => 
    array (
      0 => 
      array (
        'username' => 'user1',
        'password' => '123456',
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=DuckAdminDb;charset=utf8mb4;',
      ),
    ),
  ),
  'DuckUser\\System\\DuckUserApp' => 
  array (
    'install' => '2024-05-08T10:16:51+08:00',
    'database_list' => 
    array (
      0 => 
      array (
        'dsn' => 'sqlite:/mnt/d/Code/DuckAdmin/src/DuckUser/config/s2.sqlite3',
        'username' => '',
        'password' => '',
      ),
    ),
  ),
  'DuckUserManager\\System\\DuckUserManagerApp' => 
  array (
  ),
  'SimpleBlog\\System\\SimpleBlogApp' => 
  array (
    'database_list' => 
    array (
      0 => 
      array (
        'dsn' => 'sqlite:/mnt/d/Code/DuckAdmin/demo/runtime/x.sqlite',
        'username' => '',
        'password' => '',
      ),
    ),
    'install' => '2024-05-11T17:46:16+08:00',
  ),
);