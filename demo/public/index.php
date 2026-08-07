<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
require_once __DIR__.'/../../vendor/autoload.php';

DuckPhp\Core\AutoLoader::RunQuickly([
    'psr-4'=>[
        "DuckAdminDemo\\" => __DIR__."/../"
    ],
]);
$options=[
    // ...
];
\DuckAdminDemo\System\DemoApp::RunQuickly($options);
