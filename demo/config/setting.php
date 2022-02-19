<?php

function __env($key,$default)
{
    // 这里是通过 .env 文件配置
    return \DuckPhp\Core\App::Setting($key,$default);
}
$e =[
    'host'     =>__env('host',''),
    'dbname'   =>__env('dbname', ''),
    'username' =>__env('username', ''),
    'password' =>__env('password', ''),
];
return [
    // 'duckphp_is_debug' => true,  // 设置这项打开测试项
    'database_list' =>
        [[
            'dsn'=>"mysql:host={$e['host']};port=3306;dbname={$e['dbname']};charset=utf8;",
            'username'=>$e['username'],	
            'password'=>$e['password'],
        ],
        // 如果你要主从，需要自己设置，安装程序没有
        ],
    
];