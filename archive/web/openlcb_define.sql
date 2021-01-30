# Sequel Pro dump
# Version 2492
# http://code.google.com/p/sequel-pro
#
# Host: localhost (MySQL 5.1.52)
# Database: o286373_openlcb
# Generation Time: 2011-06-19 14:19:55 -0700
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table Person
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Person`;

CREATE TABLE `Person` (
  `person_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `person_first_name` varchar(50) DEFAULT NULL,
  `person_last_name` varchar(50) DEFAULT NULL,
  `person_email` varchar(100) NOT NULL,
  `person_organization` varchar(100) DEFAULT NULL,
  `person_request_IP_address` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `person_email` (`person_email`),
  KEY `person_first_name` (`person_first_name`),
  KEY `person_last_name` (`person_last_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;



# Dump of table UniqueIDs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `UniqueIDs`;

CREATE TABLE `UniqueIDs` (
  `uniqueid_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `person_id` int(11) DEFAULT NULL,
  `uniqueid_byte0_value` int(3) unsigned NOT NULL,
  `uniqueid_byte0_mask` int(3) unsigned NOT NULL,
  `uniqueid_byte1_value` int(3) unsigned NOT NULL,
  `uniqueid_byte1_mask` int(3) unsigned NOT NULL,
  `uniqueid_byte2_value` int(3) unsigned NOT NULL,
  `uniqueid_byte2_mask` int(3) unsigned NOT NULL,
  `uniqueid_byte3_value` int(3) unsigned NOT NULL,
  `uniqueid_byte3_mask` int(3) unsigned NOT NULL,
  `uniqueid_byte4_value` int(3) unsigned NOT NULL,
  `uniqueid_byte4_mask` int(3) unsigned NOT NULL,
  `uniqueid_byte5_value` int(3) unsigned NOT NULL,
  `uniqueid_byte5_mask` int(3) unsigned NOT NULL,
  `uniqueid_user_comment` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`uniqueid_id`),
  KEY `person_id` (`person_id`),
  KEY `uniqueid_byte0_value` (`uniqueid_byte0_value`),
  KEY `uniqueid_byte1_value` (`uniqueid_byte1_value`),
  KEY `uniqueid_byte2_value` (`uniqueid_byte2_value`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;






/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
