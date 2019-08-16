/*
Navicat MySQL Data Transfer

Source Server         : 智慧星数据库
Source Server Version : 50642
Source Host           : 47.110.49.156:3306
Source Database       : zhongyuncheng

Target Server Type    : MYSQL
Target Server Version : 50642
File Encoding         : 65001

Date: 2019-08-16 23:51:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `dede_city_site`
-- ----------------------------
DROP TABLE IF EXISTS `dede_city_site`;
CREATE TABLE `dede_city_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `pid` int(10) NOT NULL DEFAULT '0',
  `pinyin` varchar(100) DEFAULT NULL,
  `banner_id` int(10) NOT NULL DEFAULT '0',
  `news_id` int(10) NOT NULL DEFAULT '0',
  `product_id` int(10) NOT NULL DEFAULT '0',
  `is_master` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `dede_keywords_rank`
-- ----------------------------
DROP TABLE IF EXISTS `dede_keywords_rank`;
CREATE TABLE `dede_keywords_rank` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `engines` varchar(255) DEFAULT NULL COMMENT '搜索引擎',
  `source` varchar(255) DEFAULT NULL,
  `client` varchar(255) DEFAULT NULL,
  `rank` varchar(255) DEFAULT NULL,
  `collect_count` tinyint(3) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=971 DEFAULT CHARSET=utf8;



-- ----------------------------
-- Table structure for `dede_keywords_task`
-- ----------------------------
DROP TABLE IF EXISTS `dede_keywords_task`;
CREATE TABLE `dede_keywords_task` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` varchar(100) NOT NULL COMMENT '任务id',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `add_time` int(10) NOT NULL COMMENT '提交时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，0未处理，1已处理',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8;


INSERT INTO `dede_sysconfig` VALUES ('1000', 'cfg_company', '公司名称', '8', 'string', '');
INSERT INTO `dede_sysconfig` VALUES ('1001', 'domain_name', '查询域名', '8', 'string', '');
INSERT INTO `dede_sysconfig` VALUES ('1002', 'zz_key', '站长工具key', '8', 'string', '');
