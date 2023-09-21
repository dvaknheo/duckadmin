-- MySQL dump 10.16  Distrib 10.1.41-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: duckadmin
-- ------------------------------------------------------
-- Server version	10.1.41-MariaDB-0+deb9u1

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(30) NOT NULL COMMENT '用户名，登陆使用',
  `password` varchar(64) NOT NULL COMMENT '用户密码',
  `nickname` varchar(30) NOT NULL COMMENT '用户昵称',
  `role_id` int(11) NOT NULL DEFAULT '2' COMMENT '分组',
  `stat` int(11) NOT NULL DEFAULT '1' COMMENT '正常为1 0为隐藏',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='管理表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','$2y$10$2Wqa3E4RiVdsugdkWJp5D.9/xOzp542V6eSik5X.0JWpaaeWic8YC','超级管理员',1,1),(5,'t1','123456','123456',2,1),(6,'小路','','小路啊',4,1);
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_log`
--

DROP TABLE IF EXISTS `admin_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `url` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_log`
--

LOCK TABLES `admin_log` WRITE;
/*!40000 ALTER TABLE `admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_menu`
--

DROP TABLE IF EXISTS `admin_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(180) NOT NULL COMMENT '链接地址',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `name` varchar(250) NOT NULL COMMENT '菜单名称',
  `is_menu` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否菜单 ',
  `icon` varchar(250) NOT NULL DEFAULT '' COMMENT '图标',
  `stat` int(11) NOT NULL DEFAULT '0' COMMENT '是否显示',
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='左边显示的菜单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_permission`
--

DROP TABLE IF EXISTS `admin_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(250) NOT NULL,
  `role` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='url 权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_permission`
--

LOCK TABLES `admin_permission` WRITE;
/*!40000 ALTER TABLE `admin_permission` DISABLE KEYS */;
INSERT INTO `admin_permission` VALUES (1,'test',2);
/*!40000 ALTER TABLE `admin_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role`
--

DROP TABLE IF EXISTS `admin_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `description` varchar(180) NOT NULL COMMENT '备注',
  `stat` int(11) NOT NULL DEFAULT '1',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '排序ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='职位（权限）表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role`
--

LOCK TABLES `admin_role` WRITE;
/*!40000 ALTER TABLE `admin_role` DISABLE KEYS */;
INSERT INTO `admin_role` VALUES (1,'管理员','所有权限都有',1,0),(2,'新员工','新来的都扔在这里',1,0),(3,'储备人才','要离职的都放这里',1,0),(4,'编辑','小兵',1,0),(5,'编辑-组长','记得给手下改权限的时候也要给过来哦',1,0);
/*!40000 ALTER TABLE `admin_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `duckuser_users`
--

DROP TABLE IF EXISTS `duckuser_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `duckuser_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_bin NOT NULL,
  `password` varchar(64) COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `duckuser_users`
--

LOCK TABLES `duckuser_users` WRITE;
/*!40000 ALTER TABLE `duckuser_users` DISABLE KEYS */;
INSERT INTO `duckuser_users` VALUES (1,'aa11111','$2y$10$HEnsqIcNWAYGiyvwVnJGo.IIogl2X0YQL7JlTuKRYcN.Z/rhvMHI2',NULL,NULL,NULL),(2,'abc','$2y$10$G25OLpq5SRPxR4kFv.ZIwegESIyoEka/j6MV1OPjGzCb6hi5kOtbW',NULL,NULL,NULL),(3,'a','$2y$10$HKlThhmUWgEjLbm1.3qhFOa0Xq.1QXigAfT/7UZT6HUwg7c08UfP.','2018-06-11 02:50:10',NULL,NULL),(4,'t1','$2y$10$xYPQEeIw/V9ITux.EvOr0uCfA5caLew5guCytERcwx4SnuCgwmaxG','2018-07-23 02:10:14',NULL,NULL),(5,'t2','$2y$10$p3lYbiPJuQPnrH6IbhSzFujIFskchrdqjuF8Qs6SnTyCy5KOHrueW','2018-07-24 14:34:51',NULL,NULL),(11,'Jtest','333',NULL,NULL,NULL),(16,'Jtest2','123456',NULL,NULL,NULL),(21,'Test2','1',NULL,NULL,NULL),(24,'Test3','123456',NULL,NULL,'2019-12-11 04:17:28'),(25,'Test1','$2y$10$lHhJ/wW7Dlmw4KT1VwJ51OiIf.C00lgbTGfbG2JhAXuCE/TMtU2fy',NULL,NULL,'2019-12-11 04:17:25'),(29,'z1','$2y$10$jCjTeE3SxGCAtXMZEiZSaelzBDbog9vNNs.16.DwOukT8e71vNIPe',NULL,NULL,NULL),(30,'z2','$2y$10$GL/S5MnX.kqHnLNOodEpQ.wZEebByUrPLElA9uAyfg8a3BvgRbDGi',NULL,NULL,NULL),(31,'z3','$2y$10$Ld7z2FiXgDmDCUtFKvCNH.fezaZ8Z9OwPQLs8wOjgz1o2flhhafxS',NULL,NULL,NULL),(32,'z4','$2y$10$dbmtbM4M1sGDe.eykB0/6OceLWxnpDWL/cntHOFonGauH65EGLxBS',NULL,NULL,'2019-12-11 04:17:46'),(33,'z5','$2y$10$xazi2On2cJhJkRECt2lxjeFfy8.UpRWzZAuD913Ft2B1c26Qoj/RC',NULL,NULL,NULL),(34,'zz1','$2y$10$9zgyC9m6dYuVakh3DDW.N.2CvP.9GBLlOlQn8QZJRmw562/eW0xVG',NULL,NULL,NULL),(50,'az1','$2y$10$o41lgra6dugkxtoOiIfGq.Zsoi480SQjPk1w1yzPrykiX1fMK5QAK',NULL,NULL,NULL),(69,'aaaa1','$2y$10$I1xKTw2pAGROBPHq7Zhf6OGNom1QZCaxL2z/UVNyKcSNs48l9x50u',NULL,NULL,NULL),(82,'zz3','$2y$10$8wFM.mt3E8KYa0heGG91COrqjDtjckHrITwrjj5zKhmui6n06D.Dy',NULL,NULL,NULL),(83,'zz4','$2y$10$PDlOHVm8YyMGD7jxDY.I0eogjBQm.3oD4mEt3GA/gsARr4fraVil2',NULL,NULL,NULL),(84,'aaa111','$2y$10$39GZp7Jvl4A0rBNPlHPHIuP3k49IKwAS6eET5zvHNiPb967Jwk06q',NULL,NULL,NULL),(85,'tt3','$2y$10$wD796DdcBmYrBCCgT.XyX.hNBw7fjtaGv6HM4pReBhMqNPOQG5Wmq',NULL,NULL,NULL),(86,'t123456','$2y$10$jx4LSXVAjJbROoJ19bvVG.MDE6v2DKBax/KeTiwpsMeQAh46tpOFG',NULL,NULL,NULL);
/*!40000 ALTER TABLE `duckuser_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-25  8:35:25
