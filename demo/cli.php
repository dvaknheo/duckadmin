<?php
require_once(__DIR__.'/../vendor/autoload.php');

@require_once(__DIR__. '/LocalOverride.php');
require_once(__DIR__. '/DemoApp.php');

$options=[
];

\DuckPhp\HttpServer\HttpServer::_(\WorkermanHttpd\WorkermanHttpd::_()); //要不要放到  App 里？
\Demo\DemoApp::RunQuickly($options);
