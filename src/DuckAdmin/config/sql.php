<?php //regenerate by DuckPhp\Component\SqlDumper->DuckPhp\Component\SqlDumper::save at 2024-01-26T21:36:03+08:00
return array (
  'scheme' => 
  array (
    'wa_admins' => 'CREATE TABLE `wa_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'ID\',
  `username` varchar(32) NOT NULL COMMENT \'用户名\',
  `nickname` varchar(40) NOT NULL COMMENT \'昵称\',
  `password` varchar(255) NOT NULL COMMENT \'密码\',
  `avatar` varchar(255) DEFAULT \'/app/admin/avatar.png\' COMMENT \'头像\',
  `email` varchar(100) DEFAULT NULL COMMENT \'邮箱\',
  `mobile` varchar(16) DEFAULT NULL COMMENT \'手机\',
  `created_at` datetime DEFAULT NULL COMMENT \'创建时间\',
  `updated_at` datetime DEFAULT NULL COMMENT \'更新时间\',
  `login_at` datetime DEFAULT NULL COMMENT \'登录时间\',
  `status` tinyint(4) DEFAULT NULL COMMENT \'禁用\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT=\'管理员表\'',
    'wa_admin_roles' => 'CREATE TABLE `wa_admin_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'主键\',
  `role_id` int(11) NOT NULL COMMENT \'角色id\',
  `admin_id` int(11) NOT NULL COMMENT \'管理员id\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_admin_id` (`role_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT=\'管理员角色表\'',
    'wa_roles' => 'CREATE TABLE `wa_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'主键\',
  `name` varchar(80) NOT NULL COMMENT \'角色组\',
  `rules` text DEFAULT NULL COMMENT \'权限\',
  `created_at` datetime NOT NULL COMMENT \'创建时间\',
  `updated_at` datetime NOT NULL COMMENT \'更新时间\',
  `pid` int(10) unsigned DEFAULT NULL COMMENT \'父级\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT=\'管理员角色\'',
    'wa_rules' => 'CREATE TABLE `wa_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'主键\',
  `title` varchar(255) NOT NULL COMMENT \'标题\',
  `icon` varchar(255) DEFAULT NULL COMMENT \'图标\',
  `key` varchar(255) NOT NULL COMMENT \'标识\',
  `pid` int(10) unsigned DEFAULT 0 COMMENT \'上级菜单\',
  `created_at` datetime NOT NULL COMMENT \'创建时间\',
  `updated_at` datetime NOT NULL COMMENT \'更新时间\',
  `href` varchar(255) DEFAULT NULL COMMENT \'url\',
  `type` int(11) NOT NULL DEFAULT 1 COMMENT \'类型\',
  `weight` int(11) DEFAULT 0 COMMENT \'排序\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT=\'权限规则\'',
  ),
  'data' => 
  array (
  ),
);
