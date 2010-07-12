SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL,
  `issue` int(11) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `when_posted` datetime NOT NULL,
  `when_edited` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `config` (
  `name` varchar(16) NOT NULL,
  `value` text,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `config` (`name`, `value`) VALUES
('sitename', 'My Issue Tracker'),
('theme', 'default'),
('stripwhitespace', '0'),
('gzip', '0');

CREATE TABLE IF NOT EXISTS `favorites` (
  `ticketid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `global_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `groups` (`id`, `name`, `global_admin`) VALUES
(1, 'Member', 0),
(2, 'Admin (Global)', 1);

CREATE TABLE IF NOT EXISTS `issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `author` int(11) NOT NULL,
  `when_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `when_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tags` varchar(64) NOT NULL,
  `severity` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `assign` int(11) NOT NULL DEFAULT '0',
  `num_comments` int(11) NOT NULL,
  `num_views` int(11) NOT NULL,
  `misc` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `log_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `when_occured` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `actiontype` int(11) NOT NULL,
  `issue` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `slug` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `tags` (`id`, `name`, `slug`) VALUES
(1, 'suggestion', ''),
(2, 'bug', '');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(24) NOT NULL,
  `displayname` varchar(24) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `password_salt` varchar(128) NOT NULL,
  `group` int(11) NOT NULL DEFAULT '1',
  `when_registered` datetime NOT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(128) NOT NULL,
  `email_show` tinyint(1) NOT NULL DEFAULT '0',
  `website` varchar(128) NOT NULL,
  `avatar_type` int(11) NOT NULL,
  `avatar_location` varchar(128) NOT NULL,
  `num_posted_issues` int(11) NOT NULL,
  `num_posted_comments` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
