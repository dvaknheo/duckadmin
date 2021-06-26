<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
@include_once(__DIR__.'/../../DNMVCS/autoload.php');    //@DUCKPHP_HEADFILE

require_once(__DIR__.'/../vendor/autoload.php');    //@DUCKPHP_HEADFILE

$options = [
    'is_debug'=>true,
    'use_setting_file'=>true,
    'ext' => [
    ],
];
$options['ext'][DuckAdmin\System\App::class]=[
    //
];
$options['ext'][SimpleAuth\System\App::class]=[
    //
];

DuckPhp\DuckPhp::RunQuickly($options);
