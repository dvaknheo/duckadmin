CREATE TABLE `Articles` (
	"id"	INTEGER,
	"title"	TEXT,
	"content"	TEXT,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"deleted_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE `Comments` (
	"id"	INTEGER,
	"article_id"	INTEGER,
	"user_id"	INTEGER,
	"content"	TEXT,
	"created_at"	TEXT,
	"updated_at"	TEXT,
	"deleted_at"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
