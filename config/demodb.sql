BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "da_admin_roles" (
	"id"	INTEGER,
	"role_id"	INTEGER NOT NULL,
	"admin_id"	INTEGER NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	UNIQUE("role_id","admin_id")
);
CREATE TABLE IF NOT EXISTS "da_admins" (
	"id"	INTEGER,
	"username"	TEXT NOT NULL UNIQUE,
	"nickname"	TEXT NOT NULL,
	"password"	TEXT NOT NULL,
	"email"	TEXT,
	"mobile"	TEXT,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"login_at"	TEXT,
	"status"	INTEGER,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "da_options" (
	"id"	INTEGER,
	"name"	TEXT NOT NULL UNIQUE,
	"value"	TEXT NOT NULL,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "da_roles" (
	"id"	INTEGER NOT NULL,
	"name"	TEXT NOT NULL,
	"rules"	TEXT NOT NULL,
	"created_at"	TEXT NOT NULL,
	"updated_at"	TEXT NOT NULL,
	"pid"	INTEGER,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "da_rules" (
	"id"	INTEGER NOT NULL,
	"title"	TEXT NOT NULL,
	"icon"	TEXT DEFAULT NULL,
	"key"	TEXT NOT NULL,
	"pid"	INTEGER DEFAULT 0,
	"href"	TEXT DEFAULT NULL,
	"type"	INTEGER NOT NULL DEFAULT 1,
	"weight"	INTEGER DEFAULT 0,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "Users" (
	"id"	INTEGER,
	"username"	TEXT UNIQUE,
	"password"	TEXT,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"deleted_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "Articles" (
	"id"	INTEGER,
	"title"	TEXT,
	"content"	TEXT,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"deleted_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "Comments" (
	"id"	INTEGER,
	"article_id"	INTEGER,
	"user_id"	INTEGER,
	"content"	TEXT,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"deleted_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
INSERT INTO "da_admin_roles" VALUES (1,1,1);
INSERT INTO "da_admins" VALUES (1,'admin','超级管理员','$2y$10$qRlcBLoi1aRZdUjkHBglWeoLLK41QjOL1uYG9IN8T3GIBamJ2yG8i',NULL,NULL,'2024-06-10 22:12:01','2024-06-10 22:12:01',NULL,NULL);
INSERT INTO "da_roles" VALUES (1,'超级管理员','*','2022-08-13 16:15:01','2022-12-23 12:05:07',NULL);
INSERT INTO "da_rules" VALUES (1,'权限管理','layui-icon-vercode','auth',0,NULL,0,900,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (2,'账户管理',NULL,'DuckAdmin\Controller\AdminController',1,'admin/index',1,1000,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (3,'角色管理',NULL,'DuckAdmin\Controller\RoleController',1,'role/index',1,900,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (4,'菜单管理',NULL,'DuckAdmin\Controller\RuleController',1,'rule/index',1,800,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (5,'会员管理','layui-icon-username','user',0,NULL,0,800,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (6,'用户',NULL,'DuckAdmin\Controller\UserController',5,'User/index',1,800,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (7,'通用设置','layui-icon-set','common',0,NULL,0,700,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (8,'个人资料',NULL,'DuckAdmin\Controller\AccountController',7,'account/index',1,800,'2024-06-10 22:12:01','2024-06-10 22:12:01');
INSERT INTO "da_rules" VALUES (9,'系统设置',NULL,'DuckAdmin\Controller\ConfigController',7,'config/index',1,500,'2024-06-10 22:12:01','2024-06-10 22:12:01');
COMMIT;
