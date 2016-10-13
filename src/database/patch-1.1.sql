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

CREATE TABLE `cron` (
  `cron_id` int(11) NOT NULL,
  `last_run` datetime NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`cron_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO cron VALUES("1",sysdate(),"Backup script");

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `status_old` int(11) NOT NULL,
  `status_new` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `last_login` datetime NOT NULL,
  `last_activity` datetime NOT NULL,
  `requests` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO member VALUES("1","1","admin","21232f297a57a5a743894a0e4a801fc3",sysdate(),sysdate(),"0","0");

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `public` int(11) NOT NULL,
  `days` varchar(20) NOT NULL,
  `history` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO project VALUES("1","Demo","1","0,1,2,3,4,5,6","0","0");

CREATE TABLE `project_user` (
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `bcr` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `released` (
  `release_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `note` varchar(100) NOT NULL,
  `deleted` int(11) NOT NULL,
  PRIMARY KEY (`release_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `project_edit` int(11) NOT NULL,
  `story_add` int(11) NOT NULL,
  `story_edit` int(11) NOT NULL,
  `story_delete` int(11) NOT NULL,
  `story_export` int(11) NOT NULL,
  `story_import` int(11) NOT NULL,
  `project_cost` int(11) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO role VALUES("1","0","0","0","0","0","0","0");
INSERT INTO role VALUES("2","1","1","1","1","1","1","1");
INSERT INTO role VALUES("3","0","1","1","1","0","0","0");
INSERT INTO role VALUES("4","0","0","1","0","0","0","0");
INSERT INTO role VALUES("5","1","1","1","1","1","1","1");

CREATE TABLE `session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `session` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `session` (`session`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE `sprint` (
  `sprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `release_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `deleted` int(11) NOT NULL,
  `locked` int(11) NOT NULL,
  PRIMARY KEY (`sprint_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE `story` (
  `story_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `number` int(10) NOT NULL,
  `summary` varchar(1024) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `status` int(11) NOT NULL,
  `points` float NOT NULL,
  `sprint_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `reference` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `deleted` int(11) NOT NULL,
  `prio` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `story_story_id` int(11) NOT NULL,
  PRIMARY KEY (`story_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `tuser` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `valid` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `sprint_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `owner` int(11) NOT NULL,
  `prio` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `language` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO tuser VALUES("1","Administrator","admin@plaatsoft.nl","0","5","1","1","0","0","0","0","0","0","0");


