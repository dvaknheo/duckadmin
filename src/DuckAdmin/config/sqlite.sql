BEGIN TRANSACTION;
CREATE TABLE "wa_admin_roles" (
	"id"	INTEGER,
	"role_id"	INTEGER NOT NULL,
	"admin_id"	INTEGER NOT NULL,
	UNIQUE("role_id","admin_id"),
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "wa_roles" (
	"id"	INTEGER NOT NULL,
	"name"	TEXT NOT NULL,
	"rules"	TEXT NOT NULL,
	"created_at"	TEXT NOT NULL,
	"updated_at"	TEXT NOT NULL,
	"pid"	INTEGER,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "wa_rules" (
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
CREATE TABLE "wa_admins" (
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

CREATE TABLE "wa_options" (
	"id"	INTEGER,
	"name"	TEXT NOT NULL UNIQUE,
	"value"	TEXT NOT NULL,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);

COMMIT;
