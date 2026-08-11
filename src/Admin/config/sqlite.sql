-- ============================================================
-- DuckAdmin Admin 数据库结构 (SQLite)
-- 由 RouteHookWebInstaller::doSchema 在安装时执行
-- 参考旧系统 src/DuckAdmin/config/mysql.sql(wa_rules 模式)
-- ============================================================

-- 开启外键约束(连接级,仅本次连接生效;运行时依赖 Model 手动清理关联)
PRAGMA foreign_keys = ON;

-- 管理员表
CREATE TABLE admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,   -- 登录账号
    password VARCHAR(255) NOT NULL,          -- 密码哈希
    realname VARCHAR(100) DEFAULT '',        -- 姓名
    email VARCHAR(255) DEFAULT '',           -- 邮箱
    avatar VARCHAR(255) DEFAULT '',          -- 头像
    status TINYINT DEFAULT 1,                -- 状态:1=启用 0=禁用
    last_login_at DATETIME DEFAULT NULL,     -- 最后登录时间
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL         -- 软删除
);

-- 角色表
CREATE TABLE admin_roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,              -- 角色名
    description TEXT DEFAULT '',             -- 角色描述
    is_super TINYINT DEFAULT 0,              -- 1=超级管理员角色,鉴权直接放行
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL
);

-- 用户-角色关联表
CREATE TABLE admin_role_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE
);

-- 权限规则表(兼作菜单树,wa_rules 模式)
-- type: 0=目录(仅导航,url 为空) 1=菜单(可点击,url 必填) 2=操作(按钮级,url 填写对应动作)
CREATE TABLE admin_permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,              -- 名称(菜单/操作标题)
    url VARCHAR(255) DEFAULT '',             -- url,与请求地址精确匹配;目录为空
    type TINYINT DEFAULT 1,                  -- 0=目录 1=菜单 2=操作
    parent_id INTEGER DEFAULT 0,             -- 父级 id,0=顶级
    weight INTEGER DEFAULT 0,                -- 排序
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL
);

-- 角色-权限关联表
CREATE TABLE admin_role_permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES admin_permissions(id) ON DELETE CASCADE
);
