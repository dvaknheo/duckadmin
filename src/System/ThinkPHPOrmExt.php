<?php declare(strict_types=1);

namespace DuckAdmin\System;

use DuckPhp\Component\DbManager as DuckDbManager;
use DuckPhp\Core\ComponentBase;
use think\DbManager;
use think\Container;

class ThinkPHPOrmExt extends ComponentBase
{
    public $options = [
        'overrider_callback' => null,
    ];
   
    public function init(array $options, ?object $context = null)
    {
        parent::init($options, $context);
        
        $this->bootThinkORM();
        if($this->options['overrider_callback'] && is_callable($this->options['overrider_callback'])) {
            call_user_func($this->options['overrider_callback']);
        }

        return $this;
    }
    /**
     * 初始化 ThinkPHP ORM
     */
    protected function bootThinkORM(): void
    {
        // 创建容器实例
        $container = new Container();
        
        // 创建 DbManager
        $dbManager = new DbManager($container);
        $dbManager->setConfig($this->getDatabaseConfig());
        
        // 将 DbManager 注册到容器
        $container->instance('db', $dbManager);
        
        // 设置全局容器（让 think\facade\Db 可用）
        Container::setInstance($container);
    }
    /**
     * 获取数据库配置，便于 override
     */
    protected function getDatabaseConfig(): array
    {
        $config_list = DuckDbManager::_()->getDatabaseConfigList();
        $config = $config_list[0] ?? null;
        $dsn = $config['dsn'] ?? '';
        [$driver, $file] = explode(':', $dsn);

        return [
            'default' => $driver,
            'connections' => [
                $driver => [
                    'type' => $driver,
                    'database' => $file,
                    'prefix' => '',
                    'charset' => 'utf8',
                ],
            ],
        ];
    }
}
