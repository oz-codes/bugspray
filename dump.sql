-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 18, 2009 at 10:35 AM
-- Server version: 5.1.30
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bugspraytest`
--

-- --------------------------------------------------------

--
-- Table structure for table `actiontypes`
--

CREATE TABLE IF NOT EXISTS `actiontypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color` varchar(6) NOT NULL,
  `img` varchar(32) NOT NULL,
  `title` varchar(24) NOT NULL,
  `logdescription` varchar(24) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `actiontypes`
--

INSERT INTO `actiontypes` (`id`, `color`, `img`, `title`, `logdescription`) VALUES
(1, 'BFFFBF', 'open.png', 'issue-open', 'opened an issue'),
(2, 'FFBFBF', 'close.png', 'issue-close', 'locked an issue'),
(3, 'BFE9FF', 'comment.png', 'issue-comment', 'commented on an issue');

-- --------------------------------------------------------

--
-- Table structure for table `assigns_userproject`
--

CREATE TABLE IF NOT EXISTS `assigns_userproject` (
  `userid` int(11) NOT NULL,
  `projectid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `assigns_userproject`
--

INSERT INTO `assigns_userproject` (`userid`, `projectid`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `color` varchar(6) NOT NULL,
  `projects` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `color`, `projects`) VALUES
(1, 'suggestion', 'F8FFBF', ''),
(2, 'bug-severe', 'FF3F3F', ''),
(3, 'bug-medium', 'FF7F7F', ''),
(4, 'bug-low', 'FFBFBF', '');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL,
  `issue` int(11) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `when_posted` datetime NOT NULL,
  `when_edited` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `global_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `global_admin`) VALUES
(1, 'Member', 0),
(2, 'Administrator', 1);

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE IF NOT EXISTS `issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `author` int(11) NOT NULL,
  `when_opened` datetime NOT NULL,
  `project` int(11) NOT NULL,
  `closereason` varchar(64) NOT NULL,
  `discussion_closed` tinyint(1) NOT NULL,
  `category` int(11) NOT NULL,
  `tags` varchar(128) NOT NULL,
  `status` int(11) NOT NULL,
  `assign` int(11) NOT NULL DEFAULT '0',
  `num_comments` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `issues`
--


-- --------------------------------------------------------

--
-- Table structure for table `log_issues`
--

CREATE TABLE IF NOT EXISTS `log_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `when_occured` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `actiontype` int(11) NOT NULL,
  `issue` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `log_issues`
--


-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`) VALUES
(1, 'Example Project 1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(24) NOT NULL,
  `displayname` varchar(24) NOT NULL,
  `password` varchar(128) NOT NULL,
  `password_salt` varchar(128) NOT NULL,
  `group` int(11) NOT NULL,
  `when_registered` datetime NOT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(128) NOT NULL,
  `website` varchar(128) NOT NULL,
  `avatar_type` int(11) NOT NULL,
  `avatar_location` varchar(128) NOT NULL,
  `num_posted_issues` int(11) NOT NULL,
  `num_posted_comments` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `displayname`, `password`, `password_salt`, `group`, `when_registered`, `banned`, `email`, `website`, `avatar_type`, `avatar_location`, `num_posted_issues`, `num_posted_comments`) VALUES
(1, 'admin', '', '3f243fd044ddc930ff4c6c56f3c76978c5c182c5568ae2249c0a984f5e975516d325612e4a2256eef27d0410b6151254cb7b1ca0a8a6f0f48ca4aa2add5546b3', 'f0a5944c6a815b7bb44323d783eede4d', 2, '0000-00-00 00:00:00', 0, '', '', 1, 'img/defaultava.png', 0, 0);
