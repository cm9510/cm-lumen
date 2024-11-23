-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-09-01 07:48:30
-- 服务器版本： 8.0.17
-- PHP 版本： 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `cm_dev`
--

-- --------------------------------------------------------

--
-- 表的结构 `cm_members`
--

CREATE TABLE `cm_members` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '成员表',
  `nickname` varchar(30) DEFAULT '',
  `phone` char(11) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(8) NOT NULL COMMENT '盐',
  `status` tinyint(1) DEFAULT '0' COMMENT '0正常 1禁用',
  `last_login_at` int(10) DEFAULT '0',
  `created_at` int(10) NOT NULL,
  `deleted_at` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `cm_members`
--

INSERT INTO `cm_members` (`id`, `nickname`, `phone`, `password`, `salt`, `status`, `last_login_at`, `created_at`, `deleted_at`) VALUES
(1, '晚听清风', '13988883333', '802975c7ab22068fd32bb6d65ed06f58', '9eZP88DU', 0, 1693542762, 1655716595, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cm_member_log`
--

CREATE TABLE `cm_member_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(11) NOT NULL COMMENT '成员id',
  `title` varchar(18) NOT NULL COMMENT '操作类型',
  `detail` varchar(255) DEFAULT '' COMMENT '详情',
  `ip` varchar(18) DEFAULT '',
  `request` text,
  `created_at` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统日志表';

--
-- 表的结构 `cm_permissions`
--

CREATE TABLE `cm_permissions` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '权限表',
  `group_id` int(11) DEFAULT '0' COMMENT '分组id',
  `name` varchar(30) NOT NULL COMMENT '权限名，最多10个字',
  `url` varchar(66) NOT NULL COMMENT 'url',
  `status` tinyint(1) DEFAULT '0' COMMENT '0正常 1停用',
  `creator_id` int(10) NOT NULL COMMENT '创建人id',
  `updater_id` int(10) DEFAULT '0' COMMENT '最近修改人id',
  `log` tinyint(1) DEFAULT '0' COMMENT '是否记录日志 0否 1是',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `deleted_at` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `cm_permissions`
--

INSERT INTO `cm_permissions` (`id`, `group_id`, `name`, `url`, `status`, `creator_id`, `updater_id`, `log`, `created_at`, `deleted_at`) VALUES
(1, 1, '添加|修改权限', '/a/add_permission', 0, 1, 0, 1, 1654321085, 0),
(2, 1, '权限列表', '/a/permission_list', 0, 1, 0, 0, 1655357544, 0),
(3, 0, '删除权限', '/a/del_permission', 0, 1, 0, 0, 1655357558, 1692784683),
(4, 1, '删除成员|角色|权限', '/a/del_sys', 0, 1, 0, 1, 1655797425, 0),
(5, 1, '添加|修改角色', '/a/add_role', 0, 1, 0, 1, 1655797484, 0),
(6, 1, '角色列表', '/a/role_list', 0, 1, 0, 0, 1655797498, 0),
(7, 1, '添加|修改成员', '/a/add_member', 0, 1, 0, 1, 1655797587, 0),
(8, 1, '成员列表', '/a/members_list', 0, 1, 0, 0, 1655797609, 0),
(9, 1, '角色列表', '/a/role_all', 0, 1, 0, 0, 1655797629, 0),
(10, 1, '权限列表', '/a/permission_all', 0, 1, 0, 0, 1655797747, 0),
(11, 1, '成员日志列表', '/a/member_logs', 0, 1, 0, 0, 1692784683, 0),
(12, 1, '权限分组列表', '/a/permission_groups', 0, 1, 0, 0, 1693491536, 0),
(13, 1, '添加|修改权限分组', '/a/add_permission_group', 0, 1, 0, 1, 1693491573, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cm_permission_group`
--

CREATE TABLE `cm_permission_group` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(12) DEFAULT '' COMMENT 'key',
  `name` varchar(48) NOT NULL COMMENT '组名',
  `sort` int(11) DEFAULT '0' COMMENT '排序，数字越大越靠前',
  `created_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限分组表';

--
-- 转存表中的数据 `cm_permission_group`
--

INSERT INTO `cm_permission_group` (`id`, `key`, `name`, `sort`, `created_at`, `deleted_at`) VALUES
(1, 'sys', '成员/权限/角色', 1, 1693483143, 0),
(2, 'orders', '订单模块', 0, 1693484857, 1693491885);

-- --------------------------------------------------------

--
-- 表的结构 `cm_roles`
--

CREATE TABLE `cm_roles` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '角色表',
  `name` varchar(24) NOT NULL COMMENT '角色名，最多8个字',
  `key` varchar(18) NOT NULL COMMENT '唯一标识',
  `desc` varchar(90) DEFAULT '' COMMENT '描述',
  `permission_id` text COMMENT '权限id',
  `routers` text COMMENT '前端vue路由name值',
  `status` tinyint(1) DEFAULT '0' COMMENT '0正常 1停用',
  `creator_id` int(10) NOT NULL COMMENT '创建人id',
  `updater_id` int(10) DEFAULT '0' COMMENT '最近修改人id',
  `redirect` varchar(18) NOT NULL COMMENT '登入后跳转路由name',
  `created_at` int(10) NOT NULL,
  `deleted_at` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `cm_roles`
--

INSERT INTO `cm_roles` (`id`, `name`, `key`, `desc`, `permission_id`, `routers`, `status`, `creator_id`, `updater_id`, `redirect`, `created_at`, `deleted_at`) VALUES
(1, '开发员', 'developer', '系统开发人员。', '1,2,4,5,6,7,8,9,10,11,12,13', 'sysLogs,sysMembers,sysPermission,sysRole', 0, 1, 1, 'sysPermission', 1655444498, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cm_role_member`
--

CREATE TABLE `cm_role_member` (
  `member_id` int(4) NOT NULL,
  `role_id` int(4) NOT NULL,
  `created_at` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='成员-角色关系表';

--
-- 转存表中的数据 `cm_role_member`
--

INSERT INTO `cm_role_member` (`member_id`, `role_id`, `created_at`) VALUES
(1, 1, 1655798878);

--
-- 转储表的索引
--

--
-- 表的索引 `cm_members`
--
ALTER TABLE `cm_members`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_member_log`
--
ALTER TABLE `cm_member_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cm_sys_logs_id_uindex` (`id`),
  ADD KEY `idx_member` (`member_id`);

--
-- 表的索引 `cm_permissions`
--
ALTER TABLE `cm_permissions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_permission_group`
--
ALTER TABLE `cm_permission_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cm_permission_group_id_uindex` (`id`);

--
-- 表的索引 `cm_roles`
--
ALTER TABLE `cm_roles`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cm_members`
--
ALTER TABLE `cm_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '成员表', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `cm_member_log`
--
ALTER TABLE `cm_member_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- 使用表AUTO_INCREMENT `cm_permissions`
--
ALTER TABLE `cm_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '权限表', AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `cm_permission_group`
--
ALTER TABLE `cm_permission_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `cm_roles`
--
ALTER TABLE `cm_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色表', AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
