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
     * 支持 sqlite(相对/绝对路径) 与 mysql/pgsql 等 DSN
     */
    protected function getDatabaseConfig(): array
    {
        $config_list = DuckDbManager::_()->getDatabaseConfigList();
        $config = $config_list[0] ?? null;
        if (!$config) {
            return [];
        }
        $dsn = (string)($config['dsn'] ?? '');
        if ($dsn === '') {
            return [];
        }
        $parts = explode(':', $dsn, 2);
        $driver = $parts[0];
        $rest = $parts[1] ?? '';

        $conn = [
            'type' => $driver,
            'prefix' => '',
            'charset' => 'utf8',
        ];

        if ($driver === 'sqlite' || $driver === 'duckdb') {
            $conn['database'] = $this->resolveSqliteFile($rest);
        } else {
            $params = $this->parseDsnParams($rest);
            $conn['database'] = (string)($params['dbname'] ?? '');
            $conn['hostname'] = (string)($params['host'] ?? '127.0.0.1');
            if (!empty($params['port'])) {
                $conn['hostport'] = (string)$params['port'];
            }
            if (!empty($params['charset'])) {
                $conn['charset'] = (string)$params['charset'];
            }
            $conn['username'] = (string)($config['username'] ?? '');
            $conn['password'] = (string)($config['password'] ?? '');
        }

        return [
            'default' => $driver,
            'connections' => [
                $driver => $conn,
            ],
        ];
    }

    /**
     * sqlite 文件路径:相对路径基于项目目录,绝对路径原样使用
     * (原来无条件拼项目路径,传绝对路径会拼成 /proj/DATA/... 这样的错路径)
     */
    protected function resolveSqliteFile(string $file): string
    {
        if ($file === '') {
            return \DuckPhp\Core\App::_()->getProjectPath() . 'database/database.db';
        }
        $is_abs = (strpos($file, '/') === 0)
            || (strpos($file, '\\\\') === 0)
            || (preg_match('#^[a-zA-Z]:[\\\\/]#', $file) === 1);
        return $is_abs ? $file : \DuckPhp\Core\App::_()->getProjectPath() . $file;
    }

    /**
     * 解析 DSN 的 key=value;key=value 片段
     * @return array<string, string>
     */
    protected function parseDsnParams(string $rest): array
    {
        $ret = [];
        foreach (explode(';', $rest) as $frag) {
            $frag = trim($frag);
            if ($frag === '') {
                continue;
            }
            $kv = explode('=', $frag, 2);
            if (count($kv) === 2) {
                $ret[strtolower(trim($kv[0]))] = trim($kv[1]);
            }
        }
        return $ret;
    }
}
