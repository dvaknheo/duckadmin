-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2021-08-16 16:40:18
-- 服务器版本： 10.1.41-MariaDB-0+deb9u1
-- PHP Version: 7.3.13-1+0~20191218.50+debian9~1.gbp23c2da

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `duckadmin`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE `admin` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(30) NOT NULL COMMENT '用户名，登陆使用',
  `password` varchar(64) NOT NULL COMMENT '用户密码',
  `nickname` varchar(30) NOT NULL COMMENT '用户昵称',
  `role_id` int(11) NOT NULL DEFAULT '2' COMMENT '分组',
  `stat` int(11) NOT NULL DEFAULT '1' COMMENT '正常为1 0为隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理表';

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nickname`, `role_id`, `stat`) VALUES
(1, 'admin', '$2y$10$2Wqa3E4RiVdsugdkWJp5D.9/xOzp542V6eSik5X.0JWpaaeWic8YC', '超级管理员', 1, 1),
(4, 'aa', '123456', '1232', 2, 1);

-- --------------------------------------------------------

--
-- 表的结构 `admin_log`
--

CREATE TABLE `admin_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `url` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='日志表';

-- --------------------------------------------------------

--
-- 表的结构 `admin_menu`
--

CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL,
  `path` varchar(180) NOT NULL COMMENT '链接地址',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `name` varchar(250) NOT NULL COMMENT '菜单名称',
  `is_menu` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否菜单 ',
  `icon` varchar(250) NOT NULL DEFAULT '' COMMENT '图标',
  `stat` int(11) NOT NULL DEFAULT '0' COMMENT '是否显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='左边显示的菜单表';

-- --------------------------------------------------------

--
-- 表的结构 `admin_permission`
--

CREATE TABLE `admin_permission` (
  `id` int(11) NOT NULL,
  `url` varchar(250) NOT NULL,
  `role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='url 权限表';

--
-- 转存表中的数据 `admin_permission`
--

INSERT INTO `admin_permission` (`id`, `url`, `role`) VALUES
(1, 'test', 2);

-- --------------------------------------------------------

--
-- 表的结构 `admin_role`
--

CREATE TABLE `admin_role` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `description` varchar(180) NOT NULL COMMENT '备注',
  `stat` int(11) NOT NULL DEFAULT '1',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '排序ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='职位（权限）表';

--
-- 转存表中的数据 `admin_role`
--

INSERT INTO `admin_role` (`id`, `name`, `description`, `stat`, `order_id`) VALUES
(1, '管理员', '所有权限都有', 1, 0),
(2, '新员工', '新来的都扔在这里', 1, 0),
(3, '储备人才', '要离职的都放这里', 1, 0),
(4, '编辑', '小兵', 1, 0),
(5, '编辑-组长', '记得给手下改权限的时候也要给过来哦', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `path` (`path`);

--
-- Indexes for table `admin_permission`
--
ALTER TABLE `admin_permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_role`
--
ALTER TABLE `admin_role`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `admin_permission`
--
ALTER TABLE `admin_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `admin_role`
--
ALTER TABLE `admin_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;
