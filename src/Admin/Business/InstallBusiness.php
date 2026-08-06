<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 * From this time, you never be alone~
 */
namespace DuckAdmin\Admin\Business;

use DuckPhp\Component\DbManager;
use DuckPhp\Core\App;
use DuckPhp\Foundation\BusinessTrait;

class InstallBusiness
{
    use BusinessTrait;

    private const PHP_MIN_VERSION = '7.4.0';
    private const REQUIRED_EXTENSIONS = ['PDO', 'pdo_sqlite', 'json'];

    /**
     * 执行安装：初始化数据库（建表 + 种子数据）并写入 installed 标记
     *
     * @param array<string, mixed> $post
     * @return array{ok: bool, error: string}
     */
    public function install(array $post = []): array
    {
        if (!$this->initDatabase()) {
            return ['ok' => false, 'error' => '数据库初始化失败，请检查数据库目录权限'];
        }
        Helper::_()->saveOptions(['installed' => true]);
        return ['ok' => true, 'error' => ''];
    }

    /**
     * 初始化数据库：建 5 张表并插入种子数据（默认管理员 admin/admin123），与 database/migrate.php 等价
     */
    public function initDatabase(): bool
    {
        try {
            $db = DbManager::_()->Db();

            $db->execute("CREATE TABLE IF NOT EXISTS admin_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                realname VARCHAR(100) DEFAULT '',
                email VARCHAR(255) DEFAULT '',
                avatar VARCHAR(255) DEFAULT '',
                status TINYINT DEFAULT 1,
                last_login_at DATETIME DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL
            )");
            $db->execute("CREATE TABLE IF NOT EXISTS admin_roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                description TEXT DEFAULT '',
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL
            )");
            $db->execute("CREATE TABLE IF NOT EXISTS admin_role_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                role_id INTEGER NOT NULL,
                FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
                FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE
            )");
            $db->execute("CREATE TABLE IF NOT EXISTS admin_permissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                `key` VARCHAR(100) NOT NULL UNIQUE,
                description TEXT DEFAULT '',
                parent_id INTEGER DEFAULT 0,
                sort_order INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL
            )");
            $db->execute("CREATE TABLE IF NOT EXISTS admin_role_permissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                role_id INTEGER NOT NULL,
                permission_id INTEGER NOT NULL,
                FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE,
                FOREIGN KEY (permission_id) REFERENCES admin_permissions(id) ON DELETE CASCADE
            )");

            // 已有管理员则跳过种子数据，避免重复插入
            $count = (int) $db->fetchColumn("SELECT COUNT(*) FROM admin_users");
            if ($count > 0) {
                return true;
            }

            $now = date('Y-m-d H:i:s');
            $db->execute(
                "INSERT INTO admin_users (username, password, realname, email, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
                'admin',
                password_hash('admin123', PASSWORD_DEFAULT),
                '超级管理员',
                'admin@example.com',
                1,
                $now,
                $now
            );
            $db->execute(
                "INSERT INTO admin_roles (name, description, created_at, updated_at) VALUES (?, ?, ?, ?)",
                '超级管理员',
                '拥有所有权限',
                $now,
                $now
            );
            $db->execute(
                "INSERT INTO admin_roles (name, description, created_at, updated_at) VALUES (?, ?, ?, ?)",
                '普通管理员',
                '有限的管理权限',
                $now,
                $now
            );
            $db->execute("INSERT INTO admin_role_users (user_id, role_id) VALUES (?, ?)", 1, 1);

            $permissions = [
                ['系统管理', 'system', '系统管理模块'],
                ['用户管理', 'system.user', '用户管理'],
                ['用户列表', 'system.user.list', '查看用户列表'],
                ['创建用户', 'system.user.create', '创建新用户'],
                ['编辑用户', 'system.user.edit', '编辑用户信息'],
                ['删除用户', 'system.user.delete', '删除用户'],
                ['角色管理', 'system.role', '角色管理'],
                ['角色列表', 'system.role.list', '查看角色列表'],
                ['创建角色', 'system.role.create', '创建新角色'],
                ['编辑角色', 'system.role.edit', '编辑角色信息'],
                ['删除角色', 'system.role.delete', '删除角色'],
                ['权限管理', 'system.permission', '权限管理'],
                ['权限列表', 'system.permission.list', '查看权限列表'],
                ['创建权限', 'system.permission.create', '创建新权限'],
                ['编辑权限', 'system.permission.edit', '编辑权限信息'],
                ['删除权限', 'system.permission.delete', '删除权限'],
            ];
            foreach ($permissions as $perm) {
                $db->execute(
                    "INSERT INTO admin_permissions (name, `key`, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?)",
                    $perm[0],
                    $perm[1],
                    $perm[2],
                    $now,
                    $now
                );
            }
            for ($i = 1; $i <= count($permissions); $i++) {
                $db->execute("INSERT INTO admin_role_permissions (role_id, permission_id) VALUES (?, ?)", 1, $i);
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 是否已安装（DuckAdminApp options['installed']，由 ExtOptionsLoader 按 DuckAdmin phase 持久化并 bump）
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
