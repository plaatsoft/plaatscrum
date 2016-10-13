--
--  ===========
--  PlaatScrum
--  ===========
--
--  Created by wplaat
--
--  For more information visit the following website.
--  Website : www.plaatsoft.nl 
--
--  Or send an email to the following address.
--  Email   : info@plaatsoft.nl
--
--  All copyrights reserved (c) 2008-2016 PlaatSoft
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `value` varchar(128) NOT NULL,
  `options` varchar(255) NOT NULL,
  `last_update` date NOT NULL,
  `readonly` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `config` (`id`, `category`, `token`, `value`, `options`, `last_update`, `readonly`) VALUES
(NULL, '0', 'database_version', '1.2', '', sysdate(), 1);

INSERT INTO `config` (`id`, `category`, `token`, `value`, `options`, `last_update`, `readonly`) VALUES
(NULL, '0', 'build_number', '(Build 12-10-2016)', '', sysdate(), 1);

INSERT INTO `config` (`id`, `category`, `token`, `value`, `options`, `last_update`, `readonly`) VALUES 
(NULL, '0', 'timezone', 'Europe/Amsterdam', '', sysdate(), '0');

ALTER TABLE `member` CHANGE `password` `password` VARCHAR(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

UPDATE `member` SET `password` = '$2y$12$oOW5I8zbRGYRL5nVl.o3R.mUNvjf/Wg.CvP2Yj9NOgKgYJhkWDB7m' WHERE `member`.`member_id` = 1;

