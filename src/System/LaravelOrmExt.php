<?php declare(strict_types=1);

namespace DuckAdmin\System;

use DuckPhp\Core\ComponentBase;
use Illuminate\Database\Capsule\Manager as Capsule;

class LaravelOrmExt extends ComponentBase
{
    public $options = [
        'orm_driver' => null,
        'overrider_callback' => null,
    ];
    
    public function init(array $options, ?object $context = null)
    {
        parent::init($options, $context);
        
        if ('laravel' !== $this->options['orm_driver']) {
            return $this;
        }
        
        $this->bootEloquent();
        
        if($this->options['overrider_callback'] && is_callable($this->options['overrider_callback'])) {
            call_user_func($this->options['overrider_callback']);
        }
        
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
        return [
            'driver' => 'sqlite',
            'database' => $this->getDatabasePath(),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ];
    }
    
    /**
     * 获取数据库文件路径，便于 override
     */
    protected function getDatabasePath(): string
    {
        return __DIR__ . '/../../database/admin.db';
    }
}
