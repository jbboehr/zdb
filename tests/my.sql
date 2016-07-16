
DROP DATABASE IF EXISTS `zdb`;
CREATE DATABASE IF NOT EXISTS `zdb`;

DROP USER IF EXISTS 'zdb'@'localhost';

CREATE USER 'zdb'@'localhost' IDENTIFIED BY 'zdb';
GRANT USAGE ON *.* TO 'zdb'@'localhost';
GRANT ALL ON zdb.* TO 'zdb'@'localhost';

DROP TABLE IF EXISTS `zdb`.`fixture1`;
CREATE TABLE `zdb`.`fixture1` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `strval` varchar(255) NOT NULL default '',
  `dval` double NOT NULL default '0',
  `unused` tinyint(1) default NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `zdb`.`fixture1` VALUES
(1, 'test', 2.14, NULL),
(2, 'blah', 343434.14, 1);

DROP TABLE IF EXISTS `zdb`.`fixture2`;
CREATE TABLE `zdb`.`fixture2` LIKE `zdb`.`fixture1`;
INSERT `zdb`.`fixture2` SELECT * FROM `zdb`.`fixture1`;

DROP TABLE IF EXISTS `zdb`.`fixture3`;
CREATE TABLE `zdb`.`fixture3` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `zdb`.`fixture3` VALUES
(NULL),(NULL),(NULL),(NULL),(NULL),(NULL),(NULL),(NULL),(NULL),(NULL);
