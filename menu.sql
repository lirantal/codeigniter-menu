CREATE TABLE `menu` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `target` varchar(10) default NULL,
  `parent_id` int(11) NOT NULL default '0',
  `commonname` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
