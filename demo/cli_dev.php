<?php
require_once(__DIR__.'/../vendor/autoload.php');

@include_once(__DIR__. '/LocalOverride.php');
require_once(__DIR__. '/DemoApp.php');
require_once(__DIR__. '/DemoAppWithDev.php');

$options=[
];

\DuckPhp\HttpServer\HttpServer::_(\WorkermanHttpd\WorkermanHttpd::_()); //要不要放到  App 里？
\Demo\DemoApp::_(\Demo\DemoAppWithDev::_()); // TODO override 模式有问题。
\Demo\DemoAppWithDev::RunQuickly($options);
