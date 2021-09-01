<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
@include_once(__DIR__.'/../../DNMVCS/autoload.php');    //@DUCKPHP_HEADFILE

require_once(__DIR__.'/../vendor/autoload.php');    //@DUCKPHP_HEADFILE

$options = [
    'ext' => [
    ],
];
$options['ext'][DuckAdmin\Api\DuckAdminPlugin::class]=[
    //
];
$options['ext'][SimpleAuth\Api\SimpleAuthPlugin::class]=[
    //
];

DuckPhp\DuckPhp::RunQuickly($options);
