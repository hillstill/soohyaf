/*
Navicat MySQL Data Transfer

Source Server         : wamp
Source Server Version : 50612
Source Host           : localhost:3306
Source Database       : db_b2b

Target Server Type    : MYSQL
Target Server Version : 50612
File Encoding         : 65001

Date: 2015-07-09 14:03:47
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_accounts_0
-- ----------------------------
DROP TABLE IF EXISTS `tb_accounts_0`;
CREATE TABLE `tb_accounts_0` (
  `camefrom` varchar(36) NOT NULL,
  `loginname` varchar(16) NOT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `passwd_salt` varchar(4) DEFAULT NULL,
  `accountId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '账号id',
  `regYmd` int(11) NOT NULL DEFAULT '0',
  `regHHiiss` int(11) NOT NULL DEFAULT '4',
  `regClient` tinyint(4) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '',
  `statusCode` tinyint(20) NOT NULL DEFAULT '0' COMMENT '状态 (0 正常使用)',
  `nickname` varchar(36) DEFAULT NULL,
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `iRecordVerID` int(20) unsigned DEFAULT '0',
  PRIMARY KEY (`camefrom`,`loginname`),
  UNIQUE KEY `accountId` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tb_accounts_1
-- ----------------------------
DROP TABLE IF EXISTS `tb_accounts_1`;
CREATE TABLE `tb_accounts_1` (
  `camefrom` varchar(36) NOT NULL,
  `loginname` varchar(16) NOT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `passwd_salt` varchar(4) DEFAULT NULL,
  `accountId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `regYmd` int(11) NOT NULL DEFAULT '0',
  `regHHiiss` int(11) NOT NULL DEFAULT '0',
  `regClient` tinyint(4) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态（0表示正常）',
  `nickname` varchar(36) DEFAULT NULL,
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `iRecordVerID` int(20) unsigned DEFAULT '0',
  PRIMARY KEY (`camefrom`,`loginname`),
  UNIQUE KEY `accountId` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tb_device_99
-- ----------------------------
DROP TABLE IF EXISTS `tb_device_99`;
CREATE TABLE `tb_device_99` (
  `deviceId` varchar(64) NOT NULL,
  `phone` bigint(20) NOT NULL DEFAULT '0',
  `userIdentifier` varchar(64) NOT NULL,
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `extraData` varchar(1000) NOT NULL,
  `extraRet` varchar(500) NOT NULL,
  `notifyRetry` bigint(20) unsigned NOT NULL DEFAULT '9999999999999999999',
  `ip` varchar(32) NOT NULL,
  `ymd` int(11) NOT NULL DEFAULT '0',
  `hhiiss` int(11) NOT NULL DEFAULT '0',
  `iRecordVerID` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deviceId`),
  KEY `notifyRetry` (`notifyRetry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_device_log
-- ----------------------------
DROP TABLE IF EXISTS `tb_device_log`;
CREATE TABLE `tb_device_log` (
  `deviceId` varchar(64) NOT NULL,
  `dtChange` bigint(20) NOT NULL,
  `phoneOld` varchar(16) NOT NULL,
  `userIdentifierOld` varchar(64) NOT NULL,
  `extraDataOld` varchar(1000) NOT NULL,
  `extraRetOld` varchar(500) NOT NULL,
  `contractIdOld` bigint(20) NOT NULL,
  `phoneNew` varchar(16) NOT NULL,
  `userIdentifierNew` varchar(64) NOT NULL,
  `extraDataNew` varchar(1000) NOT NULL,
  `extraRetNew` varchar(500) NOT NULL,
  `contractIdNew` bigint(20) NOT NULL,
  `ipOld` varchar(32) NOT NULL,
  `ipNew` varchar(32) NOT NULL,
  PRIMARY KEY (`deviceId`,`dtChange`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tb_session_0
-- ----------------------------
DROP TABLE IF EXISTS `tb_session_0`;
CREATE TABLE `tb_session_0` (
  `sessionId` varchar(40) NOT NULL,
  `sessionData` blob,
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `accountId` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tb_session_1
-- ----------------------------
DROP TABLE IF EXISTS `tb_session_1`;
CREATE TABLE `tb_session_1` (
  `sessionId` varchar(40) NOT NULL,
  `sessionData` blob,
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `accountId` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tb_shoppoint_0
-- ----------------------------
DROP TABLE IF EXISTS `tb_shoppoint_0`;
CREATE TABLE `tb_shoppoint_0` (
  `ShopPointOSN` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIdentifier` varchar(64) NOT NULL,
  `changed` int(11) NOT NULL DEFAULT '0',
  `finalVal` int(11) NOT NULL DEFAULT '0',
  `finalStatus` int(11) NOT NULL DEFAULT '0',
  `descAdd` varchar(100) NOT NULL DEFAULT '',
  `dtAdd` int(11) NOT NULL DEFAULT '0' COMMENT '时间戳(添加)',
  `descUse` varchar(100) NOT NULL DEFAULT '',
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ShopPointOSN`),
  KEY `u` (`userIdentifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_shoppoint_1
-- ----------------------------
DROP TABLE IF EXISTS `tb_shoppoint_1`;
CREATE TABLE `tb_shoppoint_1` (
  `ShopPointOSN` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIdentifier` varchar(64) NOT NULL,
  `changed` int(11) NOT NULL DEFAULT '0',
  `finalVal` int(11) NOT NULL DEFAULT '0',
  `finalStatus` int(11) NOT NULL DEFAULT '0',
  `descAdd` varchar(100) NOT NULL DEFAULT '',
  `dtAdd` int(11) NOT NULL DEFAULT '0' COMMENT '时间戳(添加)',
  `descUse` varchar(100) NOT NULL DEFAULT '',
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ShopPointOSN`),
  KEY `u` (`userIdentifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_users_0
-- ----------------------------
DROP TABLE IF EXISTS `tb_users_0`;
CREATE TABLE `tb_users_0` (
  `accountId` bigint(20) NOT NULL,
  `contractId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `regYmd` int(255) NOT NULL DEFAULT '0',
  `regHHiiss` int(255) NOT NULL DEFAULT '0',
  `regClient` tinyint(4) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '',
  `checkin_book` varchar(200) DEFAULT NULL,
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `sLockData` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tb_users_1
-- ----------------------------
DROP TABLE IF EXISTS `tb_users_1`;
CREATE TABLE `tb_users_1` (
  `accountId` bigint(20) NOT NULL,
  `contractId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `regYmd` int(255) NOT NULL DEFAULT '0',
  `regHHiiss` int(255) NOT NULL DEFAULT '0',
  `regClient` tinyint(4) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '',
  `checkin_book` varchar(200) DEFAULT NULL,
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `sLockData` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
