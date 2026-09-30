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
    protected function bootEloquent(): void
    {
        $capsule = new Capsule;
        $capsule->addConnection($this->getDatabaseConfig());
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
        [$driver, $file] = explode(':', $dsn);
        return [
            'driver' => $driver,
            'database' => $file,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ];
    }
    
}
