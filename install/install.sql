DROP TABLE IF EXISTS `qqapi_config`;
CREATE TABLE `qqapi_config` (
  `k` varchar(32) NOT NULL,
  `v` text NULL,
  PRIMARY KEY  (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `qqapi_config` VALUES ('admin_user', 'admin');
INSERT INTO `qqapi_config` VALUES ('admin_pwd', '123456');
INSERT INTO `qqapi_config` VALUES ('cookie_open', '0');
INSERT INTO `qqapi_config` VALUES ('opentype', 'qzone,vip');
INSERT INTO `qqapi_config` VALUES ('ip_type', '0');
INSERT INTO `qqapi_config` VALUES ('white_list', '');
INSERT INTO `qqapi_config` VALUES ('mail_open', '0');
INSERT INTO `qqapi_config` VALUES ('mail_cloud', '0');
INSERT INTO `qqapi_config` VALUES ('mail_smtp', 'smtp.qq.com');
INSERT INTO `qqapi_config` VALUES ('mail_port', '465');
INSERT INTO `qqapi_config` VALUES ('mail_name', '');
INSERT INTO `qqapi_config` VALUES ('mail_pwd', '');
INSERT INTO `qqapi_config` VALUES ('sitename', 'QQ-API管理中心');
INSERT INTO `qqapi_config` VALUES ('cache_time', '300');
INSERT INTO `qqapi_config` VALUES ('cache_clean', '');
INSERT INTO `qqapi_config` VALUES ('server_ip', '127.0.0.1');
INSERT INTO `qqapi_config` VALUES ('server_port', '1111');
INSERT INTO `qqapi_config` VALUES ('server_key', '');


DROP TABLE IF EXISTS `qqapi_account`;
CREATE TABLE `qqapi_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uin` varchar(15) NOT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `qqapi_cookie`;
CREATE TABLE `qqapi_cookie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `content` varchar(200) NOT NULL,
  `addtime` datetime NOT NULL,
  `usetime` datetime DEFAULT NULL,
  `checktime` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 UNIQUE KEY `account` (`aid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `qqapi_log`;
CREATE TABLE `qqapi_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uin` varchar(15) NOT NULL,
  `type` varchar(10) NOT NULL,
  `action` varchar(20) NOT NULL,
  `time` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `reason` varchar(100) DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `uin` (`uin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `qqapi_cache`;
CREATE TABLE `qqapi_cache` (
  `key` varchar(32) NOT NULL,
  `data` mediumtext DEFAULT NULL,
  `time` int(11) NOT NULL,
 PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;