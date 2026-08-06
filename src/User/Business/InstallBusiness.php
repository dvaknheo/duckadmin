<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Business;

use DuckPhp\Component\DbManager;
use DuckPhp\Component\ExtOptionsLoader;
use DuckPhp\Core\App;
use DuckPhp\Foundation\BusinessTrait;

class InstallBusiness
{
    use BusinessTrait;

    private const PHP_MIN_VERSION = '7.4.0';
    private const REQUIRED_EXTENSIONS = ['PDO', 'pdo_sqlite', 'json'];

    /**
     * 执行安装：初始化用户数据表并写入 installed 标记
     *
     * @param array<string, mixed> $post
     * @return array{ok: bool, error: string}
     */
    public function install(array $post = []): array
    {
        if (!$this->initDatabase()) {
            return ['ok' => false, 'error' => '数据库初始化失败，请检查 database/ 目录权限'];
        }
        ExtOptionsLoader::_()->saveData(['installed' => true]);
        return ['ok' => true, 'error' => ''];
    }

    /**
     * 初始化用户数据表（Users），经 DuckUser 的 DbManager 连接（数据库配置由宿主项目注入）
     */
    public function initDatabase(): bool
    {
        try {
            $db = DbManager::_()->Db();
            $db->execute("CREATE TABLE IF NOT EXISTS `Users` (
                \"id\" INTEGER,
                \"username\" TEXT UNIQUE,
                \"password\" TEXT,
                \"created_at\" TEXT,
                \"updated_at\" TEXT,
                \"deleted_at\" TEXT,
                PRIMARY KEY(\"id\" AUTOINCREMENT)
            )");
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 是否已安装（DuckUserApp options['installed']，由 ExtOptionsLoader 按 DuckUser phase 持久化并 bump）
     */
    public function isInstalled(): bool
    {
        return (bool) (App::_()->options['installed'] ?? false);
    }

    /**
     * 环境自检（框架层只检查运行能力；数据库目录权限由宿主项目配置决定，建表失败由 install() 报错）
     *
     * @return array<int, array{name: string, ok: bool, detail: string}>
     */
    public function environmentCheck(): array
    {
        $checks = [];

        $checks[] = [
            'name' => 'PHP 版本 >= ' . self::PHP_MIN_VERSION,
            'ok' => version_compare(PHP_VERSION, self::PHP_MIN_VERSION, '>='),
            'detail' => PHP_VERSION,
        ];

        foreach (self::REQUIRED_EXTENSIONS as $ext) {
            $loaded = extension_loaded($ext);
            $checks[] = [
                'name' => '扩展 ' . $ext,
                'ok' => $loaded,
                'detail' => $loaded ? '已加载' : '未加载',
            ];
        }

        return $checks;
    }
}
