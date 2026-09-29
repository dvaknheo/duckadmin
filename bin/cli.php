<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
require_once __DIR__.'/../vendor/autoload.php';


$options=[
    // ...
];
//ini_set('display_errors', '1');
\DuckAdmin\System\DuckAdminApp::RunQuickly($options);