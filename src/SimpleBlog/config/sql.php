<?php return array (
  'scheme' => 
  array (
    'ActionLogs' => 'CREATE TABLE `ActionLogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contents` text COLLATE utf8mb4 NOT NULL,
  `type` varchar(250) COLLATE utf8mb4 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4',
    'Articles' => 'CREATE TABLE `Articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8mb4 NOT NULL,
  `content` text COLLATE utf8mb4 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4 COMMENT=\'文章表\'',
    'Comments' => 'CREATE TABLE `Comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'自增ID\',
  `article_id` int(11) NOT NULL COMMENT \'话题ID，关联其他表\',
  `user_id` int(11) NOT NULL COMMENT \'用户ID\',
  `content` text COLLATE utf8mb4 NOT NULL COMMENT \'评论内容\',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4',
  ),
  'data' => 
  array (
  ),
);
