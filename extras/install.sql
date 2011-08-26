-- create a mysql user with all privileges on nairobisizzle database
CREATE USER sizzle IDENTIFIED BY 'your_password_here';

-- create mysql database for nairobisizzle
CREATE DATABASE nairobisizzle CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT all privileges ON nairobisizzle.* TO 'sizzle'@localhost IDENTIFIED BY 'your_password_here';

USE nairobisizzle;

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE IF NOT EXISTS `bids` (
  `bid_id` int(11) NOT NULL AUTO_INCREMENT,
  `classified_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `added_by` varchar(100) NOT NULL,
  `comment` varchar(100) NOT NULL,
  `amount` float NOT NULL,
  PRIMARY KEY (`bid_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bids`
--


-- --------------------------------------------------------

--
-- Table structure for table `blocked_users`
--

CREATE TABLE IF NOT EXISTS `blocked_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `attempt_number` int(11) NOT NULL DEFAULT '0',
  `request_unblock` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `blocked_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `business`
--

CREATE TABLE IF NOT EXISTS `business` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `location` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `landline` varchar(20) NOT NULL,
  `address` varchar(20) NOT NULL,
  `logo` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `category_id` int(11) NOT NULL,
  `added_by_id` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `offline` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `business`
--


-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`) VALUES
(1, 'stationery', ''),
(2, 'Apartments - rental', ''),
(3, 'Bookstores - general', ''),
(4, 'Caterers', ''),
(5, 'Clothing', ''),
(6, 'Telephone bureau', ''),
(7, 'M-PESA agent', ''),
(8, 'Cyber cafe', ''),
(9, 'Printing', ''),
(10, 'Entertainment', '');

-- --------------------------------------------------------

--
-- Table structure for table `classifieds`
--

CREATE TABLE IF NOT EXISTS `classifieds` (
  `classified_id` int(11) NOT NULL AUTO_INCREMENT,
  `added_by` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `image_url` varchar(200) NOT NULL,
  `price` varchar(20) NOT NULL DEFAULT '0',
  `description` varchar(300) NOT NULL,
  `title` varchar(50) NOT NULL,
  `category_id` int(11) NOT NULL,
  `to_auction` int(11) NOT NULL DEFAULT '0',
  `location` varchar(100) NOT NULL,
  `offline` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`classified_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `classifieds`
--


-- --------------------------------------------------------

--
-- Table structure for table `classlist`
--

CREATE TABLE IF NOT EXISTS `classlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=228 ;

--
-- Dumping data for table `classlist`
--


-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ExpId` int(11) NOT NULL,
  `Comment` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `time` time NOT NULL,
  `host_group_id` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'public',
  `charges` float NOT NULL DEFAULT '0',
  `venue` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `events`
--


-- --------------------------------------------------------

--
-- Table structure for table `Experiences`
--

CREATE TABLE IF NOT EXISTS `Experiences` (
  `ExpId` int(11) NOT NULL AUTO_INCREMENT,
  `Category` varchar(20) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Views` text NOT NULL,
  PRIMARY KEY (`ExpId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `Experiences`
--


-- --------------------------------------------------------

--
-- Table structure for table `GroupInfo`
--

CREATE TABLE IF NOT EXISTS `GroupInfo` (
  `groupID` varchar(50) NOT NULL,
  `channelID` varchar(50) NOT NULL,
  PRIMARY KEY (`groupID`),
  UNIQUE KEY `channelID` (`channelID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `GroupInfo`
--


-- --------------------------------------------------------

--
-- Table structure for table `IgnoredInvites`
--

CREATE TABLE IF NOT EXISTS `IgnoredInvites` (
  `userID` varchar(50) NOT NULL,
  `groupID` varchar(50) NOT NULL,
  PRIMARY KEY (`groupID`),
  UNIQUE KEY `groupID` (`groupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `IgnoredInvites`
--


-- --------------------------------------------------------

--
-- Table structure for table `jobexperiences`
--

CREATE TABLE IF NOT EXISTS `jobexperiences` (
  `experienceid` int(11) NOT NULL AUTO_INCREMENT,
  `field` varchar(50) NOT NULL,
  `company` varchar(100) NOT NULL,
  `jobdescription` varchar(250) NOT NULL,
  `experience` varchar(500) NOT NULL,
  `dateposted` date NOT NULL,
  `postedby` varchar(100) NOT NULL,
  `userid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`experienceid`),
  UNIQUE KEY `jobdescription` (`jobdescription`,`experience`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `jobexperiences`
--


-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `uniqueid` int(11) NOT NULL AUTO_INCREMENT,
  `field` varchar(100) NOT NULL,
  `dateadvertised` date NOT NULL,
  `duedate` date NOT NULL,
  `company` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `qualifications` varchar(250) NOT NULL,
  `appprocedure` varchar(250) NOT NULL,
  `postedby` varchar(250) NOT NULL,
  `userid` varchar(50) NOT NULL,
  PRIMARY KEY (`uniqueid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `jobs`
--


-- --------------------------------------------------------

--
-- Table structure for table `login_log`
--

CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' ',
  `username` varchar(50) NOT NULL,
  `log_in_success` int(11) NOT NULL DEFAULT '0',
  `blocked` int(11) NOT NULL DEFAULT '0',
  `trial_number` int(11) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `remember` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1916 ;

--
-- Dumping data for table `login_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE IF NOT EXISTS `people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `basic_setting` int(11) NOT NULL DEFAULT '0',
  `contact_setting` int(11) NOT NULL DEFAULT '0',
  `location_setting` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `people`
--


-- --------------------------------------------------------

--
-- Table structure for table `photos_videos`
--

CREATE TABLE IF NOT EXISTS `photos_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `is_business` int(11) NOT NULL DEFAULT '0',
  `is_product` int(11) NOT NULL DEFAULT '0',
  `url` varchar(100) NOT NULL,
  `is_photo` int(11) NOT NULL DEFAULT '0',
  `is_video` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_classified` int(11) NOT NULL DEFAULT '0',
  `classified_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `photos_videos`
--


-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `image_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `offline` int(11) NOT NULL DEFAULT '0',
  `added_by` varchar(100) NOT NULL,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `products`
--


-- --------------------------------------------------------

--
-- Table structure for table `remember_me`
--

CREATE TABLE IF NOT EXISTS `remember_me` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `cookie_value` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

--
-- Dumping data for table `remember_me`
--


-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE IF NOT EXISTS `routes` (
  `id` varchar(100) NOT NULL DEFAULT '',
  `creator` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `routes`
--


-- --------------------------------------------------------

--
-- Table structure for table `routes_subscribers`
--

CREATE TABLE IF NOT EXISTS `routes_subscribers` (
  `route` varchar(100) NOT NULL DEFAULT '',
  `subscriber` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`route`,`subscriber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `routes_subscribers`
--


-- --------------------------------------------------------

--
-- Table structure for table `upload`
--

CREATE TABLE IF NOT EXISTS `upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `mimetype` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `userid` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `upload`
--

