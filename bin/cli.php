#!/usr/bin/env php
<?php
$file = __DIR__.'/../vendor/autoload.php';
if(is_file($file)){
 require_once $file;
}else{
    $file = __DIR__.'/../../../autoload.php';
    if(is_file($file)){
        require_once $file;
    }
}
//@include_once(__DIR__. '/LocalOverride.php');

\DuckPhp\Core\AutoLoader::RunQuickly([
    'psr-4'=>[
        'DuckAdminDemo\\' => __DIR__.'/',
        'DuckAdmin\\SingleAdmin\\' => __DIR__.'/../src/SingleAdmin/',
        'DuckAdmin\\DemoUsers\\' => __DIR__.'/../src/DemoUsers/',
    ],
]);

$options=[
];

\DuckAdminDemo\System\DemoApp::RunQuickly($options);
