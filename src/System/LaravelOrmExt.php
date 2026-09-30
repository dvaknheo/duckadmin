<?php declare(strict_types=1);

namespace DuckAdmin\System;

use DuckPhp\Core\ComponentBase;
use DuckPhp\Component\DbManager;
use Illuminate\Database\Capsule\Manager as Capsule;

class LaravelOrmExt extends ComponentBase
{
    public $options = [
    ];
    
    public function init(array $options, ?object $context = null)
    {
        parent::init($options, $context);
        $this->bootEloquent();
        return $this;
    }
    /**
     * 初始化 Eloquent ORM
     */
    public function bootEloquent(): void
    {
        $config = $this->getDatabaseConfig();
        if (!$config) {
            return;
            //throw new \Exception("Database configuration not found.");
        }
        $capsule = new Capsule;
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
    
    /**
     * 获取数据库配置，便于 override
     */
    protected function getDatabaseConfig(): array
    {
        $config_list = DbManager::_()->getDatabaseConfigList();
        $config = $config_list[0] ?? null;
        $dsn = $config['dsn'] ?? '';
        if(!$dsn) {
            return [];
        }
        @list($driver, $file) = explode(':', $dsn);
        
        $file = \DuckPhp\Core\App::_()->getProjectPath() .$file;
        return [
            'driver' => $driver,
            'database' => $file,
            'prefix' => '',
            //'foreign_key_constraints' => true,
            'foreign_key_constraints' => false,
        ];
    }
    
}
