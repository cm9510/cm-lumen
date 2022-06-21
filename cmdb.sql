/*
SQLyog Community
MySQL - 5.7.26 : Database - cm_dev
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `cm_dev`;

/*Table structure for table `cm_members` */

DROP TABLE IF EXISTS `cm_members`;

CREATE TABLE `cm_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '成员表',
  `nickname` varchar(30) DEFAULT '',
  `phone` char(11) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(8) NOT NULL COMMENT '盐',
  `status` tinyint(1) DEFAULT '0' COMMENT '0正常 1禁用',
  `last_login_at` int(10) DEFAULT '0',
  `create_at` int(10) NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cm_members` */

insert  into `cm_members`(`id`,`nickname`,`phone`,`password`,`salt`,`status`,`last_login_at`,`create_at`,`deleted`) values 
(1,'晚听清风','13966662222','802975c7ab22068fd32bb6d65ed06f58','9eZP88DU',0,1655799712,1655716595,0);

/*Table structure for table `cm_permissions` */

DROP TABLE IF EXISTS `cm_permissions`;

CREATE TABLE `cm_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限表',
  `name` varchar(30) NOT NULL COMMENT '权限名，最多10个字',
  `url` varchar(66) NOT NULL COMMENT 'url',
  `status` tinyint(1) DEFAULT '0' COMMENT '0正常 1停用',
  `creator_id` int(10) NOT NULL COMMENT '创建人id',
  `updator_id` int(10) DEFAULT '0' COMMENT '最近修改人id',
  `create_at` int(10) NOT NULL COMMENT '创建时间',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `cm_permissions` */

insert  into `cm_permissions`(`id`,`name`,`url`,`status`,`creator_id`,`updator_id`,`create_at`,`deleted`) values 
(1,'添加|修改权限','/a/add_permission',0,1,0,1654321085,0),
(2,'权限列表','/a/permission_list',0,1,0,1655357544,0),
(3,'删除权限','/a/del_permission',0,1,0,1655357558,1),
(4,'删除成员|角色|权限','/a/del_sys',0,1,0,1655797425,0),
(5,'添加|修改角色','/a/add_role',0,1,0,1655797484,0),
(6,'角色列表','/a/role_list',0,1,0,1655797498,0),
(7,'添加|修改成员','/a/add_member',0,1,0,1655797587,0),
(8,'成员列表','/a/members_list',0,1,0,1655797609,0),
(9,'所有角色','/a/role_all',0,1,0,1655797629,0),
(10,'所有权限','/a/permission_all',0,1,0,1655797747,0);

/*Table structure for table `cm_role_member` */

DROP TABLE IF EXISTS `cm_role_member`;

CREATE TABLE `cm_role_member` (
  `member_id` int(4) NOT NULL,
  `role_id` int(4) NOT NULL,
  `create_at` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='成员-角色关系表';

/*Data for the table `cm_role_member` */

insert  into `cm_role_member`(`member_id`,`role_id`,`create_at`) values 
(1,1,1655798878);

/*Table structure for table `cm_roles` */

DROP TABLE IF EXISTS `cm_roles`;

CREATE TABLE `cm_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色表',
  `name` varchar(24) NOT NULL COMMENT '角色名，最多8个字',
  `key` varchar(18) NOT NULL COMMENT '唯一标识',
  `desc` varchar(90) DEFAULT '' COMMENT '描述',
  `permission_id` text COMMENT '权限id',
  `roles` text COMMENT '前端vue路由name值',
  `status` tinyint(1) DEFAULT '0' COMMENT '0正常 1停用',
  `creator_id` int(10) NOT NULL COMMENT '创建人id',
  `updator_id` int(10) DEFAULT '0' COMMENT '最近修改人id',
  `redirect` varchar(18) NOT NULL COMMENT '登入后跳转路由name',
  `create_at` int(10) NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `cm_roles` */

insert  into `cm_roles`(`id`,`name`,`key`,`desc`,`permission_id`,`roles`,`status`,`creator_id`,`updator_id`,`redirect`,`create_at`,`deleted`) values 
(1,'开发人员','developer','开发系统的技术人员。','1,2,3,4,5,6,7,8,9,10,11','sysMembers,sysPermission,sysRole',0,1,0,'sysPermission',1655444498,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
