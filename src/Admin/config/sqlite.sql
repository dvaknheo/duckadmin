CREATE TABLE admin_users (
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
);
CREATE TABLE admin_roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT '',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL
);
CREATE TABLE admin_role_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE
);
CREATE TABLE admin_permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    key VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT '',
    parent_id INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL
);
CREATE TABLE admin_role_permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES admin_permissions(id) ON DELETE CASCADE
);
