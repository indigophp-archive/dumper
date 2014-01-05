/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50534
Source Host           : 127.0.0.1:3306
Source Database       : test2

Target Server Type    : MYSQL
Target Server Version : 50534
File Encoding         : 65001

Date: 2014-01-05 11:03:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for test
-- ----------------------------
DROP TABLE IF EXISTS `test`;
CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `test` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of test
-- ----------------------------
INSERT INTO `test` VALUES ('1', 'test_data');

-- ----------------------------
-- View structure for v_test
-- ----------------------------
DROP VIEW IF EXISTS `v_test`;
CREATE ALGORITHM=UNDEFINED DEFINER=`travis`@`localhost` SQL SECURITY DEFINER VIEW `v_test` AS select `test`.`id` AS `id`,`test`.`test` AS `test` from `test` ;
