CREATE TABLE `Users` (
	"id"	INTEGER,
	"username"	TEXT UNIQUE,
	"password"	TEXT,
	"stat"	INTEGER NOT NULL DEFAULT 1,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"deleted_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);