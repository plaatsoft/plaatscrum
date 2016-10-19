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
UPDATE config SET value="(Build 18-10-2016)" WHERE token = "build_number";

ALTER TABLE `tuser` CHANGE `prio` `prio` VARCHAR(20) NOT NULL;
