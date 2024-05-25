<?php
require_once(__DIR__.'/../vendor/autoload.php');

@include_once(__DIR__. '/LocalOverride.php');
require_once(__DIR__. '/DemoApp.php');

$options=[
];

\Demo\DemoApp::RunQuickly($options);
