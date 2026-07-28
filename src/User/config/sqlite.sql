CREATE TABLE `Users` (
	"id"	INTEGER,
	"username"	TEXT UNIQUE,
	"password"	TEXT,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"deleted_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);