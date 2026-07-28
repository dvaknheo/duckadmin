<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Database Migration
 * 
 * 运行方式: php database/migrate.php
 */

$dbPath = __DIR__ . '/admin.db';
$initFlag = __DIR__ . '/.migrated';

// 如果已迁移则跳过
if (file_exists($initFlag)) {
    echo "数据库已初始化，跳过迁移。\n";
    echo "如需重新初始化，请删除文件: " . $initFlag . "\n";
    exit(0);
}

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 开启 WAL 模式提升并发性能
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('PRAGMA foreign_keys=ON');
    
    echo "开始创建数据库表...\n";
    
    // ========== 用户表 ==========
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
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
        )
    ");
    echo "  - 创建表: admin_users\n";
    
    // ========== 角色表 ==========
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT DEFAULT '',
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL
        )
    ");
    echo "  - 创建表: admin_roles\n";
    
    // ========== 用户角色关联表 ==========
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_role_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
            FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE
        )
    ");
    echo "  - 创建表: admin_role_users\n";
    
    // ========== 权限表 ==========
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            key VARCHAR(100) NOT NULL UNIQUE,
            description TEXT DEFAULT '',
            parent_id INTEGER DEFAULT 0,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL
        )
    ");
    echo "  - 创建表: admin_permissions\n";
    
    // ========== 角色权限关联表 ==========
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_role_permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role_id INTEGER NOT NULL,
            permission_id INTEGER NOT NULL,
            FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE,
            FOREIGN KEY (permission_id) REFERENCES admin_permissions(id) ON DELETE CASCADE
        )
    ");
    echo "  - 创建表: admin_role_permissions\n";
    
    echo "\n开始插入种子数据...\n";
    
    // ========== 默认管理员 ==========
    $stmt = $db->prepare("INSERT INTO admin_users (username, password, realname, email, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'admin',
        password_hash('admin123', PASSWORD_DEFAULT),
        '超级管理员',
        'admin@example.com',
        1,
        date('Y-m-d H:i:s'),
        date('Y-m-d H:i:s'),
    ]);
    echo "  - 默认管理员: admin / admin123\n";
    
    // ========== 默认角色 ==========
    $stmt = $db->prepare("INSERT INTO admin_roles (name, description, created_at, updated_at) VALUES (?, ?, ?, ?)");
    $stmt->execute(['超级管理员', '拥有所有权限', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
    $stmt->execute(['普通管理员', '有限的管理权限', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
    echo "  - 默认角色: 超级管理员, 普通管理员\n";
    
    // ========== 分配角色 ==========
    $stmt = $db->prepare("INSERT INTO admin_role_users (user_id, role_id) VALUES (?, ?)");
    $stmt->execute([1, 1]); // admin -> 超级管理员
    echo "  - 角色分配: admin -> 超级管理员\n";
    
    // ========== 默认权限 ==========
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
    
    $stmt = $db->prepare("INSERT INTO admin_permissions (name, `key`, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
    foreach ($permissions as $i => $perm) {
        $stmt->execute([$perm[0], $perm[1], $perm[2], date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
    }
    echo "  - 默认权限: " . count($permissions) . " 条\n";
    
    // ========== 超级管理员拥有所有权限 ==========
    $stmt = $db->prepare("INSERT INTO admin_role_permissions (role_id, permission_id) VALUES (?, ?)");
    for ($i = 1; $i <= count($permissions); $i++) {
        $stmt->execute([1, $i]);
    }
    echo "  - 权限分配: 超级管理员 -> 所有权限\n";
    
    // ========== 写入迁移标记 ==========
    file_put_contents($initFlag, date('Y-m-d H:i:s'));
    
    echo "\n✅ 数据库初始化完成！\n";
    echo "   管理员账号: admin\n";
    echo "   管理员密码: admin123\n";
    
} catch (PDOException $e) {
    echo "❌ 数据库初始化失败: " . $e->getMessage() . "\n";
    exit(1);
}
