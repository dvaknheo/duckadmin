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
];
$plugin_options = [
];

define('DUCKADMIN_DIRECT_MODE', true);

DuckAdmin\App\DuckAdmin::RunAsPlugin($options, $plugin_options);
