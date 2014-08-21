# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.38-0ubuntu0.12.04.1)
# Database: edart
# Generation Time: 2014-08-05 01:45:17 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

# Create the database
# ------------------------------------------------------------

DROP DATABASE IF EXISTS `edart`;
CREATE DATABASE `edart`;
USE `edart`;

# Delete user if they exist, then recreate them
# ------------------------------------------------------------

GRANT ALL ON *.* TO 'edart'@'127.0.0.1' IDENTIFIED BY '7AFfnNJcWpn6HPEcbmPja';
FLUSH PRIVILEGES;

# Dump of table exchange
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exchange`;

CREATE TABLE `exchange` (
  `id` longtext NOT NULL,
  `item1` longtext NOT NULL,
  `item2` longtext NOT NULL,
  `date` bigint(20) NOT NULL,
  `availability` longtext NOT NULL,
  `messages` longtext NOT NULL,
  `who_ranked` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table feed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `feed`;

CREATE TABLE `feed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usr` int(11) DEFAULT NULL,
  `string` longtext,
  `date` bigint(11) DEFAULT NULL,
  `link` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `item`;

CREATE TABLE `item` (
  `usr` int(11) NOT NULL,
  `id` text NOT NULL,
  `name` text NOT NULL,
  `category` int(11) NOT NULL,
  `description` text NOT NULL,
  `stadd1` text NOT NULL,
  `stadd2` text NOT NULL,
  `room` text NOT NULL,
  `citytown` text NOT NULL,
  `state` text NOT NULL,
  `duedate` bigint(20) NOT NULL,
  `condition` int(11) NOT NULL,
  `image` longblob NOT NULL,
  `emv` text NOT NULL,
  `offers` longtext NOT NULL,
  `expiration` bigint(20) unsigned NOT NULL,
  `adddate` bigint(20) unsigned NOT NULL,
  `reviews` longtext,
  `views` bigint(11) DEFAULT '0',
  KEY `usr` (`usr`),
  KEY `usr_2` (`usr`),
  CONSTRAINT `UserID` FOREIGN KEY (`usr`) REFERENCES `usr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table lookup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lookup`;

CREATE TABLE `lookup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(11) NOT NULL,
  `text` text NOT NULL,
  `class` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table msg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `msg`;

CREATE TABLE `msg` (
  `thread` longtext NOT NULL,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `subject` text NOT NULL,
  `msg` longtext NOT NULL,
  `d1` int(11) NOT NULL DEFAULT '1',
  `d2` int(11) NOT NULL DEFAULT '1',
  `r1` int(11) NOT NULL DEFAULT '0',
  `r2` int(11) NOT NULL DEFAULT '0',
  `date` bigint(20) NOT NULL,
  KEY `receiver` (`to`),
  KEY `sender` (`from`),
  CONSTRAINT `receiver` FOREIGN KEY (`to`) REFERENCES `usr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sender` FOREIGN KEY (`from`) REFERENCES `usr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table notify
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notify`;

CREATE TABLE `notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `link` text NOT NULL,
  `usr` int(11) DEFAULT NULL,
  `read` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NotifiedUser` (`usr`),
  CONSTRAINT `NotifiedUser` FOREIGN KEY (`usr`) REFERENCES `usr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table request
# ------------------------------------------------------------

CREATE TABLE `request` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usr` int(11) NOT NULL,
  `name` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usr` (`usr`),
  CONSTRAINT `usr` FOREIGN KEY (`usr`) REFERENCES `usr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table usr
# ------------------------------------------------------------

DROP TABLE IF EXISTS `usr`;

CREATE TABLE `usr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` text NOT NULL,
  `lname` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `profile_pic` longblob NOT NULL,
  `gender` int(11) NOT NULL DEFAULT '0',
  `join_date` bigint(20) NOT NULL,
  `last_location` text NOT NULL,
  `dob` bigint(20) NOT NULL,
  `bio` text NOT NULL,
  `rank` longtext NOT NULL,
  `do_mail` tinyint(4) NOT NULL DEFAULT '1',
  `privacy` longtext,
  `followers` longtext,
  `active` int(11) DEFAULT '0',
  `last_login` bigint(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

	
# Add dummy user
# ------------------------------------------------------------

INSERT INTO `usr` (`id`, `fname`, `lname`, `email`, `password`, `profile_pic`, `gender`, `join_date`, `last_location`, `dob`, `bio`, `rank`, `do_mail`, `privacy`, `followers`, `active`, `last_login`, `status`)
VALUES
(1,'eDart','Developer','developer@edart.edu','56c3c1f5ee89d904b249351a309bc1b8e650b1e044fe0ca98835f3984d0be59esMAF1RroA6tdVh8B9m8K25D66V85Wb6kJ4P3G7Uvh8u7Yws7r6u3RM6sDYhIF0x26DR0H6m2T8rfoGH1tMF9HGp5mQJ3QiDG6OXd05h1KQ02D929Nu5y7a15zchbLRGM71vdH5JOCPwmg9hHC895emy8t33x8Ehy03yE75t73tuBj7XnPhKWNL769Ab9Ik6mSgBLhZ5XVA384QED2E25r1sW5Ms87f6DJyaPaj9V4o6xQMx2wR39l2Qo04d85H80','',0,1407288325,'',0,'','',1,NULL,NULL,0,NULL,2);

# Add lookup values
# ------------------------------------------------------------

INSERT INTO `lookup` (`id`, `code`, `text`, `class`)
VALUES
	(1,1,'Apparel',1),
	(2,2,'Athletic Clothing',1),
	(3,3,'Books',1),
	(4,4,'Computers',1),
	(5,5,'Electronics',1),
	(6,6,'Furniture/Decor',1),
	(7,7,'Games',1),
	(8,8,'Jewellery',1),
	(9,9,'Linens',1),
	(10,10,'Movies/Videos',1),
	(11,11,'Music',1),
	(12,12,'School/Office Supplies',1),
	(13,13,'Sport Accessories',1),
	(14,1,'Almost New',2),
	(15,2,'Subtly Used',2),
	(16,3,'Noticably Used',2),
	(17,4,'Extremely Used',2),
	(18,5,'Hardcore Usage',2),
	(19,1,'Male',3),
	(20,2,'Female',3),
	(21,3,'Computer Scientist',3),
	(22,1,'He',4),
	(23,2,'She',4),
	(24,3,'Their',4),
	(25,600,'Operation Successful!',6),
	(26,104,'Invalid email address',5),
	(27,105,'Passwords do not match',5),
	(28,106,'WPI address required',5),
	(29,601,'Password change successful!',6),
	(30,602,'Item successfully deleted!',6),
	(31,603,'Information successfully saved!',6),
	(32,604,'Privacy settings changed!',6),
	(36,103,'This user already exists. If you forgot your password, click \"Forgot\" to reset it.',5);

# Dump of table validate
# ------------------------------------------------------------

DROP TABLE IF EXISTS `validate`;

CREATE TABLE `validate` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `key` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
