<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
@include_once(__DIR__. '/../LocalOverride.php');
$file = __DIR__.'/../../vendor/autoload.php';
if(is_file($file)){
 require_once $file;
}else{
    $file = __DIR__.'/../../../../autoload.php';
    if(is_file($file)){
        require_once $file;
    }
}

$options=[
    // ...
];
\DuckAdminDemo\System\DemoApp::RunQuickly($options);
