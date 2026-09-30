<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\System;

use DuckPhp\Component\DbManager as DuckDbManager;
use DuckPhp\Core\ComponentBase;
use think\DbManager;
use think\Container;

class ThinkPHPOrmExt extends ComponentBase
{
    public $options = [
    ];
   
    public function init(array $options, ?object $context = null)
    {
        parent::init($options, $context);
        $this->bootThinkORM();
        return $this;
    }
    /**
     * 初始化 ThinkPHP ORM
     */
    public function bootThinkORM(): void
    {
        $config = $this->getDatabaseConfig();
        if (!$config) {
            return;
            //throw new \Exception("Database configuration not found.");
        }
        // 创建容器实例
        $container = new Container();
        
        // 创建 DbManager
        $dbManager = new DbManager($container);
        $dbManager->setConfig($config);
        
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
        if(!$dsn) {
            return [];
        }
        @list($driver, $file) = explode(':', $dsn);
        
        $file = \DuckPhp\Core\App::_()->getProjectPath() .$file;

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
