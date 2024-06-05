<?php
require_once(__DIR__.'/../vendor/autoload.php');

@include_once(__DIR__. '/LocalOverride.php');

$options=[
];

\DuckAdminDemo\DemoApp::_(\DuckAdminDemo\DemoAppWithDev::_());
\DuckAdminDemo\DemoApp::RunQuickly($options);
