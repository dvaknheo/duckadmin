<?php
require_once(__DIR__.'/../vendor/autoload.php');
@include_once(__DIR__. '/LocalOverride.php');

$options=[
];

\DuckAdminDemo\System\DemoApp::RunQuickly($options);
