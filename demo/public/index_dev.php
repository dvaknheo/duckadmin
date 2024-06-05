<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

require_once(__DIR__.'/../../vendor/autoload.php');

@include_once(__DIR__. '/../LocalOverride.php');
require_once(__DIR__. '/../DemoApp.php');
require_once(__DIR__. '/../DemoAppWithDev.php');

$options=[
    // ...
];
\DuckAdminDemo\DemoApp::_(\DuckAdminDemo\DemoAppWithDev::_());
\DuckAdminDemo\DemoApp::RunQuickly($options);
