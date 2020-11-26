/*
Navicat MariaDB Data Transfer

Source Server         : admin.montelcompany.me_3306
Source Server Version : 100147
Source Host           : admin.montelcompany.me:3306
Source Database       : adminweb

Target Server Type    : MariaDB
Target Server Version : 100147
File Encoding         : 65001

Date: 2020-11-27 00:20:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for bills
-- ----------------------------
DROP TABLE IF EXISTS `bills`;
CREATE TABLE `bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `number` int(11) NOT NULL,
  `doc_num` varchar(64) NOT NULL,
  `amount` decimal(21,10) DEFAULT NULL,
  `provider` varchar(64) NOT NULL,
  `service` varchar(255) NOT NULL DEFAULT '',
  `calls_local` decimal(21,10) DEFAULT '0.0000000000',
  `calls_other` decimal(21,10) DEFAULT '0.0000000000',
  `calls_landline` decimal(21,10) DEFAULT '0.0000000000',
  `sms_national` decimal(21,10) DEFAULT '0.0000000000',
  `sms_international` decimal(21,10) DEFAULT '0.0000000000',
  `gprs` decimal(21,10) DEFAULT '0.0000000000',
  `calls_special` decimal(21,10) DEFAULT '0.0000000000',
  `call_international` decimal(21,10) DEFAULT '0.0000000000',
  `roaming` decimal(21,10) DEFAULT '0.0000000000',
  `addational_service` decimal(21,10) DEFAULT '0.0000000000',
  `mms` decimal(21,10) DEFAULT '0.0000000000',
  `over_limit` decimal(21,10) DEFAULT '0.0000000000',
  `discount` decimal(21,10) DEFAULT '0.0000000000',
  `cb` decimal(11,2) DEFAULT '0.00',
  `OverFee` decimal(11,2) DEFAULT '0.00',
  `OverFeeTRate` decimal(11,2) DEFAULT '0.00',
  `tAmount` decimal(11,2) DEFAULT '0.00',
  `revenue` decimal(11,2) DEFAULT '0.00',
  PRIMARY KEY (`number`,`doc_num`,`service`,`provider`),
  KEY `prim` (`id`,`datetime`,`number`,`doc_num`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bills_blackhole
-- ----------------------------
DROP TABLE IF EXISTS `bills_blackhole`;
CREATE TABLE `bills_blackhole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `number` int(11) NOT NULL,
  `doc_num` varchar(64) NOT NULL,
  `amount` decimal(21,10) DEFAULT NULL,
  `provider` varchar(64) DEFAULT NULL,
  `service` varchar(255) DEFAULT '',
  `calls_local` decimal(21,10) DEFAULT '0.0000000000',
  `calls_other` decimal(21,10) DEFAULT '0.0000000000',
  `calls_landline` decimal(21,10) DEFAULT '0.0000000000',
  `sms_national` decimal(21,10) DEFAULT '0.0000000000',
  `sms_international` decimal(21,10) DEFAULT '0.0000000000',
  `gprs` decimal(21,10) DEFAULT '0.0000000000',
  `calls_special` decimal(21,10) DEFAULT '0.0000000000',
  `call_international` decimal(21,10) DEFAULT '0.0000000000',
  `roaming` decimal(21,10) DEFAULT '0.0000000000',
  `addational_service` decimal(21,10) DEFAULT '0.0000000000',
  `mms` decimal(21,10) DEFAULT '0.0000000000',
  `over_limit` decimal(21,10) DEFAULT '0.0000000000',
  `discount` decimal(21,10) DEFAULT '0.0000000000',
  PRIMARY KEY (`number`,`doc_num`),
  KEY `prim` (`id`,`datetime`,`number`,`doc_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bills_copy
-- ----------------------------
DROP TABLE IF EXISTS `bills_copy`;
CREATE TABLE `bills_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `number` int(11) NOT NULL,
  `doc_num` varchar(64) NOT NULL,
  `amount` decimal(21,10) DEFAULT NULL,
  `provider` varchar(64) NOT NULL,
  `service` varchar(255) NOT NULL DEFAULT '',
  `calls_local` decimal(21,10) DEFAULT '0.0000000000',
  `calls_other` decimal(21,10) DEFAULT '0.0000000000',
  `calls_landline` decimal(21,10) DEFAULT '0.0000000000',
  `sms_national` decimal(21,10) DEFAULT '0.0000000000',
  `sms_international` decimal(21,10) DEFAULT '0.0000000000',
  `gprs` decimal(21,10) DEFAULT '0.0000000000',
  `calls_special` decimal(21,10) DEFAULT '0.0000000000',
  `call_international` decimal(21,10) DEFAULT '0.0000000000',
  `roaming` decimal(21,10) DEFAULT '0.0000000000',
  `addational_service` decimal(21,10) DEFAULT '0.0000000000',
  `mms` decimal(21,10) DEFAULT '0.0000000000',
  `over_limit` decimal(21,10) DEFAULT '0.0000000000',
  `discount` decimal(21,10) DEFAULT '0.0000000000',
  `cb` decimal(11,2) DEFAULT '0.00',
  `OverFee` decimal(11,2) DEFAULT '0.00',
  `OverFeeTRate` decimal(11,2) DEFAULT '0.00',
  `tAmount` decimal(11,2) DEFAULT '0.00',
  `revenue` decimal(11,2) DEFAULT '0.00',
  PRIMARY KEY (`number`,`doc_num`,`service`,`provider`),
  KEY `prim` (`id`,`datetime`,`number`,`doc_num`)
) ENGINE=InnoDB AUTO_INCREMENT=6626 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` int(11) NOT NULL,
  `city` varchar(64) DEFAULT NULL,
  `telegram` varchar(64) DEFAULT NULL,
  `Facebook` varchar(64) NOT NULL,
  `credit` int(11) NOT NULL,
  `description` text NOT NULL,
  `tariff` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `payments` decimal(11,2) NOT NULL,
  `expenses` decimal(21,10) NOT NULL,
  `created` date NOT NULL,
  `registered` date NOT NULL,
  `lastpaydate` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `tPercent` decimal(5,2) DEFAULT '1.00',
  `tDicsount` decimal(11,2) DEFAULT '1.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3567 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for export
-- ----------------------------
DROP TABLE IF EXISTS `export`;
CREATE TABLE `export` (
  `ФИО` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `Телефон` varchar(255) DEFAULT NULL,
  `Facebook` varchar(255) DEFAULT NULL,
  `Лимит` varchar(255) DEFAULT NULL,
  `Примечание` varchar(255) DEFAULT NULL,
  `Тариф` varchar(255) DEFAULT NULL,
  `Состояние` varchar(255) DEFAULT NULL,
  `Поступления` varchar(255) DEFAULT NULL,
  `Расходы` varchar(255) DEFAULT NULL,
  `Баланс` varchar(255) DEFAULT NULL,
  `Создан` varchar(255) DEFAULT NULL,
  `Дата обращения` varchar(255) DEFAULT NULL,
  `ID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PurchaseOrderHeader
-- ----------------------------
DROP TABLE IF EXISTS `PurchaseOrderHeader`;
CREATE TABLE `PurchaseOrderHeader` (
  `PurchaseOrderID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `VendorID` int(11) NOT NULL,
  PRIMARY KEY (`PurchaseOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for statuses
-- ----------------------------
DROP TABLE IF EXISTS `statuses`;
CREATE TABLE `statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `decription` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tariffs
-- ----------------------------
DROP TABLE IF EXISTS `tariffs`;
CREATE TABLE `tariffs` (
  `id` int(11) NOT NULL,
  `Name` varchar(64) NOT NULL,
  `decription` text NOT NULL,
  `tRate` decimal(5,2) DEFAULT NULL,
  `tDicsount` decimal(11,2) DEFAULT NULL,
  `tFixAdd` decimal(11,2) NOT NULL DEFAULT '0.00',
  `tOperatorFee` decimal(11,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TRIGGER IF EXISTS `befor_insert`;
DELIMITER ;;
CREATE TRIGGER `befor_insert` BEFORE INSERT ON `bills` FOR EACH ROW begin
select tPercent, tRate, tFixAdd, tOperatorFee, expenses, payments INTO @customer_rate, @tariff_rate, @tFixAdd, @tOperatorFee, @expenses,  @payments  
from adminweb.customers INNER JOIN adminweb.tariffs on tariffs.id = customers.tariff where customers.phone = NEW.number LIMIT 1;

SET NEW.OverFee = NEW.calls_local + NEW.calls_other + NEW.calls_landline + NEW.sms_national + NEW.sms_international + NEW.gprs + NEW.calls_special + NEW.call_international + NEW.roaming + NEW.addational_service + NEW.mms + NEW.over_limit;
SET NEW.OverFeeTRate = NEW.OverFee * @tariff_rate;
SET NEW.tAmount = @tOperatorFee + NEW.OverFeeTRate + @tFixAdd;
SET NEW.revenue = NEW.tAmount - ((NEW.OverFee + NEW.amount) * 1.2) + NEW.discount;
SET NEW.cb = round(@payments - @expenses,2) - NEW.tAmount;
UPDATE adminweb.customers SET expenses = expenses + NEW.tAmount;
end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `insert_bill`;
DELIMITER ;;
CREATE TRIGGER `insert_bill` AFTER UPDATE ON `bills_blackhole` FOR EACH ROW BEGIN
	select tPercent, tRate INTO @customer_rate, @tariff_rate  from adminweb.customers INNER JOIN adminweb.tariffs on tariffs.id = customers.tariff where customers.phone = NEW.number LIMIT 1;

	SET @bill_summ = NEW.amount + NEW.calls_local + NEW.calls_other + NEW.calls_landline + NEW.sms_national + NEW.sms_international + NEW.gprs + NEW.calls_special + NEW.call_international + NEW.roaming + NEW.addational_service + NEW.mms + NEW.over_limit;
	UPDATE adminweb.customers SET expenses = expenses + (@bill_summ * @tariff_rate * tPercent)
	WHERE
		phone = NEW.number
	LIMIT 1;
BEGIN
	INSERT INTO adminweb.bills (
		`number`,
		`doc_num`,
		`amount`,
		`provider`,
		`calls_local`,
		`calls_other`,
		`calls_landline`,
		`sms_national`,
		`sms_international`,
		`gprs`,
		`calls_special`,
		`call_international`,
		`roaming`,
		`addational_service`,
		`mms`,
		`over_limit`,
		`service`
	)
	VALUES
		(
			NEW.number,
			'' + NEW.doc_num + '-p',
			NEW.amount * @tariff_rate * @customer_rate - NEW.amount,
			NEW.provider,
			NEW.calls_local * @tariff_rate * @customer_rate - NEW.calls_local,
			NEW.calls_other * @tariff_rate * @customer_rate - NEW.calls_other,
			NEW.calls_landline * @tariff_rate * @customer_rate - NEW.calls_landline,
			NEW.sms_national * @tariff_rate * @customer_rate - NEW.sms_national,
			NEW.sms_international * @tariff_rate * @customer_rate - NEW.sms_international,
			NEW.gprs * @tariff_rate * @customer_rate - NEW.gprs,
			NEW.calls_special * @tariff_rate * @customer_rate - NEW.calls_special,
			NEW.call_international * @tariff_rate * @customer_rate - NEW.call_international,
			NEW.roaming * @tariff_rate * @customer_rate - NEW.roaming,
			NEW.addational_service * @tariff_rate * @customer_rate - NEW.addational_service,
			NEW.mms * @tariff_rate * @customer_rate - NEW.mms,
			NEW.over_limit * @tariff_rate * @customer_rate - NEW.over_limit,
			'promet'
		) ON DUPLICATE KEY UPDATE amount =
	VALUES
		(amount);
	INSERT INTO adminweb.bills (
			`number`,
			`doc_num`,
			`amount`,
			`provider`,
			`calls_local`,
			`calls_other`,
			`calls_landline`,
			`sms_national`,
			`sms_international`,
			`gprs`,
			`calls_special`,
			`call_international`,
			`roaming`,
			`addational_service`,
			`mms`,
			`over_limit`,
			`service`
		)
		VALUES
			(
				NEW.number,
				'' + NEW.doc_num,
				NEW.amount,
				NEW.provider,
				NEW.calls_local,
				NEW.calls_other,
				NEW.calls_landline,
				NEW.sms_national,
				NEW.sms_international,
				NEW.gprs,
				NEW.calls_special,
				NEW.call_international,
				NEW.roaming,
				NEW.addational_service,
				NEW.mms,
				NEW.over_limit,
				'bill'
			) ON DUPLICATE KEY UPDATE amount =
		VALUES
			(amount);	
END;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `befor_insert_copy`;
DELIMITER ;;
CREATE TRIGGER `befor_insert_copy` BEFORE INSERT ON `bills_copy` FOR EACH ROW begin
select tPercent, tRate, tFixAdd, tOperatorFee INTO @customer_rate, @tariff_rate, @tFixAdd, @tOperatorFee  from adminweb.customers INNER JOIN adminweb.tariffs on tariffs.id = customers.tariff where customers.phone = NEW.number LIMIT 1;

SET NEW.OverFee = NEW.calls_local + NEW.calls_other + NEW.calls_landline + NEW.sms_national + NEW.sms_international + NEW.gprs + NEW.calls_special + NEW.call_international + NEW.roaming + NEW.addational_service + NEW.mms + NEW.over_limit;
SET NEW.OverFeeTRate = NEW.OverFee * @tariff_rate;
SET NEW.tAmount = @tOperatorFee + NEW.OverFeeTRate + @tFixAdd;
SET NEW.revenue = NEW.tAmount - ((NEW.OverFee - NEW.amount) * 1.2) + NEW.discount;

UPDATE adminweb.customers SET expenses = expenses + NEW.tAmount;

SET NEW.cb = (select round(payments - expenses,2) - NEW.tAmount from adminweb.customers where phone = NEW.number  limit 1);
end
;;
DELIMITER ;
