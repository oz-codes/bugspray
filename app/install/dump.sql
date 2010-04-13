SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `actiontypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color` varchar(6) NOT NULL,
  `img` varchar(32) NOT NULL,
  `title` varchar(24) NOT NULL,
  `logdescription` varchar(24) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `actiontypes` (`id`, `color`, `img`, `title`, `logdescription`) VALUES
(1, 'BFFFBF', 'open.png', 'issue-open', 'opened an issue'),
(2, 'FFBFBF', 'close.png', 'issue-close', 'locked an issue'),
(3, 'BFE9FF', 'comment.png', 'issue-comment', 'commented on an issue');

CREATE TABLE IF NOT EXISTS `assigns_userproject` (
  `userid` int(11) NOT NULL,
  `projectid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `assigns_userproject` (`userid`, `projectid`) VALUES
(1, 1);

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `color` varchar(6) NOT NULL,
  `projects` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `categories` (`id`, `name`, `color`, `projects`) VALUES
(1, 'suggestion', 'F8FFBF', ''),
(2, 'bug-severe', 'FF3F3F', ''),
(3, 'bug-medium', 'FF7F7F', ''),
(4, 'bug-low', 'FFBFBF', '');

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL,
  `issue` int(11) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `when_posted` datetime NOT NULL,
  `when_edited` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

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
  `project` int(11) NOT NULL,
  `closereason` varchar(64) NOT NULL,
  `discussion_closed` tinyint(1) NOT NULL,
  `category` int(11) NOT NULL,
  `tags` varchar(128) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `assign` int(11) NOT NULL DEFAULT '0',
  `num_comments` int(11) NOT NULL,
  `num_views` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `log_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `when_occured` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `actiontype` int(11) NOT NULL,
  `issue` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `projects` (`id`, `name`) VALUES
(1, 'Example Project 1');

CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('open','assigned','resolved','declined') NOT NULL,
  `name` varchar(24) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `statuses` (`id`, `type`, `name`) VALUES
(1, 'open', 'open'),
(2, 'assigned', 'assigned'),
(3, 'resolved', 'resolved'),
(4, 'declined', 'duplicate'),
(5, 'declined', 'declined'),
(6, 'declined', 'bydesign'),
(7, 'declined', 'nonissue'),
(8, 'declined', 'spam');

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