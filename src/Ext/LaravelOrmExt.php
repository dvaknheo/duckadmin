<?php declare(strict_types=1);

use DuckAdmin\Ext;

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
     * 支持 sqlite(相对/绝对路径) 与 mysql/pgsql 等 DSN
     */
    protected function getDatabaseConfig(): array
    {
        $config_list = DbManager::_()->getDatabaseConfigList();
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

        $ret = [
            'prefix' => '',
            //'foreign_key_constraints' => true,
            'foreign_key_constraints' => false,
        ];

        if ($driver === 'sqlite' || $driver === 'duckdb') {
            $ret['driver'] = 'sqlite';
            $ret['database'] = $this->resolveSqliteFile($rest);
            return $ret;
        }

        // mysql / pgsql 等:解析 dsn 参数
        $params = $this->parseDsnParams($rest);
        $ret['driver'] = ($driver === 'postgresql') ? 'pgsql' : $driver;
        $ret['database'] = (string)($params['dbname'] ?? '');
        if (isset($params['host']) && $params['host'] !== '') {
            $ret['host'] = $params['host'];
        }
        if (!empty($params['port'])) {
            $ret['port'] = (int)$params['port'];
        }
        if (!empty($params['charset'])) {
            $ret['charset'] = $params['charset'];
        }
        if ((string)($config['username'] ?? '') !== '') {
            $ret['username'] = (string)$config['username'];
        }
        if ((string)($config['password'] ?? '') !== '') {
            $ret['password'] = (string)$config['password'];
        }
        return $ret;
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
