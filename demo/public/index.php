<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

$file = __DIR__.'/../../vendor/autoload.php';
if(is_file($file)){
 require_once $file;
}else{
    $file = __DIR__.'/../../../../vendor/autoload.php';
    if(is_file($file)){
        require_once $file;
    }
}
@include_once(__DIR__. '/../LocalOverride.php');

$options=[
    // ...
];
\DuckAdminDemo\System\DemoApp::RunQuickly($options);
