/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Atom`
--

DROP TABLE IF EXISTS `Atom`;
CREATE TABLE `Atom` (
  `molecule_id` int(10) unsigned default NULL,
  `rack_id` int(10) unsigned default NULL,
  `unit_no` int(10) unsigned default NULL,
  `atom` enum('front','interior','rear') default NULL
) ENGINE=MyISAM;

--
-- Table structure for table `Attribute`
--

DROP TABLE IF EXISTS `Attribute`;
CREATE TABLE `Attribute` (
  `attr_id` int(10) unsigned NOT NULL auto_increment,
  `attr_type` enum('string','uint','float','dict') default NULL,
  `attr_name` char(64) default NULL,
  PRIMARY KEY  (`attr_id`),
  UNIQUE KEY `attr_name` (`attr_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10000;

--
-- Table structure for table `AttributeMap`
--

DROP TABLE IF EXISTS `AttributeMap`;
CREATE TABLE `AttributeMap` (
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `attr_id` int(10) unsigned NOT NULL default '1',
  `chapter_no` int(10) unsigned NOT NULL,
  UNIQUE KEY `objtype_id` (`objtype_id`,`attr_id`)
) ENGINE=MyISAM;

--
-- Table structure for table `AttributeValue`
--

DROP TABLE IF EXISTS `AttributeValue`;
CREATE TABLE `AttributeValue` (
  `object_id` int(10) unsigned default NULL,
  `attr_id` int(10) unsigned default NULL,
  `string_value` char(128) default NULL,
  `uint_value` int(10) unsigned default NULL,
  `float_value` float default NULL,
  UNIQUE KEY `object_id` (`object_id`,`attr_id`)
) ENGINE=MyISAM;

--
-- Table structure for table `Chapter`
--

DROP TABLE IF EXISTS `Chapter`;
CREATE TABLE `Chapter` (
  `chapter_no` int(10) unsigned NOT NULL auto_increment,
  `sticky` enum('yes','no') default 'no',
  `chapter_name` char(128) NOT NULL,
  PRIMARY KEY  (`chapter_no`),
  UNIQUE KEY `chapter_name` (`chapter_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10000;

--
-- Table structure for table `Dictionary`
--

DROP TABLE IF EXISTS `Dictionary`;
CREATE TABLE `Dictionary` (
  `chapter_no` int(10) unsigned NOT NULL,
  `dict_key` int(10) unsigned NOT NULL auto_increment,
  `dict_value` char(128) default NULL,
  PRIMARY KEY  (`dict_key`),
  UNIQUE KEY `chap_to_key` (`chapter_no`,`dict_key`),
  UNIQUE KEY `chap_to_val` (`chapter_no`,`dict_value`)
) ENGINE=MyISAM AUTO_INCREMENT=50000;

--
-- Table structure for table `IPAddress`
--

DROP TABLE IF EXISTS `IPAddress`;
CREATE TABLE `IPAddress` (
  `ip` int(10) unsigned NOT NULL,
  `name` char(255) NOT NULL,
  `reserved` enum('yes','no') default NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM;

--
-- Table structure for table `IPBonds`
--

DROP TABLE IF EXISTS `IPBonds`;
CREATE TABLE `IPBonds` (
  `object_id` int(11) NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `name` char(255) NOT NULL,
  `type` enum('regular','shared','virtual') default NULL,
  PRIMARY KEY  (`object_id`,`ip`)
) ENGINE=MyISAM;

--
-- Table structure for table `IPRanges`
--

DROP TABLE IF EXISTS `IPRanges`;
CREATE TABLE `IPRanges` (
  `id` int(11) NOT NULL auto_increment,
  `ip` int(10) unsigned NOT NULL,
  `mask` int(11) NOT NULL,
  `name` char(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `Link`
--

DROP TABLE IF EXISTS `Link`;
CREATE TABLE `Link` (
  `porta` int(11) NOT NULL,
  `portb` int(11) NOT NULL,
  PRIMARY KEY  (`porta`,`portb`),
  UNIQUE KEY `porta` (`porta`),
  UNIQUE KEY `portb` (`portb`)
) ENGINE=MyISAM;

--
-- Table structure for table `Molecule`
--

DROP TABLE IF EXISTS `Molecule`;
CREATE TABLE `Molecule` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `MountOperation`
--

DROP TABLE IF EXISTS `MountOperation`;
CREATE TABLE `MountOperation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL,
  `ctime` timestamp NOT NULL,
  `user_name` char(64) default NULL,
  `old_molecule_id` int(10) unsigned default NULL,
  `new_molecule_id` int(10) unsigned default NULL,
  `comment` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `Port`
--

DROP TABLE IF EXISTS `Port`;
CREATE TABLE `Port` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) NOT NULL,
  `name` char(255) NOT NULL,
  `type` int(11) NOT NULL,
  `l2address` char(64) default NULL,
  `reservation_comment` char(255) default NULL,
  `label` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `object_id` (`object_id`,`name`),
  UNIQUE KEY `l2address` (`l2address`)
) ENGINE=MyISAM;

