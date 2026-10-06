<?php declare(strict_types=1);
/**
 * 覆盖测试辅助：打印「按采集标记解析出的提供者」，用来验证 --flag 真的生效。
 *
 * 为什么需要它：采集标记是通过请求头生效的，如果标记没生效，
 * 整组采集会**静默地**测了另一个提供者 —— 这正是旧 runner 要校验
 * 「提供者写进本组配置了吗」的原因。现在由应用自己回答这个问题最可靠。
 *
 * 用法（标记走环境变量，避免被命令行解析当成子命令）：
 *   DUCKCOVERAGE_FLAG=single+demo php .ai/provider_probe.php
 *   DUCKCOVERAGE_FLAG=laravel+demo php .ai/provider_probe.php
 *   php .ai/provider_probe.php                       # 不带标记，看默认解析结果
 *
 * 输出（一行）：
 *   admin=SingleAdminApp  user=DemoUsersApp
 */
require __DIR__ . '/../vendor/autoload.php';

$_SERVER['HTTP_X_DUCKCOVERAGE_FLAG'] = (string)(getenv('DUCKCOVERAGE_FLAG') ?: '');

\DuckAdmin\System\DuckAdminApp::RunQuickly([], function () {
    $on = [];
    foreach (\DuckPhp\Core\App::_()->options['app'] as $class => $cfg) {
        if (!is_array($cfg)) {
            continue;
        }
        if (!empty($cfg['admin_provider_enable'])) {
            $on[] = 'admin=' . substr((string)strrchr($class, '\\'), 1);
        }
        if (!empty($cfg['user_provider_enable'])) {
            $on[] = 'user=' . substr((string)strrchr($class, '\\'), 1);
        }
    }
    echo implode('  ', $on), "\n";
});
