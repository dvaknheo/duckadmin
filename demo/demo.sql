-- MariaDB dump 10.19  Distrib 10.5.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: webman_admin
-- ------------------------------------------------------
-- Server version	10.5.18-MariaDB-0+deb11u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ActionLogs`
--

DROP TABLE IF EXISTS `ActionLogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ActionLogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contents` text NOT NULL,
  `type` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ActionLogs`
--

LOCK TABLES `ActionLogs` WRITE;
/*!40000 ALTER TABLE `ActionLogs` DISABLE KEYS */;
INSERT INTO `ActionLogs` VALUES (1,'添加文章 2','添加文章','2023-11-29 03:30:50'),(2,'编辑 ID 为 1,原标题，原内容，更改后标题，更改后内容','编辑文章','2023-11-29 03:33:21'),(3,'2-user1 评论成功','','2023-12-06 02:56:56'),(4,'2-user1 评论成功','','2023-12-06 02:57:01'),(5,'2-user1 评论成功','','2024-01-03 13:23:47');
/*!40000 ALTER TABLE `ActionLogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Articles`
--

DROP TABLE IF EXISTS `Articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='文章表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Articles`
--

LOCK TABLES `Articles` WRITE;
/*!40000 ALTER TABLE `Articles` DISABLE KEYS */;
INSERT INTO `Articles` VALUES (1,'fgf11','33','2023-11-29 03:29:20','2023-11-29 03:33:21',NULL),(2,'fgf','gfdg','2023-11-29 03:30:50','2023-11-29 03:30:50',NULL);
/*!40000 ALTER TABLE `Articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Comments`
--

DROP TABLE IF EXISTS `Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `article_id` int(11) NOT NULL COMMENT '话题ID，关联其他表',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '评论内容',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Comments`
--

LOCK TABLES `Comments` WRITE;
/*!40000 ALTER TABLE `Comments` DISABLE KEYS */;
INSERT INTO `Comments` VALUES (1,1,2,'fsadf',NULL,NULL,NULL),(2,1,2,'zzzz',NULL,NULL,NULL),(3,1,2,'fsafdsa','2024-01-03 13:23:47',NULL,NULL);
/*!40000 ALTER TABLE `Comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (1,'t1','$2y$10$r1cG/qZR2MqCb7jm.xkVOuEDRvDVe7VQKmhrGEpCVFOo7vs44xXpS',NULL,NULL,NULL),(2,'user1','$2y$10$hQ8oxFHLRiG6e4GE7d9/DukGYbJB/pSar0L7TenjUGpGYGAbfHppm',NULL,NULL,NULL);
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_admin_roles`
--

DROP TABLE IF EXISTS `wa_admin_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_admin_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_admin_id` (`role_id`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='管理员角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_admin_roles`
--

LOCK TABLES `wa_admin_roles` WRITE;
/*!40000 ALTER TABLE `wa_admin_roles` DISABLE KEYS */;
INSERT INTO `wa_admin_roles` VALUES (1,1,1);
/*!40000 ALTER TABLE `wa_admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_admins`
--

DROP TABLE IF EXISTS `wa_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `nickname` varchar(40) NOT NULL COMMENT '昵称',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `avatar` varchar(255) DEFAULT '/app/admin/avatar.png' COMMENT '头像',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `login_at` datetime DEFAULT NULL COMMENT '登录时间',
  `status` tinyint(4) DEFAULT NULL COMMENT '禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_admins`
--

LOCK TABLES `wa_admins` WRITE;
/*!40000 ALTER TABLE `wa_admins` DISABLE KEYS */;
INSERT INTO `wa_admins` VALUES (1,'admin1','超级管理员','$2y$10$1bRAcXYt5wQjS4xsY0nkHeS8poSYcJJdWgGgVBAaPXioukPjzcumO','/app/admin/avatar.png',NULL,NULL,'2023-11-12 10:35:47','2023-11-26 15:31:34','2024-01-17 05:28:10',NULL);
/*!40000 ALTER TABLE `wa_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_options`
--

DROP TABLE IF EXISTS `wa_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '键',
  `value` longtext NOT NULL COMMENT '值',
  `created_at` datetime NOT NULL DEFAULT '2022-08-15 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '2022-08-15 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='选项表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_options`
--

LOCK TABLES `wa_options` WRITE;
/*!40000 ALTER TABLE `wa_options` DISABLE KEYS */;
INSERT INTO `wa_options` VALUES (1,'system_config','{\"logo\":{\"title\":\"DuckPhp Admin\",\"image\":\"\\/app\\/admin\\/res\\/logo.png\"},\"menu\":{\"data\":\"\\/app\\/admin\\/rule\\/get\",\"accordion\":true,\"collapse\":false,\"control\":false,\"controlWidth\":500,\"select\":0,\"async\":true},\"tab\":{\"enable\":true,\"keepState\":true,\"preload\":false,\"session\":true,\"max\":\"30\",\"index\":{\"id\":\"0\",\"href\":\"\\/app\\/admin\\/index\\/dashboard\",\"title\":\"\\u4eea\\u8868\\u76d8\"}},\"theme\":{\"defaultColor\":\"2\",\"defaultMenu\":\"dark-theme\",\"defaultHeader\":\"dark-theme\",\"allowCustom\":true,\"banner\":false},\"colors\":[{\"id\":1,\"color\":\"#36b368\",\"second\":\"#f0f9eb\"},{\"id\":2,\"color\":\"#2d8cf0\",\"second\":\"#ecf5ff\"},{\"id\":3,\"color\":\"#f6ad55\",\"second\":\"#fdf6ec\"},{\"id\":4,\"color\":\"#f56c6c\",\"second\":\"#fef0f0\"},{\"id\":5,\"color\":\"#3963bc\",\"second\":\"#ecf5ff\"}],\"other\":{\"keepLoad\":\"500\",\"autoHead\":false,\"footer\":false},\"header\":{\"message\":false}}','2022-08-15 00:00:00','2022-08-15 00:00:00');
/*!40000 ALTER TABLE `wa_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_roles`
--

DROP TABLE IF EXISTS `wa_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(80) NOT NULL COMMENT '角色组',
  `rules` text DEFAULT NULL COMMENT '权限',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `pid` int(10) unsigned DEFAULT NULL COMMENT '父级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='管理员角色';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_roles`
--

LOCK TABLES `wa_roles` WRITE;
/*!40000 ALTER TABLE `wa_roles` DISABLE KEYS */;
INSERT INTO `wa_roles` VALUES (1,'超级管理员','*','2022-08-13 16:15:01','2022-12-23 12:05:07',NULL),(2,'saaa','2,4,6','2023-11-26 17:46:53','2023-11-26 17:47:44',1);
/*!40000 ALTER TABLE `wa_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_rules`
--

DROP TABLE IF EXISTS `wa_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `key` varchar(255) NOT NULL COMMENT '标识',
  `pid` int(10) unsigned DEFAULT 0 COMMENT '上级菜单',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `href` varchar(255) DEFAULT NULL COMMENT 'url',
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '类型',
  `weight` int(11) DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='权限规则';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_rules`
--

LOCK TABLES `wa_rules` WRITE;
/*!40000 ALTER TABLE `wa_rules` DISABLE KEYS */;
INSERT INTO `wa_rules` VALUES (1,'权限管理','layui-icon-vercode','auth',0,'2023-11-12 10:30:57','2023-11-12 10:30:57',NULL,0,900),(2,'账户管理',NULL,'DuckAdmin\\Controller\\AdminController',1,'2023-11-12 10:30:57','2023-11-12 10:30:57','admin/index',1,1000),(3,'角色管理',NULL,'DuckAdmin\\Controller\\RoleController',1,'2023-11-12 10:30:57','2023-11-12 10:30:57','role/index',1,900),(4,'菜单管理',NULL,'DuckAdmin\\Controller\\RuleController',1,'2023-11-12 10:30:57','2023-11-12 10:30:57','rule/index',1,800),(5,'会员管理','layui-icon-username','user',0,'2023-11-12 10:30:57','2023-11-12 10:30:57',NULL,0,800),(6,'用户','','DuckAdmin\\Controller\\UserController',5,'2023-11-12 10:30:57','2024-01-17 06:54:37','User/index',1,800),(7,'通用设置','layui-icon-set','common',0,'2023-11-12 10:30:57','2023-11-12 10:30:57',NULL,0,700),(8,'个人资料',NULL,'DuckAdmin\\Controller\\AccountController',7,'2023-11-12 10:30:57','2023-11-12 10:30:57','account/index',1,800),(9,'系统设置',NULL,'DuckAdmin\\Controller\\ConfigController',7,'2023-11-12 10:30:57','2023-11-12 10:30:57','config/index',1,500);
/*!40000 ALTER TABLE `wa_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_users`
--

DROP TABLE IF EXISTS `wa_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `nickname` varchar(40) NOT NULL COMMENT '昵称',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `sex` enum('0','1') NOT NULL DEFAULT '1' COMMENT '性别',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `email` varchar(128) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机',
  `level` tinyint(4) NOT NULL DEFAULT 0 COMMENT '等级',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `money` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '余额(元)',
  `score` int(11) NOT NULL DEFAULT 0 COMMENT '积分',
  `last_time` datetime DEFAULT NULL COMMENT '登录时间',
  `last_ip` varchar(50) DEFAULT NULL COMMENT '登录ip',
  `join_time` datetime DEFAULT NULL COMMENT '注册时间',
  `join_ip` varchar(50) DEFAULT NULL COMMENT '注册ip',
  `token` varchar(50) DEFAULT NULL COMMENT 'token',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `role` int(11) NOT NULL DEFAULT 1 COMMENT '角色',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `join_time` (`join_time`),
  KEY `mobile` (`mobile`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_users`
--

LOCK TABLES `wa_users` WRITE;
/*!40000 ALTER TABLE `wa_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `wa_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-17 15:12:53