--
-- Table structure for table `PortCompat`
--

DROP TABLE IF EXISTS `PortCompat`;
CREATE TABLE `PortCompat` (
  `type1` int(10) unsigned NOT NULL,
  `type2` int(10) unsigned NOT NULL
) ENGINE=MyISAM;

--
-- Table structure for table `PortForwarding`
--

DROP TABLE IF EXISTS `PortForwarding`;
CREATE TABLE `PortForwarding` (
  `object_id` int(11) NOT NULL,
  `proto` int(11) NOT NULL,
  `localip` int(10) unsigned NOT NULL,
  `localport` int(11) NOT NULL,
  `remoteip` int(10) unsigned NOT NULL,
  `remoteport` int(11) NOT NULL,
  `description` char(255) default NULL,
  PRIMARY KEY  (`object_id`,`proto`,`localip`,`localport`,`remoteip`,`remoteport`),
  KEY `localip` (`localip`),
  KEY `remoteip` (`remoteip`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM;

--
-- Table structure for table `Rack`
--

DROP TABLE IF EXISTS `Rack`;
CREATE TABLE `Rack` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `deleted` enum('yes','no') NOT NULL default 'no',
  `row_id` int(10) unsigned NOT NULL default '1',
  `height` int(10) unsigned NOT NULL default '42',
  `comment` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `RackHistory`
--

DROP TABLE IF EXISTS `RackHistory`;
CREATE TABLE `RackHistory` (
  `id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `deleted` enum('yes','no') default NULL,
  `row_id` int(10) unsigned default NULL,
  `height` int(10) unsigned default NULL,
  `comment` text,
  `ctime` timestamp NOT NULL,
  `user_name` char(64) default NULL
) ENGINE=MyISAM;

--
-- Table structure for table `RackObject`
--

DROP TABLE IF EXISTS `RackObject`;
CREATE TABLE `RackObject` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `barcode` char(16) default NULL,
  `deleted` enum('yes','no') NOT NULL default 'no',
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `RackObject_asset_no` (`asset_no`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=MyISAM;

--
-- Table structure for table `RackObjectHistory`
--

DROP TABLE IF EXISTS `RackObjectHistory`;
CREATE TABLE `RackObjectHistory` (
  `id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `barcode` char(16) default NULL,
  `deleted` enum('yes','no') default NULL,
  `objtype_id` int(10) unsigned default NULL,
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  `ctime` timestamp NOT NULL,
  `user_name` char(64) default NULL
) ENGINE=MyISAM;

--
-- Table structure for table `RackSpace`
--

DROP TABLE IF EXISTS `RackSpace`;
CREATE TABLE `RackSpace` (
  `rack_id` int(10) unsigned NOT NULL default '0',
  `unit_no` int(10) unsigned NOT NULL default '0',
  `atom` enum('front','interior','rear') NOT NULL default 'interior',
  `state` enum('A','U','T','W') NOT NULL default 'A',
  `object_id` int(10) unsigned default NULL,
  `problem_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`rack_id`,`unit_no`,`atom`)
) ENGINE=MyISAM;

--
-- Table structure for table `UserAccount`
--

DROP TABLE IF EXISTS `UserAccount`;
CREATE TABLE `UserAccount` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` char(64) NOT NULL,
  `user_enabled` enum('yes','no') NOT NULL default 'no',
  `user_password_hash` char(128) default NULL,
  `user_realname` char(64) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10000;

--
-- Table structure for table `UserPermission`
--

DROP TABLE IF EXISTS `UserPermission`;
CREATE TABLE `UserPermission` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `page` char(64) NOT NULL default '%',
  `tab` char(64) NOT NULL default '%',
  `access` enum('yes','no') NOT NULL default 'no',
  UNIQUE KEY `user_id` (`user_id`,`page`,`tab`)
) ENGINE=MyISAM;

--
-- Table structure for table `Config`
--

DROP TABLE IF EXISTS `Config`;
CREATE TABLE `Config` (
  `varname` char(32) NOT NULL,
  `varvalue` char(64) NOT NULL,
  `vartype` enum('string','uint') NOT NULL default 'string',
  `emptyok` enum('yes','no') NOT NULL default 'no',
  `is_hidden` enum('yes','no') NOT NULL default 'yes',
  `description` text,
  PRIMARY KEY  (`varname`)
) ENGINE=MyISAM;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
