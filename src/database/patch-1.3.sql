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

UPDATE config SET value="1.3" WHERE token = "database_version";
UPDATE config SET value="(Build 22-10-2016)" WHERE token = "build_number";

ALTER TABLE tuser DROP status;
ALTER TABLE tuser DROP owner;
ALTER TABLE tuser DROP prio;
ALTER TABLE tuser DROP type;

CREATE TABLE `filter` (
  `filter_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `prio` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `owner` int(11) NOT NULL
  PRIMARY KEY (`filter_id`),
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
