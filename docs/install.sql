CREATE TABLE IF NOT EXISTS `oestatisticslog` (
  `OXTIME` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation time',
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Shop id (oxshops)',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXSESSID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Session id',
  `OXCLASS` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Logged class name',
  `OXFNC` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Logged function name',
  `OXCNID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Active category id (oxcategories)',
  `OXANID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Active article id (oxarticles)',
  `OXPARAMETER` varchar(64) NOT NULL default '' COMMENT 'Template name or search param',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp'
) ENGINE=InnoDB COMMENT 'Stores logs from actions processing';

CREATE TABLE IF NOT EXISTS `oestatistics` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Shop id (oxshops)',
  `OXTITLE` char(32) NOT NULL default '' COMMENT 'Title',
  `OXVALUE` text NOT NULL COMMENT 'Serialized array of reports',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=InnoDB COMMENT 'Statistics reports'