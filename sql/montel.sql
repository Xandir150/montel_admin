/*
Navicat MariaDB Data Transfer

Source Server         : admin.montelcompany.me_3306
Source Server Version : 100147
Source Host           : admin.montelcompany.me:3306
Source Database       : montel

Target Server Type    : MariaDB
Target Server Version : 100147
File Encoding         : 65001

Date: 2020-11-27 00:21:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for collection
-- ----------------------------
DROP TABLE IF EXISTS `collection`;
CREATE TABLE `collection` (
  `place` varchar(64) NOT NULL,
  `amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`place`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `number` int(11) NOT NULL,
  `place` varchar(64) NOT NULL,
  `amount` int(11) NOT NULL,
  `provider` varchar(64) NOT NULL,
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `cb` decimal(11,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1326 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for payments_comment
-- ----------------------------
DROP TABLE IF EXISTS `payments_comment`;
CREATE TABLE `payments_comment` (
  `id` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pings
-- ----------------------------
DROP TABLE IF EXISTS `pings`;
CREATE TABLE `pings` (
  `id` varchar(64) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TRIGGER IF EXISTS `befor_insert`;
DELIMITER ;;
CREATE TRIGGER `befor_insert` BEFORE INSERT ON `payments` FOR EACH ROW begin
SET NEW.cb = (select round(payments - expenses,2) + NEW.amount / 100 from adminweb.customers where phone = NEW.number  limit 1);
end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `collect`;
DELIMITER ;;
CREATE TRIGGER `collect` AFTER INSERT ON `payments` FOR EACH ROW begin
-- update payments set cb = (select round(payments - expenses,2) + NEW.amount / 100 from adminweb.customers where phone = NEW.number  limit 1);
if (NEW.number = 68829160) then
INSERT INTO collection(place,amount)
VALUES(NEW.place, 0)
ON DUPLICATE KEY UPDATE amount = 0;
else
INSERT INTO collection(place,amount)
VALUES(NEW.place, NEW.amount)
ON DUPLICATE KEY UPDATE amount = amount + NEW.amount;
END IF;
UPDATE adminweb.customers SET lastpaydate=CURRENT_TIMESTAMP WHERE phone=NEW.number LIMIT 1;
UPDATE adminweb.customers set payments= payments + NEW.amount / 100 where phone = NEW.number LIMIT 1;
end
;;
DELIMITER ;
