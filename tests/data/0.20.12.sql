
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
DROP TABLE IF EXISTS `Atom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Atom` (
  `molecule_id` int(10) unsigned DEFAULT NULL,
  `rack_id` int(10) unsigned DEFAULT NULL,
  `unit_no` int(10) unsigned DEFAULT NULL,
  `atom` enum('front','interior','rear') COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `Atom-FK-molecule_id` (`molecule_id`),
  KEY `Atom-FK-rack_id` (`rack_id`),
  CONSTRAINT `Atom-FK-molecule_id` FOREIGN KEY (`molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Atom-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Atom` WRITE;
/*!40000 ALTER TABLE `Atom` DISABLE KEYS */;
INSERT INTO `Atom` VALUES (1,967,35,'front'),(1,967,35,'interior');
/*!40000 ALTER TABLE `Atom` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('string','uint','float','dict','date') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Attribute` WRITE;
/*!40000 ALTER TABLE `Attribute` DISABLE KEYS */;
INSERT INTO `Attribute` VALUES (1,'string','OEM S/N 1'),(2,'dict','HW type'),(3,'string','FQDN'),(4,'dict','SW type'),(5,'string','SW version'),(6,'uint','number of ports'),(7,'float','max. current, Ampers'),(8,'float','power load, percents'),(13,'float','max power, Watts'),(14,'string','contact person'),(16,'uint','flash memory, MB'),(17,'uint','DRAM, MB'),(18,'uint','CPU, MHz'),(20,'string','OEM S/N 2'),(21,'date','support contract expiration'),(22,'date','HW warranty expiration'),(24,'date','SW warranty expiration'),(25,'string','UUID'),(26,'dict','Hypervisor'),(27,'uint','Height, units'),(28,'string','Slot number'),(29,'uint','Sort order'),(30,'dict','Mgmt type'),(9999,'string','base MAC address');
/*!40000 ALTER TABLE `Attribute` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `AttributeMap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AttributeMap` (
  `objtype_id` int(10) unsigned NOT NULL DEFAULT '1',
  `attr_id` int(10) unsigned NOT NULL DEFAULT '1',
  `chapter_id` int(10) unsigned DEFAULT NULL,
  `sticky` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  UNIQUE KEY `objtype_id` (`objtype_id`,`attr_id`),
  KEY `attr_id` (`attr_id`),
  KEY `chapter_id` (`chapter_id`),
  CONSTRAINT `AttributeMap-FK-attr_id` FOREIGN KEY (`attr_id`) REFERENCES `Attribute` (`id`),
  CONSTRAINT `AttributeMap-FK-chapter_id` FOREIGN KEY (`chapter_id`) REFERENCES `Chapter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `AttributeMap` WRITE;
/*!40000 ALTER TABLE `AttributeMap` DISABLE KEYS */;
INSERT INTO `AttributeMap` VALUES (2,1,NULL,'no'),(2,2,27,'no'),(2,3,NULL,'no'),(2,5,NULL,'no'),(4,1,NULL,'no'),(4,2,11,'no'),(4,3,NULL,'no'),(4,4,13,'no'),(4,14,NULL,'no'),(4,21,NULL,'no'),(4,22,NULL,'no'),(4,24,NULL,'no'),(4,25,NULL,'no'),(4,26,29,'yes'),(4,28,NULL,'yes'),(5,1,NULL,'no'),(5,2,18,'no'),(6,1,NULL,'no'),(6,2,19,'no'),(6,20,NULL,'no'),(7,1,NULL,'no'),(7,2,17,'no'),(7,3,NULL,'no'),(7,4,16,'no'),(7,5,NULL,'no'),(7,14,NULL,'no'),(7,16,NULL,'no'),(7,17,NULL,'no'),(7,18,NULL,'no'),(7,21,NULL,'no'),(7,22,NULL,'no'),(7,24,NULL,'no'),(8,1,NULL,'yes'),(8,2,12,'yes'),(8,3,NULL,'no'),(8,4,14,'yes'),(8,5,NULL,'no'),(8,14,NULL,'no'),(8,16,NULL,'no'),(8,17,NULL,'no'),(8,18,NULL,'no'),(8,20,NULL,'no'),(8,21,NULL,'no'),(8,22,NULL,'no'),(8,24,NULL,'no'),(8,28,NULL,'yes'),(9,6,NULL,'no'),(12,1,NULL,'no'),(12,3,NULL,'no'),(12,7,NULL,'no'),(12,8,NULL,'no'),(12,13,NULL,'no'),(12,20,NULL,'no'),(15,2,23,'no'),(445,1,NULL,'no'),(445,2,21,'no'),(445,3,NULL,'no'),(445,5,NULL,'no'),(445,14,NULL,'no'),(445,22,NULL,'no'),(447,1,NULL,'no'),(447,2,9999,'no'),(447,3,NULL,'no'),(447,5,NULL,'no'),(447,14,NULL,'no'),(447,22,NULL,'no'),(798,1,NULL,'no'),(798,2,24,'no'),(798,3,NULL,'no'),(798,5,NULL,'no'),(798,14,NULL,'no'),(798,16,NULL,'no'),(798,17,NULL,'no'),(798,18,NULL,'no'),(798,20,NULL,'no'),(798,21,NULL,'no'),(798,22,NULL,'no'),(798,24,NULL,'no'),(798,28,NULL,'yes'),(965,1,NULL,'no'),(965,2,25,'no'),(965,3,NULL,'no'),(965,4,37,'no'),(1055,2,26,'no'),(1055,28,NULL,'yes'),(1323,1,NULL,'no'),(1323,2,28,'no'),(1323,3,NULL,'no'),(1323,5,NULL,'no'),(1397,1,NULL,'no'),(1397,2,34,'no'),(1397,14,NULL,'no'),(1397,21,NULL,'no'),(1397,22,NULL,'no'),(1398,1,NULL,'no'),(1398,2,35,'no'),(1398,14,NULL,'no'),(1398,21,NULL,'no'),(1398,22,NULL,'no'),(1502,1,NULL,'no'),(1502,2,31,'no'),(1502,3,NULL,'no'),(1502,14,NULL,'no'),(1502,20,NULL,'no'),(1502,21,NULL,'no'),(1502,22,NULL,'no'),(1503,1,NULL,'no'),(1503,2,30,'no'),(1503,3,NULL,'no'),(1503,4,14,'no'),(1503,5,NULL,'no'),(1503,14,NULL,'no'),(1503,16,NULL,'no'),(1503,17,NULL,'no'),(1503,18,NULL,'no'),(1503,20,NULL,'no'),(1503,21,NULL,'no'),(1503,22,NULL,'no'),(1503,24,NULL,'no'),(1504,3,NULL,'no'),(1504,4,13,'no'),(1504,14,NULL,'no'),(1504,24,NULL,'no'),(1505,14,NULL,'no'),(1506,14,NULL,'no'),(1506,17,NULL,'no'),(1506,18,NULL,'no'),(1507,1,NULL,'no'),(1507,2,32,'no'),(1507,3,NULL,'no'),(1507,4,33,'no'),(1507,5,NULL,'no'),(1507,14,NULL,'no'),(1507,20,NULL,'no'),(1507,21,NULL,'no'),(1507,22,NULL,'no'),(1560,27,NULL,'yes'),(1560,29,NULL,'yes'),(1562,14,NULL,'no'),(1644,1,NULL,'no'),(1644,2,36,'no'),(1644,3,NULL,'no'),(1787,3,NULL,'no'),(1787,14,NULL,'no'),(1787,30,38,'yes');
/*!40000 ALTER TABLE `AttributeMap` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `AttributeValue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AttributeValue` (
  `object_id` int(10) unsigned NOT NULL,
  `object_tid` int(10) unsigned NOT NULL DEFAULT '0',
  `attr_id` int(10) unsigned NOT NULL,
  `string_value` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uint_value` int(10) unsigned DEFAULT NULL,
  `float_value` float DEFAULT NULL,
  PRIMARY KEY (`object_id`,`attr_id`),
  KEY `attr_id-uint_value` (`attr_id`,`uint_value`),
  KEY `attr_id-string_value` (`attr_id`,`string_value`(12)),
  KEY `id-tid` (`object_id`,`object_tid`),
  KEY `object_tid-attr_id` (`object_tid`,`attr_id`),
  CONSTRAINT `AttributeValue-FK-map` FOREIGN KEY (`object_tid`, `attr_id`) REFERENCES `AttributeMap` (`objtype_id`, `attr_id`),
  CONSTRAINT `AttributeValue-FK-object` FOREIGN KEY (`object_id`, `object_tid`) REFERENCES `Object` (`id`, `objtype_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `AttributeValue` WRITE;
/*!40000 ALTER TABLE `AttributeValue` DISABLE KEYS */;
INSERT INTO `AttributeValue` VALUES (905,7,2,NULL,269,NULL),(906,8,2,NULL,165,NULL),(907,7,2,NULL,269,NULL),(907,7,4,NULL,258,NULL),(910,4,2,NULL,57,NULL),(910,4,4,NULL,220,NULL),(911,4,2,NULL,57,NULL),(911,4,4,NULL,220,NULL),(912,4,2,NULL,57,NULL),(912,4,4,NULL,220,NULL),(913,4,2,NULL,57,NULL),(913,4,4,NULL,220,NULL),(914,4,2,NULL,57,NULL),(914,4,4,NULL,220,NULL),(915,8,2,NULL,118,NULL),(915,8,4,NULL,245,NULL),(916,5,2,NULL,320,NULL),(917,7,2,NULL,267,NULL),(918,4,2,NULL,88,NULL),(918,4,4,NULL,220,NULL),(919,4,2,NULL,88,NULL),(919,4,4,NULL,220,NULL),(920,6,2,NULL,326,NULL),(921,5,2,NULL,318,NULL),(922,5,2,NULL,318,NULL),(923,4,2,NULL,102,NULL),(923,4,4,NULL,233,NULL),(924,4,2,NULL,102,NULL),(924,4,4,NULL,233,NULL),(925,4,2,NULL,102,NULL),(925,4,4,NULL,233,NULL),(926,8,2,NULL,126,NULL),(926,8,4,NULL,244,NULL),(927,7,2,NULL,269,NULL),(927,7,4,NULL,258,NULL),(956,4,2,NULL,62,NULL),(956,4,4,NULL,791,NULL),(957,4,2,NULL,62,NULL),(957,4,4,NULL,791,NULL),(958,4,2,NULL,62,NULL),(958,4,4,NULL,791,NULL),(959,4,2,NULL,62,NULL),(959,4,4,NULL,791,NULL),(960,4,2,NULL,62,NULL),(960,4,4,NULL,791,NULL),(961,8,2,NULL,755,NULL),(962,445,2,NULL,470,NULL),(967,1560,27,NULL,42,NULL),(967,1560,29,NULL,1,NULL),(968,1560,27,NULL,12,NULL),(968,1560,29,NULL,2,NULL),(969,1560,27,NULL,42,NULL),(969,1560,29,NULL,3,NULL),(970,1560,27,NULL,16,NULL),(970,1560,29,NULL,1,NULL),(971,1560,27,NULL,42,NULL),(971,1560,29,NULL,2,NULL),(972,1560,27,NULL,42,NULL),(972,1560,29,NULL,1,NULL),(973,1560,27,NULL,42,NULL),(973,1560,29,NULL,3,NULL),(974,1560,27,NULL,16,NULL),(974,1560,29,NULL,1,NULL),(979,8,2,NULL,167,NULL),(979,8,4,NULL,252,NULL),(980,8,2,NULL,1337,NULL),(980,8,4,NULL,1369,NULL),(981,8,2,NULL,188,NULL),(981,8,4,NULL,252,NULL);
/*!40000 ALTER TABLE `AttributeValue` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `CachedPAV`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CachedPAV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `CachedPAV-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `CachedPAV-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `CachedPAV` WRITE;
/*!40000 ALTER TABLE `CachedPAV` DISABLE KEYS */;
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/1',1),(980,'gi0/0/10',1),(980,'gi0/0/11',1),(980,'gi0/0/12',1),(980,'gi0/0/13',1),(980,'gi0/0/14',1),(980,'gi0/0/15',1),(980,'gi0/0/16',1),(980,'gi0/0/17',1),(980,'gi0/0/18',1),(980,'gi0/0/19',1),(980,'gi0/0/2',1),(980,'gi0/0/20',1),(980,'gi0/0/21',1),(980,'gi0/0/22',1),(980,'gi0/0/23',1),(980,'gi0/0/24',1),(980,'gi0/0/25',1),(980,'gi0/0/26',1),(980,'gi0/0/27',1),(980,'gi0/0/28',1),(980,'gi0/0/29',1),(980,'gi0/0/3',1),(980,'gi0/0/30',1),(980,'gi0/0/31',1),(980,'gi0/0/32',1),(980,'gi0/0/33',1),(980,'gi0/0/34',1),(980,'gi0/0/35',1),(980,'gi0/0/36',1),(980,'gi0/0/37',1),(980,'gi0/0/38',1),(980,'gi0/0/39',1),(980,'gi0/0/40',1),(980,'gi0/0/41',1),(980,'gi0/0/42',1),(980,'gi0/0/43',1),(980,'gi0/0/44',1),(980,'gi0/0/45',1),(980,'gi0/0/46',1),(980,'gi0/0/47',1),(980,'gi0/0/6',1),(980,'gi0/0/7',1),(980,'gi0/0/8',1),(980,'gi0/0/9',1),(980,'gi0/1',1),(980,'gi0/10',1),(980,'gi0/11',1),(980,'gi0/12',1),(980,'gi0/13',1),(980,'gi0/14',1),(980,'gi0/15',1),(980,'gi0/16',1),(980,'gi0/17',1),(980,'gi0/18',1),(980,'gi0/19',1),(980,'gi0/2',1),(980,'gi0/20',1),(980,'gi0/21',1),(980,'gi0/22',1),(980,'gi0/23',1),(980,'gi0/24',1),(980,'gi0/25',1),(980,'gi0/26',1),(980,'gi0/27',1),(980,'gi0/28',1),(980,'gi0/29',1),(980,'gi0/3',1),(980,'gi0/30',1),(980,'gi0/31',1),(980,'gi0/32',1),(980,'gi0/33',1),(980,'gi0/34',1),(980,'gi0/35',1),(980,'gi0/36',1),(980,'gi0/37',1),(980,'gi0/38',1),(980,'gi0/39',1),(980,'gi0/4',1),(980,'gi0/40',1),(980,'gi0/41',1),(980,'gi0/42',1),(980,'gi0/43',1),(980,'gi0/44',1),(980,'gi0/45',1),(980,'gi0/46',1),(980,'gi0/47',1),(980,'gi0/48',1),(980,'gi0/5',1),(980,'gi0/6',1),(980,'gi0/7',1),(980,'gi0/8',1),(980,'gi0/9',1),(981,'gi0/10',1),(981,'gi0/11',1),(981,'gi0/12',1),(981,'gi0/2',1),(981,'gi0/4',1),(981,'gi0/5',1),(981,'gi0/6',1),(981,'gi0/7',1),(981,'gi0/8',1),(981,'gi0/9',1),(979,'gi0/13',3),(979,'gi0/24',3),(980,'gi0/0/4',3),(980,'gi0/0/48',3),(981,'gi0/1',3),(981,'gi0/3',3),(980,'gi0/0/48',5),(980,'gi0/0/5',5),(981,'gi0/3',5),(979,'gi0/1',7),(979,'gi0/2',7),(979,'gi0/24',7),(981,'gi0/1',7),(979,'gi0/1',8),(979,'gi0/2',8),(979,'gi0/24',8),(981,'gi0/1',8),(979,'gi0/1',9),(979,'gi0/10',9),(979,'gi0/11',9),(979,'gi0/12',9),(979,'gi0/14',9),(979,'gi0/15',9),(979,'gi0/16',9),(979,'gi0/17',9),(979,'gi0/18',9),(979,'gi0/19',9),(979,'gi0/2',9),(979,'gi0/20',9),(979,'gi0/21',9),(979,'gi0/22',9),(979,'gi0/23',9),(979,'gi0/24',9),(979,'gi0/3',9),(979,'gi0/4',9),(979,'gi0/5',9),(979,'gi0/6',9),(979,'gi0/7',9),(979,'gi0/8',9),(979,'gi0/9',9),(981,'gi0/1',9),(980,'gi0/0/1',11),(980,'gi0/0/48',11),(981,'gi0/3',11),(980,'gi0/0/1',12),(980,'gi0/0/48',12),(981,'gi0/3',12),(980,'gi0/0/1',13),(980,'gi0/0/48',13),(981,'gi0/3',13);
/*!40000 ALTER TABLE `CachedPAV` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `CachedPNV`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CachedPNV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `CachedPNV-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `CachedPAV` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `CachedPNV` WRITE;
/*!40000 ALTER TABLE `CachedPNV` DISABLE KEYS */;
INSERT INTO `CachedPNV` VALUES (979,'gi0/1',9),(979,'gi0/10',9),(979,'gi0/11',9),(979,'gi0/12',9),(979,'gi0/13',3),(979,'gi0/14',9),(979,'gi0/15',9),(979,'gi0/16',9),(979,'gi0/17',9),(979,'gi0/18',9),(979,'gi0/19',9),(979,'gi0/2',9),(979,'gi0/20',9),(979,'gi0/21',9),(979,'gi0/22',9),(979,'gi0/23',9),(979,'gi0/3',9),(979,'gi0/4',9),(979,'gi0/5',9),(979,'gi0/6',9),(979,'gi0/7',9),(979,'gi0/8',9),(979,'gi0/9',9),(980,'gi0/0/1',1),(980,'gi0/0/10',1),(980,'gi0/0/11',1),(980,'gi0/0/12',1),(980,'gi0/0/13',1),(980,'gi0/0/14',1),(980,'gi0/0/15',1),(980,'gi0/0/16',1),(980,'gi0/0/17',1),(980,'gi0/0/18',1),(980,'gi0/0/19',1),(980,'gi0/0/2',1),(980,'gi0/0/20',1),(980,'gi0/0/21',1),(980,'gi0/0/22',1),(980,'gi0/0/23',1),(980,'gi0/0/24',1),(980,'gi0/0/25',1),(980,'gi0/0/26',1),(980,'gi0/0/27',1),(980,'gi0/0/28',1),(980,'gi0/0/29',1),(980,'gi0/0/3',1),(980,'gi0/0/30',1),(980,'gi0/0/31',1),(980,'gi0/0/32',1),(980,'gi0/0/33',1),(980,'gi0/0/34',1),(980,'gi0/0/35',1),(980,'gi0/0/36',1),(980,'gi0/0/37',1),(980,'gi0/0/38',1),(980,'gi0/0/39',1),(980,'gi0/0/4',3),(980,'gi0/0/40',1),(980,'gi0/0/41',1),(980,'gi0/0/42',1),(980,'gi0/0/43',1),(980,'gi0/0/44',1),(980,'gi0/0/45',1),(980,'gi0/0/46',1),(980,'gi0/0/47',1),(980,'gi0/0/5',5),(980,'gi0/0/6',1),(980,'gi0/0/7',1),(980,'gi0/0/8',1),(980,'gi0/0/9',1),(980,'gi0/1',1),(980,'gi0/10',1),(980,'gi0/11',1),(980,'gi0/12',1),(980,'gi0/13',1),(980,'gi0/14',1),(980,'gi0/15',1),(980,'gi0/16',1),(980,'gi0/17',1),(980,'gi0/18',1),(980,'gi0/19',1),(980,'gi0/2',1),(980,'gi0/20',1),(980,'gi0/21',1),(980,'gi0/22',1),(980,'gi0/23',1),(980,'gi0/24',1),(980,'gi0/25',1),(980,'gi0/26',1),(980,'gi0/27',1),(980,'gi0/28',1),(980,'gi0/29',1),(980,'gi0/3',1),(980,'gi0/30',1),(980,'gi0/31',1),(980,'gi0/32',1),(980,'gi0/33',1),(980,'gi0/34',1),(980,'gi0/35',1),(980,'gi0/36',1),(980,'gi0/37',1),(980,'gi0/38',1),(980,'gi0/39',1),(980,'gi0/4',1),(980,'gi0/40',1),(980,'gi0/41',1),(980,'gi0/42',1),(980,'gi0/43',1),(980,'gi0/44',1),(980,'gi0/45',1),(980,'gi0/46',1),(980,'gi0/47',1),(980,'gi0/48',1),(980,'gi0/5',1),(980,'gi0/6',1),(980,'gi0/7',1),(980,'gi0/8',1),(980,'gi0/9',1),(981,'gi0/10',1),(981,'gi0/11',1),(981,'gi0/12',1),(981,'gi0/2',1),(981,'gi0/4',1),(981,'gi0/5',1),(981,'gi0/6',1),(981,'gi0/7',1),(981,'gi0/8',1),(981,'gi0/9',1);
/*!40000 ALTER TABLE `CachedPNV` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `CachedPVM`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CachedPVM` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `vlan_mode` enum('access','trunk') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'access',
  PRIMARY KEY (`object_id`,`port_name`),
  CONSTRAINT `CachedPVM-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `CachedPVM` WRITE;
/*!40000 ALTER TABLE `CachedPVM` DISABLE KEYS */;
INSERT INTO `CachedPVM` VALUES (979,'gi0/1','trunk'),(979,'gi0/10','access'),(979,'gi0/11','access'),(979,'gi0/12','access'),(979,'gi0/13','access'),(979,'gi0/14','access'),(979,'gi0/15','access'),(979,'gi0/16','access'),(979,'gi0/17','access'),(979,'gi0/18','access'),(979,'gi0/19','access'),(979,'gi0/2','trunk'),(979,'gi0/20','access'),(979,'gi0/21','access'),(979,'gi0/22','access'),(979,'gi0/23','access'),(979,'gi0/24','trunk'),(979,'gi0/3','access'),(979,'gi0/4','access'),(979,'gi0/5','access'),(979,'gi0/6','access'),(979,'gi0/7','access'),(979,'gi0/8','access'),(979,'gi0/9','access'),(980,'gi0/0/1','trunk'),(980,'gi0/0/10','access'),(980,'gi0/0/11','access'),(980,'gi0/0/12','access'),(980,'gi0/0/13','access'),(980,'gi0/0/14','access'),(980,'gi0/0/15','access'),(980,'gi0/0/16','access'),(980,'gi0/0/17','access'),(980,'gi0/0/18','access'),(980,'gi0/0/19','access'),(980,'gi0/0/2','access'),(980,'gi0/0/20','access'),(980,'gi0/0/21','access'),(980,'gi0/0/22','access'),(980,'gi0/0/23','access'),(980,'gi0/0/24','access'),(980,'gi0/0/25','access'),(980,'gi0/0/26','access'),(980,'gi0/0/27','access'),(980,'gi0/0/28','access'),(980,'gi0/0/29','access'),(980,'gi0/0/3','access'),(980,'gi0/0/30','access'),(980,'gi0/0/31','access'),(980,'gi0/0/32','access'),(980,'gi0/0/33','access'),(980,'gi0/0/34','access'),(980,'gi0/0/35','access'),(980,'gi0/0/36','access'),(980,'gi0/0/37','access'),(980,'gi0/0/38','access'),(980,'gi0/0/39','access'),(980,'gi0/0/4','access'),(980,'gi0/0/40','access'),(980,'gi0/0/41','access'),(980,'gi0/0/42','access'),(980,'gi0/0/43','access'),(980,'gi0/0/44','access'),(980,'gi0/0/45','access'),(980,'gi0/0/46','access'),(980,'gi0/0/47','access'),(980,'gi0/0/48','trunk'),(980,'gi0/0/5','access'),(980,'gi0/0/6','access'),(980,'gi0/0/7','access'),(980,'gi0/0/8','access'),(980,'gi0/0/9','access'),(980,'gi0/1','access'),(980,'gi0/10','access'),(980,'gi0/11','access'),(980,'gi0/12','access'),(980,'gi0/13','access'),(980,'gi0/14','access'),(980,'gi0/15','access'),(980,'gi0/16','access'),(980,'gi0/17','access'),(980,'gi0/18','access'),(980,'gi0/19','access'),(980,'gi0/2','access'),(980,'gi0/20','access'),(980,'gi0/21','access'),(980,'gi0/22','access'),(980,'gi0/23','access'),(980,'gi0/24','access'),(980,'gi0/25','access'),(980,'gi0/26','access'),(980,'gi0/27','access'),(980,'gi0/28','access'),(980,'gi0/29','access'),(980,'gi0/3','access'),(980,'gi0/30','access'),(980,'gi0/31','access'),(980,'gi0/32','access'),(980,'gi0/33','access'),(980,'gi0/34','access'),(980,'gi0/35','access'),(980,'gi0/36','access'),(980,'gi0/37','access'),(980,'gi0/38','access'),(980,'gi0/39','access'),(980,'gi0/4','access'),(980,'gi0/40','access'),(980,'gi0/41','access'),(980,'gi0/42','access'),(980,'gi0/43','access'),(980,'gi0/44','access'),(980,'gi0/45','access'),(980,'gi0/46','access'),(980,'gi0/47','access'),(980,'gi0/48','access'),(980,'gi0/5','access'),(980,'gi0/6','access'),(980,'gi0/7','access'),(980,'gi0/8','access'),(980,'gi0/9','access'),(981,'gi0/1','trunk'),(981,'gi0/10','trunk'),(981,'gi0/11','trunk'),(981,'gi0/12','trunk'),(981,'gi0/2','trunk'),(981,'gi0/3','trunk'),(981,'gi0/4','trunk'),(981,'gi0/5','trunk'),(981,'gi0/6','trunk'),(981,'gi0/7','trunk'),(981,'gi0/8','trunk'),(981,'gi0/9','trunk');
/*!40000 ALTER TABLE `CachedPVM` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `CactiGraph`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CactiGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph_id` int(10) unsigned NOT NULL,
  `caption` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph_id`),
  KEY `graph_id` (`graph_id`),
  KEY `server_id` (`server_id`),
  CONSTRAINT `CactiGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `CactiGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `CactiServer` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `CactiGraph` WRITE;
/*!40000 ALTER TABLE `CactiGraph` DISABLE KEYS */;
/*!40000 ALTER TABLE `CactiGraph` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `CactiServer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CactiServer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `base_url` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `CactiServer` WRITE;
/*!40000 ALTER TABLE `CactiServer` DISABLE KEYS */;
/*!40000 ALTER TABLE `CactiServer` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Chapter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Chapter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sticky` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `name` char(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Chapter` WRITE;
/*!40000 ALTER TABLE `Chapter` DISABLE KEYS */;
INSERT INTO `Chapter` VALUES (1,'yes','ObjectType'),(11,'no','server models'),(12,'no','network switch models'),(13,'no','server OS type'),(14,'no','switch OS type'),(16,'no','router OS type'),(17,'no','router models'),(18,'no','disk array models'),(19,'no','tape library models'),(21,'no','KVM switch models'),(23,'no','console models'),(24,'no','network security models'),(25,'no','wireless models'),(26,'no','fibre channel switch models'),(27,'no','PDU models'),(28,'no','Voice/video hardware'),(29,'no','Yes/No'),(30,'no','network chassis models'),(31,'no','server chassis models'),(32,'no','virtual switch models'),(33,'no','virtual switch OS type'),(34,'no','power supply chassis models'),(35,'no','power supply models'),(36,'no','serial console server models'),(37,'no','wireless OS type'),(38,'no','management interface type'),(9999,'no','multiplexer models');
/*!40000 ALTER TABLE `Chapter` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Config` (
  `varname` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `varvalue` text COLLATE utf8_unicode_ci NOT NULL,
  `vartype` enum('string','uint') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'string',
  `emptyok` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `is_hidden` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `is_userdefined` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`varname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Config` WRITE;
/*!40000 ALTER TABLE `Config` DISABLE KEYS */;
INSERT INTO `Config` VALUES ('8021Q_DEPLOY_MAXAGE','3600','uint','no','no','no','802.1Q deploy maximum age'),('8021Q_DEPLOY_MINAGE','300','uint','no','no','no','802.1Q deploy minimum age'),('8021Q_DEPLOY_RETRY','10800','uint','no','no','no','802.1Q deploy retry timer'),('8021Q_EXTSYNC_LISTSRC','false','string','yes','no','no','List source: objects with extended 802.1Q sync'),('8021Q_INSTANT_DEPLOY','no','string','no','no','yes','802.1Q: instant deploy'),('8021Q_MULTILINK_LISTSRC','false','string','yes','no','no','List source: IPv4/IPv6 networks allowing multiple VLANs from same domain'),('8021Q_WRI_AFTER_CONFT_LISTSRC','false','string','no','no','no','802.1Q: save device configuration after deploy (RackCode)'),('ADDNEW_AT_TOP','yes','string','no','no','yes','Render \"add new\" line at top of the list'),('ASSETWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}','string','yes','no','no','List source: objects for that asset tag should be set'),('AUTOPORTS_CONFIG','4 = 1*33*kvm + 2*24*eth%u;15 = 1*446*kvm','string','yes','no','no','AutoPorts configuration'),('CACTI_LISTSRC','false','string','yes','no','no','List of object with Cacti graphs'),('CACTI_RRA_ID','1','uint','no','no','yes','RRA ID for Cacti graphs displayed in RackTables'),('CDP_RUNNERS_LISTSRC','','string','yes','no','no','List of devices running CDP'),('DATETIME_FORMAT','%Y-%m-%d','string','no','no','yes','PHP strftime() format to use for date output'),('DATETIME_ZONE','UTC','string','yes','no','yes','Timezone to use for displaying/calculating dates'),('DB_VERSION','0.20.12','string','no','yes','no','Database version.'),('DEFAULT_IPV4_RS_INSERVICE','no','string','no','no','yes','Inservice status for new SLB real servers'),('DEFAULT_OBJECT_TYPE','4','uint','yes','no','yes','Default object type for new objects'),('DEFAULT_PORT_IIF_ID','1','uint','no','no','no','Default port inner interface ID'),('DEFAULT_PORT_OIF_IDS','1=24; 3=1078; 4=1077; 5=1079; 6=1080; 8=1082; 9=1084; 10=1588; 11=1668; 12=1589; 13=1590; 14=1591; 15=1588','string','no','no','no','Default port outer interface IDs'),('DEFAULT_RACK_HEIGHT','42','uint','yes','no','yes','Default rack height'),('DEFAULT_SLB_RS_PORT','','uint','yes','no','yes','Default port of SLB real server'),('DEFAULT_SLB_VS_PORT','','uint','yes','no','yes','Default port of SLB virtual service'),('DEFAULT_SNMP_COMMUNITY','public','string','no','no','no','Default SNMP Community string'),('DEFAULT_VDOM_ID','','uint','yes','no','yes','Default VLAN domain ID'),('DEFAULT_VST_ID','','uint','yes','no','yes','Default VLAN switch template ID'),('DETECT_URLS','no','string','yes','no','yes','Detect URLs in text fields'),('ENABLE_BULKPORT_FORM','yes','string','no','no','yes','Enable \"Bulk Port\" form'),('ENABLE_MULTIPORT_FORM','no','string','no','no','yes','Enable \"Add/update multiple ports\" form'),('enterprise','MyCompanyName','string','no','no','no','Organization name'),('EXT_IPV4_VIEW','yes','string','no','no','yes','Extended IPv4 view'),('FILTER_DEFAULT_ANDOR','and','string','no','no','yes','Default list filter boolean operation (or/and)'),('FILTER_PREDICATE_SIEVE','','string','yes','no','yes','Predicate sieve regex(7)'),('FILTER_RACKLIST_BY_TAGS','yes','string','yes','no','yes','Rackspace: show only racks matching the current object\'s tags'),('FILTER_SUGGEST_ANDOR','yes','string','no','no','yes','Suggest and/or selector in list filter'),('FILTER_SUGGEST_EXTRA','yes','string','no','no','yes','Suggest extra expression in list filter'),('FILTER_SUGGEST_PREDICATES','yes','string','no','no','yes','Suggest predicates in list filter'),('FILTER_SUGGEST_TAGS','yes','string','no','no','yes','Suggest tags in list filter'),('IPV4_ADDRS_PER_PAGE','256','uint','no','no','yes','IPv4 addresses per page'),('IPV4_AUTO_RELEASE','1','uint','no','no','yes','Auto-release IPv4 addresses on allocation'),('IPV4_ENABLE_KNIGHT','yes','string','no','no','yes','Enable IPv4 knight feature'),('IPV4_JAYWALK','no','string','no','no','no','Enable IPv4 address allocations w/o covering network'),('IPV4_TREE_RTR_AS_CELL','no','string','no','no','yes','Show full router info for each network in IPv4 tree view'),('IPV4_TREE_SHOW_UNALLOCATED','yes','string','no','no','yes','Show unallocated networks in IPv4 tree'),('IPV4_TREE_SHOW_USAGE','no','string','no','no','yes','Show address usage in IPv4 tree'),('IPV4_TREE_SHOW_VLAN','yes','string','no','no','yes','Show VLAN for each network in IPv4 tree'),('IPV4LB_LISTSRC','{load balancer}','string','yes','no','no','List source: IPv4 load balancers'),('IPV4NAT_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8} or {$typeid_798}','string','yes','no','no','List source: IPv4 NAT performers'),('IPV4OBJ_LISTSRC','not ({$typeid_3} or {$typeid_9} or {$typeid_10} or {$typeid_11})','string','yes','no','no','List source: IPv4-enabled objects'),('LLDP_RUNNERS_LISTSRC','','string','yes','no','no','List of devices running LLDP'),('MASSCOUNT','8','uint','no','no','yes','&quot;Fast&quot; form is this many records tall'),('MAX_UNFILTERED_ENTITIES','0','uint','no','no','yes','Max item count to display on unfiltered result page'),('MAXSELSIZE','30','uint','no','no','yes','&lt;SELECT&gt; lists height'),('MGMT_PROTOS','ssh: {$typeid_4}; telnet: {$typeid_8}','string','yes','no','yes','Mapping of management protocol to devices'),('MUNIN_LISTSRC','false','string','yes','no','no','List of object with Munin graphs'),('NAMEWARN_LISTSRC','{$typeid_4} or {$typeid_7} or {$typeid_8}','string','yes','no','no','List source: objects for that common name should be set'),('NEAREST_RACKS_CHECKBOX','yes','string','yes','no','yes','Enable nearest racks in port list filter by default'),('PORT_EXCLUSION_LISTSRC','{$typeid_3} or {$typeid_10} or {$typeid_11} or {$typeid_1505} or {$typeid_1506}','string','yes','no','no','List source: objects without ports'),('PREVIEW_IMAGE_MAXPXS','320','uint','yes','no','yes','Max pixels per axis for image file preview'),('PREVIEW_TEXT_COLS','80','uint','yes','no','yes','Columns for text file preview'),('PREVIEW_TEXT_MAXCHARS','10240','uint','yes','no','yes','Max chars for text file preview'),('PREVIEW_TEXT_ROWS','25','uint','yes','no','yes','Rows for text file preview'),('PROXIMITY_RANGE','0','uint','yes','no','yes','Proximity range (0 is current rack only)'),('QUICK_LINK_PAGES','depot,ipv4space,rackspace','string','yes','no','yes','List of pages to display in quick links'),('RACK_PRESELECT_THRESHOLD','1','uint','no','no','yes','Rack pre-selection threshold'),('RACKS_PER_ROW','12','uint','yes','no','yes','Racks per row'),('REVERSED_RACKS_LISTSRC','false','string','yes','no','no','List of racks with reversed (top to bottom) units order'),('ROW_SCALE','2','uint','no','no','yes','Picture scale for rack row display'),('SEARCH_DOMAINS','','string','yes','no','yes','DNS domain list (comma-separated) to search in FQDN attributes'),('SHOW_AUTOMATIC_TAGS','no','string','no','no','yes','Show automatic tags'),('SHOW_EXPLICIT_TAGS','yes','string','no','no','yes','Show explicit tags'),('SHOW_IMPLICIT_TAGS','yes','string','no','no','yes','Show implicit tags'),('SHOW_LAST_TAB','yes','string','yes','no','yes','Remember last tab shown for each page'),('SHOW_OBJECTTYPE','yes','string','no','no','yes','Show object type column on depot page'),('SHRINK_TAG_TREE_ON_CLICK','yes','string','no','no','yes','Dynamically hide useless tags in tagtree'),('STATIC_FILTER','yes','string','no','no','yes','Enable Filter Caching'),('SYNC_8021Q_LISTSRC','','string','yes','no','no','List of VLAN switches sync is enabled on'),('SYNCDOMAIN_MAX_PROCESSES','0','uint','yes','no','no','How many worker proceses syncdomain cron script should create'),('TAGS_QUICKLIST_SIZE','20','uint','no','no','yes','Tags quick list size'),('TAGS_QUICKLIST_THRESHOLD','50','uint','yes','no','yes','Tags quick list threshold'),('TAGS_TOPLIST_SIZE','50','uint','yes','no','yes','Tags top list size'),('TREE_THRESHOLD','25','uint','yes','no','yes','Tree view auto-collapse threshold'),('VENDOR_SIEVE','','string','yes','no','yes','Vendor sieve configuration'),('VIRTUAL_OBJ_LISTSRC','1504,1505,1506,1507','string','no','no','no','List source: virtual objects'),('VLANIPV4NET_LISTSRC','','string','yes','no','yes','List of VLAN-based IPv4 networks'),('VLANSWITCH_LISTSRC','','string','yes','no','yes','List of VLAN running switches');
/*!40000 ALTER TABLE `Config` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Dictionary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Dictionary` (
  `chapter_id` int(10) unsigned NOT NULL,
  `dict_key` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dict_sticky` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `dict_value` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`dict_key`),
  UNIQUE KEY `dict_unique` (`chapter_id`,`dict_value`,`dict_sticky`),
  CONSTRAINT `Dictionary-FK-chapter_id` FOREIGN KEY (`chapter_id`) REFERENCES `Chapter` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Dictionary` WRITE;
/*!40000 ALTER TABLE `Dictionary` DISABLE KEYS */;
INSERT INTO `Dictionary` VALUES (1,1,'yes','BlackBox'),(1,10,'yes','CableOrganizer'),(1,15,'yes','console'),(1,5,'yes','DiskArray'),(1,1055,'yes','FC switch'),(1,445,'yes','KVM switch'),(1,1562,'yes','Location'),(1,1787,'yes','Management interface'),(1,14,'yes','MediaConverter'),(1,13,'yes','Modem'),(1,447,'yes','multiplexer'),(1,1503,'yes','Network chassis'),(1,798,'yes','Network security'),(1,8,'yes','Network switch'),(1,9,'yes','PatchPanel'),(1,2,'yes','PDU'),(1,1398,'yes','Power supply'),(1,1397,'yes','Power supply chassis'),(1,1560,'yes','Rack'),(1,7,'yes','Router'),(1,1561,'yes','Row'),(1,1644,'yes','serial console server'),(1,4,'yes','Server'),(1,1502,'yes','Server chassis'),(1,3,'yes','Shelf'),(1,11,'yes','spacer'),(1,6,'yes','TapeLibrary'),(1,12,'yes','UPS'),(1,1504,'yes','VM'),(1,1505,'yes','VM Cluster'),(1,1506,'yes','VM Resource Pool'),(1,1507,'yes','VM Virtual Switch'),(1,1323,'yes','Voice/video'),(1,965,'yes','Wireless'),(11,1814,'yes','[[Fujitsu%GSKIP%PRIMERGY RX100 S7 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx100/index.html]]'),(11,1815,'yes','[[Fujitsu%GSKIP%PRIMERGY RX200 S7 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx200/index.html]]'),(11,1816,'yes','[[Fujitsu%GSKIP%PRIMERGY RX300 S6 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx300/index.html]]'),(11,1817,'yes','[[Fujitsu%GSKIP%PRIMERGY RX300 S7 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx300/index.html]]'),(11,1818,'yes','[[Fujitsu%GSKIP%PRIMERGY RX350 S7 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx350/index.html]]'),(11,1819,'yes','[[Fujitsu%GSKIP%PRIMERGY RX500 S7 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx500/index.html]]'),(11,1820,'yes','[[Fujitsu%GSKIP%PRIMERGY RX600 S6 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx600/index.html]]'),(11,1821,'yes','[[Fujitsu%GSKIP%PRIMERGY RX900 S2 | http://www.fujitsu.com/fts/products/computing/servers/primergy/rack/rx900/index.html]]'),(11,1838,'yes','[[IBM xSeries%GPASS%x3250 M3 | http://www-03.ibm.com/systems/x/hardware/rack/x3250m3/index.html]]'),(11,1832,'yes','[[IBM xSeries%GPASS%x3250 M4 | http://www-03.ibm.com/systems/x/hardware/rack/x3250m4/index.html]]'),(11,1831,'yes','[[IBM xSeries%GPASS%x3530 M4 | http://www-03.ibm.com/systems/x/hardware/rack/x3530m4/index.html]]'),(11,1837,'yes','[[IBM xSeries%GPASS%x3550 M3 | http://www-03.ibm.com/systems/x/hardware/rack/x3550m3/index.html]]'),(11,1830,'yes','[[IBM xSeries%GPASS%x3550 M4 | http://www-03.ibm.com/systems/x/hardware/rack/x3550m4/index.html]]'),(11,1836,'yes','[[IBM xSeries%GPASS%x3620 M3 | http://www-03.ibm.com/systems/x/hardware/rack/x3620m3/index.html]]'),(11,1835,'yes','[[IBM xSeries%GPASS%x3630 M3 | http://www-03.ibm.com/systems/x/hardware/rack/x3630m3/index.html]]'),(11,1829,'yes','[[IBM xSeries%GPASS%x3630 M4 | http://www-03.ibm.com/systems/x/hardware/rack/x3630m4/index.html]]'),(11,1834,'yes','[[IBM xSeries%GPASS%x3650 M3 | http://www-03.ibm.com/systems/x/hardware/rack/x3650m3/index.html]]'),(11,1828,'yes','[[IBM xSeries%GPASS%x3650 M4 | http://www-03.ibm.com/systems/x/hardware/rack/x3650m4/index.html]]'),(11,1824,'yes','[[IBM xSeries%GPASS%x3690 X5 | http://www-03.ibm.com/systems/x/hardware/enterprise/x3690x5/index.html]]'),(11,1827,'yes','[[IBM xSeries%GPASS%x3750 M4 | http://www-03.ibm.com/systems/x/hardware/rack/x3750m4/index.html]]'),(11,1833,'yes','[[IBM xSeries%GPASS%x3755 M3 | http://www-03.ibm.com/systems/x/hardware/rack/x3755m3/index.html]]'),(11,1825,'yes','[[IBM xSeries%GPASS%x3850 X5 | http://www-03.ibm.com/systems/x/hardware/enterprise/x3850x5/index.html]]'),(11,1826,'yes','[[IBM xSeries%GPASS%x3950 X5 | http://www-03.ibm.com/systems/x/hardware/enterprise/x3850x5/index.html]]'),(11,1736,'yes','Cisco%GPASS%UCS B200 M1'),(11,1737,'yes','Cisco%GPASS%UCS B200 M2'),(11,1738,'yes','Cisco%GPASS%UCS B200 M3'),(11,2225,'yes','Cisco%GPASS%UCS B200 M4'),(11,1745,'yes','Cisco%GPASS%UCS B22  M3'),(11,1739,'yes','Cisco%GPASS%UCS B230 M1'),(11,1740,'yes','Cisco%GPASS%UCS B230 M2'),(11,1741,'yes','Cisco%GPASS%UCS B250 M1'),(11,1742,'yes','Cisco%GPASS%UCS B250 M2'),(11,2223,'yes','Cisco%GPASS%UCS B260 M4'),(11,1744,'yes','Cisco%GPASS%UCS B420 M3'),(11,2558,'yes','Cisco%GPASS%UCS B420 M4'),(11,2559,'yes','Cisco%GPASS%UCS B440 M1'),(11,1743,'yes','Cisco%GPASS%UCS B440 M2'),(11,2224,'yes','Cisco%GPASS%UCS B460 M4'),(11,1746,'yes','Cisco%GPASS%UCS C200 M2'),(11,1747,'yes','Cisco%GPASS%UCS C210 M2'),(11,1751,'yes','Cisco%GPASS%UCS C22  M3'),(11,2562,'yes','Cisco%GPASS%UCS C22  M3(LFF)'),(11,1752,'yes','Cisco%GPASS%UCS C220 M3'),(11,2563,'yes','Cisco%GPASS%UCS C220 M3(LFF)'),(11,2566,'yes','Cisco%GPASS%UCS C220 M4'),(11,2567,'yes','Cisco%GPASS%UCS C220 M4(LFF)'),(11,1753,'yes','Cisco%GPASS%UCS C24  M3'),(11,2564,'yes','Cisco%GPASS%UCS C24  M3(LFF)'),(11,1754,'yes','Cisco%GPASS%UCS C240 M3'),(11,2565,'yes','Cisco%GPASS%UCS C240 M3(16 drive)'),(11,2579,'yes','Cisco%GPASS%UCS C240 M3(LFF)'),(11,2568,'yes','Cisco%GPASS%UCS C240 M4'),(11,2569,'yes','Cisco%GPASS%UCS C240 M4(16 drive)'),(11,2570,'yes','Cisco%GPASS%UCS C240 M4(LFF)'),(11,2571,'yes','Cisco%GPASS%UCS C240 M4S'),(11,1748,'yes','Cisco%GPASS%UCS C250 M2'),(11,1749,'yes','Cisco%GPASS%UCS C260 M2'),(11,2573,'yes','Cisco%GPASS%UCS C420 M3'),(11,1750,'yes','Cisco%GPASS%UCS C460 M2'),(11,2575,'yes','Cisco%GPASS%UCS C460 M4'),(11,1518,'yes','Dell PowerEdge (blade)%GPASS%1955'),(11,2031,'yes','Dell PowerEdge (blade)%GPASS%M520'),(11,1519,'yes','Dell PowerEdge (blade)%GPASS%M605'),(11,1520,'yes','Dell PowerEdge (blade)%GPASS%M610'),(11,1521,'yes','Dell PowerEdge (blade)%GPASS%M610x'),(11,1696,'yes','Dell PowerEdge (blade)%GPASS%M620'),(11,2385,'yes','Dell PowerEdge (blade)%GPASS%M630'),(11,1522,'yes','Dell PowerEdge (blade)%GPASS%M710%L2,1%'),(11,1697,'yes','Dell PowerEdge (blade)%GPASS%M710HD'),(11,1523,'yes','Dell PowerEdge (blade)%GPASS%M805%L2,1%'),(11,2032,'yes','Dell PowerEdge (blade)%GPASS%M820%L2,1%'),(11,1524,'yes','Dell PowerEdge (blade)%GPASS%M905%L2,1%'),(11,1525,'yes','Dell PowerEdge (blade)%GPASS%M910%L2,1%'),(11,1698,'yes','Dell PowerEdge (blade)%GPASS%M915%L2,1%'),(11,1676,'yes','Dell PowerEdge C%GPASS%C1100'),(11,1677,'yes','Dell PowerEdge C%GPASS%C2100'),(11,1678,'yes','Dell PowerEdge C%GPASS%C5125'),(11,1679,'yes','Dell PowerEdge C%GPASS%C5220'),(11,1680,'yes','Dell PowerEdge C%GPASS%C6100'),(11,1681,'yes','Dell PowerEdge C%GPASS%C6105'),(11,1682,'yes','Dell PowerEdge C%GPASS%C6145'),(11,1683,'yes','Dell PowerEdge C%GPASS%C6220'),(11,63,'yes','Dell PowerEdge%GPASS%1550'),(11,55,'yes','Dell PowerEdge%GPASS%1650'),(11,364,'yes','Dell PowerEdge%GPASS%1850'),(11,62,'yes','Dell PowerEdge%GPASS%1950'),(11,362,'yes','Dell PowerEdge%GPASS%2450'),(11,360,'yes','Dell PowerEdge%GPASS%2550'),(11,359,'yes','Dell PowerEdge%GPASS%2650'),(11,56,'yes','Dell PowerEdge%GPASS%2850'),(11,366,'yes','Dell PowerEdge%GPASS%2900'),(11,65,'yes','Dell PowerEdge%GPASS%2950'),(11,367,'yes','Dell PowerEdge%GPASS%2970'),(11,358,'yes','Dell PowerEdge%GPASS%4400'),(11,67,'yes','Dell PowerEdge%GPASS%4600'),(11,66,'yes','Dell PowerEdge%GPASS%650'),(11,355,'yes','Dell PowerEdge%GPASS%6850'),(11,356,'yes','Dell PowerEdge%GPASS%6950'),(11,361,'yes','Dell PowerEdge%GPASS%750'),(11,363,'yes','Dell PowerEdge%GPASS%850'),(11,365,'yes','Dell PowerEdge%GPASS%860'),(11,1059,'yes','Dell PowerEdge%GPASS%R200'),(11,1686,'yes','Dell PowerEdge%GPASS%R210'),(11,1712,'yes','Dell PowerEdge%GPASS%R210 II'),(11,2435,'yes','Dell PowerEdge%GPASS%R220'),(11,1060,'yes','Dell PowerEdge%GPASS%R300'),(11,1687,'yes','Dell PowerEdge%GPASS%R310'),(11,989,'yes','Dell PowerEdge%GPASS%R410'),(11,1688,'yes','Dell PowerEdge%GPASS%R415'),(11,2147,'yes','Dell PowerEdge%GPASS%R420'),(11,2386,'yes','Dell PowerEdge%GPASS%R430'),(11,1672,'yes','Dell PowerEdge%GPASS%R510'),(11,1689,'yes','Dell PowerEdge%GPASS%R515'),(11,2387,'yes','Dell PowerEdge%GPASS%R530'),(11,990,'yes','Dell PowerEdge%GPASS%R610'),(11,1690,'yes','Dell PowerEdge%GPASS%R620'),(11,2388,'yes','Dell PowerEdge%GPASS%R630'),(11,991,'yes','Dell PowerEdge%GPASS%R710'),(11,1691,'yes','Dell PowerEdge%GPASS%R715'),(11,1692,'yes','Dell PowerEdge%GPASS%R720'),(11,1693,'yes','Dell PowerEdge%GPASS%R720xd'),(11,2389,'yes','Dell PowerEdge%GPASS%R730'),(11,2390,'yes','Dell PowerEdge%GPASS%R730xd'),(11,992,'yes','Dell PowerEdge%GPASS%R805'),(11,1694,'yes','Dell PowerEdge%GPASS%R810'),(11,1695,'yes','Dell PowerEdge%GPASS%R815'),(11,357,'yes','Dell PowerEdge%GPASS%R900'),(11,993,'yes','Dell PowerEdge%GPASS%R905'),(11,1381,'yes','Dell PowerEdge%GPASS%R910'),(11,368,'yes','Dell PowerEdge%GPASS%SC1435'),(11,2384,'yes','HP ProLiant%GPASS%DL120'),(11,101,'yes','HP ProLiant%GPASS%DL140'),(11,102,'yes','HP ProLiant%GPASS%DL145'),(11,534,'yes','HP ProLiant%GPASS%DL160'),(11,535,'yes','HP ProLiant%GPASS%DL180'),(11,536,'yes','HP ProLiant%GPASS%DL185'),(11,103,'yes','HP ProLiant%GPASS%DL320'),(11,539,'yes','HP ProLiant%GPASS%DL320p'),(11,538,'yes','HP ProLiant%GPASS%DL320s'),(11,104,'yes','HP ProLiant%GPASS%DL360'),(11,537,'yes','HP ProLiant%GPASS%DL365'),(11,105,'yes','HP ProLiant%GPASS%DL380'),(11,106,'yes','HP ProLiant%GPASS%DL385'),(11,107,'yes','HP ProLiant%GPASS%DL580'),(11,108,'yes','HP ProLiant%GPASS%DL585'),(11,2615,'yes','HP ProLiant%GPASS%DL980'),(11,109,'yes','HP ProLiant%GPASS%ML110'),(11,540,'yes','HP ProLiant%GPASS%ML115'),(11,110,'yes','HP ProLiant%GPASS%ML150'),(11,111,'yes','HP ProLiant%GPASS%ML310'),(11,112,'yes','HP ProLiant%GPASS%ML350'),(11,113,'yes','HP ProLiant%GPASS%ML370'),(11,114,'yes','HP ProLiant%GPASS%ML570'),(11,1478,'yes','IBM BladeCenter%GPASS%HS12'),(11,1479,'yes','IBM BladeCenter%GPASS%HS20'),(11,1480,'yes','IBM BladeCenter%GPASS%HS21'),(11,1481,'yes','IBM BladeCenter%GPASS%HS21 XM'),(11,1482,'yes','IBM BladeCenter%GPASS%HS22'),(11,1483,'yes','IBM BladeCenter%GPASS%HS22V'),(11,1484,'yes','IBM BladeCenter%GPASS%HX5'),(11,1485,'yes','IBM BladeCenter%GPASS%JS12'),(11,1486,'yes','IBM BladeCenter%GPASS%JS20'),(11,1487,'yes','IBM BladeCenter%GPASS%JS21'),(11,1488,'yes','IBM BladeCenter%GPASS%JS22'),(11,1489,'yes','IBM BladeCenter%GPASS%JS23'),(11,1490,'yes','IBM BladeCenter%GPASS%JS43'),(11,1491,'yes','IBM BladeCenter%GPASS%LS20'),(11,1492,'yes','IBM BladeCenter%GPASS%LS21'),(11,1493,'yes','IBM BladeCenter%GPASS%LS22'),(11,1494,'yes','IBM BladeCenter%GPASS%LS41'),(11,1495,'yes','IBM BladeCenter%GPASS%LS42'),(11,1496,'yes','IBM BladeCenter%GPASS%PS700'),(11,1497,'yes','IBM BladeCenter%GPASS%PS701'),(11,1498,'yes','IBM BladeCenter%GPASS%PS702'),(11,1499,'yes','IBM BladeCenter%GPASS%PS703'),(11,1563,'yes','IBM BladeCenter%GPASS%PS704'),(11,1564,'yes','IBM BladeCenter%GPASS%QS21'),(11,1565,'yes','IBM BladeCenter%GPASS%QS22'),(11,92,'yes','IBM pSeries%GPASS%185'),(11,93,'yes','IBM pSeries%GPASS%505'),(11,94,'yes','IBM pSeries%GPASS%505Q'),(11,95,'yes','IBM pSeries%GPASS%510'),(11,96,'yes','IBM pSeries%GPASS%510Q'),(11,97,'yes','IBM pSeries%GPASS%520'),(11,98,'yes','IBM pSeries%GPASS%520Q'),(11,99,'yes','IBM pSeries%GPASS%550'),(11,100,'yes','IBM pSeries%GPASS%550Q'),(11,43,'yes','IBM xSeries%GPASS%305'),(11,44,'yes','IBM xSeries%GPASS%306'),(11,45,'yes','IBM xSeries%GPASS%306m'),(11,68,'yes','IBM xSeries%GPASS%3250'),(11,59,'yes','IBM xSeries%GPASS%326'),(11,46,'yes','IBM xSeries%GPASS%326m'),(11,47,'yes','IBM xSeries%GPASS%330'),(11,48,'yes','IBM xSeries%GPASS%335'),(11,69,'yes','IBM xSeries%GPASS%3455'),(11,54,'yes','IBM xSeries%GPASS%346'),(11,70,'yes','IBM xSeries%GPASS%3550'),(11,71,'yes','IBM xSeries%GPASS%3650'),(11,73,'yes','IBM xSeries%GPASS%3650 T'),(11,72,'yes','IBM xSeries%GPASS%3655'),(11,74,'yes','IBM xSeries%GPASS%3755'),(11,75,'yes','IBM xSeries%GPASS%3850'),(11,42,'yes','noname/unknown'),(11,792,'yes','SGI%GPASS%Altix XE250'),(11,955,'yes','SGI%GPASS%Altix XE270'),(11,793,'yes','SGI%GPASS%Altix XE310'),(11,794,'yes','SGI%GPASS%Altix XE320'),(11,956,'yes','SGI%GPASS%Altix XE340'),(11,957,'yes','SGI%GPASS%Altix XE500'),(11,50,'yes','Sun%GPASS%Enterprise 420R'),(11,61,'yes','Sun%GPASS%Enterprise 4500'),(11,90,'yes','Sun%GPASS%Fire E2900'),(11,52,'yes','Sun%GPASS%Fire E4900'),(11,83,'yes','Sun%GPASS%Fire V125'),(11,91,'yes','Sun%GPASS%Fire V1280'),(11,57,'yes','Sun%GPASS%Fire V210'),(11,84,'yes','Sun%GPASS%Fire V215'),(11,58,'yes','Sun%GPASS%Fire V240'),(11,85,'yes','Sun%GPASS%Fire V245'),(11,82,'yes','Sun%GPASS%Fire V40z'),(11,87,'yes','Sun%GPASS%Fire V440'),(11,86,'yes','Sun%GPASS%Fire V445'),(11,88,'yes','Sun%GPASS%Fire V490'),(11,89,'yes','Sun%GPASS%Fire V890'),(11,51,'yes','Sun%GPASS%Fire X2100'),(11,80,'yes','Sun%GPASS%Fire X2100 M2'),(11,81,'yes','Sun%GPASS%Fire X2200 M2'),(11,79,'yes','Sun%GPASS%Fire X4100'),(11,78,'yes','Sun%GPASS%Fire X4200'),(11,77,'yes','Sun%GPASS%Fire X4500'),(11,76,'yes','Sun%GPASS%Fire X4600'),(11,60,'yes','Sun%GPASS%Netra t1 105'),(11,53,'yes','Sun%GPASS%Netra X1'),(11,49,'yes','Sun%GPASS%Ultra 10'),(11,64,'yes','Sun%GPASS%Ultra 5'),(12,1022,'yes','[[Brocade%GPASS%FastIron CX 624S | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-cx-series/overview.page]]'),(12,1023,'yes','[[Brocade%GPASS%FastIron CX 624S-HPOE | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-cx-series/overview.page]]'),(12,1024,'yes','[[Brocade%GPASS%FastIron CX 648S | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-cx-series/overview.page]]'),(12,1025,'yes','[[Brocade%GPASS%FastIron CX 648S-HPOE | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-cx-series/overview.page]]'),(12,1026,'yes','[[Brocade%GPASS%FastIron WS 624 | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1027,'yes','[[Brocade%GPASS%FastIron WS 624-POE | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1028,'yes','[[Brocade%GPASS%FastIron WS 624G | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1029,'yes','[[Brocade%GPASS%FastIron WS 624G-POE | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1030,'yes','[[Brocade%GPASS%FastIron WS 648 | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1031,'yes','[[Brocade%GPASS%FastIron WS 648-POE | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1032,'yes','[[Brocade%GPASS%FastIron WS 648G | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1033,'yes','[[Brocade%GPASS%FastIron WS 648G-POE | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fastiron-ws-series/overview.page]]'),(12,1362,'yes','[[Brocade%GPASS%FCX 648 | http://www.brocade.com/sites/dotcom/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/fcx-series-data-center/index.page ]]'),(12,2239,'yes','[[Brocade%GPASS%ICX-6430-48 | http://www.brocade.com/products/all/switches/product-details/icx-6430-and-6450-switches/index.page]]'),(12,2240,'yes','[[Brocade%GPASS%ICX-6450-48 | http://www.brocade.com/products/all/switches/product-details/icx-6430-and-6450-switches/index.page]]'),(12,1889,'yes','[[Brocade%GPASS%ICX-6610-48-PE | http://www.brocade.com/products/all/switches/product-details/icx-6610-switch/index.page]]'),(12,1890,'yes','[[Brocade%GPASS%ICX-6650-48-E-ADV | http://www.brocade.com/products/all/switches/product-details/icx-6650-switch/index.page]]'),(12,1034,'yes','[[Brocade%GPASS%NetIron CES 2024C | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/service-provider/product-details/netiron-ces-2000-series/overview.page]]'),(12,1035,'yes','[[Brocade%GPASS%NetIron CES 2024F | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/service-provider/product-details/netiron-ces-2000-series/overview.page]]'),(12,1036,'yes','[[Brocade%GPASS%NetIron CES 2048C | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/service-provider/product-details/netiron-ces-2000-series/overview.page]]'),(12,1038,'yes','[[Brocade%GPASS%NetIron CES 2048CX | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/service-provider/product-details/netiron-ces-2000-series/overview.page]]'),(12,1037,'yes','[[Brocade%GPASS%NetIron CES 2048F | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/service-provider/product-details/netiron-ces-2000-series/overview.page]]'),(12,1039,'yes','[[Brocade%GPASS%NetIron CES 2048FX | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/service-provider/product-details/netiron-ces-2000-series/overview.page]]'),(12,1043,'yes','[[Brocade%GPASS%ServerIron 4G-SSL-FIPS | http://www.brocade.com/sites/dotcom/products-solutions/products/ethernet-switches-routers/application-switching/product-details/serveriron-4g-application-switches/index.page]]'),(12,1040,'yes','[[Brocade%GPASS%ServerIron ADX 1000 | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/application-switching/product-details/serveriron-adx-series/overview.page]]'),(12,1041,'yes','[[Brocade%GPASS%ServerIron ADX 4000 | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/application-switching/product-details/serveriron-adx-series/overview.page]]'),(12,1042,'yes','[[Brocade%GPASS%ServerIron ADX 8000 | http://www.brocade.com/products-solutions/products/ethernet-switches-routers/application-switching/product-details/serveriron-adx-series/overview.page]]'),(12,1044,'yes','[[Brocade%GPASS%TurboIron 24X | http://www.brocade.com/sites/dotcom/products-solutions/products/ethernet-switches-routers/enterprise-mobility/product-details/turboiron-24x-switch/index.page]]'),(12,795,'yes','[[Cisco (blade)%GPASS%Catalyst 3032-DEL | http://www.cisco.com/en/US/products/ps8772/index.html]]'),(12,1018,'yes','[[Cisco%GPASS%Catalyst 4900M | http://www.cisco.com/en/US/products/ps9310/index.html]]'),(12,1019,'yes','[[Cisco%GPASS%Catalyst 4928-10GE | http://www.cisco.com/en/US/products/ps9903/index.html]]'),(12,147,'yes','[[Cisco%GPASS%Catalyst 4948 | http://www.cisco.com/en/US/products/ps6026/index.html]]'),(12,377,'yes','[[Cisco%GPASS%Catalyst 4948-10GE | http://www.cisco.com/en/US/products/ps6230/index.html]]'),(12,2026,'yes','[[Cisco%GPASS%Catalyst 4948E | http://www.cisco.com/en/US/products/ps10947/index.html]]'),(12,2029,'yes','[[Cisco%GPASS%Catalyst C2960CG-8TC-L | http://www.cisco.com/en/US/products/ps6406/index.html]]'),(12,2038,'yes','[[Cisco%GPASS%Catalyst WS-CBS3012-IBM/-I | http://www.cisco.com/en/US/products/ps8766/index.html]]'),(12,958,'yes','[[Cisco%GPASS%Nexus 2148T | http://cisco.com/en/US/products/ps10118/index.html]]'),(12,1413,'yes','[[Cisco%GPASS%Nexus 2224TP | http://cisco.com/en/US/products/ps11045/index.html]]'),(12,1415,'yes','[[Cisco%GPASS%Nexus 2232PP | http://cisco.com/en/US/products/ps10784/index.html]]'),(12,1414,'yes','[[Cisco%GPASS%Nexus 2248TP | http://cisco.com/en/US/products/ps10783/index.html]]'),(12,2334,'yes','[[Cisco%GPASS%Nexus 3016 | http://www.cisco.com/c/en/us/products/switches/nexus-3016-switch/index.html]]'),(12,2333,'yes','[[Cisco%GPASS%Nexus 3048 | http://www.cisco.com/c/en/us/products/switches/nexus-3048-switch/index.html]]'),(12,2332,'yes','[[Cisco%GPASS%Nexus 3064 | http://www.cisco.com/c/en/us/products/switches/nexus-3064-switch/index.html]]'),(12,2331,'yes','[[Cisco%GPASS%Nexus 3132Q | http://www.cisco.com/c/en/us/products/switches/nexus-3132q-switch/index.html]]'),(12,2330,'yes','[[Cisco%GPASS%Nexus 3164Q | http://www.cisco.com/c/en/us/products/switches/nexus-3164q-switch/index.html]]'),(12,2329,'yes','[[Cisco%GPASS%Nexus 3172 | http://www.cisco.com/c/en/us/products/switches/nexus-3172-switch/index.html]]'),(12,2328,'yes','[[Cisco%GPASS%Nexus 3524 | http://www.cisco.com/c/en/us/products/switches/nexus-3524-switch/index.html]]'),(12,2327,'yes','[[Cisco%GPASS%Nexus 3548 | http://www.cisco.com/c/en/us/products/switches/nexus-3548-switch/index.html]]'),(12,959,'yes','[[Cisco%GPASS%Nexus 5010 | http://cisco.com/en/US/products/ps9711/index.html]]'),(12,960,'yes','[[Cisco%GPASS%Nexus 5020 | http://cisco.com/en/US/products/ps9710/index.html]]'),(12,1412,'yes','[[Cisco%GPASS%Nexus 5548P | http://cisco.com/en/US/products/ps11215/index.html]]'),(12,2085,'yes','[[Cisco%GPASS%Nexus 6001 | http://www.cisco.com/en/US/products/ps12869/index.html]]'),(12,2086,'yes','[[Cisco%GPASS%Nexus 6004 | http://www.cisco.com/en/US/products/ps12807/index.html]]'),(12,961,'yes','[[Cisco%GPASS%Nexus 7010 | http://cisco.com/en/US/products/ps9512/index.html]]'),(12,962,'yes','[[Cisco%GPASS%Nexus 7018 | http://cisco.com/en/US/products/ps10098/index.html]]'),(12,2479,'yes','[[Cisco%GPASS%Nexus 7702 | http://www.cisco.com/c/en/us/products/switches/nexus-7700-2-slot-switch/index.html]]'),(12,2480,'yes','[[Cisco%GPASS%Nexus 7706 | http://www.cisco.com/c/en/us/products/switches/nexus-7700-6-slot-switch/index.html]]'),(12,2481,'yes','[[Cisco%GPASS%Nexus 7710 | http://www.cisco.com/c/en/us/products/switches/nexus-7700-10-slot-switch/index.html]]'),(12,2482,'yes','[[Cisco%GPASS%Nexus 7718 | http://www.cisco.com/c/en/us/products/switches/nexus-7700-18-slot-switch/index.html]]'),(12,2235,'yes','[[Cisco%GPASS%Nexus 9504 | http://www.cisco.com/c/en/us/products/switches/nexus-9504-switch/index.html]]'),(12,2236,'yes','[[Cisco%GPASS%Nexus 9508 | http://www.cisco.com/c/en/us/products/switches/nexus-9508-switch/index.html]]'),(12,2237,'yes','[[Cisco%GPASS%Nexus 9516 | http://www.cisco.com/c/en/us/products/switches/nexus-9516-switch/index.html]]'),(12,634,'yes','[[D-Link%GPASS%DES-1024D | http://www.dlink.com/products/?sec=0&pid=75]]'),(12,635,'yes','[[D-Link%GPASS%DES-1026G | http://www.dlink.com/products/?sec=0&pid=76]]'),(12,628,'yes','[[D-Link%GPASS%DES-1228 | http://www.dlink.com/products/?sec=0&pid=540]]'),(12,629,'yes','[[D-Link%GPASS%DES-1228P | http://www.dlink.com/products/?sec=0&pid=541]]'),(12,630,'yes','[[D-Link%GPASS%DES-1252 | http://www.dlink.com/products/?sec=0&pid=555]]'),(12,627,'yes','[[D-Link%GPASS%DES-1316 | http://www.dlink.com/products/?sec=0&pid=353]]'),(12,618,'yes','[[D-Link%GPASS%DES-3010FA | http://www.dlink.com/products/?sec=0&pid=423]]'),(12,619,'yes','[[D-Link%GPASS%DES-3010GA | http://www.dlink.com/products/?sec=0&pid=424]]'),(12,620,'yes','[[D-Link%GPASS%DES-3010PA | http://www.dlink.com/products/?sec=0&pid=469]]'),(12,614,'yes','[[D-Link%GPASS%DES-3028 | http://www.dlink.com/products/?sec=0&pid=630]]'),(12,615,'yes','[[D-Link%GPASS%DES-3028P | http://www.dlink.com/products/?sec=0&pid=631]]'),(12,616,'yes','[[D-Link%GPASS%DES-3052 | http://www.dlink.com/products/?sec=0&pid=632]]'),(12,617,'yes','[[D-Link%GPASS%DES-3052P | http://www.dlink.com/products/?sec=0&pid=633]]'),(12,621,'yes','[[D-Link%GPASS%DES-3226L | http://www.dlink.com/products/?sec=0&pid=298]]'),(12,613,'yes','[[D-Link%GPASS%DES-3228PA | http://www.dlink.com/products/?sec=0&pid=644]]'),(12,622,'yes','[[D-Link%GPASS%DES-3526 | http://www.dlink.com/products/?sec=0&pid=330]]'),(12,623,'yes','[[D-Link%GPASS%DES-3550 | http://www.dlink.com/products/?sec=0&pid=331]]'),(12,601,'yes','[[D-Link%GPASS%DES-3828 | http://www.dlink.com/products/?sec=0&pid=439]]'),(12,602,'yes','[[D-Link%GPASS%DES-3828P | http://www.dlink.com/products/?sec=0&pid=440]]'),(12,589,'yes','[[D-Link%GPASS%DES-6500 | http://www.dlink.com/products/?sec=0&pid=341]]'),(12,631,'yes','[[D-Link%GPASS%DGS-1016D | http://www.dlink.com/products/?sec=0&pid=337]]'),(12,632,'yes','[[D-Link%GPASS%DGS-1024D | http://www.dlink.com/products/?sec=0&pid=338]]'),(12,624,'yes','[[D-Link%GPASS%DGS-1216T | http://www.dlink.com/products/?sec=0&pid=324]]'),(12,625,'yes','[[D-Link%GPASS%DGS-1224T | http://www.dlink.com/products/?sec=0&pid=329]]'),(12,626,'yes','[[D-Link%GPASS%DGS-1248T | http://www.dlink.com/products/?sec=0&pid=367]]'),(12,610,'yes','[[D-Link%GPASS%DGS-3024 | http://www.dlink.com/products/?sec=0&pid=404]]'),(12,612,'yes','[[D-Link%GPASS%DGS-3048 | http://www.dlink.com/products/?sec=0&pid=496]]'),(12,603,'yes','[[D-Link%GPASS%DGS-3100-24 | http://www.dlink.com/products/?sec=0&pid=635]]'),(12,604,'yes','[[D-Link%GPASS%DGS-3100-24P | http://www.dlink.com/products/?sec=0&pid=636]]'),(12,605,'yes','[[D-Link%GPASS%DGS-3100-48 | http://www.dlink.com/products/?sec=0&pid=637]]'),(12,606,'yes','[[D-Link%GPASS%DGS-3100-48P | http://www.dlink.com/products/?sec=0&pid=638]]'),(12,611,'yes','[[D-Link%GPASS%DGS-3224TGR | http://www.dlink.com/products/?sec=0&pid=269]]'),(12,597,'yes','[[D-Link%GPASS%DGS-3324SR | http://www.dlink.com/products/?sec=0&pid=294]]'),(12,598,'yes','[[D-Link%GPASS%DGS-3324SRi | http://www.dlink.com/products/?sec=0&pid=309]]'),(12,594,'yes','[[D-Link%GPASS%DGS-3612G | http://www.dlink.com/products/?sec=0&pid=557]]'),(12,595,'yes','[[D-Link%GPASS%DGS-3627 | http://www.dlink.com/products/?sec=0&pid=639]]'),(12,596,'yes','[[D-Link%GPASS%DGS-3650 | http://www.dlink.com/products/?sec=0&pid=640]]'),(12,633,'yes','[[D-Link%GPASS%DSS-24+ | http://www.dlink.com/products/?sec=0&pid=73]]'),(12,593,'yes','[[D-Link%GPASS%DWS-1008 | http://www.dlink.com/products/?sec=0&pid=434]]'),(12,590,'yes','[[D-Link%GPASS%DWS-3227 | http://www.dlink.com/products/?sec=0&pid=506]]'),(12,591,'yes','[[D-Link%GPASS%DWS-3227P | http://www.dlink.com/products/?sec=0&pid=507]]'),(12,592,'yes','[[D-Link%GPASS%DWS-3250 | http://www.dlink.com/products/?sec=0&pid=468]]'),(12,607,'yes','[[D-Link%GPASS%DXS-3227 | http://www.dlink.com/products/?sec=0&pid=483]]'),(12,608,'yes','[[D-Link%GPASS%DXS-3227P | http://www.dlink.com/products/?sec=0&pid=497]]'),(12,609,'yes','[[D-Link%GPASS%DXS-3250 | http://www.dlink.com/products/?sec=0&pid=443]]'),(12,599,'yes','[[D-Link%GPASS%DXS-3326GSR | http://www.dlink.com/products/?sec=0&pid=339]]'),(12,600,'yes','[[D-Link%GPASS%DXS-3350SR | http://www.dlink.com/products/?sec=0&pid=340]]'),(12,2218,'yes','[[Edge-Core%GPASS%AS4600-54T | http://www.edge-core.com/ProdDtl.asp?sno=425&AS4600-54T%20with%20ONIE]]'),(12,2217,'yes','[[Edge-Core%GPASS%AS5600-52X | http://www.edge-core.com/ProdDtl.asp?sno=423&AS5600-52X%20with%20ONIE]]'),(12,2216,'yes','[[Edge-Core%GPASS%AS5610-52X | http://www.edge-core.com/ProdDtl.asp?sno=436&AS5610-52X%20with%20ONIE]]'),(12,2214,'yes','[[Edge-Core%GPASS%AS6700-32X | http://www.edge-core.com/ProdDtl.asp?sno=435&AS6700-32X%20with%20ONIE]]'),(12,2215,'yes','[[Edge-Core%GPASS%AS6701-32X | http://www.edge-core.com/ProdDtl.asp?sno=435&AS6700-32X%20with%20ONIE]]'),(12,708,'yes','[[Extreme Networks%GPASS%Alpine 3804 | http://www.extremenetworks.com/products/Alpine-3800.aspx]]'),(12,709,'yes','[[Extreme Networks%GPASS%Alpine 3808 | http://www.extremenetworks.com/products/Alpine-3800.aspx]]'),(12,713,'yes','[[Extreme Networks%GPASS%BlackDiamond 10808 | http://www.extremenetworks.com/products/blackdiamond-10808.aspx]]'),(12,714,'yes','[[Extreme Networks%GPASS%BlackDiamond 12802R | http://www.extremenetworks.com/products/blackdiamond-12800r.aspx]]'),(12,716,'yes','[[Extreme Networks%GPASS%BlackDiamond 12804C | http://www.extremenetworks.com/products/blackdiamond-12804c.aspx]]'),(12,715,'yes','[[Extreme Networks%GPASS%BlackDiamond 12804R | http://www.extremenetworks.com/products/blackdiamond-12800r.aspx]]'),(12,710,'yes','[[Extreme Networks%GPASS%BlackDiamond 6808 | http://www.extremenetworks.com/products/blackdiamond-6800.aspx]]'),(12,711,'yes','[[Extreme Networks%GPASS%BlackDiamond 8806 | http://www.extremenetworks.com/products/blackdiamond-8800.aspx]]'),(12,712,'yes','[[Extreme Networks%GPASS%BlackDiamond 8810 | http://www.extremenetworks.com/products/blackdiamond-8800.aspx]]'),(12,700,'yes','[[Extreme Networks%GPASS%Summit 200-24 | http://www.extremenetworks.com/products/summit-200.aspx]]'),(12,699,'yes','[[Extreme Networks%GPASS%Summit 200-24fx | http://www.extremenetworks.com/products/summit-200.aspx]]'),(12,701,'yes','[[Extreme Networks%GPASS%Summit 200-48 | http://www.extremenetworks.com/products/summit-200.aspx]]'),(12,702,'yes','[[Extreme Networks%GPASS%Summit 300-24 | http://www.extremenetworks.com/products/summit-300.aspx]]'),(12,703,'yes','[[Extreme Networks%GPASS%Summit 300-48 | http://www.extremenetworks.com/products/summit-300.aspx]]'),(12,704,'yes','[[Extreme Networks%GPASS%Summit 400-24p | http://www.extremenetworks.com/products/summit-400-24p.aspx]]'),(12,705,'yes','[[Extreme Networks%GPASS%Summit 400-24t | http://www.extremenetworks.com/products/summit-400-24t.aspx]]'),(12,706,'yes','[[Extreme Networks%GPASS%Summit 400-48t | http://www.extremenetworks.com/products/summit-400-48t.aspx]]'),(12,686,'yes','[[Extreme Networks%GPASS%Summit X150-24p | http://www.extremenetworks.com/products/summit-x150.aspx]]'),(12,684,'yes','[[Extreme Networks%GPASS%Summit X150-24t | http://www.extremenetworks.com/products/summit-x150.aspx]]'),(12,685,'yes','[[Extreme Networks%GPASS%Summit X150-48t | http://www.extremenetworks.com/products/summit-x150.aspx]]'),(12,689,'yes','[[Extreme Networks%GPASS%Summit X250e-24p | http://www.extremenetworks.com/products/summit-x250e.aspx]]'),(12,687,'yes','[[Extreme Networks%GPASS%Summit X250e-24t | http://www.extremenetworks.com/products/summit-x250e.aspx]]'),(12,691,'yes','[[Extreme Networks%GPASS%Summit X250e-24x | http://www.extremenetworks.com/products/summit-x250e.aspx]]'),(12,690,'yes','[[Extreme Networks%GPASS%Summit X250e-48p | http://www.extremenetworks.com/products/summit-x250e.aspx]]'),(12,688,'yes','[[Extreme Networks%GPASS%Summit X250e-48t | http://www.extremenetworks.com/products/summit-x250e.aspx]]'),(12,692,'yes','[[Extreme Networks%GPASS%Summit X450-24t | http://www.extremenetworks.com/products/summit-x450.aspx]]'),(12,693,'yes','[[Extreme Networks%GPASS%Summit X450-24x | http://www.extremenetworks.com/products/summit-x450.aspx]]'),(12,694,'yes','[[Extreme Networks%GPASS%Summit X450a-24t | http://www.extremenetworks.com/products/summit-x450a.aspx]]'),(12,696,'yes','[[Extreme Networks%GPASS%Summit X450a-24x | http://www.extremenetworks.com/products/summit-x450a.aspx]]'),(12,695,'yes','[[Extreme Networks%GPASS%Summit X450a-48t | http://www.extremenetworks.com/products/summit-x450a.aspx]]'),(12,697,'yes','[[Extreme Networks%GPASS%Summit X450e-24p | http://www.extremenetworks.com/products/summit-x450e.aspx]]'),(12,698,'yes','[[Extreme Networks%GPASS%Summit X450e-48p | http://www.extremenetworks.com/products/summit-x450e.aspx]]'),(12,1353,'yes','[[Extreme Networks%GPASS%Summit X480-24x | http://extremenetworks.com/products/summit-X480.aspx]]'),(12,1354,'yes','[[Extreme Networks%GPASS%Summit X480-48t | http://extremenetworks.com/products/summit-X480.aspx]]'),(12,1355,'yes','[[Extreme Networks%GPASS%Summit X480-48x | http://extremenetworks.com/products/summit-X480.aspx]]'),(12,1356,'yes','[[Extreme Networks%GPASS%Summit X650 | http://extremenetworks.com/products/summit-x650.aspx]]'),(12,707,'yes','[[Extreme Networks%GPASS%Summit48si | http://www.extremenetworks.com/products/summit-48si.aspx]]'),(12,946,'yes','[[F5%GPASS%ARX 1000 | http://www.f5.com/pdf/products/arx-series-ds.pdf]]'),(12,947,'yes','[[F5%GPASS%ARX 4000 | http://www.f5.com/pdf/products/arx-series-ds.pdf]]'),(12,945,'yes','[[F5%GPASS%ARX 500 | http://www.f5.com/pdf/products/arx-series-ds.pdf]]'),(12,948,'yes','[[F5%GPASS%ARX 6000 | http://www.f5.com/pdf/products/arx-series-ds.pdf]]'),(12,936,'yes','[[F5%GPASS%BIG-IP 1500 | http://www.f5.com/pdf/products/big-ip-platforms-2007-ds.pdf]]'),(12,937,'yes','[[F5%GPASS%BIG-IP 1600 | http://www.f5.com/pdf/products/big-ip-platforms-ds.pdf]]'),(12,2191,'yes','[[F5%GPASS%BIG-IP 2000S | http://www.f5.com/pdf/products/big-ip-platforms-datasheet.pdf]]'),(12,2192,'yes','[[F5%GPASS%BIG-IP 2200S | http://www.f5.com/pdf/products/big-ip-platforms-datasheet.pdf]]'),(12,938,'yes','[[F5%GPASS%BIG-IP 3400 | http://www.f5.com/pdf/products/big-ip-platforms-2007-ds.pdf]]'),(12,939,'yes','[[F5%GPASS%BIG-IP 3600 | http://www.f5.com/pdf/products/big-ip-platforms-ds.pdf]]'),(12,2193,'yes','[[F5%GPASS%BIG-IP 3900 | http://www.f5.com/pdf/products/big-ip-platforms-datasheet.pdf]]'),(12,2194,'yes','[[F5%GPASS%BIG-IP 4000S | http://www.f5.com/pdf/products/big-ip-platforms-datasheet.pdf]]'),(12,2195,'yes','[[F5%GPASS%BIG-IP 4200V | http://www.f5.com/pdf/products/big-ip-platforms-datasheet.pdf]]'),(12,2196,'yes','[[F5%GPASS%BIG-IP 5000S | http://www.f5.com/pdf/products/big-ip-platforms-datasheet.pdf]]'),(12,2197,'yes','[[F5%GPASS%BIG-IP 5200V | http://www.f5.com/pdf/products/big-ip-platforms-datasheet.pdf]]'),(12,940,'yes','[[F5%GPASS%BIG-IP 6400 | http://www.f5.com/pdf/products/big-ip-platforms-2007-ds.pdf]]'),(12,941,'yes','[[F5%GPASS%BIG-IP 6800 | http://www.f5.com/pdf/products/big-ip-platforms-2007-ds.pdf]]'),(12,942,'yes','[[F5%GPASS%BIG-IP 6900 | http://www.f5.com/pdf/products/big-ip-platforms-ds.pdf]]'),(12,943,'yes','[[F5%GPASS%BIG-IP 8400 | http://www.f5.com/pdf/products/big-ip-platforms-ds.pdf]]'),(12,944,'yes','[[F5%GPASS%BIG-IP 8800 | http://www.f5.com/pdf/products/big-ip-platforms-ds.pdf]]'),(12,934,'yes','[[F5%GPASS%BIG-IP WebAccelerator 4500 | http://www.f5.com/pdf/products/big-ip-webaccelerator-ds.pdf]]'),(12,1472,'yes','[[Force10%GPASS%S4810 | http://www.force10networks.com/products/s4810.asp]]'),(12,1470,'yes','[[Force10%GPASS%S55 | http://www.force10networks.com/products/s55.asp]]'),(12,1471,'yes','[[Force10%GPASS%S60 | http://www.force10networks.com/products/s60.asp]]'),(12,1892,'yes','[[Fortinet%GPASS%Fortigate 300C | http://www.fortinet.com/products/fortigate/300C.html]]'),(12,1891,'yes','[[Fortinet%GPASS%Fortigate 3140B| http://www.fortinet.com/products/fortigate/3140B.html]]'),(12,1893,'yes','[[Fortinet%GPASS%Fortigate 800C | http://www.fortinet.com/products/fortigate/800C.html]]'),(12,2241,'yes','[[IBM%GPASS%RackSwitch G8000 | http://www-03.ibm.com/systems/networking/switches/rack/g8000/]]'),(12,1887,'yes','[[IBM%GPASS%RackSwitch G8052 | http://www-03.ibm.com/systems/networking/switches/rack/g8052/]]'),(12,1888,'yes','[[IBM%GPASS%RackSwitch G8264 | http://www-03.ibm.com/systems/networking/switches/rack/g8264/]]'),(12,909,'yes','[[Juniper%GPASS%E120 BSR | http://www.juniper.net/products_and_services/e_series_broadband_service/index.html]]'),(12,899,'yes','[[Juniper%GPASS%EX3200-24P | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,900,'yes','[[Juniper%GPASS%EX3200-24T | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,901,'yes','[[Juniper%GPASS%EX3200-48P | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,902,'yes','[[Juniper%GPASS%EX3200-48T | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,903,'yes','[[Juniper%GPASS%EX4200-24F | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,904,'yes','[[Juniper%GPASS%EX4200-24P | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,2148,'yes','[[Juniper%GPASS%EX4200-24PX | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,905,'yes','[[Juniper%GPASS%EX4200-24T | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,906,'yes','[[Juniper%GPASS%EX4200-48P | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,2149,'yes','[[Juniper%GPASS%EX4200-48PX | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,907,'yes','[[Juniper%GPASS%EX4200-48T | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,908,'yes','[[Juniper%GPASS%EX8208 | http://www.juniper.net/products_and_services/ex_series/index.html]]'),(12,2058,'yes','[[TP-Link%GPASS%TL-SL5428E | http://www.tp-link.com/en/products/details/?model=TL-SL5428E]]'),(12,750,'yes','3Com%GPASS%3870 24-port'),(12,751,'yes','3Com%GPASS%3870 48-port'),(12,752,'yes','3Com%GPASS%4200 26-port'),(12,753,'yes','3Com%GPASS%4200 28-port'),(12,754,'yes','3Com%GPASS%4200 50-port'),(12,755,'yes','3Com%GPASS%4200G 12-port'),(12,756,'yes','3Com%GPASS%4200G 24-port'),(12,758,'yes','3Com%GPASS%4200G 48-port'),(12,757,'yes','3Com%GPASS%4200G PWR 24-port'),(12,759,'yes','3Com%GPASS%4210 26-port'),(12,761,'yes','3Com%GPASS%4210 26-port PWR'),(12,760,'yes','3Com%GPASS%4210 52-port'),(12,766,'yes','3Com%GPASS%4500 26-port'),(12,767,'yes','3Com%GPASS%4500 50-port'),(12,768,'yes','3Com%GPASS%4500 PWR 26-port'),(12,769,'yes','3Com%GPASS%4500 PWR 50-port'),(12,770,'yes','3Com%GPASS%4500G 24-port'),(12,771,'yes','3Com%GPASS%4500G 48-port'),(12,772,'yes','3Com%GPASS%4500G PWR 24-port'),(12,773,'yes','3Com%GPASS%4500G PWR 48-port'),(12,2092,'yes','3Com%GPASS%4510G 24-port'),(12,774,'yes','3Com%GPASS%5500-EI 28-port'),(12,778,'yes','3Com%GPASS%5500-EI 28-port FX'),(12,776,'yes','3Com%GPASS%5500-EI 28-port PWR'),(12,775,'yes','3Com%GPASS%5500-EI 52-port'),(12,777,'yes','3Com%GPASS%5500-EI 52-port PWR'),(12,779,'yes','3Com%GPASS%5500G-EI 24-port'),(12,783,'yes','3Com%GPASS%5500G-EI 24-port SFP'),(12,780,'yes','3Com%GPASS%5500G-EI 48-port'),(12,782,'yes','3Com%GPASS%5500G-EI 48-port PWR'),(12,781,'yes','3Com%GPASS%5500G-EI PWR 24-port'),(12,784,'yes','3Com%GPASS%7754'),(12,785,'yes','3Com%GPASS%7757'),(12,786,'yes','3Com%GPASS%7758'),(12,787,'yes','3Com%GPASS%8807'),(12,788,'yes','3Com%GPASS%8810'),(12,789,'yes','3Com%GPASS%8814'),(12,738,'yes','3Com%GPASS%Baseline 2016'),(12,739,'yes','3Com%GPASS%Baseline 2024'),(12,740,'yes','3Com%GPASS%Baseline 2126-G'),(12,743,'yes','3Com%GPASS%Baseline 2226 Plus'),(12,745,'yes','3Com%GPASS%Baseline 2250 Plus'),(12,744,'yes','3Com%GPASS%Baseline 2426-PWR Plus'),(12,741,'yes','3Com%GPASS%Baseline 2816'),(12,742,'yes','3Com%GPASS%Baseline 2824'),(12,746,'yes','3Com%GPASS%Baseline 2916-SFP Plus'),(12,748,'yes','3Com%GPASS%Baseline 2924-PWR Plus'),(12,747,'yes','3Com%GPASS%Baseline 2924-SFP Plus'),(12,749,'yes','3Com%GPASS%Baseline 2948-SFP Plus'),(12,763,'yes','3Com%GPASS%SS3 4400 24-port'),(12,762,'yes','3Com%GPASS%SS3 4400 48-port'),(12,764,'yes','3Com%GPASS%SS3 4400 PWR'),(12,765,'yes','3Com%GPASS%SS3 4400 SE 24-port'),(12,2095,'yes','Allied Telesis%GPASS%AT-GS950/24'),(12,1720,'yes','Allied Telesis%GPASS%AT9924T'),(12,2267,'yes','Arista%GPASS%7010T-48'),(12,1726,'yes','Arista%GPASS%7048T-A'),(12,1729,'yes','Arista%GPASS%7050Q-16'),(12,2056,'yes','Arista%GPASS%7050QX-32'),(12,2266,'yes','Arista%GPASS%7050QX-32S'),(12,1731,'yes','Arista%GPASS%7050S-52'),(12,1730,'yes','Arista%GPASS%7050S-64'),(12,2057,'yes','Arista%GPASS%7050SX-128'),(12,2258,'yes','Arista%GPASS%7050SX-64'),(12,2259,'yes','Arista%GPASS%7050SX-72'),(12,2260,'yes','Arista%GPASS%7050SX-96'),(12,2055,'yes','Arista%GPASS%7050T-36'),(12,1728,'yes','Arista%GPASS%7050T-52'),(12,1727,'yes','Arista%GPASS%7050T-64'),(12,2265,'yes','Arista%GPASS%7050TX-128'),(12,2261,'yes','Arista%GPASS%7050TX-48'),(12,2262,'yes','Arista%GPASS%7050TX-64'),(12,2263,'yes','Arista%GPASS%7050TX-72'),(12,2264,'yes','Arista%GPASS%7050TX-96'),(12,2054,'yes','Arista%GPASS%7100S'),(12,1725,'yes','Arista%GPASS%7124FX'),(12,1610,'yes','Arista%GPASS%7124S'),(12,1723,'yes','Arista%GPASS%7124SX'),(12,1722,'yes','Arista%GPASS%7148S'),(12,1724,'yes','Arista%GPASS%7148SX'),(12,2051,'yes','Arista%GPASS%7150S-24'),(12,2052,'yes','Arista%GPASS%7150S-52'),(12,2053,'yes','Arista%GPASS%7150S-64'),(12,2071,'yes','Arista%GPASS%7250QX-64'),(12,2255,'yes','Arista%GPASS%7280SE-64'),(12,2256,'yes','Arista%GPASS%7280SE-68'),(12,2257,'yes','Arista%GPASS%7280SE-72'),(12,2068,'yes','Arista%GPASS%7304'),(12,2069,'yes','Arista%GPASS%7308'),(12,2070,'yes','Arista%GPASS%7316'),(12,2066,'yes','Arista%GPASS%7504'),(12,2067,'yes','Arista%GPASS%7508'),(12,1534,'yes','Cisco (blade)%GPASS%Catalyst 3130G'),(12,1535,'yes','Cisco (blade)%GPASS%Catalyst 3130X'),(12,2228,'yes','Cisco (blade)%GPASS%VS-S2T-10G'),(12,2229,'yes','Cisco (blade)%GPASS%VS-S2T-10G-XL'),(12,1555,'yes','Cisco (blade)%GPASS%WS-SUP32-10GE-3B'),(12,1554,'yes','Cisco (blade)%GPASS%WS-SUP32-GE-3B'),(12,1552,'yes','Cisco (blade)%GPASS%WS-SUP720-3B'),(12,1536,'yes','Cisco (blade)%GPASS%WS-X6148-GE-TX'),(12,1537,'yes','Cisco (blade)%GPASS%WS-X6148A-GE-45AF'),(12,1538,'yes','Cisco (blade)%GPASS%WS-X6148A-GE-TX'),(12,1539,'yes','Cisco (blade)%GPASS%WS-X6408A-GBIC'),(12,1540,'yes','Cisco (blade)%GPASS%WS-X6416-GBIC'),(12,1541,'yes','Cisco (blade)%GPASS%WS-X6516A-GBIC'),(12,1543,'yes','Cisco (blade)%GPASS%WS-X6548-GE-45AF'),(12,1542,'yes','Cisco (blade)%GPASS%WS-X6548-GE-TX'),(12,1544,'yes','Cisco (blade)%GPASS%WS-X6704-10GE'),(12,1545,'yes','Cisco (blade)%GPASS%WS-X6708-10G-3C'),(12,1546,'yes','Cisco (blade)%GPASS%WS-X6708-10G-3CXL'),(12,1547,'yes','Cisco (blade)%GPASS%WS-X6716-10GT-3C'),(12,1548,'yes','Cisco (blade)%GPASS%WS-X6716-10GT-3CXL'),(12,1549,'yes','Cisco (blade)%GPASS%WS-X6724-SFP'),(12,1550,'yes','Cisco (blade)%GPASS%WS-X6748-GE-TX'),(12,1551,'yes','Cisco (blade)%GPASS%WS-X6748-SFP'),(12,2232,'yes','Cisco (blade)%GPASS%WS-X6904-40G-2T'),(12,2233,'yes','Cisco (blade)%GPASS%WS-X6904-40G-2TXL'),(12,2230,'yes','Cisco (blade)%GPASS%WS-X6908-10G-2T'),(12,2231,'yes','Cisco (blade)%GPASS%WS-X6908-10G-2TXL'),(12,1553,'yes','Cisco (blade)%GPASS%WS-XSUP720-3BXL'),(12,2094,'yes','Cisco%GPASS%Catalyst 1924'),(12,1348,'yes','Cisco%GPASS%Catalyst 2350-48TD'),(12,1606,'yes','Cisco%GPASS%Catalyst 2360-48TD'),(12,126,'yes','Cisco%GPASS%Catalyst 2912XL'),(12,1719,'yes','Cisco%GPASS%Catalyst 2924M-XL'),(12,124,'yes','Cisco%GPASS%Catalyst 2924XL'),(12,1796,'yes','Cisco%GPASS%Catalyst 2948G-L3'),(12,380,'yes','Cisco%GPASS%Catalyst 2950-24'),(12,382,'yes','Cisco%GPASS%Catalyst 2950C-24'),(12,388,'yes','Cisco%GPASS%Catalyst 2950G-12'),(12,389,'yes','Cisco%GPASS%Catalyst 2950G-24'),(12,390,'yes','Cisco%GPASS%Catalyst 2950G-48'),(12,384,'yes','Cisco%GPASS%Catalyst 2950SX-48'),(12,386,'yes','Cisco%GPASS%Catalyst 2950T-24'),(12,387,'yes','Cisco%GPASS%Catalyst 2950T-48'),(12,379,'yes','Cisco%GPASS%Catalyst 2960-24-S'),(12,1898,'yes','Cisco%GPASS%Catalyst 2960-24LC-S'),(12,1895,'yes','Cisco%GPASS%Catalyst 2960-24LT-L'),(12,1347,'yes','Cisco%GPASS%Catalyst 2960-24PC-L'),(12,1897,'yes','Cisco%GPASS%Catalyst 2960-24PC-S'),(12,1710,'yes','Cisco%GPASS%Catalyst 2960-24TC-L'),(12,1894,'yes','Cisco%GPASS%Catalyst 2960-24TC-S'),(12,164,'yes','Cisco%GPASS%Catalyst 2960-24TT-L'),(12,1370,'yes','Cisco%GPASS%Catalyst 2960-48PST-L'),(12,1896,'yes','Cisco%GPASS%Catalyst 2960-48PST-S'),(12,1590,'yes','Cisco%GPASS%Catalyst 2960-48TC-L'),(12,140,'yes','Cisco%GPASS%Catalyst 2960-48TC-S'),(12,1572,'yes','Cisco%GPASS%Catalyst 2960-48TT-L'),(12,1573,'yes','Cisco%GPASS%Catalyst 2960-48TT-S'),(12,165,'yes','Cisco%GPASS%Catalyst 2960-8TC-L'),(12,1899,'yes','Cisco%GPASS%Catalyst 2960-8TC-S'),(12,2135,'yes','Cisco%GPASS%Catalyst 2960-Plus 24LC-L'),(12,2140,'yes','Cisco%GPASS%Catalyst 2960-Plus 24LC-S'),(12,2134,'yes','Cisco%GPASS%Catalyst 2960-Plus 24PC-L'),(12,2139,'yes','Cisco%GPASS%Catalyst 2960-Plus 24PC-S'),(12,2137,'yes','Cisco%GPASS%Catalyst 2960-Plus 24TC-L'),(12,2141,'yes','Cisco%GPASS%Catalyst 2960-Plus 24TC-S'),(12,2133,'yes','Cisco%GPASS%Catalyst 2960-Plus 48PST-L'),(12,2138,'yes','Cisco%GPASS%Catalyst 2960-Plus 48PST-S'),(12,2136,'yes','Cisco%GPASS%Catalyst 2960-Plus 48TC-L'),(12,2219,'yes','Cisco%GPASS%Catalyst 2960-Plus 48TC-S'),(12,167,'yes','Cisco%GPASS%Catalyst 2960G-24TC-L'),(12,166,'yes','Cisco%GPASS%Catalyst 2960G-48TC-L'),(12,168,'yes','Cisco%GPASS%Catalyst 2960G-8TC-L'),(12,1900,'yes','Cisco%GPASS%Catalyst 2960PD-8TT-L'),(12,1387,'yes','Cisco%GPASS%Catalyst 2960S-24PD-L'),(12,1394,'yes','Cisco%GPASS%Catalyst 2960S-24PS-L'),(12,1384,'yes','Cisco%GPASS%Catalyst 2960S-24TD-L'),(12,1389,'yes','Cisco%GPASS%Catalyst 2960S-24TS-L'),(12,1391,'yes','Cisco%GPASS%Catalyst 2960S-24TS-S'),(12,1385,'yes','Cisco%GPASS%Catalyst 2960S-48FPD-L'),(12,1392,'yes','Cisco%GPASS%Catalyst 2960S-48FPS-L'),(12,1386,'yes','Cisco%GPASS%Catalyst 2960S-48LPD-L'),(12,1393,'yes','Cisco%GPASS%Catalyst 2960S-48LPS-L'),(12,1383,'yes','Cisco%GPASS%Catalyst 2960S-48TD-L'),(12,1388,'yes','Cisco%GPASS%Catalyst 2960S-48TS-L'),(12,1390,'yes','Cisco%GPASS%Catalyst 2960S-48TS-S'),(12,1904,'yes','Cisco%GPASS%Catalyst 2960S-F24PS-L'),(12,1906,'yes','Cisco%GPASS%Catalyst 2960S-F24TS-L'),(12,1908,'yes','Cisco%GPASS%Catalyst 2960S-F24TS-S'),(12,1902,'yes','Cisco%GPASS%Catalyst 2960S-F48FPS-L'),(12,1903,'yes','Cisco%GPASS%Catalyst 2960S-F48LPS-L'),(12,1905,'yes','Cisco%GPASS%Catalyst 2960S-F48TS-L'),(12,1907,'yes','Cisco%GPASS%Catalyst 2960S-F48TS-S'),(12,2112,'yes','Cisco%GPASS%Catalyst 2960X-24PD-L'),(12,2117,'yes','Cisco%GPASS%Catalyst 2960X-24PS-L'),(12,2118,'yes','Cisco%GPASS%Catalyst 2960X-24PSQ-L'),(12,2114,'yes','Cisco%GPASS%Catalyst 2960X-24TD-L'),(12,2120,'yes','Cisco%GPASS%Catalyst 2960X-24TS-L'),(12,2122,'yes','Cisco%GPASS%Catalyst 2960X-24TS-LL'),(12,2110,'yes','Cisco%GPASS%Catalyst 2960X-48FPD-L'),(12,2115,'yes','Cisco%GPASS%Catalyst 2960X-48FPS-L'),(12,2111,'yes','Cisco%GPASS%Catalyst 2960X-48LPD-L'),(12,2116,'yes','Cisco%GPASS%Catalyst 2960X-48LPS-L'),(12,2113,'yes','Cisco%GPASS%Catalyst 2960X-48TD-L'),(12,2119,'yes','Cisco%GPASS%Catalyst 2960X-48TS-L'),(12,2121,'yes','Cisco%GPASS%Catalyst 2960X-48TS-LL'),(12,2125,'yes','Cisco%GPASS%Catalyst 2960XR-24PD-I'),(12,2130,'yes','Cisco%GPASS%Catalyst 2960XR-24PS-I'),(12,2127,'yes','Cisco%GPASS%Catalyst 2960XR-24TD-I'),(12,2132,'yes','Cisco%GPASS%Catalyst 2960XR-24TS-I'),(12,2123,'yes','Cisco%GPASS%Catalyst 2960XR-48FPD-I'),(12,2128,'yes','Cisco%GPASS%Catalyst 2960XR-48FPS-I'),(12,2124,'yes','Cisco%GPASS%Catalyst 2960XR-48LPD-I'),(12,2129,'yes','Cisco%GPASS%Catalyst 2960XR-48LPS-I'),(12,2126,'yes','Cisco%GPASS%Catalyst 2960XR-48TD-I'),(12,2131,'yes','Cisco%GPASS%Catalyst 2960XR-48TS-I'),(12,210,'yes','Cisco%GPASS%Catalyst 2970G-24T'),(12,211,'yes','Cisco%GPASS%Catalyst 2970G-24TS'),(12,391,'yes','Cisco%GPASS%Catalyst 3508G XL'),(12,392,'yes','Cisco%GPASS%Catalyst 3512 XL'),(12,394,'yes','Cisco%GPASS%Catalyst 3524 PWR XL'),(12,395,'yes','Cisco%GPASS%Catalyst 3548 XL'),(12,399,'yes','Cisco%GPASS%Catalyst 3550-12G'),(12,398,'yes','Cisco%GPASS%Catalyst 3550-12T'),(12,400,'yes','Cisco%GPASS%Catalyst 3550-24'),(12,402,'yes','Cisco%GPASS%Catalyst 3550-24 DC'),(12,401,'yes','Cisco%GPASS%Catalyst 3550-24 FX'),(12,403,'yes','Cisco%GPASS%Catalyst 3550-24 PWR'),(12,404,'yes','Cisco%GPASS%Catalyst 3550-48'),(12,171,'yes','Cisco%GPASS%Catalyst 3560-24PS'),(12,169,'yes','Cisco%GPASS%Catalyst 3560-24TS'),(12,172,'yes','Cisco%GPASS%Catalyst 3560-48PS'),(12,170,'yes','Cisco%GPASS%Catalyst 3560-48TS'),(12,141,'yes','Cisco%GPASS%Catalyst 3560-E'),(12,2059,'yes','Cisco%GPASS%Catalyst 3560CG-8PC-S'),(12,1607,'yes','Cisco%GPASS%Catalyst 3560E-12D'),(12,1721,'yes','Cisco%GPASS%Catalyst 3560E-12SD'),(12,1575,'yes','Cisco%GPASS%Catalyst 3560E-24TD'),(12,1574,'yes','Cisco%GPASS%Catalyst 3560E-48TD'),(12,175,'yes','Cisco%GPASS%Catalyst 3560G-24PS'),(12,173,'yes','Cisco%GPASS%Catalyst 3560G-24TS'),(12,176,'yes','Cisco%GPASS%Catalyst 3560G-48PS'),(12,174,'yes','Cisco%GPASS%Catalyst 3560G-48TS'),(12,1806,'yes','Cisco%GPASS%Catalyst 3560V2-24PS'),(12,1804,'yes','Cisco%GPASS%Catalyst 3560V2-24TS'),(12,1808,'yes','Cisco%GPASS%Catalyst 3560V2-24TS-SD'),(12,1807,'yes','Cisco%GPASS%Catalyst 3560V2-48PS'),(12,1805,'yes','Cisco%GPASS%Catalyst 3560V2-48TS'),(12,1578,'yes','Cisco%GPASS%Catalyst 3560X-24P'),(12,1576,'yes','Cisco%GPASS%Catalyst 3560X-24T'),(12,1579,'yes','Cisco%GPASS%Catalyst 3560X-48P'),(12,1580,'yes','Cisco%GPASS%Catalyst 3560X-48PF'),(12,1577,'yes','Cisco%GPASS%Catalyst 3560X-48T'),(12,2647,'yes','Cisco%GPASS%Catalyst 3650-12x48UQ'),(12,2648,'yes','Cisco%GPASS%Catalyst 3650-12X48UR'),(12,2649,'yes','Cisco%GPASS%Catalyst 3650-12X48UZ'),(12,2638,'yes','Cisco%GPASS%Catalyst 3650-24PD'),(12,2639,'yes','Cisco%GPASS%Catalyst 3650-24PDM'),(12,2633,'yes','Cisco%GPASS%Catalyst 3650-24PS'),(12,2636,'yes','Cisco%GPASS%Catalyst 3650-24TD'),(12,2631,'yes','Cisco%GPASS%Catalyst 3650-24TS'),(12,2641,'yes','Cisco%GPASS%Catalyst 3650-48FD'),(12,2644,'yes','Cisco%GPASS%Catalyst 3650-48FQ'),(12,2645,'yes','Cisco%GPASS%Catalyst 3650-48FQM'),(12,2635,'yes','Cisco%GPASS%Catalyst 3650-48FS'),(12,2640,'yes','Cisco%GPASS%Catalyst 3650-48PD'),(12,2643,'yes','Cisco%GPASS%Catalyst 3650-48PQ'),(12,2634,'yes','Cisco%GPASS%Catalyst 3650-48PS'),(12,2637,'yes','Cisco%GPASS%Catalyst 3650-48TD'),(12,2642,'yes','Cisco%GPASS%Catalyst 3650-48TQ'),(12,2632,'yes','Cisco%GPASS%Catalyst 3650-48TS'),(12,2646,'yes','Cisco%GPASS%Catalyst 3650-8X24UQ'),(12,180,'yes','Cisco%GPASS%Catalyst 3750-24FS'),(12,178,'yes','Cisco%GPASS%Catalyst 3750-24PS'),(12,376,'yes','Cisco%GPASS%Catalyst 3750-24TE-M'),(12,143,'yes','Cisco%GPASS%Catalyst 3750-24TS'),(12,179,'yes','Cisco%GPASS%Catalyst 3750-48PS'),(12,177,'yes','Cisco%GPASS%Catalyst 3750-48TS'),(12,144,'yes','Cisco%GPASS%Catalyst 3750-E'),(12,188,'yes','Cisco%GPASS%Catalyst 3750G-12S'),(12,187,'yes','Cisco%GPASS%Catalyst 3750G-16TD'),(12,185,'yes','Cisco%GPASS%Catalyst 3750G-24PS'),(12,181,'yes','Cisco%GPASS%Catalyst 3750G-24T'),(12,182,'yes','Cisco%GPASS%Catalyst 3750G-24TS'),(12,183,'yes','Cisco%GPASS%Catalyst 3750G-24TS-1U'),(12,189,'yes','Cisco%GPASS%Catalyst 3750G-24WS'),(12,186,'yes','Cisco%GPASS%Catalyst 3750G-48PS'),(12,184,'yes','Cisco%GPASS%Catalyst 3750G-48TS'),(12,1586,'yes','Cisco%GPASS%Catalyst 3750X-12S'),(12,1583,'yes','Cisco%GPASS%Catalyst 3750X-24P'),(12,1587,'yes','Cisco%GPASS%Catalyst 3750X-24S'),(12,1581,'yes','Cisco%GPASS%Catalyst 3750X-24T'),(12,1584,'yes','Cisco%GPASS%Catalyst 3750X-48P'),(12,1585,'yes','Cisco%GPASS%Catalyst 3750X-48PF'),(12,1582,'yes','Cisco%GPASS%Catalyst 3750X-48T'),(12,2657,'yes','Cisco%GPASS%Catalyst 3850-12S'),(12,2656,'yes','Cisco%GPASS%Catalyst 3850-12X48U'),(12,2659,'yes','Cisco%GPASS%Catalyst 3850-12XS'),(12,2650,'yes','Cisco%GPASS%Catalyst 3850-24P'),(12,2658,'yes','Cisco%GPASS%Catalyst 3850-24S'),(12,2189,'yes','Cisco%GPASS%Catalyst 3850-24T'),(12,2653,'yes','Cisco%GPASS%Catalyst 3850-24U'),(12,2660,'yes','Cisco%GPASS%Catalyst 3850-24XS'),(12,2655,'yes','Cisco%GPASS%Catalyst 3850-24XU'),(12,2652,'yes','Cisco%GPASS%Catalyst 3850-48F'),(12,2651,'yes','Cisco%GPASS%Catalyst 3850-48P'),(12,2190,'yes','Cisco%GPASS%Catalyst 3850-48T'),(12,2654,'yes','Cisco%GPASS%Catalyst 3850-48U'),(12,2661,'yes','Cisco%GPASS%Catalyst 3850-48XS'),(12,145,'yes','Cisco%GPASS%Catalyst 4503'),(12,372,'yes','Cisco%GPASS%Catalyst 4503-E'),(12,373,'yes','Cisco%GPASS%Catalyst 4506-E'),(12,374,'yes','Cisco%GPASS%Catalyst 4507R-E'),(12,375,'yes','Cisco%GPASS%Catalyst 4510R-E'),(12,2662,'yes','Cisco%GPASS%Catalyst 4510R+E'),(12,146,'yes','Cisco%GPASS%Catalyst 6513'),(12,1056,'yes','Cisco%GPASS%Catalyst CBS3030-DEL'),(12,142,'yes','Cisco%GPASS%Catalyst Express 500-24LC'),(12,159,'yes','Cisco%GPASS%Catalyst Express 500-24PC'),(12,160,'yes','Cisco%GPASS%Catalyst Express 500-24TT'),(12,161,'yes','Cisco%GPASS%Catalyst Express 500G-12TC'),(12,381,'yes','Cisco%GPASS%Catalyst WS-C2950-12'),(12,383,'yes','Cisco%GPASS%Catalyst WS-C2950G-24-DC'),(12,385,'yes','Cisco%GPASS%Catalyst WS-C2950SX-24'),(12,393,'yes','Cisco%GPASS%Catalyst WS-C3524-XL'),(12,139,'yes','Cisco%GPASS%Catalyst WS-C3560-8PC'),(12,2211,'yes','Cisco%GPASS%CGS-2520-24TC'),(12,396,'yes','Cisco%GPASS%ME 2400-24TS-A'),(12,397,'yes','Cisco%GPASS%ME 2400-24TS-D'),(12,410,'yes','Cisco%GPASS%ME 3400-24FS-A'),(12,408,'yes','Cisco%GPASS%ME 3400-24TS-A'),(12,409,'yes','Cisco%GPASS%ME 3400-24TS-D'),(12,405,'yes','Cisco%GPASS%ME 3400G-12CS-A'),(12,406,'yes','Cisco%GPASS%ME 3400G-12CS-D'),(12,407,'yes','Cisco%GPASS%ME 3400G-2CS-A'),(12,2664,'yes','Cisco%GPASS%ME 3600X-24FS-M'),(12,2663,'yes','Cisco%GPASS%ME 3600X-24TS-M'),(12,378,'yes','Cisco%GPASS%ME 4924-10GE'),(12,370,'yes','Cisco%GPASS%ME 6524GS-8S'),(12,371,'yes','Cisco%GPASS%ME 6524GT-8S'),(12,2030,'yes','Cisco%GPASS%ME-3400EG-2CS-A'),(12,2433,'yes','Cisco%GPASS%Nexus 93120TX'),(12,2434,'yes','Cisco%GPASS%Nexus 93128TX'),(12,2426,'yes','Cisco%GPASS%Nexus 9332PQ'),(12,2427,'yes','Cisco%GPASS%Nexus 9372PX'),(12,2428,'yes','Cisco%GPASS%Nexus 9372PX-E'),(12,2429,'yes','Cisco%GPASS%Nexus 9372TX'),(12,2430,'yes','Cisco%GPASS%Nexus 9372TX-E'),(12,2431,'yes','Cisco%GPASS%Nexus 9396PX'),(12,2432,'yes','Cisco%GPASS%Nexus 9396TX'),(12,2345,'yes','Cisco%GPASS%SF200-24'),(12,2347,'yes','Cisco%GPASS%SF200-24FP'),(12,2346,'yes','Cisco%GPASS%SF200-24P'),(12,2348,'yes','Cisco%GPASS%SF200-48'),(12,2349,'yes','Cisco%GPASS%SF200-48P'),(12,2357,'yes','Cisco%GPASS%SF220-24'),(12,2356,'yes','Cisco%GPASS%SF220-24P'),(12,2355,'yes','Cisco%GPASS%SF220-48'),(12,2354,'yes','Cisco%GPASS%SF220-48P'),(12,2365,'yes','Cisco%GPASS%SF300-08'),(12,2373,'yes','Cisco%GPASS%SF300-24'),(12,2375,'yes','Cisco%GPASS%SF300-24MP'),(12,1784,'yes','Cisco%GPASS%SF300-24P'),(12,2362,'yes','Cisco%GPASS%SF300-24PP'),(12,1612,'yes','Cisco%GPASS%SF300-48'),(12,2366,'yes','Cisco%GPASS%SF300-48P'),(12,2363,'yes','Cisco%GPASS%SF300-48PP'),(12,2374,'yes','Cisco%GPASS%SF302-08'),(12,2091,'yes','Cisco%GPASS%SF302-08MP'),(12,2359,'yes','Cisco%GPASS%SF302-08MPP'),(12,2372,'yes','Cisco%GPASS%SF302-08P'),(12,2358,'yes','Cisco%GPASS%SF302-08PP'),(12,2407,'yes','Cisco%GPASS%SF500-24'),(12,2409,'yes','Cisco%GPASS%SF500-24MP'),(12,2408,'yes','Cisco%GPASS%SF500-24P'),(12,2410,'yes','Cisco%GPASS%SF500-48'),(12,2412,'yes','Cisco%GPASS%SF500-48MP'),(12,2411,'yes','Cisco%GPASS%SF500-48P'),(12,2343,'yes','Cisco%GPASS%SG200-08'),(12,2344,'yes','Cisco%GPASS%SG200-08P'),(12,2342,'yes','Cisco%GPASS%SG200-10FP'),(12,2341,'yes','Cisco%GPASS%SG200-18'),(12,2338,'yes','Cisco%GPASS%SG200-26'),(12,2340,'yes','Cisco%GPASS%SG200-26FP'),(12,2339,'yes','Cisco%GPASS%SG200-26P'),(12,2335,'yes','Cisco%GPASS%SG200-50'),(12,2337,'yes','Cisco%GPASS%SG200-50FP'),(12,2336,'yes','Cisco%GPASS%SG200-50P'),(12,2353,'yes','Cisco%GPASS%SG220-26'),(12,2352,'yes','Cisco%GPASS%SG220-26P'),(12,2351,'yes','Cisco%GPASS%SG220-50'),(12,2350,'yes','Cisco%GPASS%SG220-50P'),(12,1785,'yes','Cisco%GPASS%SG300-10'),(12,2367,'yes','Cisco%GPASS%SG300-10MP'),(12,2361,'yes','Cisco%GPASS%SG300-10MPP'),(12,2368,'yes','Cisco%GPASS%SG300-10P'),(12,2360,'yes','Cisco%GPASS%SG300-10PP'),(12,2376,'yes','Cisco%GPASS%SG300-10SFP'),(12,2371,'yes','Cisco%GPASS%SG300-20'),(12,2370,'yes','Cisco%GPASS%SG300-28'),(12,2377,'yes','Cisco%GPASS%SG300-28MP'),(12,2369,'yes','Cisco%GPASS%SG300-28P'),(12,2364,'yes','Cisco%GPASS%SG300-28PP'),(12,1783,'yes','Cisco%GPASS%SG300-52'),(12,2379,'yes','Cisco%GPASS%SG300-52MP'),(12,2378,'yes','Cisco%GPASS%SG300-52P'),(12,2413,'yes','Cisco%GPASS%SG500-28'),(12,2415,'yes','Cisco%GPASS%SG500-28MPP'),(12,2414,'yes','Cisco%GPASS%SG500-28P'),(12,2416,'yes','Cisco%GPASS%SG500-52'),(12,2418,'yes','Cisco%GPASS%SG500-52MP'),(12,2417,'yes','Cisco%GPASS%SG500-52P'),(12,2419,'yes','Cisco%GPASS%SG500X-24'),(12,2421,'yes','Cisco%GPASS%SG500X-24MPP'),(12,2420,'yes','Cisco%GPASS%SG500X-24P'),(12,2422,'yes','Cisco%GPASS%SG500X-48'),(12,2424,'yes','Cisco%GPASS%SG500X-48MP'),(12,2423,'yes','Cisco%GPASS%SG500X-48P'),(12,2425,'yes','Cisco%GPASS%SG500XG-8F8T'),(12,1755,'yes','Cisco%GPASS%UCS 6120 Fabric Interconnect'),(12,1756,'yes','Cisco%GPASS%UCS 6140 Fabric Interconnect'),(12,1757,'yes','Cisco%GPASS%UCS 6248 Fabric Interconnect'),(12,1758,'yes','Cisco%GPASS%UCS 6296 Fabric Interconnect'),(12,2577,'yes','Cisco%GPASS%UCS 6332 Fabric Interconnect'),(12,2578,'yes','Cisco%GPASS%UCS 6332-16UP Fabric Interconnect'),(12,2576,'yes','Cisco%GPASS%UCS-Mini 6324 Fabric Interconnect'),(12,1797,'yes','D-Link%GPASS%DGS-1210-10P'),(12,1798,'yes','D-Link%GPASS%DGS-1210-16'),(12,1799,'yes','D-Link%GPASS%DGS-1210-24'),(12,1800,'yes','D-Link%GPASS%DGS-1210-48'),(12,1531,'yes','Dell PowerConnect (blade)%GPASS%5316M'),(12,1532,'yes','Dell PowerConnect (blade)%GPASS%M6220'),(12,1699,'yes','Dell PowerConnect (blade)%GPASS%M6348'),(12,1533,'yes','Dell PowerConnect (blade)%GPASS%M8024'),(12,1700,'yes','Dell PowerConnect (blade)%GPASS%M8428'),(12,338,'yes','Dell PowerConnect%GPASS%2216'),(12,339,'yes','Dell PowerConnect%GPASS%2224'),(12,340,'yes','Dell PowerConnect%GPASS%2324'),(12,341,'yes','Dell PowerConnect%GPASS%2708'),(12,342,'yes','Dell PowerConnect%GPASS%2716'),(12,343,'yes','Dell PowerConnect%GPASS%2724'),(12,344,'yes','Dell PowerConnect%GPASS%2748'),(12,1061,'yes','Dell PowerConnect%GPASS%2808'),(12,1062,'yes','Dell PowerConnect%GPASS%2816'),(12,1063,'yes','Dell PowerConnect%GPASS%2824'),(12,1064,'yes','Dell PowerConnect%GPASS%2848'),(12,1611,'yes','Dell PowerConnect%GPASS%3348'),(12,345,'yes','Dell PowerConnect%GPASS%3424'),(12,346,'yes','Dell PowerConnect%GPASS%3424P'),(12,347,'yes','Dell PowerConnect%GPASS%3448'),(12,348,'yes','Dell PowerConnect%GPASS%3448P'),(12,1065,'yes','Dell PowerConnect%GPASS%3524'),(12,1066,'yes','Dell PowerConnect%GPASS%3524P'),(12,1067,'yes','Dell PowerConnect%GPASS%3548'),(12,1068,'yes','Dell PowerConnect%GPASS%3548P'),(12,1622,'yes','Dell PowerConnect%GPASS%5224'),(12,349,'yes','Dell PowerConnect%GPASS%5324'),(12,1069,'yes','Dell PowerConnect%GPASS%5424'),(12,1070,'yes','Dell PowerConnect%GPASS%5448'),(12,1791,'yes','Dell PowerConnect%GPASS%5524'),(12,1792,'yes','Dell PowerConnect%GPASS%5548'),(12,1623,'yes','Dell PowerConnect%GPASS%6024F'),(12,350,'yes','Dell PowerConnect%GPASS%6224'),(12,352,'yes','Dell PowerConnect%GPASS%6224F'),(12,351,'yes','Dell PowerConnect%GPASS%6224P'),(12,353,'yes','Dell PowerConnect%GPASS%6248'),(12,354,'yes','Dell PowerConnect%GPASS%6248P'),(12,1702,'yes','Dell PowerConnect%GPASS%8024'),(12,1703,'yes','Dell PowerConnect%GPASS%8024F'),(12,2097,'yes','Dell PowerConnect%GPASS%8132'),(12,2098,'yes','Dell PowerConnect%GPASS%8132F'),(12,2099,'yes','Dell PowerConnect%GPASS%8164'),(12,2100,'yes','Dell PowerConnect%GPASS%8164F'),(12,2165,'yes','Dell%GPASS%N2024'),(12,2166,'yes','Dell%GPASS%N2024P'),(12,2167,'yes','Dell%GPASS%N2048'),(12,2168,'yes','Dell%GPASS%N2048P'),(12,2160,'yes','Dell%GPASS%N3024'),(12,2161,'yes','Dell%GPASS%N3024F'),(12,2162,'yes','Dell%GPASS%N3024P'),(12,2163,'yes','Dell%GPASS%N3048'),(12,2164,'yes','Dell%GPASS%N3048P'),(12,2156,'yes','Dell%GPASS%N4032'),(12,2157,'yes','Dell%GPASS%N4032F'),(12,2158,'yes','Dell%GPASS%N4064'),(12,2159,'yes','Dell%GPASS%N4064F'),(12,2291,'yes','Dell%GPASS%S5000'),(12,2292,'yes','Dell%GPASS%S6000'),(12,2154,'yes','Dell%GPASS%Z9000'),(12,2155,'yes','Dell%GPASS%Z9500'),(12,2005,'yes','Enterasys%GPASS%08G20G2-08'),(12,2006,'yes','Enterasys%GPASS%08G20G2-08P'),(12,2007,'yes','Enterasys%GPASS%08G20G4-24'),(12,2008,'yes','Enterasys%GPASS%08G20G4-24P'),(12,2009,'yes','Enterasys%GPASS%08G20G4-48'),(12,2010,'yes','Enterasys%GPASS%08G20G4-48P'),(12,2011,'yes','Enterasys%GPASS%08H20G4-24'),(12,2012,'yes','Enterasys%GPASS%08H20G4-24P'),(12,2013,'yes','Enterasys%GPASS%08H20G4-48'),(12,2014,'yes','Enterasys%GPASS%08H20G4-48P'),(12,2001,'yes','Enterasys%GPASS%7124-24'),(12,2002,'yes','Enterasys%GPASS%7124-24T'),(12,2003,'yes','Enterasys%GPASS%7124-48'),(12,2004,'yes','Enterasys%GPASS%7124-48T'),(12,1978,'yes','Enterasys%GPASS%A4H124-24'),(12,1980,'yes','Enterasys%GPASS%A4H124-24FX'),(12,1979,'yes','Enterasys%GPASS%A4H124-24P'),(12,1981,'yes','Enterasys%GPASS%A4H124-48'),(12,1982,'yes','Enterasys%GPASS%A4H124-48P'),(12,1983,'yes','Enterasys%GPASS%A4H254-8F8T'),(12,1984,'yes','Enterasys%GPASS%B5G124-24'),(12,1985,'yes','Enterasys%GPASS%B5G124-24P2'),(12,1986,'yes','Enterasys%GPASS%B5G124-48'),(12,1987,'yes','Enterasys%GPASS%B5G124-48P2'),(12,1988,'yes','Enterasys%GPASS%B5K125-24'),(12,1989,'yes','Enterasys%GPASS%B5K125-24P2'),(12,1990,'yes','Enterasys%GPASS%B5K125-48'),(12,1991,'yes','Enterasys%GPASS%B5K125-48P2'),(12,1992,'yes','Enterasys%GPASS%C5G124-24'),(12,1993,'yes','Enterasys%GPASS%C5G124-24P2'),(12,1994,'yes','Enterasys%GPASS%C5G124-48'),(12,1995,'yes','Enterasys%GPASS%C5G124-48P2'),(12,1996,'yes','Enterasys%GPASS%C5K125-24'),(12,1997,'yes','Enterasys%GPASS%C5K125-24P2'),(12,1998,'yes','Enterasys%GPASS%C5K125-48'),(12,1999,'yes','Enterasys%GPASS%C5K125-48P2'),(12,2000,'yes','Enterasys%GPASS%C5K175-24'),(12,2015,'yes','Enterasys%GPASS%D2G124-12'),(12,2016,'yes','Enterasys%GPASS%D2G124-12P'),(12,2017,'yes','Enterasys%GPASS%G3G124-24'),(12,2018,'yes','Enterasys%GPASS%G3G124-24P'),(12,2019,'yes','Enterasys%GPASS%G3G170-24'),(12,1970,'yes','Enterasys%GPASS%SSA130'),(12,1971,'yes','Enterasys%GPASS%SSA150'),(12,1972,'yes','Enterasys%GPASS%SSA180'),(12,2294,'yes','Extreme Networks%GPASS%Summit X430-24p'),(12,2295,'yes','Extreme Networks%GPASS%Summit X430-24t'),(12,2296,'yes','Extreme Networks%GPASS%Summit X430-48t'),(12,2293,'yes','Extreme Networks%GPASS%Summit X430-8p'),(12,2301,'yes','Extreme Networks%GPASS%Summit X440-24p'),(12,2307,'yes','Extreme Networks%GPASS%Summit X440-24p-10G'),(12,2299,'yes','Extreme Networks%GPASS%Summit X440-24t'),(12,2306,'yes','Extreme Networks%GPASS%Summit X440-24t-10G'),(12,2300,'yes','Extreme Networks%GPASS%Summit X440-24tDC'),(12,2302,'yes','Extreme Networks%GPASS%Summit X440-24x'),(12,2308,'yes','Extreme Networks%GPASS%Summit X440-24x-10G'),(12,2305,'yes','Extreme Networks%GPASS%Summit X440-48p'),(12,2303,'yes','Extreme Networks%GPASS%Summit X440-48t'),(12,2304,'yes','Extreme Networks%GPASS%Summit X440-48tDC'),(12,2298,'yes','Extreme Networks%GPASS%Summit X440-8p'),(12,2297,'yes','Extreme Networks%GPASS%Summit X440-8t'),(12,2313,'yes','Extreme Networks%GPASS%Summit X460-24p'),(12,2309,'yes','Extreme Networks%GPASS%Summit X460-24t'),(12,2311,'yes','Extreme Networks%GPASS%Summit X460-24x'),(12,2314,'yes','Extreme Networks%GPASS%Summit X460-48p'),(12,2310,'yes','Extreme Networks%GPASS%Summit X460-48t'),(12,2312,'yes','Extreme Networks%GPASS%Summit X460-48x'),(12,2319,'yes','Extreme Networks%GPASS%Summit X460-G2-24p-10GE4'),(12,2323,'yes','Extreme Networks%GPASS%Summit X460-G2-24p-GE4'),(12,2315,'yes','Extreme Networks%GPASS%Summit X460-G2-24t-10GE4'),(12,2321,'yes','Extreme Networks%GPASS%Summit X460-G2-24t-GE4'),(12,2317,'yes','Extreme Networks%GPASS%Summit X460-G2-24x-10GE4'),(12,2320,'yes','Extreme Networks%GPASS%Summit X460-G2-48p-10GE4'),(12,2324,'yes','Extreme Networks%GPASS%Summit X460-G2-48p-GE4'),(12,2316,'yes','Extreme Networks%GPASS%Summit X460-G2-48t-10GE4'),(12,2322,'yes','Extreme Networks%GPASS%Summit X460-G2-48t-GE4'),(12,2318,'yes','Extreme Networks%GPASS%Summit X460-G2-48x-10GE4'),(12,1801,'yes','Extreme Networks%GPASS%Summit X670-48x'),(12,2325,'yes','Extreme Networks%GPASS%Summit X670-G2-48x-4q'),(12,2326,'yes','Extreme Networks%GPASS%Summit X670-G2-72x'),(12,1803,'yes','Extreme Networks%GPASS%Summit X670V-48t'),(12,1802,'yes','Extreme Networks%GPASS%Summit X670V-48x'),(12,2065,'yes','Extreme Networks%GPASS%Summit X770'),(12,2035,'yes','F5 (blade)%GPASS%VIPRION 2100'),(12,2036,'yes','F5 (blade)%GPASS%VIPRION 4200'),(12,2037,'yes','F5 (blade)%GPASS%VIPRION 4300'),(12,545,'yes','Force10%GPASS%C150'),(12,546,'yes','Force10%GPASS%C300'),(12,549,'yes','Force10%GPASS%E1200'),(12,547,'yes','Force10%GPASS%E300'),(12,548,'yes','Force10%GPASS%E600'),(12,541,'yes','Force10%GPASS%S2410CP'),(12,683,'yes','Force10%GPASS%S2410P'),(12,544,'yes','Force10%GPASS%S25P'),(12,1962,'yes','Force10%GPASS%S4820T'),(12,542,'yes','Force10%GPASS%S50N'),(12,543,'yes','Force10%GPASS%S50V'),(12,190,'yes','Foundry%GPASS%EdgeIron 2402CF'),(12,191,'yes','Foundry%GPASS%EdgeIron 24G'),(12,194,'yes','Foundry%GPASS%EdgeIron 24GS'),(12,192,'yes','Foundry%GPASS%EdgeIron 4802CF'),(12,193,'yes','Foundry%GPASS%EdgeIron 48G'),(12,195,'yes','Foundry%GPASS%EdgeIron 48GS'),(12,196,'yes','Foundry%GPASS%EdgeIron 8X10G'),(12,198,'yes','Foundry%GPASS%FastIron Edge 12GCF'),(12,199,'yes','Foundry%GPASS%FastIron Edge 12GCF-PREM'),(12,121,'yes','Foundry%GPASS%FastIron Edge 2402'),(12,202,'yes','Foundry%GPASS%FastIron Edge 2402-POE'),(12,128,'yes','Foundry%GPASS%FastIron Edge 2402-PREM'),(12,122,'yes','Foundry%GPASS%FastIron Edge 4802'),(12,203,'yes','Foundry%GPASS%FastIron Edge 4802-POE'),(12,197,'yes','Foundry%GPASS%FastIron Edge 4802-PREM'),(12,200,'yes','Foundry%GPASS%FastIron Edge 9604'),(12,201,'yes','Foundry%GPASS%FastIron Edge 9604-PREM'),(12,123,'yes','Foundry%GPASS%FastIron Edge X424'),(12,136,'yes','Foundry%GPASS%FastIron Edge X424-POE'),(12,135,'yes','Foundry%GPASS%FastIron Edge X424HF'),(12,134,'yes','Foundry%GPASS%FastIron Edge X448'),(12,129,'yes','Foundry%GPASS%FastIron GS 624P'),(12,130,'yes','Foundry%GPASS%FastIron GS 624P-POE'),(12,411,'yes','Foundry%GPASS%FastIron GS 624XGP'),(12,412,'yes','Foundry%GPASS%FastIron GS 624XGP-POE'),(12,127,'yes','Foundry%GPASS%FastIron GS 648P'),(12,131,'yes','Foundry%GPASS%FastIron GS 648P-POE'),(12,117,'yes','Foundry%GPASS%FastIron II'),(12,413,'yes','Foundry%GPASS%FastIron LS 624'),(12,414,'yes','Foundry%GPASS%FastIron LS 648'),(12,125,'yes','Foundry%GPASS%FastIron SuperX'),(12,138,'yes','Foundry%GPASS%FastIron SX 1600'),(12,137,'yes','Foundry%GPASS%FastIron SX 800'),(12,116,'yes','Foundry%GPASS%FastIron WorkGroup'),(12,204,'yes','Foundry%GPASS%FastIron Workgroup X424'),(12,205,'yes','Foundry%GPASS%FastIron Workgroup X448'),(12,416,'yes','Foundry%GPASS%NetIron M2404C'),(12,415,'yes','Foundry%GPASS%NetIron M2404F'),(12,118,'yes','Foundry%GPASS%ServerIron'),(12,120,'yes','Foundry%GPASS%ServerIron 350'),(12,206,'yes','Foundry%GPASS%ServerIron 450'),(12,132,'yes','Foundry%GPASS%ServerIron 4G'),(12,133,'yes','Foundry%GPASS%ServerIron 4G-SSL'),(12,207,'yes','Foundry%GPASS%ServerIron 850'),(12,208,'yes','Foundry%GPASS%ServerIron GT C'),(12,209,'yes','Foundry%GPASS%ServerIron GT E'),(12,119,'yes','Foundry%GPASS%ServerIron XL'),(12,1179,'yes','Hitachi Cable%GPASS%Apresia13000-24GX-PSR'),(12,1180,'yes','Hitachi Cable%GPASS%Apresia13000-48X'),(12,1189,'yes','Hitachi Cable%GPASS%Apresia18005'),(12,1188,'yes','Hitachi Cable%GPASS%Apresia18008'),(12,1187,'yes','Hitachi Cable%GPASS%Apresia18020'),(12,1178,'yes','Hitachi Cable%GPASS%Apresia2124-SS2'),(12,1177,'yes','Hitachi Cable%GPASS%Apresia2124GT-SS2'),(12,1176,'yes','Hitachi Cable%GPASS%Apresia2124GT2'),(12,1175,'yes','Hitachi Cable%GPASS%Apresia2248G2'),(12,1169,'yes','Hitachi Cable%GPASS%Apresia3108FG2'),(12,1170,'yes','Hitachi Cable%GPASS%Apresia3124GT-HR2'),(12,1185,'yes','Hitachi Cable%GPASS%Apresia3124GT-PSR2'),(12,1186,'yes','Hitachi Cable%GPASS%Apresia3124GT2'),(12,1183,'yes','Hitachi Cable%GPASS%Apresia3248G-PSR2'),(12,1184,'yes','Hitachi Cable%GPASS%Apresia3248G2'),(12,1182,'yes','Hitachi Cable%GPASS%Apresia3424GT-PoE'),(12,1181,'yes','Hitachi Cable%GPASS%Apresia3424GT-SS'),(12,1174,'yes','Hitachi Cable%GPASS%Apresia4224GT-PSR'),(12,1173,'yes','Hitachi Cable%GPASS%Apresia4328GT'),(12,1171,'yes','Hitachi Cable%GPASS%Apresia4348GT'),(12,1172,'yes','Hitachi Cable%GPASS%Apresia4348GT-PSR'),(12,1192,'yes','Hitachi Cable%GPASS%Apresia6148G-PSR'),(12,1193,'yes','Hitachi Cable%GPASS%Apresia6148GT-PSR'),(12,1191,'yes','Hitachi Cable%GPASS%Apresia8004'),(12,1190,'yes','Hitachi Cable%GPASS%Apresia8007'),(12,1194,'yes','Hitachi Cable%GPASS%VXC-1024FE'),(12,2467,'yes','HP EI%GPASS%5130-24G-2SFP+-2XGT (JG938A)'),(12,2462,'yes','HP EI%GPASS%5130-24G-4SFP+ (JG932A)'),(12,2469,'yes','HP EI%GPASS%5130-24G-PoE+-2SFP+-2XGT (JG940A)'),(12,2465,'yes','HP EI%GPASS%5130-24G-PoE+-4SFP+ (JG936A)'),(12,2463,'yes','HP EI%GPASS%5130-24G-SFP-4SFP+ (JG933A)'),(12,2468,'yes','HP EI%GPASS%5130-48G-2SFP+-2XGT (JG939A)'),(12,2464,'yes','HP EI%GPASS%5130-48G-4SFP+ (JG934A)'),(12,2470,'yes','HP EI%GPASS%5130-48G-PoE+-2SFP+-2XGT (JG941A)'),(12,2466,'yes','HP EI%GPASS%5130-48G-PoE+-4SFP+ (JG937A)'),(12,1605,'yes','HP GbE2c w/SFP'),(12,1629,'yes','HP ProCurve (blade)%GPASS%5400zl 20 1Gb + 4 Mini-GBIC J8705A'),(12,1634,'yes','HP ProCurve (blade)%GPASS%5400zl 20 1Gb-PoE+ + 4 Mini-GBIC J9308A'),(12,1636,'yes','HP ProCurve (blade)%GPASS%5400zl 24 100Mb PoE+ J9478A'),(12,1628,'yes','HP ProCurve (blade)%GPASS%5400zl 24 1Gb-PoE J8702A'),(12,1633,'yes','HP ProCurve (blade)%GPASS%5400zl 24 1Gb-PoE+ J9307A'),(12,1630,'yes','HP ProCurve (blade)%GPASS%5400zl 24 Mini-GBIC J8706A'),(12,1632,'yes','HP ProCurve (blade)%GPASS%5400zl 4 10GbE CX4 J8708A'),(12,1635,'yes','HP ProCurve (blade)%GPASS%5400zl 4 10GbE SFP+ J9309A'),(12,1631,'yes','HP ProCurve (blade)%GPASS%5400zl 4 10GbE X2 J8707A'),(12,1627,'yes','HP ProCurve (blade)%GPASS%5400zl Management Module J8726A'),(12,848,'yes','HP ProCurve%GPASS%1400-24G'),(12,849,'yes','HP ProCurve%GPASS%1700-24'),(12,850,'yes','HP ProCurve%GPASS%1800-24G'),(12,2242,'yes','HP ProCurve%GPASS%1810G-24'),(12,2176,'yes','HP ProCurve%GPASS%1910-24G'),(12,2238,'yes','HP ProCurve%GPASS%1910-48G'),(12,851,'yes','HP ProCurve%GPASS%2124'),(12,852,'yes','HP ProCurve%GPASS%2312'),(12,853,'yes','HP ProCurve%GPASS%2324'),(12,854,'yes','HP ProCurve%GPASS%2510-24'),(12,855,'yes','HP ProCurve%GPASS%2510-48'),(12,856,'yes','HP ProCurve%GPASS%2510G-24'),(12,857,'yes','HP ProCurve%GPASS%2510G-48'),(12,858,'yes','HP ProCurve%GPASS%2512'),(12,1711,'yes','HP ProCurve%GPASS%2520-24-PoE J9138A'),(12,1967,'yes','HP ProCurve%GPASS%2520-8-PoE J9137A'),(12,859,'yes','HP ProCurve%GPASS%2524'),(12,1423,'yes','HP ProCurve%GPASS%2600-8-PWR'),(12,864,'yes','HP ProCurve%GPASS%2626'),(12,865,'yes','HP ProCurve%GPASS%2626-PWR'),(12,866,'yes','HP ProCurve%GPASS%2650'),(12,867,'yes','HP ProCurve%GPASS%2650-PWR'),(12,868,'yes','HP ProCurve%GPASS%2810-24G'),(12,869,'yes','HP ProCurve%GPASS%2810-48G J9022A'),(12,870,'yes','HP ProCurve%GPASS%2824'),(12,871,'yes','HP ProCurve%GPASS%2848'),(12,872,'yes','HP ProCurve%GPASS%2900-24G'),(12,873,'yes','HP ProCurve%GPASS%2900-48G'),(12,2096,'yes','HP ProCurve%GPASS%2910-24G-PoE J9146A'),(12,2213,'yes','HP ProCurve%GPASS%2920-48G J9728A'),(12,874,'yes','HP ProCurve%GPASS%3400cl-24G'),(12,875,'yes','HP ProCurve%GPASS%3400cl-48G'),(12,876,'yes','HP ProCurve%GPASS%3500yl-24G-PWR'),(12,877,'yes','HP ProCurve%GPASS%3500yl-48G-PWR'),(12,1086,'yes','HP ProCurve%GPASS%4000M'),(12,878,'yes','HP ProCurve%GPASS%4202vl-72'),(12,879,'yes','HP ProCurve%GPASS%4204vl'),(12,880,'yes','HP ProCurve%GPASS%4204vl-48GS'),(12,881,'yes','HP ProCurve%GPASS%4208vl'),(12,882,'yes','HP ProCurve%GPASS%4208vl-72GS'),(12,883,'yes','HP ProCurve%GPASS%4208vl-96'),(12,884,'yes','HP ProCurve%GPASS%5304xl'),(12,885,'yes','HP ProCurve%GPASS%5308xl'),(12,886,'yes','HP ProCurve%GPASS%5348xl'),(12,887,'yes','HP ProCurve%GPASS%5372xl'),(12,892,'yes','HP ProCurve%GPASS%6108'),(12,893,'yes','HP ProCurve%GPASS%6200yl-24G-mGBIC'),(12,894,'yes','HP ProCurve%GPASS%6400cl'),(12,895,'yes','HP ProCurve%GPASS%6410cl'),(12,1637,'yes','HP ProCurve%GPASS%6600-24G J9263A'),(12,1638,'yes','HP ProCurve%GPASS%6600-24G-4XG J9264A'),(12,1639,'yes','HP ProCurve%GPASS%6600-24XG J9265A'),(12,1640,'yes','HP ProCurve%GPASS%6600-48G J9451A'),(12,1641,'yes','HP ProCurve%GPASS%6600-48G-4XG J9452A'),(12,896,'yes','HP ProCurve%GPASS%8108fl'),(12,897,'yes','HP ProCurve%GPASS%8116fl'),(12,898,'yes','HP ProCurve%GPASS%8212zl'),(12,860,'yes','HP ProCurve%GPASS%E2610-24 J9085A'),(12,861,'yes','HP ProCurve%GPASS%E2610-24-PoE J9087A'),(12,1570,'yes','HP ProCurve%GPASS%E2610-24/12-PoE J9086A'),(12,862,'yes','HP ProCurve%GPASS%E2610-48 J9088A'),(12,863,'yes','HP ProCurve%GPASS%E2610-48-PoE J9089A'),(12,2396,'yes','HP ProCurve%GPASS%E2620-24-PoE J9625A'),(12,1571,'yes','HP ProCurve%GPASS%E2910-24G'),(12,1349,'yes','HP ProCurve%GPASS%E2910-48G J9147A'),(12,1600,'yes','HP ProCurve%GPASS%E2910-48G-PoE+ J9148A'),(12,1780,'yes','Huawei%GPASS%CE12804'),(12,1781,'yes','Huawei%GPASS%CE12808'),(12,1782,'yes','Huawei%GPASS%CE12812'),(12,2088,'yes','Huawei%GPASS%CE5810-24T4S-EI'),(12,2087,'yes','Huawei%GPASS%CE5810-48T4S-EI'),(12,1769,'yes','Huawei%GPASS%CE5850-48T4S2Q-EI'),(12,2382,'yes','Huawei%GPASS%CE5850-48T4S2Q-HI'),(12,2604,'yes','Huawei%GPASS%CE5855-24T4S2Q-EI'),(12,2605,'yes','Huawei%GPASS%CE5855-48T4S2Q-EI'),(12,2602,'yes','Huawei%GPASS%CE6810-24S2Q-LI'),(12,2603,'yes','Huawei%GPASS%CE6810-32T16S4Q-LI'),(12,2601,'yes','Huawei%GPASS%CE6810-48S-LI'),(12,2599,'yes','Huawei%GPASS%CE6810-48S4Q-EI'),(12,2600,'yes','Huawei%GPASS%CE6810-48S4Q-LI'),(12,1772,'yes','Huawei%GPASS%CE6850-48S4Q-EI'),(12,2598,'yes','Huawei%GPASS%CE6850-48S6Q-HI'),(12,1773,'yes','Huawei%GPASS%CE6850-48T4Q-EI'),(12,2596,'yes','Huawei%GPASS%CE6850-48T6Q-HI'),(12,2592,'yes','Huawei%GPASS%CE6850U-24S2Q-HI'),(12,2593,'yes','Huawei%GPASS%CE6850U-48S6Q-HI'),(12,2594,'yes','Huawei%GPASS%CE6851-48S6Q-HI'),(12,2597,'yes','Huawei%GPASS%CE6855-48S6Q-HI'),(12,2595,'yes','Huawei%GPASS%CE6855-48T6Q-HI'),(12,2591,'yes','Huawei%GPASS%CE6860-48S8CQ-EI'),(12,2590,'yes','Huawei%GPASS%CE6870-24S6CQ-EI'),(12,2589,'yes','Huawei%GPASS%CE6870-48S6CQ-EI'),(12,2226,'yes','Huawei%GPASS%CE7850-32Q-EI'),(12,2588,'yes','Huawei%GPASS%CE7855-32Q-EI'),(12,2557,'yes','Huawei%GPASS%CE8860-4C-EI'),(12,1613,'yes','Huawei%GPASS%S2309TP-EI'),(12,1620,'yes','Huawei%GPASS%S2309TP-PWR-EI'),(12,1614,'yes','Huawei%GPASS%S2309TP-SI'),(12,1615,'yes','Huawei%GPASS%S2318TP-EI'),(12,1616,'yes','Huawei%GPASS%S2318TP-SI'),(12,1617,'yes','Huawei%GPASS%S2326TP-EI'),(12,1621,'yes','Huawei%GPASS%S2326TP-PWR-EI'),(12,1618,'yes','Huawei%GPASS%S2326TP-SI'),(12,1619,'yes','Huawei%GPASS%S2352P-EI'),(12,1909,'yes','Huawei%GPASS%S2700-18TP-EI'),(12,1910,'yes','Huawei%GPASS%S2700-18TP-SI'),(12,1911,'yes','Huawei%GPASS%S2700-26TP-EI'),(12,1912,'yes','Huawei%GPASS%S2700-26TP-PWR-EI'),(12,1913,'yes','Huawei%GPASS%S2700-26TP-SI'),(12,1914,'yes','Huawei%GPASS%S2700-52P-EI'),(12,1915,'yes','Huawei%GPASS%S2700-52P-PWR-EI'),(12,1916,'yes','Huawei%GPASS%S2700-9TP-EI'),(12,1917,'yes','Huawei%GPASS%S2700-9TP-PWR-EI'),(12,1918,'yes','Huawei%GPASS%S2700-9TP-SI'),(12,1919,'yes','Huawei%GPASS%S2710-52P-PWR-SI'),(12,1920,'yes','Huawei%GPASS%S2710-52P-SI'),(12,1921,'yes','Huawei%GPASS%S3700-26C-HI'),(12,1922,'yes','Huawei%GPASS%S3700-28TP-EI'),(12,1760,'yes','Huawei%GPASS%S3700-28TP-EI-24S'),(12,1923,'yes','Huawei%GPASS%S3700-28TP-EI-MC'),(12,1761,'yes','Huawei%GPASS%S3700-28TP-EI-MC-AC'),(12,1762,'yes','Huawei%GPASS%S3700-28TP-PWR-EI'),(12,1924,'yes','Huawei%GPASS%S3700-28TP-PWR-SI'),(12,1925,'yes','Huawei%GPASS%S3700-28TP-SI'),(12,1926,'yes','Huawei%GPASS%S3700-52P-EI'),(12,1927,'yes','Huawei%GPASS%S3700-52P-EI-24S'),(12,1928,'yes','Huawei%GPASS%S3700-52P-EI-48S'),(12,1929,'yes','Huawei%GPASS%S3700-52P-PWR-EI'),(12,1930,'yes','Huawei%GPASS%S3700-52P-PWR-SI'),(12,1931,'yes','Huawei%GPASS%S3700-52P-SI'),(12,1344,'yes','Huawei%GPASS%S5324TP-PWR-SI'),(12,1343,'yes','Huawei%GPASS%S5324TP-SI'),(12,1335,'yes','Huawei%GPASS%S5328C-EI'),(12,1321,'yes','Huawei%GPASS%S5328C-EI-24S'),(12,1336,'yes','Huawei%GPASS%S5328C-PWR-EI'),(12,1340,'yes','Huawei%GPASS%S5328C-PWR-SI'),(12,1339,'yes','Huawei%GPASS%S5328C-SI'),(12,1346,'yes','Huawei%GPASS%S5348TP-PWR-SI'),(12,1345,'yes','Huawei%GPASS%S5348TP-SI'),(12,1337,'yes','Huawei%GPASS%S5352C-EI'),(12,1338,'yes','Huawei%GPASS%S5352C-PWR-EI'),(12,1342,'yes','Huawei%GPASS%S5352C-PWR-SI'),(12,1341,'yes','Huawei%GPASS%S5352C-SI'),(12,1932,'yes','Huawei%GPASS%S5700-10P-LI'),(12,1933,'yes','Huawei%GPASS%S5700-10P-PWR-LI'),(12,1934,'yes','Huawei%GPASS%S5700-24TP-PWR-SI'),(12,1935,'yes','Huawei%GPASS%S5700-24TP-SI'),(12,1936,'yes','Huawei%GPASS%S5700-26X-SI-12S'),(12,1937,'yes','Huawei%GPASS%S5700-28C-EI'),(12,1938,'yes','Huawei%GPASS%S5700-28C-EI-24S'),(12,1763,'yes','Huawei%GPASS%S5700-28C-HI'),(12,1939,'yes','Huawei%GPASS%S5700-28C-HI-24S'),(12,1764,'yes','Huawei%GPASS%S5700-28C-PWR-EI'),(12,1940,'yes','Huawei%GPASS%S5700-28C-PWR-SI'),(12,1941,'yes','Huawei%GPASS%S5700-28C-SI'),(12,1942,'yes','Huawei%GPASS%S5700-28P-LI'),(12,2484,'yes','Huawei%GPASS%S5700-28P-LI-24S'),(12,1943,'yes','Huawei%GPASS%S5700-28P-PWR-LI'),(12,2488,'yes','Huawei%GPASS%S5700-28TP-LI'),(12,2489,'yes','Huawei%GPASS%S5700-28TP-PWR-LI'),(12,1944,'yes','Huawei%GPASS%S5700-28X-LI'),(12,2483,'yes','Huawei%GPASS%S5700-28X-LI-24S'),(12,1945,'yes','Huawei%GPASS%S5700-28X-PWR-LI'),(12,1765,'yes','Huawei%GPASS%S5700-48TP-PWR-SI'),(12,1946,'yes','Huawei%GPASS%S5700-48TP-SI'),(12,1947,'yes','Huawei%GPASS%S5700-52C-EI'),(12,1948,'yes','Huawei%GPASS%S5700-52C-PWR-EI'),(12,1949,'yes','Huawei%GPASS%S5700-52C-PWR-SI'),(12,1950,'yes','Huawei%GPASS%S5700-52C-SI'),(12,1951,'yes','Huawei%GPASS%S5700-52P-LI'),(12,1767,'yes','Huawei%GPASS%S5700-52P-PWR-LI'),(12,1952,'yes','Huawei%GPASS%S5700-52X-LI'),(12,2485,'yes','Huawei%GPASS%S5700-52X-LI-48CS'),(12,1953,'yes','Huawei%GPASS%S5700-52X-PWR-LI'),(12,1768,'yes','Huawei%GPASS%S5700S-28P-LI'),(12,1954,'yes','Huawei%GPASS%S5700S-52P-LI'),(12,2490,'yes','Huawei%GPASS%S5701-28TP-PWR-LI'),(12,2486,'yes','Huawei%GPASS%S5701-28X-LI'),(12,2487,'yes','Huawei%GPASS%S5701-28X-LI-24S'),(12,1955,'yes','Huawei%GPASS%S5710-28C-EI'),(12,1956,'yes','Huawei%GPASS%S5710-28C-LI'),(12,1957,'yes','Huawei%GPASS%S5710-28C-PWR-EI'),(12,1958,'yes','Huawei%GPASS%S5710-28C-PWR-LI'),(12,1766,'yes','Huawei%GPASS%S5710-52C-EI'),(12,1959,'yes','Huawei%GPASS%S5710-52C-LI'),(12,1960,'yes','Huawei%GPASS%S5710-52C-PWR-EI'),(12,1961,'yes','Huawei%GPASS%S5710-52C-PWR-LI'),(12,2491,'yes','Huawei%GPASS%S5720-32P-EI'),(12,2492,'yes','Huawei%GPASS%S5720-32X-EI'),(12,2493,'yes','Huawei%GPASS%S5720-32X-EI-24S'),(12,2494,'yes','Huawei%GPASS%S5720-36C-EI'),(12,2497,'yes','Huawei%GPASS%S5720-36C-EI-28S'),(12,2495,'yes','Huawei%GPASS%S5720-36C-PWR-EI'),(12,2496,'yes','Huawei%GPASS%S5720-36PC-EI'),(12,2498,'yes','Huawei%GPASS%S5720-50X-EI'),(12,2499,'yes','Huawei%GPASS%S5720-50X-EI-46S'),(12,2501,'yes','Huawei%GPASS%S5720-52P-EI'),(12,2500,'yes','Huawei%GPASS%S5720-52X-EI'),(12,2503,'yes','Huawei%GPASS%S5720-56C-EI'),(12,2502,'yes','Huawei%GPASS%S5720-56C-EI-48S'),(12,2505,'yes','Huawei%GPASS%S5720-56C-PWR-EI'),(12,2504,'yes','Huawei%GPASS%S5720-56PC-EI'),(12,1770,'yes','Huawei%GPASS%S6700-24-EI'),(12,1771,'yes','Huawei%GPASS%S6700-48-EI'),(12,2606,'yes','Huawei%GPASS%S6720-30C-EI-24S'),(12,2607,'yes','Huawei%GPASS%S6720-54C-EI-48S'),(12,2608,'yes','Huawei%GPASS%S6720S-26Q-EI-24S'),(12,1774,'yes','Huawei%GPASS%S7703'),(12,1775,'yes','Huawei%GPASS%S7706'),(12,1776,'yes','Huawei%GPASS%S7712'),(12,1357,'yes','Huawei%GPASS%S9303'),(12,1358,'yes','Huawei%GPASS%S9306'),(12,1359,'yes','Huawei%GPASS%S9312'),(12,1777,'yes','Huawei%GPASS%S9703'),(12,1778,'yes','Huawei%GPASS%S9706'),(12,1779,'yes','Huawei%GPASS%S9712'),(12,2395,'yes','Juniper%GPASS%EX2200-24P-4G'),(12,2506,'yes','Juniper%GPASS%QFX10000'),(12,2510,'yes','Juniper%GPASS%QFX3500'),(12,2509,'yes','Juniper%GPASS%QFX3600'),(12,2508,'yes','Juniper%GPASS%QFX5100'),(12,2507,'yes','Juniper%GPASS%QFX5200'),(12,2212,'yes','Linksys%GPASS%SRW2024P'),(12,1624,'yes','Linksys%GPASS%SRW2048'),(12,1966,'yes','Linksys%GPASS%SRW224G4'),(12,2093,'yes','Linksys%GPASS%SRW248G4'),(12,2515,'yes','Mellanox%GPASS%CS7500'),(12,2516,'yes','Mellanox%GPASS%CS7510'),(12,2517,'yes','Mellanox%GPASS%CS7520'),(12,2513,'yes','Mellanox%GPASS%SB7700'),(12,2514,'yes','Mellanox%GPASS%SB7790'),(12,2511,'yes','Mellanox%GPASS%SB7800'),(12,2512,'yes','Mellanox%GPASS%SB7890'),(12,2534,'yes','Mellanox%GPASS%SN2100'),(12,2533,'yes','Mellanox%GPASS%SN2410'),(12,2532,'yes','Mellanox%GPASS%SN2700'),(12,2072,'yes','Mellanox%GPASS%SX1012'),(12,2073,'yes','Mellanox%GPASS%SX1016'),(12,2074,'yes','Mellanox%GPASS%SX1024'),(12,2531,'yes','Mellanox%GPASS%SX1024(52)'),(12,2075,'yes','Mellanox%GPASS%SX1035'),(12,2076,'yes','Mellanox%GPASS%SX1036'),(12,2530,'yes','Mellanox%GPASS%SX1410'),(12,2529,'yes','Mellanox%GPASS%SX1710'),(12,2519,'yes','Mellanox%GPASS%SX6005'),(12,2520,'yes','Mellanox%GPASS%SX6012'),(12,2521,'yes','Mellanox%GPASS%SX6015'),(12,2522,'yes','Mellanox%GPASS%SX6018'),(12,2523,'yes','Mellanox%GPASS%SX6025'),(12,2524,'yes','Mellanox%GPASS%SX6036'),(12,2525,'yes','Mellanox%GPASS%SX6506'),(12,2526,'yes','Mellanox%GPASS%SX6512'),(12,2527,'yes','Mellanox%GPASS%SX6518'),(12,2528,'yes','Mellanox%GPASS%SX6536'),(12,2518,'yes','Mellanox%GPASS%SX6710'),(12,2062,'yes','MikroTik%GPASS%CRS125-24G-1S-RM'),(12,2381,'yes','MikroTik%GPASS%CRS226-24G-2S+RM'),(12,2170,'yes','NEC%GPASS%PF5220F-20S2XW'),(12,2169,'yes','NEC%GPASS%PF5220F-24T2XW'),(12,1810,'yes','NEC%GPASS%PF5240'),(12,2077,'yes','NEC%GPASS%PF5248'),(12,2171,'yes','NEC%GPASS%PF5459-48GT-4X2Q'),(12,2172,'yes','NEC%GPASS%PF5459-48XP-4Q'),(12,1811,'yes','NEC%GPASS%PF5820'),(12,555,'yes','NETGEAR%GPASS%FS524'),(12,571,'yes','NETGEAR%GPASS%FS726T'),(12,578,'yes','NETGEAR%GPASS%FS726TP'),(12,568,'yes','NETGEAR%GPASS%FS728TP'),(12,570,'yes','NETGEAR%GPASS%FS728TS'),(12,577,'yes','NETGEAR%GPASS%FS750T2'),(12,576,'yes','NETGEAR%GPASS%FS752TPS'),(12,569,'yes','NETGEAR%GPASS%FS752TS'),(12,564,'yes','NETGEAR%GPASS%FSM726'),(12,560,'yes','NETGEAR%GPASS%FSM726S'),(12,582,'yes','NETGEAR%GPASS%FSM7326P'),(12,579,'yes','NETGEAR%GPASS%FSM7328PS'),(12,587,'yes','NETGEAR%GPASS%FSM7328S'),(12,583,'yes','NETGEAR%GPASS%FSM7352PS'),(12,588,'yes','NETGEAR%GPASS%FSM7352S'),(12,563,'yes','NETGEAR%GPASS%FSM750S'),(12,575,'yes','NETGEAR%GPASS%GS716T'),(12,567,'yes','NETGEAR%GPASS%GS724T'),(12,565,'yes','NETGEAR%GPASS%GS724TP'),(12,573,'yes','NETGEAR%GPASS%GS724TS'),(12,574,'yes','NETGEAR%GPASS%GS748T'),(12,572,'yes','NETGEAR%GPASS%GS748TP'),(12,566,'yes','NETGEAR%GPASS%GS748TS'),(12,559,'yes','NETGEAR%GPASS%GSM7212'),(12,557,'yes','NETGEAR%GPASS%GSM7224R'),(12,562,'yes','NETGEAR%GPASS%GSM7224v1'),(12,1602,'yes','NETGEAR%GPASS%GSM7224v2'),(12,558,'yes','NETGEAR%GPASS%GSM7248'),(12,561,'yes','NETGEAR%GPASS%GSM7248R'),(12,586,'yes','NETGEAR%GPASS%GSM7312'),(12,581,'yes','NETGEAR%GPASS%GSM7324'),(12,584,'yes','NETGEAR%GPASS%GSM7328FS'),(12,585,'yes','NETGEAR%GPASS%GSM7328Sv1'),(12,1601,'yes','NETGEAR%GPASS%GSM7328Sv2'),(12,580,'yes','NETGEAR%GPASS%GSM7352S'),(12,1794,'yes','NETGEAR%GPASS%GSM7352Sv2'),(12,556,'yes','NETGEAR%GPASS%JFS516'),(12,552,'yes','NETGEAR%GPASS%JFS524'),(12,553,'yes','NETGEAR%GPASS%JFS524F'),(12,551,'yes','NETGEAR%GPASS%JGS516'),(12,554,'yes','NETGEAR%GPASS%JGS524'),(12,550,'yes','NETGEAR%GPASS%JGS524F'),(12,115,'yes','noname/unknown'),(12,1085,'yes','Nortel%GPASS%BES50GE-12T PWR'),(12,2078,'yes','Pica8%GPASS%P-3290'),(12,2079,'yes','Pica8%GPASS%P-3295'),(12,2145,'yes','Pica8%GPASS%P-3297'),(12,2089,'yes','Pica8%GPASS%P-3780'),(12,2090,'yes','Pica8%GPASS%P-3922'),(12,2146,'yes','Pica8%GPASS%P-3930'),(12,2289,'yes','Pica8%GPASS%P-5101'),(12,2290,'yes','Pica8%GPASS%P-5401'),(12,2545,'yes','Quanta%GPASS%T1048-LB9'),(12,2544,'yes','Quanta%GPASS%T1048-LB9A'),(12,2546,'yes','Quanta%GPASS%T1048-LY4A'),(12,2547,'yes','Quanta%GPASS%T1048-LY4B'),(12,2548,'yes','Quanta%GPASS%T1048-LY4C'),(12,2542,'yes','Quanta%GPASS%T1048-P02'),(12,2543,'yes','Quanta%GPASS%T1048-P02S'),(12,2551,'yes','Quanta%GPASS%T3040-LY3'),(12,2553,'yes','Quanta%GPASS%T3048-LY2'),(12,2552,'yes','Quanta%GPASS%T3048-LY2R'),(12,2550,'yes','Quanta%GPASS%T3048-LY8'),(12,2549,'yes','Quanta%GPASS%T3048-LY9'),(12,2554,'yes','Quanta%GPASS%T3064-LY1R'),(12,2556,'yes','Quanta%GPASS%T5016-LB8D'),(12,2555,'yes','Quanta%GPASS%T5032-LY6'),(12,1371,'yes','SMC%GPASS%8024L2'),(12,1372,'yes','SMC%GPASS%8124PL2'),(12,1373,'yes','SMC%GPASS%8126L2'),(12,1374,'yes','SMC%GPASS%8150L2'),(12,1375,'yes','SMC%GPASS%8612XL3'),(12,1376,'yes','SMC%GPASS%8708L2'),(12,1377,'yes','SMC%GPASS%8824M'),(12,1378,'yes','SMC%GPASS%8848M'),(12,1379,'yes','SMC%GPASS%8926EM'),(12,1380,'yes','SMC%GPASS%8950EM'),(12,1566,'yes','SMC%GPASS%SMC6110L2'),(12,1567,'yes','SMC%GPASS%SMC6128L2'),(12,1568,'yes','SMC%GPASS%SMC6128PL2'),(12,1569,'yes','SMC%GPASS%SMC6152L2'),(12,1793,'yes','TP-Link%GPASS%TL-SG5426'),(12,2624,'yes','Ubiquiti EdgeSwitch ES-48-LITE'),(13,441,'yes','[[CentOS%GSKIP%CentOS V2 | http://www.centos.org/]]'),(13,442,'yes','[[CentOS%GSKIP%CentOS V3 | http://www.centos.org/]]'),(13,443,'yes','[[CentOS%GSKIP%CentOS V4 | http://www.centos.org/]]'),(13,444,'yes','[[CentOS%GSKIP%CentOS V5 | http://www.centos.org/]]'),(13,1667,'yes','[[CentOS%GSKIP%CentOS V6 | http://www.centos.org/]]'),(13,2404,'yes','[[CentOS%GSKIP%CentOS V7 | http://www.centos.org/]]'),(13,418,'yes','[[Debian%GSKIP%Debian 2.0 (hamm) | http://debian.org/releases/hamm/]]'),(13,419,'yes','[[Debian%GSKIP%Debian 2.1 (slink) | http://debian.org/releases/slink/]]'),(13,420,'yes','[[Debian%GSKIP%Debian 2.2 (potato) | http://debian.org/releases/potato/]]'),(13,234,'yes','[[Debian%GSKIP%Debian 3.0 (woody) | http://debian.org/releases/woody/]]'),(13,235,'yes','[[Debian%GSKIP%Debian 3.1 (sarge) | http://debian.org/releases/sarge/]]'),(13,421,'yes','[[Debian%GSKIP%Debian 4.0 (etch) | http://debian.org/releases/etch/]]'),(13,954,'yes','[[Debian%GSKIP%Debian 5.0 (lenny) | http://debian.org/releases/lenny/]]'),(13,1395,'yes','[[Debian%GSKIP%Debian 6.0 (squeeze) | http://debian.org/releases/squeeze/]]'),(13,1709,'yes','[[Debian%GSKIP%Debian 7 (wheezy) | http://debian.org/releases/wheezy/]]'),(13,2405,'yes','[[Debian%GSKIP%Debian 8 (Jessie) | http://debian.org/releases/jessie/]]'),(13,732,'yes','[[FreeBSD%GSKIP%FreeBSD 7.0 | http://www.freebsd.org/releases/7.0R/announce.html]]'),(13,1057,'yes','[[FreeBSD%GSKIP%FreeBSD 7.1 | http://www.freebsd.org/releases/7.1R/relnotes.html]]'),(13,1058,'yes','[[FreeBSD%GSKIP%FreeBSD 7.2 | http://www.freebsd.org/releases/7.2R/relnotes.html]]'),(13,2406,'yes','[[Gentoo%GSKIP%Gentoo | http://gentoo.org]]'),(13,1051,'yes','[[Gentoo%GSKIP%Gentoo 2006.0 | http://www.gentoo.org/proj/en/releng/release/2006.0/2006.0.xml]]'),(13,1052,'yes','[[Gentoo%GSKIP%Gentoo 2007.0 | http://www.gentoo.org/proj/en/releng/release/2007.0/2007.0-press-release.txt]]'),(13,1053,'yes','[[Gentoo%GSKIP%Gentoo 2008.0 | http://www.gentoo.org/proj/en/releng/release/2008.0/index.xml]]'),(13,733,'yes','[[NetBSD%GSKIP%NetBSD 2.0 | http://netbsd.org/releases/formal-2.0/]]'),(13,734,'yes','[[NetBSD%GSKIP%NetBSD 2.1 | http://netbsd.org/releases/formal-2.0/NetBSD-2.1.html]]'),(13,735,'yes','[[NetBSD%GSKIP%NetBSD 3.0 | http://netbsd.org/releases/formal-3/]]'),(13,736,'yes','[[NetBSD%GSKIP%NetBSD 3.1 | http://netbsd.org/releases/formal-3/NetBSD-3.1.html]]'),(13,737,'yes','[[NetBSD%GSKIP%NetBSD 4.0 | http://netbsd.org/releases/formal-4/NetBSD-4.0.html]]'),(13,1046,'yes','[[NetBSD%GSKIP%NetBSD 5.0 | http://netbsd.org/releases/formal-5/NetBSD-5.0.html]]'),(13,720,'yes','[[OpenBSD%GSKIP%OpenBSD 3.3 | http://www.openbsd.org/33.html]]'),(13,721,'yes','[[OpenBSD%GSKIP%OpenBSD 3.4 | http://www.openbsd.org/34.html]]'),(13,722,'yes','[[OpenBSD%GSKIP%OpenBSD 3.5 | http://www.openbsd.org/35.html]]'),(13,723,'yes','[[OpenBSD%GSKIP%OpenBSD 3.6 | http://www.openbsd.org/36.html]]'),(13,724,'yes','[[OpenBSD%GSKIP%OpenBSD 3.7 | http://www.openbsd.org/37.html]]'),(13,725,'yes','[[OpenBSD%GSKIP%OpenBSD 3.8 | http://www.openbsd.org/38.html]]'),(13,726,'yes','[[OpenBSD%GSKIP%OpenBSD 3.9 | http://www.openbsd.org/39.html]]'),(13,727,'yes','[[OpenBSD%GSKIP%OpenBSD 4.0 | http://www.openbsd.org/40.html]]'),(13,728,'yes','[[OpenBSD%GSKIP%OpenBSD 4.1 | http://www.openbsd.org/41.html]]'),(13,729,'yes','[[OpenBSD%GSKIP%OpenBSD 4.2 | http://www.openbsd.org/42.html]]'),(13,730,'yes','[[OpenBSD%GSKIP%OpenBSD 4.3 | http://www.openbsd.org/43.html]]'),(13,797,'yes','[[OpenBSD%GSKIP%OpenBSD 4.4 | http://www.openbsd.org/44.html]]'),(13,1047,'yes','[[OpenBSD%GSKIP%OpenBSD 4.5 | http://www.openbsd.org/45.html]]'),(13,1713,'yes','[[OpenBSD%GSKIP%OpenBSD 4.6 | http://www.openbsd.org/46.html]]'),(13,1714,'yes','[[OpenBSD%GSKIP%OpenBSD 4.7 | http://www.openbsd.org/47.html]]'),(13,1715,'yes','[[OpenBSD%GSKIP%OpenBSD 4.8 | http://www.openbsd.org/48.html]]'),(13,1716,'yes','[[OpenBSD%GSKIP%OpenBSD 4.9 | http://www.openbsd.org/49.html]]'),(13,1717,'yes','[[OpenBSD%GSKIP%OpenBSD 5.0 | http://www.openbsd.org/50.html]]'),(13,1718,'yes','[[OpenBSD%GSKIP%OpenBSD 5.1 | http://www.openbsd.org/51.html]]'),(13,2101,'yes','[[OpenBSD%GSKIP%OpenBSD 5.2 | http://www.openbsd.org/52.html]]'),(13,2102,'yes','[[OpenBSD%GSKIP%OpenBSD 5.3 | http://www.openbsd.org/53.html]]'),(13,2103,'yes','[[OpenBSD%GSKIP%OpenBSD 5.4 | http://www.openbsd.org/54.html]]'),(13,2609,'yes','[[OpenBSD%GSKIP%OpenBSD 5.5 | http://www.openbsd.org/55.html]]'),(13,2610,'yes','[[OpenBSD%GSKIP%OpenBSD 5.6 | http://www.openbsd.org/56.html]]'),(13,2611,'yes','[[OpenBSD%GSKIP%OpenBSD 5.7 | http://www.openbsd.org/57.html]]'),(13,2612,'yes','[[OpenBSD%GSKIP%OpenBSD 5.8 | http://www.openbsd.org/58.html]]'),(13,2613,'yes','[[OpenBSD%GSKIP%OpenBSD 5.9 | http://www.openbsd.org/59.html]]'),(13,2614,'yes','[[OpenBSD%GSKIP%OpenBSD 6.0 | http://www.openbsd.org/60.html]]'),(13,2180,'yes','[[PROXMOX%GSKIP%Proxmox VE 3.0 | http://pve.proxmox.com/wiki/Roadmap#Proxmox_VE_3.0]]'),(13,2181,'yes','[[PROXMOX%GSKIP%Proxmox VE 3.1 | http://pve.proxmox.com/wiki/Roadmap#Proxmox_VE_3.1]]'),(13,2182,'yes','[[PROXMOX%GSKIP%Proxmox VE 3.2 | http://pve.proxmox.com/wiki/Roadmap#Proxmox_VE_3.2]]'),(13,2234,'yes','[[PROXMOX%GSKIP%Proxmox VE 3.3 | http://pve.proxmox.com/wiki/Roadmap#Proxmox_VE_3.3]]'),(13,1332,'yes','[[RH Fedora%GSKIP%Fedora 12 | http://docs.fedoraproject.org/release-notes/f12/en-US/html/]]'),(13,1595,'yes','[[RH Fedora%GSKIP%Fedora 13 | http://docs.fedoraproject.org/release-notes/f13/en-US/html/]]'),(13,1596,'yes','[[RH Fedora%GSKIP%Fedora 14 | http://docs.fedoraproject.org/release-notes/f14/en-US/html/]]'),(13,49999,'yes','[[RH Fedora%GSKIP%Fedora 15 | http://docs.fedoraproject.org/release-notes/f15/en-US/html/]]'),(13,1701,'yes','[[RH Fedora%GSKIP%Fedora 16 | http://docs.fedoraproject.org/en-US/Fedora/16/html/Release_Notes/]]'),(13,1732,'yes','[[RH Fedora%GSKIP%Fedora 17 | http://docs.fedoraproject.org/en-US/Fedora/17/html/Release_Notes/]]'),(13,2060,'yes','[[RH Fedora%GSKIP%Fedora 18 | http://docs.fedoraproject.org/en-US/Fedora/18/html/Release_Notes/]]'),(13,2061,'yes','[[RH Fedora%GSKIP%Fedora 19 | http://docs.fedoraproject.org/en-US/Fedora/19/html/Release_Notes/]]'),(13,1417,'yes','[[SciLin%GSKIP%SL3.x | https://www.scientificlinux.org/]]'),(13,1418,'yes','[[SciLin%GSKIP%SL4.x | https://www.scientificlinux.org/]]'),(13,1419,'yes','[[SciLin%GSKIP%SL5.1 | https://www.scientificlinux.org/]]'),(13,1420,'yes','[[SciLin%GSKIP%SL5.2 | https://www.scientificlinux.org/]]'),(13,1421,'yes','[[SciLin%GSKIP%SL5.3 | https://www.scientificlinux.org/]]'),(13,1422,'yes','[[SciLin%GSKIP%SL5.4 | https://www.scientificlinux.org/]]'),(13,1666,'yes','[[SciLin%GSKIP%SL5.x | https://www.scientificlinux.org/]]'),(13,1665,'yes','[[SciLin%GSKIP%SL6.x | https://www.scientificlinux.org/]]'),(13,1048,'yes','[[Solaris%GSKIP%OpenSolaris 2008.05 | http://opensolaris.org/os/project/indiana/resources/relnotes/200805/x86/]]'),(13,1049,'yes','[[Solaris%GSKIP%OpenSolaris 2008.11 | http://opensolaris.org/os/project/indiana/resources/relnotes/200811/x86/]]'),(13,1050,'yes','[[Solaris%GSKIP%OpenSolaris 2009.06 | http://opensolaris.org/os/project/indiana/resources/relnotes/200906/x86/]]'),(13,2177,'yes','[[Univention%GSKIP%Univention Corporate Server 3.2 (borgfeld) | http://docs.univention.de/release-notes-3.2-2-de.html]]'),(13,1331,'yes','ALT_Linux%GSKIP%ALTLinux 5'),(13,229,'yes','ALT_Linux%GSKIP%ALTLinux Master 2.0'),(13,230,'yes','ALT_Linux%GSKIP%ALTLinux Master 2.2'),(13,231,'yes','ALT_Linux%GSKIP%ALTLinux Master 2.4'),(13,243,'yes','ALT_Linux%GSKIP%ALTLinux Master 4.0'),(13,422,'yes','ALT_Linux%GSKIP%ALTLinux Server 4.0'),(13,423,'yes','ALT_Linux%GSKIP%ALTLinux Sisyphus'),(13,236,'yes','FreeBSD%GSKIP%FreeBSD 1.x'),(13,2104,'yes','FreeBSD%GSKIP%FreeBSD 10.x'),(13,2616,'yes','FreeBSD%GSKIP%FreeBSD 11.x'),(13,237,'yes','FreeBSD%GSKIP%FreeBSD 2.x'),(13,238,'yes','FreeBSD%GSKIP%FreeBSD 3.x'),(13,239,'yes','FreeBSD%GSKIP%FreeBSD 4.x'),(13,240,'yes','FreeBSD%GSKIP%FreeBSD 5.x'),(13,241,'yes','FreeBSD%GSKIP%FreeBSD 6.x'),(13,1416,'yes','FreeBSD%GSKIP%FreeBSD 8.x'),(13,1734,'yes','FreeBSD%GSKIP%FreeBSD 9.x'),(13,1333,'yes','Gentoo%GSKIP%Gentoo 10.0'),(13,1334,'yes','Gentoo%GSKIP%Gentoo 10.1'),(13,221,'yes','MicroSoft%GSKIP%Windows 2000'),(13,223,'yes','MicroSoft%GSKIP%Windows 2003'),(13,1318,'yes','MicroSoft%GSKIP%Windows Server 2008'),(13,1812,'yes','MicroSoft%GSKIP%Windows Server 2008 R2'),(13,2063,'yes','MicroSoft%GSKIP%Windows Server 2012'),(13,2064,'yes','MicroSoft%GSKIP%Windows Server 2012 R2'),(13,224,'yes','MicroSoft%GSKIP%Windows Vista'),(13,222,'yes','MicroSoft%GSKIP%Windows XP'),(13,424,'yes','OpenSUSE%GSKIP%openSUSE 10.0'),(13,425,'yes','OpenSUSE%GSKIP%openSUSE 10.1'),(13,426,'yes','OpenSUSE%GSKIP%openSUSE 10.2'),(13,427,'yes','OpenSUSE%GSKIP%openSUSE 10.3'),(13,933,'yes','OpenSUSE%GSKIP%openSUSE 11.1'),(13,791,'yes','OpenSUSE%GSKIP%openSUSE 11.x'),(13,1733,'yes','OpenSUSE%GSKIP%openSUSE 12.x'),(13,2269,'yes','OpenSUSE%GSKIP%openSUSE 13.x'),(13,225,'yes','Red Hat Enterprise%GSKIP%RHEL V1'),(13,226,'yes','Red Hat Enterprise%GSKIP%RHEL V2'),(13,227,'yes','Red Hat Enterprise%GSKIP%RHEL V3'),(13,228,'yes','Red Hat Enterprise%GSKIP%RHEL V4'),(13,436,'yes','Red Hat Enterprise%GSKIP%RHEL V5'),(13,1396,'yes','Red Hat Enterprise%GSKIP%RHEL V6'),(13,2143,'yes','Red Hat Enterprise%GSKIP%RHEL V7'),(13,932,'yes','RH Fedora%GSKIP%Fedora 10'),(13,1045,'yes','RH Fedora%GSKIP%Fedora 11'),(13,2268,'yes','RH Fedora%GSKIP%Fedora 20'),(13,232,'yes','RH Fedora%GSKIP%Fedora 7'),(13,242,'yes','RH Fedora%GSKIP%Fedora 8'),(13,790,'yes','RH Fedora%GSKIP%Fedora 9'),(13,212,'yes','RH Fedora%GSKIP%Fedora C1'),(13,213,'yes','RH Fedora%GSKIP%Fedora C2'),(13,214,'yes','RH Fedora%GSKIP%Fedora C3'),(13,215,'yes','RH Fedora%GSKIP%Fedora C4'),(13,216,'yes','RH Fedora%GSKIP%Fedora C5'),(13,217,'yes','RH Fedora%GSKIP%Fedora C6'),(13,1319,'yes','SlackWare%GSKIP%Slackware 13.0'),(13,220,'yes','Solaris%GSKIP%Solaris 10'),(13,2572,'yes','Solaris%GSKIP%Solaris 11'),(13,218,'yes','Solaris%GSKIP%Solaris 8'),(13,219,'yes','Solaris%GSKIP%Solaris 9'),(13,233,'yes','SUSE Enterprise%GSKIP%SLES10'),(13,1317,'yes','SUSE Enterprise%GSKIP%SLES11'),(13,2380,'yes','SUSE Enterprise%GSKIP%SLES12'),(13,2403,'yes','SUSE Enterprise%GSKIP%SLES9'),(13,1704,'yes','Ubuntu%GSKIP%Ubuntu 10.04 LTS'),(13,1705,'yes','Ubuntu%GSKIP%Ubuntu 10.10'),(13,1706,'yes','Ubuntu%GSKIP%Ubuntu 11.04'),(13,1707,'yes','Ubuntu%GSKIP%Ubuntu 11.10'),(13,1708,'yes','Ubuntu%GSKIP%Ubuntu 12.04 LTS'),(13,1813,'yes','Ubuntu%GSKIP%Ubuntu 12.10'),(13,2107,'yes','Ubuntu%GSKIP%Ubuntu 13.04'),(13,2108,'yes','Ubuntu%GSKIP%Ubuntu 13.10'),(13,2109,'yes','Ubuntu%GSKIP%Ubuntu 14.04 LTS'),(13,2560,'yes','Ubuntu%GSKIP%Ubuntu 15.10'),(13,2561,'yes','Ubuntu%GSKIP%Ubuntu 16.04 LTS'),(13,428,'yes','Ubuntu%GSKIP%Ubuntu 4.10'),(13,429,'yes','Ubuntu%GSKIP%Ubuntu 5.04'),(13,430,'yes','Ubuntu%GSKIP%Ubuntu 5.10'),(13,431,'yes','Ubuntu%GSKIP%Ubuntu 6.06 LTS'),(13,432,'yes','Ubuntu%GSKIP%Ubuntu 6.10'),(13,433,'yes','Ubuntu%GSKIP%Ubuntu 7.04'),(13,434,'yes','Ubuntu%GSKIP%Ubuntu 7.10'),(13,435,'yes','Ubuntu%GSKIP%Ubuntu 8.04 LTS'),(13,796,'yes','Ubuntu%GSKIP%Ubuntu 8.10'),(13,1054,'yes','Ubuntu%GSKIP%Ubuntu 9.04'),(13,1320,'yes','Ubuntu%GSKIP%Ubuntu 9.10'),(13,1508,'yes','VMWare Hypervisor%GSKIP%VMware ESX 3.5'),(13,1510,'yes','VMWare Hypervisor%GSKIP%VMware ESX 4.0'),(13,1512,'yes','VMWare Hypervisor%GSKIP%VMware ESX 4.1'),(13,1509,'yes','VMWare Hypervisor%GSKIP%VMware ESXi 3.5'),(13,1511,'yes','VMWare Hypervisor%GSKIP%VMware ESXi 4.0'),(13,1513,'yes','VMWare Hypervisor%GSKIP%VMware ESXi 4.1'),(13,1608,'yes','VMWare Hypervisor%GSKIP%VMware ESXi 5.0'),(13,2105,'yes','VMWare Hypervisor%GSKIP%VMware ESXi 5.1'),(13,2106,'yes','VMWare Hypervisor%GSKIP%VMware ESXi 5.5'),(13,2446,'yes','VMWare Hypervisor%GSKIP%VMware ESXi 6.0'),(13,1514,'yes','Xen Hypervisor%GSKIP%XenServer 4.0'),(13,1515,'yes','Xen Hypervisor%GSKIP%XenServer 5.0'),(13,1516,'yes','Xen Hypervisor%GSKIP%XenServer 5.5'),(13,2617,'yes','Xen Hypervisor%GSKIP%XenServer 5.6'),(13,2618,'yes','Xen Hypervisor%GSKIP%XenServer 6.0'),(13,2619,'yes','Xen Hypervisor%GSKIP%XenServer 6.1'),(13,2620,'yes','Xen Hypervisor%GSKIP%XenServer 6.2'),(13,2621,'yes','Xen Hypervisor%GSKIP%XenServer 6.5'),(13,2622,'yes','Xen Hypervisor%GSKIP%XenServer 7.0'),(14,1675,'yes','Arista EOS 4'),(14,250,'yes','Cisco IOS 11.2'),(14,253,'yes','Cisco IOS 11.3'),(14,244,'yes','Cisco IOS 12.0'),(14,251,'yes','Cisco IOS 12.1'),(14,252,'yes','Cisco IOS 12.2'),(14,1901,'yes','Cisco IOS 15.0'),(14,2082,'yes','Cisco IOS 15.1'),(14,2142,'yes','Cisco IOS 15.2'),(14,963,'yes','Cisco NX-OS 4.0'),(14,964,'yes','Cisco NX-OS 4.1'),(14,1365,'yes','Cisco NX-OS 4.2'),(14,1410,'yes','Cisco NX-OS 5.0'),(14,1411,'yes','Cisco NX-OS 5.1'),(14,1809,'yes','Cisco NX-OS 5.2'),(14,1643,'yes','Cisco NX-OS 6.0'),(14,2028,'yes','Cisco NX-OS 6.1'),(14,1350,'yes','ExtremeXOS 10'),(14,1351,'yes','ExtremeXOS 11'),(14,1352,'yes','ExtremeXOS 12'),(14,1592,'yes','Force10 FTOS 6'),(14,1593,'yes','Force10 FTOS 7'),(14,1594,'yes','Force10 FTOS 8'),(14,1591,'yes','Force10 SFTOS 2'),(14,249,'yes','Foundry basic L3'),(14,248,'yes','Foundry full L3'),(14,247,'yes','Foundry L2'),(14,245,'yes','Foundry SLB'),(14,246,'yes','Foundry WXM'),(14,2080,'yes','Huawei VRP 5.11'),(14,2081,'yes','Huawei VRP 5.12'),(14,1360,'yes','Huawei VRP 5.3'),(14,1361,'yes','Huawei VRP 5.5'),(14,1369,'yes','Huawei VRP 5.7'),(14,2027,'yes','Huawei VRP 8.5'),(14,1363,'yes','IronWare 5'),(14,1364,'yes','IronWare 7'),(14,1367,'yes','JunOS 10'),(14,2151,'yes','JunOS 11'),(14,2152,'yes','JunOS 12'),(14,2397,'yes','JunOS 13'),(14,2398,'yes','JunOS 14'),(14,2399,'yes','JunOS 15'),(14,1366,'yes','JunOS 9'),(14,1786,'yes','Marvell ROS 1.1'),(16,260,'yes','Cisco IOS 11.2'),(16,261,'yes','Cisco IOS 11.3'),(16,254,'yes','Cisco IOS 12.0'),(16,255,'yes','Cisco IOS 12.1'),(16,256,'yes','Cisco IOS 12.2'),(16,257,'yes','Cisco IOS 12.3'),(16,258,'yes','Cisco IOS 12.4'),(16,1963,'yes','Cisco IOS 15.1'),(16,1759,'yes','Cisco IOS XR 4.2'),(16,259,'yes','Foundry L3'),(16,1597,'yes','JunOS 10'),(16,1598,'yes','JunOS 11'),(16,1599,'yes','JunOS 12'),(16,2400,'yes','JunOS 13'),(16,2401,'yes','JunOS 14'),(16,2402,'yes','JunOS 15'),(16,2473,'yes','OpenWrt 14'),(16,2474,'yes','OpenWrt 15'),(16,2475,'yes','RouterOS 6'),(17,1327,'yes','[[ Cisco%GPASS%2901 | http://www.cisco.com/en/US/products/ps10539/index.html]]'),(17,1328,'yes','[[ Cisco%GPASS%2911 | http://www.cisco.com/en/US/products/ps10540/index.html]]'),(17,1329,'yes','[[ Cisco%GPASS%2921 | http://www.cisco.com/en/US/products/ps10543/index.html]]'),(17,1330,'yes','[[ Cisco%GPASS%2951 | http://www.cisco.com/en/US/products/ps10544/index.html]]'),(17,2580,'yes','[[Cisco%GPASS%1905 | http://www.cisco.com/c/en/us/products/routers/1905-serial-integrated-services-router-isr/index.html]]'),(17,2581,'yes','[[Cisco%GPASS%1921 | http://www.cisco.com/c/en/us/products/routers/1921-integrated-services-router-isr/index.html]]'),(17,2582,'yes','[[Cisco%GPASS%1941 | http://www.cisco.com/c/en/us/products/routers/1941-integrated-services-router-isr/index.html]]'),(17,2583,'yes','[[Cisco%GPASS%1941W | http://www.cisco.com/c/en/us/products/routers/1941w-integrated-services-router-isr/index.html]]'),(17,2584,'yes','[[Cisco%GPASS%3925 | http://www.cisco.com/c/en/us/products/routers/3925-integrated-services-router-isr/index.html]]'),(17,2585,'yes','[[Cisco%GPASS%3925E | http://www.cisco.com/c/en/us/products/routers/3925e-integrated-services-router-isr/index.html]]'),(17,2586,'yes','[[Cisco%GPASS%3945 | http://www.cisco.com/c/en/us/products/routers/3945-integrated-services-router-isr/index.html]]'),(17,2587,'yes','[[Cisco%GPASS%3945E | http://www.cisco.com/c/en/us/products/routers/3945e-integrated-services-router-isr/index.html]]'),(17,2083,'yes','[[Cisco%GPASS%ASR 9001 | http://cisco.com/en/US/products/ps12074/index.html]]'),(17,1016,'yes','[[Cisco%GPASS%ASR 9006 | http://cisco.com/en/US/products/ps10075/index.html]]'),(17,1017,'yes','[[Cisco%GPASS%ASR 9010 | http://cisco.com/en/US/products/ps10076/index.html]]'),(17,2084,'yes','[[Cisco%GPASS%ASR 9922 | http://cisco.com/en/US/products/ps11755/index.html]]'),(17,949,'yes','[[F5%GPASS%WANJet 300 | http://www.f5.com/pdf/products/wanjet-hardware-ds.pdf]]'),(17,950,'yes','[[F5%GPASS%WANJet 500 | http://www.f5.com/pdf/products/wanjet-hardware-ds.pdf]]'),(17,910,'yes','[[Juniper%GPASS%E320 BSR | http://www.juniper.net/products_and_services/e_series_broadband_service/index.html]]'),(17,914,'yes','[[Juniper%GPASS%ERX-1410 | http://www.juniper.net/products_and_services/e_series_broadband_service/index.html]]'),(17,915,'yes','[[Juniper%GPASS%ERX-1440 | http://www.juniper.net/products_and_services/e_series_broadband_service/index.html]]'),(17,911,'yes','[[Juniper%GPASS%ERX-310 | http://www.juniper.net/products_and_services/e_series_broadband_service/index.html]]'),(17,912,'yes','[[Juniper%GPASS%ERX-705 | http://www.juniper.net/products_and_services/e_series_broadband_service/index.html]]'),(17,913,'yes','[[Juniper%GPASS%ERX-710 | http://www.juniper.net/products_and_services/e_series_broadband_service/index.html]]'),(17,916,'yes','[[Juniper%GPASS%J2320 | http://www.juniper.net/products_and_services/j_series_services_routers/index.html]]'),(17,917,'yes','[[Juniper%GPASS%J2350 | http://www.juniper.net/products_and_services/j_series_services_routers/index.html]]'),(17,918,'yes','[[Juniper%GPASS%J4350 | http://www.juniper.net/products_and_services/j_series_services_routers/index.html]]'),(17,919,'yes','[[Juniper%GPASS%J6350 | http://www.juniper.net/products_and_services/j_series_services_routers/index.html]]'),(17,921,'yes','[[Juniper%GPASS%M10i | http://www.juniper.net/products_and_services/m_series_routing_portfolio/index.html]]'),(17,923,'yes','[[Juniper%GPASS%M120 | http://www.juniper.net/products_and_services/m_series_routing_portfolio/index.html]]'),(17,924,'yes','[[Juniper%GPASS%M320 | http://www.juniper.net/products_and_services/m_series_routing_portfolio/index.html]]'),(17,922,'yes','[[Juniper%GPASS%M40e | http://www.juniper.net/products_and_services/m_series_routing_portfolio/index.html]]'),(17,920,'yes','[[Juniper%GPASS%M7i | http://www.juniper.net/products_and_services/m_series_routing_portfolio/index.html]]'),(17,2250,'yes','[[Juniper%GPASS%MX10 | http://www.juniper.net/products-services/routing/mx-series/mx10]]'),(17,2252,'yes','[[Juniper%GPASS%MX104 | http://www.juniper.net/products-services/routing/mx-series/mx104]]'),(17,2253,'yes','[[Juniper%GPASS%MX2010 | http://www.juniper.net/products-services/routing/mx-series/mx2010]]'),(17,2254,'yes','[[Juniper%GPASS%MX2020 | http://www.juniper.net/products-services/routing/mx-series/mx2020]]'),(17,925,'yes','[[Juniper%GPASS%MX240 | http://www.juniper.net/products-services/routing/mx-series/mx240]]'),(17,2251,'yes','[[Juniper%GPASS%MX40 | http://www.juniper.net/products-services/routing/mx-series/mx40]]'),(17,926,'yes','[[Juniper%GPASS%MX480 | http://www.juniper.net/products-services/routing/mx-series/mx480]]'),(17,2249,'yes','[[Juniper%GPASS%MX5 | http://www.juniper.net/products-services/routing/mx-series/mx5]]'),(17,1368,'yes','[[Juniper%GPASS%MX80 | http://www.juniper.net/products-services/routing/mx-series/mx80]]'),(17,927,'yes','[[Juniper%GPASS%MX960 | http://www.juniper.net/products-services/routing/mx-series/mx960]]'),(17,930,'yes','[[Juniper%GPASS%T1600 | http://www.juniper.net/products_and_services/t_series_core_platforms/index.html]]'),(17,928,'yes','[[Juniper%GPASS%T320 | http://www.juniper.net/products_and_services/t_series_core_platforms/index.html]]'),(17,929,'yes','[[Juniper%GPASS%T640 | http://www.juniper.net/products_and_services/t_series_core_platforms/index.html]]'),(17,931,'yes','[[Juniper%GPASS%TX Matrix | http://www.juniper.net/products_and_services/t_series_core_platforms/index.html]]'),(17,487,'yes','[[RAD%GPASS%FCD-IPM | http://www.rad.com/Article/0,6583,36426-E1_T1_or_Fractional_E1_T1_Modular_Access_Device_with_Integrated_Router,00.html]]'),(17,304,'yes','Cisco%GPASS%1801'),(17,303,'yes','Cisco%GPASS%1802'),(17,302,'yes','Cisco%GPASS%1803'),(17,301,'yes','Cisco%GPASS%1811'),(17,300,'yes','Cisco%GPASS%1812'),(17,299,'yes','Cisco%GPASS%1841'),(17,264,'yes','Cisco%GPASS%2610XM'),(17,265,'yes','Cisco%GPASS%2611XM'),(17,272,'yes','Cisco%GPASS%2612'),(17,273,'yes','Cisco%GPASS%2620XM'),(17,268,'yes','Cisco%GPASS%2621XM'),(17,274,'yes','Cisco%GPASS%2650XM'),(17,270,'yes','Cisco%GPASS%2651XM'),(17,275,'yes','Cisco%GPASS%2691'),(17,279,'yes','Cisco%GPASS%2801'),(17,280,'yes','Cisco%GPASS%2811'),(17,281,'yes','Cisco%GPASS%2821'),(17,282,'yes','Cisco%GPASS%2851'),(17,266,'yes','Cisco%GPASS%3620'),(17,267,'yes','Cisco%GPASS%3640'),(17,283,'yes','Cisco%GPASS%3725'),(17,284,'yes','Cisco%GPASS%3745'),(17,285,'yes','Cisco%GPASS%3825'),(17,286,'yes','Cisco%GPASS%3845'),(17,2625,'yes','Cisco%GPASS%4221'),(17,2626,'yes','Cisco%GPASS%4321'),(17,2627,'yes','Cisco%GPASS%4331'),(17,2628,'yes','Cisco%GPASS%4351'),(17,2629,'yes','Cisco%GPASS%4431'),(17,2630,'yes','Cisco%GPASS%4451'),(17,305,'yes','Cisco%GPASS%7202'),(17,306,'yes','Cisco%GPASS%7204'),(17,271,'yes','Cisco%GPASS%7204VXR'),(17,307,'yes','Cisco%GPASS%7206'),(17,269,'yes','Cisco%GPASS%7206VXR'),(17,276,'yes','Cisco%GPASS%7603'),(17,308,'yes','Cisco%GPASS%7604'),(17,277,'yes','Cisco%GPASS%7606'),(17,263,'yes','Cisco%GPASS%7609'),(17,278,'yes','Cisco%GPASS%7613'),(17,2286,'yes','Cisco%GPASS%800M'),(17,2285,'yes','Cisco%GPASS%812 CiFi'),(17,2284,'yes','Cisco%GPASS%819'),(17,2283,'yes','Cisco%GPASS%861'),(17,2282,'yes','Cisco%GPASS%866VAE'),(17,2281,'yes','Cisco%GPASS%867VAE'),(17,2279,'yes','Cisco%GPASS%880 3G'),(17,2280,'yes','Cisco%GPASS%880G'),(17,2278,'yes','Cisco%GPASS%881'),(17,2276,'yes','Cisco%GPASS%886VA'),(17,2277,'yes','Cisco%GPASS%886VA-W'),(17,2273,'yes','Cisco%GPASS%887V'),(17,2274,'yes','Cisco%GPASS%887VA'),(17,2275,'yes','Cisco%GPASS%887VA-W'),(17,2272,'yes','Cisco%GPASS%888'),(17,2271,'yes','Cisco%GPASS%891'),(17,2270,'yes','Cisco%GPASS%892'),(17,2288,'yes','Cisco%GPASS%C881W'),(17,2287,'yes','Cisco%GPASS%C892FSP'),(17,309,'yes','Cisco%GPASS%OSR-7609'),(17,1609,'yes','Fortinet%GPASS%Fortigate 310B'),(17,311,'yes','Foundry%GPASS%BigIron 15000'),(17,262,'yes','Foundry%GPASS%BigIron 4000'),(17,310,'yes','Foundry%GPASS%BigIron 8000'),(17,298,'yes','Foundry%GPASS%BigIron RX-16'),(17,417,'yes','Foundry%GPASS%BigIron RX-32'),(17,296,'yes','Foundry%GPASS%BigIron RX-4'),(17,297,'yes','Foundry%GPASS%BigIron RX-8'),(17,290,'yes','Foundry%GPASS%NetIron MLX-16'),(17,291,'yes','Foundry%GPASS%NetIron MLX-32'),(17,288,'yes','Foundry%GPASS%NetIron MLX-4'),(17,289,'yes','Foundry%GPASS%NetIron MLX-8'),(17,294,'yes','Foundry%GPASS%NetIron XMR 16000'),(17,295,'yes','Foundry%GPASS%NetIron XMR 32000'),(17,292,'yes','Foundry%GPASS%NetIron XMR 4000'),(17,293,'yes','Foundry%GPASS%NetIron XMR 8000'),(17,2245,'yes','Huawei%GPASS%NE20E-S16'),(17,2243,'yes','Huawei%GPASS%NE20E-S4'),(17,2244,'yes','Huawei%GPASS%NE20E-S8'),(17,2248,'yes','Huawei%GPASS%NE40E-X16'),(17,2246,'yes','Huawei%GPASS%NE40E-X3'),(17,2247,'yes','Huawei%GPASS%NE40E-X8'),(17,2472,'yes','MikroTik%GPASS%3011UiAS-RM'),(17,2173,'yes','MikroTik%GPASS%CCR1009-8G-1S'),(17,2174,'yes','MikroTik%GPASS%CCR1009-8G-1S-1S+'),(17,2048,'yes','MikroTik%GPASS%CCR1016-12G'),(17,2175,'yes','MikroTik%GPASS%CCR1016-12S-1S+'),(17,2050,'yes','MikroTik%GPASS%CCR1036-12G-4S'),(17,2049,'yes','MikroTik%GPASS%CCR1036-8G-2S+'),(17,2471,'yes','MikroTik%GPASS%CCR1072-1G-8S+'),(17,2039,'yes','MikroTik%GPASS%RB1000U'),(17,2040,'yes','MikroTik%GPASS%RB1100'),(17,2042,'yes','MikroTik%GPASS%RB1100AH'),(17,2043,'yes','MikroTik%GPASS%RB1100AHx2'),(17,2041,'yes','MikroTik%GPASS%RB1100Hx2'),(17,2044,'yes','MikroTik%GPASS%RB1200'),(17,2046,'yes','MikroTik%GPASS%RB2011iL-RM'),(17,2045,'yes','MikroTik%GPASS%RB2011L-RM'),(17,2047,'yes','MikroTik%GPASS%RB2011UAS-RM'),(18,1878,'yes','[[Infortrend%GPASS%ES A04U-G2421 | http://www.infortrend.com/global/products/models/ES%20A04U-G2421]]'),(18,1865,'yes','[[Infortrend%GPASS%ES A08F-G2422 | http://www.infortrend.com/global/products/models/ES%20A08F-G2422]]'),(18,1868,'yes','[[Infortrend%GPASS%ES A08S-C2133 | http://www.infortrend.com/global/products/models/ES%20A08S-C2133]]'),(18,1869,'yes','[[Infortrend%GPASS%ES A08S-C2134 | http://www.infortrend.com/global/products/models/ES%20A08S-C2134]]'),(18,1871,'yes','[[Infortrend%GPASS%ES A08S-G2130 | http://www.infortrend.com/global/products/models/ES%20A08S-G2130]]'),(18,1876,'yes','[[Infortrend%GPASS%ES A08U-G2421 | http://www.infortrend.com/global/products/models/ES%20A08U-G2421]]'),(18,1867,'yes','[[Infortrend%GPASS%ES A12E-G2121 | http://www.infortrend.com/global/products/models/ES%20A12E-G2121]]'),(18,1866,'yes','[[Infortrend%GPASS%ES A12F-G2422 | http://www.infortrend.com/global/products/models/ES%20A12F-G2422]]'),(18,1872,'yes','[[Infortrend%GPASS%ES A12S-G2130 | http://www.infortrend.com/global/products/models/ES%20A12S-G2130]]'),(18,1877,'yes','[[Infortrend%GPASS%ES A12U-G2421 | http://www.infortrend.com/global/products/models/ES%20A12U-G2421]]'),(18,1882,'yes','[[Infortrend%GPASS%ES A16F-J2430-G | http://www.infortrend.com/global/products/models/ES%20A16F-J2430-G]]'),(18,1870,'yes','[[Infortrend%GPASS%ES A16S-G2130 | http://www.infortrend.com/global/products/models/ES%20A16S-G2130]]'),(18,1875,'yes','[[Infortrend%GPASS%ES A24U-G2421 | http://www.infortrend.com/global/products/models/ES%20A24U-G2421]]'),(18,1880,'yes','[[Infortrend%GPASS%ES B12S-J1000-R | http://www.infortrend.com/global/products/models/ES%20B12S-J1000-R]]'),(18,1881,'yes','[[Infortrend%GPASS%ES B12S-J1000-S | http://www.infortrend.com/global/products/models/ES%20B12S-J1000-S]]'),(18,1879,'yes','[[Infortrend%GPASS%ES F16F-J4000-R | http://www.infortrend.com/global/products/models/ES%20F16F-J4000-R]]'),(18,1853,'yes','[[Infortrend%GPASS%ES F16F-R4031 | http://www.infortrend.com/global/products/models/ES%20F16F-R4031]]'),(18,1854,'yes','[[Infortrend%GPASS%ES F16F-R4840 | http://www.infortrend.com/global/products/models/ES%20F16F-R4840]]'),(18,1855,'yes','[[Infortrend%GPASS%ES F16F-S4031 | http://www.infortrend.com/global/products/models/ES%20F16F-S4031]]'),(18,1856,'yes','[[Infortrend%GPASS%ES F16F-S4840 | http://www.infortrend.com/global/products/models/ES%20F16F-S4840]]'),(18,1863,'yes','[[Infortrend%GPASS%ES S12F-G1842 | http://www.infortrend.com/global/products/models/ES%20S12F-G1842]]'),(18,1864,'yes','[[Infortrend%GPASS%ES S12F-R1840 | http://www.infortrend.com/global/products/models/ES%20S12F-R1840]]'),(18,1885,'yes','[[Infortrend%GPASS%ES S12S-J1000-G | http://www.infortrend.com/global/products/models/ES%20S12S-J1000-G]]'),(18,1886,'yes','[[Infortrend%GPASS%ES S12S-J1002-R | http://www.infortrend.com/global/products/models/ES%20S12S-J1002-R]]'),(18,1874,'yes','[[Infortrend%GPASS%ES S12U-G1440 | http://www.infortrend.com/global/products/models/ES%20S12U-G1440]]'),(18,1861,'yes','[[Infortrend%GPASS%ES S16F-G1840 | http://www.infortrend.com/global/products/models/ES%20S16F-G1840]]'),(18,1862,'yes','[[Infortrend%GPASS%ES S16F-R1840 | http://www.infortrend.com/global/products/models/ES%20S16F-R1840]]'),(18,1883,'yes','[[Infortrend%GPASS%ES S16S-J1000-R1 | http://www.infortrend.com/global/products/models/ES%20S16S-J1000-R1]]'),(18,1884,'yes','[[Infortrend%GPASS%ES S16S-J1000-S1 | http://www.infortrend.com/global/products/models/ES%20S16S-J1000-S1]]'),(18,1873,'yes','[[Infortrend%GPASS%ES S16U-G1440 | http://www.infortrend.com/global/products/models/ES%20S16U-G1440]]'),(18,1857,'yes','[[Infortrend%GPASS%ES S24F-G1440 | http://www.infortrend.com/global/products/models/ES%20S24F-G1440]]'),(18,1858,'yes','[[Infortrend%GPASS%ES S24F-G1840 | http://www.infortrend.com/global/products/models/ES%20S24F-G1840]]'),(18,1859,'yes','[[Infortrend%GPASS%ES S24F-R1440 | http://www.infortrend.com/global/products/models/ES%20S24F-R1440]]'),(18,1860,'yes','[[Infortrend%GPASS%ES S24F-R1840 | http://www.infortrend.com/global/products/models/ES%20S24F-R1840]]'),(18,1088,'yes','[[NetApp%GPASS%FAS2020 | http://www.netapp.com/us/products/storage-systems/fas2000/]]'),(18,1089,'yes','[[NetApp%GPASS%FAS2050 | http://www.netapp.com/us/products/storage-systems/fas2000/]]'),(18,1094,'yes','[[NetApp%GPASS%FAS3140 | http://www.netapp.com/us/products/storage-systems/fas3100/]]'),(18,1095,'yes','[[NetApp%GPASS%FAS3160 | http://www.netapp.com/us/products/storage-systems/fas3100/]]'),(18,1096,'yes','[[NetApp%GPASS%FAS3170 | http://www.netapp.com/us/products/storage-systems/fas3100/]]'),(18,1098,'yes','[[NetApp%GPASS%FAS6040 | http://www.netapp.com/us/products/storage-systems/fas6000/]]'),(18,1100,'yes','[[NetApp%GPASS%FAS6080 | http://www.netapp.com/us/products/storage-systems/fas6000/]]'),(18,1101,'yes','[[NetApp%GPASS%V3140 | http://www.netapp.com/us/products/storage-systems/v3100/]]'),(18,1102,'yes','[[NetApp%GPASS%V3160 | http://www.netapp.com/us/products/storage-systems/v3100/]]'),(18,1103,'yes','[[NetApp%GPASS%V3170 | http://www.netapp.com/us/products/storage-systems/v3100/]]'),(18,1104,'yes','[[NetApp%GPASS%V6030 | http://www.netapp.com/us/products/storage-systems/v6000/]]'),(18,1105,'yes','[[NetApp%GPASS%V6040 | http://www.netapp.com/us/products/storage-systems/v6000/]]'),(18,1106,'yes','[[NetApp%GPASS%V6070 | http://www.netapp.com/us/products/storage-systems/v6000/]]'),(18,1107,'yes','[[NetApp%GPASS%V6080 | http://www.netapp.com/us/products/storage-systems/v6000/]]'),(18,997,'yes','Dell EqualLogic PS5000'),(18,998,'yes','Dell EqualLogic PS6000'),(18,437,'yes','Dell PowerVault%GPASS%210S'),(18,323,'yes','Dell PowerVault%GPASS%220S'),(18,438,'yes','Dell PowerVault%GPASS%221S'),(18,995,'yes','Dell PowerVault%GPASS%MD1000'),(18,996,'yes','Dell PowerVault%GPASS%MD1120'),(18,1382,'yes','Dell PowerVault%GPASS%MD1220'),(18,324,'yes','Dell PowerVault%GPASS%MD3000'),(18,322,'yes','Dell PowerVault%GPASS%NX1950'),(18,313,'yes','Dell/EMC AX150'),(18,316,'yes','EMC CLARiiON CX300'),(18,1003,'yes','EMC CLARiiON CX4 DAE'),(18,999,'yes','EMC CLARiiON CX4-120 SPE'),(18,1000,'yes','EMC CLARiiON CX4-240 SPE'),(18,1001,'yes','EMC CLARiiON CX4-480 SPE'),(18,1002,'yes','EMC CLARiiON CX4-960 SPE'),(18,314,'yes','EMC CLARiiON CX600'),(18,2436,'yes','EMC VNXe1600'),(18,2437,'yes','EMC VNXe3200'),(18,2623,'yes','HP StorageWorks P6300'),(18,1108,'yes','NetApp%GPASS%DS14mk2 AT'),(18,1109,'yes','NetApp%GPASS%DS14mk2 FC'),(18,1110,'yes','NetApp%GPASS%DS14mk4 FC'),(18,2456,'yes','NetApp%GPASS%FAS2220'),(18,2457,'yes','NetApp%GPASS%FAS2240-2'),(18,2458,'yes','NetApp%GPASS%FAS2240-4'),(18,2459,'yes','NetApp%GPASS%FAS2520'),(18,2460,'yes','NetApp%GPASS%FAS2552'),(18,2461,'yes','NetApp%GPASS%FAS2554'),(18,1090,'yes','NetApp%GPASS%FAS3020'),(18,1091,'yes','NetApp%GPASS%FAS3040'),(18,1092,'yes','NetApp%GPASS%FAS3050'),(18,1093,'yes','NetApp%GPASS%FAS3070'),(18,2391,'yes','NetApp%GPASS%FAS3210'),(18,2392,'yes','NetApp%GPASS%FAS3220'),(18,2393,'yes','NetApp%GPASS%FAS3240'),(18,2394,'yes','NetApp%GPASS%FAS3270'),(18,1097,'yes','NetApp%GPASS%FAS6030'),(18,1099,'yes','NetApp%GPASS%FAS6070'),(18,321,'yes','Sun%GPASS%StorageTek 3120'),(18,320,'yes','Sun%GPASS%StorageTek 3320'),(18,319,'yes','Sun%GPASS%StorageTek 3510'),(18,318,'yes','Sun%GPASS%StorageTek 3511'),(18,317,'yes','Sun%GPASS%StorageTek 6140'),(18,312,'yes','Sun%GPASS%StorEdge A1000'),(18,315,'yes','Sun%GPASS%StorEdge D240'),(19,1850,'yes','[[NEC%GPASS%LL009F | http://www.nec.com/en/global/prod/tapestorage/index_009.html]]'),(19,1851,'yes','[[NEC%GPASS%T30A | http://www.nec.com/en/global/prod/tapestorage/index_t30a.html]]'),(19,1852,'yes','[[NEC%GPASS%T60A | http://www.nec.com/en/global/prod/tapestorage/index_t60a.html]]'),(19,334,'yes','Dell PowerVault%GPASS%124T'),(19,331,'yes','Dell PowerVault%GPASS%132T'),(19,325,'yes','Dell PowerVault%GPASS%136T'),(19,330,'yes','Dell PowerVault%GPASS%ML6000'),(19,333,'yes','Dell PowerVault%GPASS%TL2000'),(19,332,'yes','Dell PowerVault%GPASS%TL4000'),(19,335,'yes','Sun%GPASS%StorageTek C2'),(19,329,'yes','Sun%GPASS%StorageTek C4'),(19,327,'yes','Sun%GPASS%StorageTek L1400'),(19,326,'yes','Sun%GPASS%StorageTek SL500'),(19,328,'yes','Sun%GPASS%StorageTek SL8500'),(21,1847,'yes','[[APC%GPASS%AP5201 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP5201]]'),(21,1848,'yes','[[APC%GPASS%AP5202 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP5202]]'),(21,492,'yes','[[Aten ACS1208A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224111025006&layerid=subClass2]]'),(21,493,'yes','[[Aten ACS1216A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224111953008&layerid=subClass2]]'),(21,500,'yes','[[Aten CS1004 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224100546008&layerid=subClass2]]'),(21,503,'yes','[[Aten CS138A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224111458007&layerid=subClass2]]'),(21,498,'yes','[[Aten CS1708 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=2005022410563008&layerid=subClass2]]'),(21,499,'yes','[[Aten CS1716 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224110022008&layerid=subClass2]]'),(21,494,'yes','[[Aten CS1754 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050217161051008&layerid=subClass2]]'),(21,495,'yes','[[Aten CS1758 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224093143008&layerid=subClass2]]'),(21,501,'yes','[[Aten CS228 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224114323008&layerid=subClass2]]'),(21,502,'yes','[[Aten CS428 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224114721008&layerid=subClass2]]'),(21,491,'yes','[[Aten CS78 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20070319151852001&layerid=subClass2]]'),(21,504,'yes','[[Aten CS88A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=2005022411042006&layerid=subClass2]]'),(21,496,'yes','[[Aten CS9134 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20070130133658002&pid=20050217172845005&layerid=subClass2]]'),(21,497,'yes','[[Aten CS9138 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224094519006&layerid=subClass2]]'),(21,510,'yes','[[Aten KH0116 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411130954001&pid=20060411145734003&layerid=subClass1]]'),(21,508,'yes','[[Aten KH1508 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411130954001&pid=20061101174038001&layerid=subClass1]]'),(21,509,'yes','[[Aten KH1516 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411130954001&pid=20061101175320001&layerid=subClass1]]'),(21,511,'yes','[[Aten KH98 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=2007012911116003&pid=20061221104352001&layerid=subClass1]]'),(21,506,'yes','[[Aten KM0216 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131626002&pid=20060417153950007&layerid=subClass1]]'),(21,507,'yes','[[Aten KM0432 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131626002&pid=2006041715359007&layerid=subClass1]]'),(21,505,'yes','[[Aten KM0832 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131626002&pid=20060628154826001&layerid=subClass1]]'),(21,468,'yes','[[Avocent AutoView 3200 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentAutoViewAppliances.aspx]]'),(21,456,'yes','[[Avocent DSR1030 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentDSRAppliances.aspx]]'),(21,451,'yes','[[Avocent DSR1031 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentDSRAppliances.aspx]]'),(21,457,'yes','[[Avocent DSR2030 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentDSRAppliances.aspx]]'),(21,458,'yes','[[Avocent DSR2035 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentDSRAppliances.aspx]]'),(21,459,'yes','[[Avocent DSR4030 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentDSRAppliances.aspx]]'),(21,460,'yes','[[Avocent DSR8030 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentDSRAppliances.aspx]]'),(21,461,'yes','[[Avocent DSR8035 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentDSRAppliances.aspx]]'),(21,1839,'yes','[[Avocent%GPASS%AutoView 3008 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentAutoViewAppliances.aspx]]'),(21,1840,'yes','[[Avocent%GPASS%AutoView 3016 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/DigitalKVMAppliances/Pages/AvocentAutoViewAppliances.aspx]]'),(21,636,'yes','[[D-Link%GPASS%DKVM-16 | http://www.dlink.com/products/?sec=0&pid=228]]'),(21,637,'yes','[[D-Link%GPASS%DKVM-8E | http://www.dlink.com/products/?sec=0&pid=161]]'),(21,462,'yes','Avocent AutoView 1415'),(21,463,'yes','Avocent AutoView 1515'),(21,464,'yes','Avocent AutoView 2015'),(21,465,'yes','Avocent AutoView 2020'),(21,466,'yes','Avocent AutoView 2030'),(21,467,'yes','Avocent AutoView 3100'),(21,452,'yes','Avocent DSR1020'),(21,448,'yes','Avocent DSR1021'),(21,449,'yes','Avocent DSR1022'),(21,450,'yes','Avocent DSR1024'),(21,453,'yes','Avocent DSR2020'),(21,454,'yes','Avocent DSR4020'),(21,455,'yes','Avocent DSR8020'),(21,471,'yes','Avocent SwitchView 1000 16-port'),(21,469,'yes','Avocent SwitchView 1000 4-port'),(21,470,'yes','Avocent SwitchView 1000 8-port'),(21,2198,'yes','Raritan%GPASS%Dominion KXII-108'),(21,2199,'yes','Raritan%GPASS%Dominion KXII-116'),(21,2200,'yes','Raritan%GPASS%Dominion KXII-132'),(21,2201,'yes','Raritan%GPASS%Dominion KXIII-108'),(21,2202,'yes','Raritan%GPASS%Dominion KXIII-116'),(21,2203,'yes','Raritan%GPASS%Dominion KXIII-132'),(21,2447,'yes','Raritan%GPASS%Dominion KXIII-216'),(21,2448,'yes','Raritan%GPASS%Dominion KXIII-232'),(21,2449,'yes','Raritan%GPASS%Dominion KXIII-416'),(21,2450,'yes','Raritan%GPASS%Dominion KXIII-432'),(21,2451,'yes','Raritan%GPASS%Dominion KXIII-464'),(21,2452,'yes','Raritan%GPASS%Dominion KXIII-808'),(21,2453,'yes','Raritan%GPASS%Dominion KXIII-832'),(21,2454,'yes','Raritan%GPASS%Dominion KXIII-864'),(21,2455,'yes','TrippLite%GPASS%B051-000'),(23,526,'yes','[[Aten ACS1208AL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413597003&layerid=subClass1]]'),(23,527,'yes','[[Aten ACS1216AL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413597003&layerid=subClass1]]'),(23,525,'yes','[[Aten CL1200 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20050722165040002&layerid=subClass1]]'),(23,523,'yes','[[Aten CL1208 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005072215482&layerid=subClass1]]'),(23,524,'yes','[[Aten CL1216 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005072215482&layerid=subClass1]]'),(23,522,'yes','[[Aten CL1758 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20051229164553003&layerid=subClass1]]'),(23,521,'yes','[[Aten CS1200L | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20050224140854008&layerid=subClass1]]'),(23,519,'yes','[[Aten CS1208DL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413505007&layerid=subClass1]]'),(23,520,'yes','[[Aten CS1216DL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413505007&layerid=subClass1]]'),(23,512,'yes','[[Aten KL1100 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20071225113046001&layerid=subClass1]]'),(23,518,'yes','[[Aten KL1116 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=20060420101520005&layerid=subClass1]]'),(23,513,'yes','[[Aten KL1508 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=20070710020717009&layerid=subClass1]]'),(23,514,'yes','[[Aten KL1516 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=20070716232614001&layerid=subClass1]]'),(23,517,'yes','[[Aten KL3116 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20060913162532009&layerid=subClass1]]'),(23,515,'yes','[[Aten KL9108 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20060811153413009&layerid=subClass1]]'),(23,516,'yes','[[Aten KL9116 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=2006081115384001&layerid=subClass1]]'),(23,1845,'yes','[[Avocent%GPASS%AP17KMMP | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/LCDConsoleTrays/Pages/AvocentLCDConsoleTray.aspx]]'),(23,1844,'yes','[[Avocent%GPASS%ECS17KMM | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/LCDConsoleTrays/Pages/AvocentLCDConsoleTray.aspx]]'),(23,1843,'yes','[[Avocent%GPASS%ECS17KMMP | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/LCDConsoleTrays/Pages/AvocentLCDConsoleTray.aspx]]'),(23,1841,'yes','[[Avocent%GPASS%ECS19PWRUSB | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/LCDConsoleTrays/Pages/AvocentLCDConsoleTray.aspx]]'),(23,1842,'yes','[[Avocent%GPASS%ECS19UWRUSB | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/LCDConsoleTrays/Pages/AvocentLCDConsoleTray.aspx]]'),(23,1846,'yes','[[Avocent%GPASS%LCD17 | http://www.emersonnetworkpower.com/en-US/Products/InfrastructureManagement/LCDConsoleTrays/Pages/AvocentLCDConsoleTray.aspx]]'),(23,2204,'yes','Dell%GPASS%18.5in LED KMM'),(24,717,'yes','[[Cisco%GPASS%ASR 1002 | http://cisco.com/en/US/products/ps9436/index.html]]'),(24,718,'yes','[[Cisco%GPASS%ASR 1004 | http://cisco.com/en/US/products/ps9437/index.html]]'),(24,719,'yes','[[Cisco%GPASS%ASR 1006 | http://cisco.com/en/US/products/ps9438/index.html]]'),(24,806,'yes','[[Cisco%GPASS%IDS 4215 | http://www.cisco.com/en/US/products/hw/vpndevc/ps4077/ps5367/index.html]]'),(24,807,'yes','[[Cisco%GPASS%IDS 4240 | http://www.cisco.com/en/US/products/ps5768/index.html]]'),(24,808,'yes','[[Cisco%GPASS%IDS 4255 | http://www.cisco.com/en/US/products/ps5769/index.html]]'),(24,809,'yes','[[Cisco%GPASS%IDS 4260 | http://www.cisco.com/en/US/products/ps6751/index.html]]'),(24,810,'yes','[[Cisco%GPASS%IDS 4270 | http://www.cisco.com/en/US/products/ps9157/index.html]]'),(24,818,'yes','[[D-Link%GPASS%DFL-1600 | http://www.dlink.com/products/?sec=0&pid=454]]'),(24,819,'yes','[[D-Link%GPASS%DFL-M510 | http://www.dlink.com/products/?sec=0&pid=455]]'),(24,820,'yes','[[Extreme Networks%GPASS%Sentriant AG200 | http://www.extremenetworks.com/products/sentriant-ag200.aspx]]'),(24,821,'yes','[[Extreme Networks%GPASS%Sentriant NG300 | http://www.extremenetworks.com/products/sentriant-ng300.aspx]]'),(24,951,'yes','[[F5%GPASS%FirePass 1200 | http://www.f5.com/pdf/products/firepass-hardware-ds.pdf]]'),(24,952,'yes','[[F5%GPASS%FirePass 4100 | http://www.f5.com/pdf/products/firepass-hardware-ds.pdf]]'),(24,953,'yes','[[F5%GPASS%FirePass 4300 | http://www.f5.com/pdf/products/firepass-hardware-ds.pdf]]'),(24,828,'yes','[[Juniper%GPASS%ISG 1000 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]'),(24,829,'yes','[[Juniper%GPASS%ISG 2000 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]'),(24,830,'yes','[[Juniper%GPASS%NetScreen 5200 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]'),(24,831,'yes','[[Juniper%GPASS%NetScreen 5400 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/isg_series_slash_gprs/index.html]]'),(24,832,'yes','[[Juniper%GPASS%SRX 5600 | http://www.juniper.net/products_and_services/srx_series/index.html]]'),(24,833,'yes','[[Juniper%GPASS%SRX 5800 | http://www.juniper.net/products_and_services/srx_series/index.html]]'),(24,823,'yes','[[Juniper%GPASS%SSG 140 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_140/index.html]]'),(24,824,'yes','[[Juniper%GPASS%SSG 320 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_300_series/index.html]]'),(24,825,'yes','[[Juniper%GPASS%SSG 350 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_300_series/index.html]]'),(24,826,'yes','[[Juniper%GPASS%SSG 520 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_500_series/index.html]]'),(24,827,'yes','[[Juniper%GPASS%SSG 550 | http://www.juniper.net/products_and_services/firewall_slash_ipsec_vpn/ssg_500_series/index.html]]'),(24,840,'yes','[[SonicWall%GPASS%NSA 240 | http://www.sonicwall.com/us/products/NSA_240.html]]'),(24,841,'yes','[[SonicWall%GPASS%NSA 2400 | http://www.sonicwall.com/us/products/NSA_2400.html]]'),(24,842,'yes','[[SonicWall%GPASS%NSA 3500 | http://www.sonicwall.com/us/products/NSA_3500.html]]'),(24,843,'yes','[[SonicWall%GPASS%NSA 4500 | http://www.sonicwall.com/us/products/NSA_4500.html]]'),(24,844,'yes','[[SonicWall%GPASS%NSA 5000 | http://www.sonicwall.com/us/products/NSA_5000.html]]'),(24,845,'yes','[[SonicWall%GPASS%NSA E5500 | http://www.sonicwall.com/us/products/NSA_E5500.html]]'),(24,846,'yes','[[SonicWall%GPASS%NSA E6500 | http://www.sonicwall.com/us/products/NSA_E6500.html]]'),(24,847,'yes','[[SonicWall%GPASS%NSA E7500 | http://www.sonicwall.com/us/products/NSA_E7500.html]]'),(24,834,'yes','[[SonicWall%GPASS%PRO 1260 | http://www.sonicwall.com/us/products/PRO_1260.html]]'),(24,835,'yes','[[SonicWall%GPASS%PRO 2040 | http://www.sonicwall.com/us/products/PRO_2040.html]]'),(24,836,'yes','[[SonicWall%GPASS%PRO 3060 | http://www.sonicwall.com/us/products/PRO_3060.html]]'),(24,837,'yes','[[SonicWall%GPASS%PRO 4060 | http://www.sonicwall.com/us/products/PRO_4060.html]]'),(24,838,'yes','[[SonicWall%GPASS%PRO 4100 | http://www.sonicwall.com/us/products/PRO_4100.html]]'),(24,839,'yes','[[SonicWall%GPASS%PRO 5060 | http://www.sonicwall.com/us/products/PRO_5060.html]]'),(24,799,'yes','Cisco%GPASS%ASA 5505'),(24,800,'yes','Cisco%GPASS%ASA 5510'),(24,2183,'yes','Cisco%GPASS%ASA 5512-X'),(24,2184,'yes','Cisco%GPASS%ASA 5515-X'),(24,801,'yes','Cisco%GPASS%ASA 5520'),(24,2185,'yes','Cisco%GPASS%ASA 5525-X'),(24,802,'yes','Cisco%GPASS%ASA 5540'),(24,2186,'yes','Cisco%GPASS%ASA 5545-X'),(24,803,'yes','Cisco%GPASS%ASA 5550'),(24,2187,'yes','Cisco%GPASS%ASA 5555-X'),(24,804,'yes','Cisco%GPASS%ASA 5580-20'),(24,805,'yes','Cisco%GPASS%ASA 5580-40'),(24,2188,'yes','Cisco%GPASS%ASA 5585-X'),(24,2574,'yes','Cisco%GPASS%ASR 920'),(24,822,'yes','Force10%GPASS%P-Series'),(24,811,'yes','Foundry%GPASS%SecureIron 100'),(24,812,'yes','Foundry%GPASS%SecureIron 100C'),(24,813,'yes','Foundry%GPASS%SecureIron 300'),(24,814,'yes','Foundry%GPASS%SecureIron 300C'),(24,815,'yes','Foundry%GPASS%SecureIronLS 100-4802'),(24,816,'yes','Foundry%GPASS%SecureIronLS 300-32GC02'),(24,817,'yes','Foundry%GPASS%SecureIronLS 300-32GC10G'),(24,287,'yes','Juniper%GPASS%NetScreen 100'),(24,2438,'yes','Palo Alto Networks%GPASS%PA-200'),(24,2440,'yes','Palo Alto Networks%GPASS%PA-3020'),(24,2441,'yes','Palo Alto Networks%GPASS%PA-3050'),(24,2442,'yes','Palo Alto Networks%GPASS%PA-3060'),(24,2439,'yes','Palo Alto Networks%GPASS%PA-500'),(24,2443,'yes','Palo Alto Networks%GPASS%PA-5020'),(24,2444,'yes','Palo Alto Networks%GPASS%PA-5050'),(24,2445,'yes','Palo Alto Networks%GPASS%PA-5060'),(25,971,'yes','[[Cisco%GPASS%Aironet 1140 | http://cisco.com/en/US/products/ps10092/index.html]]'),(25,972,'yes','[[Cisco%GPASS%Aironet 1200 | http://cisco.com/en/US/products/hw/wireless/ps430/ps4076/index.html]]'),(25,973,'yes','[[Cisco%GPASS%Aironet 1230 AG | http://cisco.com/en/US/products/ps6132/index.html]]'),(25,974,'yes','[[Cisco%GPASS%Aironet 1240 AG | http://cisco.com/en/US/products/ps6521/index.html]]'),(25,975,'yes','[[Cisco%GPASS%Aironet 1250 | http://cisco.com/en/US/products/ps8382/index.html]]'),(25,976,'yes','[[Cisco%GPASS%Aironet 1520 | http://cisco.com/en/US/products/ps8368/index.html]]'),(25,966,'yes','Cisco%GPASS%2106'),(25,967,'yes','Cisco%GPASS%2112'),(25,968,'yes','Cisco%GPASS%2125'),(25,969,'yes','Cisco%GPASS%4402'),(25,970,'yes','Cisco%GPASS%4404'),(25,2024,'yes','Cisco%GPASS%877'),(25,2025,'yes','Cisco%GPASS%878'),(25,1964,'yes','Cisco%GPASS%AIR-AP1041N'),(25,1965,'yes','Cisco%GPASS%AIR-AP1042N'),(25,1311,'yes','Cisco%GPASS%AIR-AP1121G'),(25,1309,'yes','Cisco%GPASS%AIR-AP1131AG'),(25,1310,'yes','Cisco%GPASS%AIR-AP1131G'),(25,1467,'yes','Cisco%GPASS%AIR-AP1141N'),(25,1307,'yes','Cisco%GPASS%AIR-AP1231G'),(25,1308,'yes','Cisco%GPASS%AIR-AP1232AG'),(25,1305,'yes','Cisco%GPASS%AIR-AP1242AG'),(25,1306,'yes','Cisco%GPASS%AIR-AP1242G'),(25,1303,'yes','Cisco%GPASS%AIR-AP1252AG'),(25,1304,'yes','Cisco%GPASS%AIR-AP1252G'),(25,1589,'yes','Cisco%GPASS%AIR-AP1261N'),(25,1468,'yes','Cisco%GPASS%AIR-AP1262N'),(25,1312,'yes','Cisco%GPASS%AIR-AP521G'),(25,1302,'yes','Cisco%GPASS%AIR-BR1310G'),(25,1301,'yes','Cisco%GPASS%AIR-BR1410A'),(25,2150,'yes','Cisco%GPASS%AIR-LAP1142N'),(25,1313,'yes','Cisco%GPASS%AIR-WLC2106'),(25,1315,'yes','Cisco%GPASS%AIR-WLC4402'),(25,1314,'yes','Cisco%GPASS%AIR-WLC526'),(25,977,'yes','Foundry%GPASS%AP150'),(25,979,'yes','Foundry%GPASS%AP201'),(25,980,'yes','Foundry%GPASS%AP208'),(25,981,'yes','Foundry%GPASS%AP250'),(25,982,'yes','Foundry%GPASS%AP300'),(25,985,'yes','Foundry%GPASS%MC1000'),(25,986,'yes','Foundry%GPASS%MC3000'),(25,987,'yes','Foundry%GPASS%MC4100'),(25,984,'yes','Foundry%GPASS%MC500'),(25,988,'yes','Foundry%GPASS%MC5000'),(25,978,'yes','Foundry%GPASS%OAP180'),(25,983,'yes','Foundry%GPASS%RS4000'),(25,1795,'yes','Motorola%GPASS%RFS 4000'),(26,1009,'yes','[[Cisco%GPASS%MDS 9124 | http://www.cisco.com/en/US/products/ps7079/index.html]]'),(26,1010,'yes','[[Cisco%GPASS%MDS 9134 | http://www.cisco.com/en/US/products/ps8414/index.html]]'),(26,1529,'yes','Brocade (blade)%GPASS%M4424'),(26,1530,'yes','Brocade (blade)%GPASS%M5424'),(26,1526,'yes','Brocade (blade)%GPASS%McDATA 3014'),(26,1527,'yes','Brocade (blade)%GPASS%McDATA 4314'),(26,1528,'yes','Brocade (blade)%GPASS%McDATA 4416'),(26,1004,'yes','Brocade%GPASS%300'),(26,1005,'yes','Brocade%GPASS%4900'),(26,1006,'yes','Brocade%GPASS%5000'),(26,1007,'yes','Brocade%GPASS%5100'),(26,1008,'yes','Brocade%GPASS%5300'),(26,1071,'yes','Brocade%GPASS%Silkworm 2400'),(26,1072,'yes','Brocade%GPASS%Silkworm 2800'),(26,1073,'yes','Brocade%GPASS%Silkworm 3200'),(26,1074,'yes','Brocade%GPASS%Silkworm 3800'),(26,1075,'yes','Brocade%GPASS%Silkworm 3900'),(26,1076,'yes','Brocade%GPASS%Silkworm 4100'),(26,1011,'yes','QLogic%GPASS%1400'),(26,1012,'yes','QLogic%GPASS%3800'),(26,1013,'yes','QLogic%GPASS%5600Q'),(26,1014,'yes','QLogic%GPASS%5800V'),(26,1015,'yes','QLogic%GPASS%9000'),(27,1111,'yes','[[APC%GPASS%AP7152 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7152]]'),(27,1112,'yes','[[APC%GPASS%AP7155 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7155]]'),(27,1113,'yes','[[APC%GPASS%AP7175 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7175]]'),(27,1114,'yes','[[APC%GPASS%AP7526 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7526]]'),(27,1115,'yes','[[APC%GPASS%AP7551 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7551]]'),(27,1116,'yes','[[APC%GPASS%AP7552 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7552]]'),(27,1117,'yes','[[APC%GPASS%AP7553 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7553]]'),(27,1118,'yes','[[APC%GPASS%AP7554 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7554]]'),(27,1119,'yes','[[APC%GPASS%AP7555 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7555]]'),(27,1120,'yes','[[APC%GPASS%AP7557 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7557]]'),(27,1121,'yes','[[APC%GPASS%AP7585 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7585]]'),(27,1122,'yes','[[APC%GPASS%AP7586 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7586]]'),(27,1123,'yes','[[APC%GPASS%AP7611 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7611]]'),(27,1124,'yes','[[APC%GPASS%AP7631 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7631]]'),(27,1125,'yes','[[APC%GPASS%AP7820 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7820]]'),(27,1126,'yes','[[APC%GPASS%AP7821 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7821]]'),(27,1127,'yes','[[APC%GPASS%AP7822 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7822]]'),(27,1128,'yes','[[APC%GPASS%AP7850 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7850]]'),(27,1129,'yes','[[APC%GPASS%AP7851 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7851]]'),(27,1130,'yes','[[APC%GPASS%AP7852 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7852]]'),(27,1131,'yes','[[APC%GPASS%AP7853 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7853]]'),(27,1132,'yes','[[APC%GPASS%AP7854 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7854]]'),(27,1133,'yes','[[APC%GPASS%AP7855A | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7855A]]'),(27,1134,'yes','[[APC%GPASS%AP7856 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7856]]'),(27,1135,'yes','[[APC%GPASS%AP7856A | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7856A]]'),(27,1136,'yes','[[APC%GPASS%AP7857 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7857]]'),(27,1154,'yes','[[APC%GPASS%AP7900 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7900]]'),(27,1155,'yes','[[APC%GPASS%AP7901 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7901]]'),(27,1156,'yes','[[APC%GPASS%AP7902 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7902]]'),(27,1151,'yes','[[APC%GPASS%AP7902J | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7902J]]'),(27,1160,'yes','[[APC%GPASS%AP7911 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7911]]'),(27,1137,'yes','[[APC%GPASS%AP7920 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7920]]'),(27,1138,'yes','[[APC%GPASS%AP7921 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7921]]'),(27,1139,'yes','[[APC%GPASS%AP7922 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7922]]'),(27,1157,'yes','[[APC%GPASS%AP7930 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7930]]'),(27,1152,'yes','[[APC%GPASS%AP7930J | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7930J]]'),(27,1158,'yes','[[APC%GPASS%AP7931 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7931]]'),(27,1159,'yes','[[APC%GPASS%AP7932 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7932]]'),(27,1153,'yes','[[APC%GPASS%AP7932J | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7932J]]'),(27,1161,'yes','[[APC%GPASS%AP7940 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7940]]'),(27,1162,'yes','[[APC%GPASS%AP7941 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7941]]'),(27,1140,'yes','[[APC%GPASS%AP7950 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7950]]'),(27,1141,'yes','[[APC%GPASS%AP7951 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7951]]'),(27,1142,'yes','[[APC%GPASS%AP7952 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7952]]'),(27,1143,'yes','[[APC%GPASS%AP7953 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7953]]'),(27,1144,'yes','[[APC%GPASS%AP7954 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7954]]'),(27,1145,'yes','[[APC%GPASS%AP7957 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7957]]'),(27,1163,'yes','[[APC%GPASS%AP7960 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7960]]'),(27,1164,'yes','[[APC%GPASS%AP7961 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7961]]'),(27,1165,'yes','[[APC%GPASS%AP7968 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7968]]'),(27,1166,'yes','[[APC%GPASS%AP7990 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7990]]'),(27,1167,'yes','[[APC%GPASS%AP7991 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7991]]'),(27,1168,'yes','[[APC%GPASS%AP7998 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP7998]]'),(27,2178,'yes','[[APC%GPASS%AP8941 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP8941]]'),(27,2179,'yes','[[APC%GPASS%AP8959EU3 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP8959EU3]]'),(27,1146,'yes','[[APC%GPASS%AP9559 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP9559]]'),(27,1147,'yes','[[APC%GPASS%AP9565 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP9565]]'),(27,1148,'yes','[[APC%GPASS%AP9568 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP9568]]'),(27,1149,'yes','[[APC%GPASS%AP9572 | http://www.apc.com/products/resource/include/techspec_index.cfm?base_sku=AP9572]]'),(28,1324,'yes','Cisco%GPASS%MCS 7816'),(28,1325,'yes','Cisco%GPASS%MCS 7825'),(28,1326,'yes','Cisco%GPASS%MCS 7835'),(29,1500,'yes','No'),(29,1501,'yes','Yes'),(30,1150,'yes','[[Cisco%GPASS%Catalyst 6509-V-E%L1,9V% | http://www.cisco.com/en/US/products/ps9306/index.html]]'),(30,156,'yes','Cisco%GPASS%Catalyst 4506%L6,1H%'),(30,157,'yes','Cisco%GPASS%Catalyst 4507R%L7,1H%'),(30,158,'yes','Cisco%GPASS%Catalyst 4510R%L10,1H%'),(30,152,'yes','Cisco%GPASS%Catalyst 6503-E%L3,1H%'),(30,153,'yes','Cisco%GPASS%Catalyst 6503%L3,1H%'),(30,151,'yes','Cisco%GPASS%Catalyst 6504-E%L4,1H%'),(30,150,'yes','Cisco%GPASS%Catalyst 6506-E%L6,1H%'),(30,154,'yes','Cisco%GPASS%Catalyst 6506%L6,1H%'),(30,148,'yes','Cisco%GPASS%Catalyst 6509-E%L9,1H%'),(30,149,'yes','Cisco%GPASS%Catalyst 6509-NEB-A%L9,1H%'),(30,155,'yes','Cisco%GPASS%Catalyst 6509-NEB%L9,1H%'),(30,369,'yes','Cisco%GPASS%Catalyst 6509%L9,1H%'),(30,2227,'yes','Cisco%GPASS%Catalyst 6807-XL%L7,1H%'),(30,1969,'yes','Enterasys%GPASS%K10%L5,2H%'),(30,1968,'yes','Enterasys%GPASS%K6%L3,2H%'),(30,2020,'yes','Enterasys%GPASS%N1%L1,1H%'),(30,2021,'yes','Enterasys%GPASS%N3%L3,1H%'),(30,2022,'yes','Enterasys%GPASS%N5%L5,1H%'),(30,2023,'yes','Enterasys%GPASS%N7%L1,7V%'),(30,1973,'yes','Enterasys%GPASS%S1%L1,1H%'),(30,1974,'yes','Enterasys%GPASS%S3%L3,1H%'),(30,1975,'yes','Enterasys%GPASS%S4%L4,1H%'),(30,1976,'yes','Enterasys%GPASS%S6%L6,1H%'),(30,1977,'yes','Enterasys%GPASS%S8%L8,1H%'),(30,935,'yes','F5%GPASS%VIPRION 2400%L2,2H%'),(30,2033,'yes','F5%GPASS%VIPRION 4480%L4,1H%'),(30,2034,'yes','F5%GPASS%VIPRION 4800%L1,8V%'),(30,888,'yes','HP ProCurve%GPASS%5406zl J8697A%L4,2H%'),(30,889,'yes','HP ProCurve%GPASS%5406zl-48G J8699A%L4,2H%'),(30,1625,'yes','HP ProCurve%GPASS%5406zl-48G PoE+ J9447A%L4,2H%'),(30,890,'yes','HP ProCurve%GPASS%5412zl J8698A%L7,2H%'),(30,891,'yes','HP ProCurve%GPASS%5412zl-96G J8700A%L7,2H%'),(30,1626,'yes','HP ProCurve%GPASS%5412zl-96G PoE+ J9448A%L7,2H%'),(30,2541,'yes','Ixia%GPASS%400Tv2'),(30,2537,'yes','Ixia%GPASS%XG12'),(30,2539,'yes','Ixia%GPASS%XGS12-HS'),(30,2540,'yes','Ixia%GPASS%XGS12-SD'),(30,2538,'yes','Ixia%GPASS%XGS2'),(30,2536,'yes','Ixia%GPASS%XM12'),(30,2535,'yes','Ixia%GPASS%XM2'),(31,1822,'yes','[[Fujitsu%GSKIP%PRIMERGY BX400 S1%L1,9V% | http://www.fujitsu.com/fts/products/computing/servers/primergy/blades/bx400/index.html]]'),(31,1823,'yes','[[Fujitsu%GSKIP%PRIMERGY BX900 S2%L2,9V% | http://www.fujitsu.com/fts/products/computing/servers/primergy/blades/bx900s2/index.html]]'),(31,2220,'yes','Cisco%GPASS%UCS 5108 AC2 Blade Chassis%L4,2H%'),(31,1735,'yes','Cisco%GPASS%UCS 5108 Blade Chassis%L4,2H%'),(31,2221,'yes','Cisco%GPASS%UCS 5108 DC2 Blade Chassis%L4,2H%'),(31,2222,'yes','Cisco%GPASS%UCS 5108 HVDC Blade Chassis%L4,2H%'),(31,1684,'yes','Dell PowerEdge C%GPASS%C410x%L1,10V%'),(31,1685,'yes','Dell PowerEdge C%GPASS%C5000%L1,12V%'),(31,1517,'yes','Dell PowerEdge%GPASS%1855%L1,10V%'),(31,994,'yes','Dell PowerEdge%GPASS%M1000e%L2,8V%'),(31,1475,'yes','IBM%GPASS%BladeCenter E%L1,14V%'),(31,1474,'yes','IBM%GPASS%BladeCenter H%L1,14V%'),(31,1477,'yes','IBM%GPASS%BladeCenter HT'),(31,1473,'yes','IBM%GPASS%BladeCenter S'),(31,1476,'yes','IBM%GPASS%BladeCenter T'),(32,1558,'yes','Cisco%GPASS%Nexus 1000V'),(32,1557,'yes','VMware%GPASS%Distributed vSwitch'),(32,1556,'yes','VMware%GPASS%Standard vSwitch'),(33,1559,'yes','NS-OS 4.0'),(34,1400,'yes','Cisco%GPASS%RPS 2300'),(34,1401,'yes','D-Link%GPASS%DPS-800'),(34,1402,'yes','D-Link%GPASS%DPS-900'),(35,1849,'yes','[[Cisco%GPASS%RPS 2300 | http://www.cisco.com/en/US/products/ps7130/index.html]]'),(35,1405,'yes','Cisco%GPASS%C3K-PWR-1150WAC'),(35,1404,'yes','Cisco%GPASS%C3K-PWR-750WAC'),(35,1403,'yes','Cisco%GPASS%RPS 675'),(35,1406,'yes','D-Link%GPASS%DPS-200'),(35,1407,'yes','D-Link%GPASS%DPS-500'),(35,1408,'yes','D-Link%GPASS%DPS-510'),(35,1409,'yes','D-Link%GPASS%DPS-600'),(36,1653,'yes','[[Moxa%GPASS%CN2510-16 | http://www.moxa.com/product/CN2510.htm]]'),(36,1652,'yes','[[Moxa%GPASS%CN2510-8 | http://www.moxa.com/product/CN2510.htm]]'),(36,1655,'yes','[[Moxa%GPASS%CN2610-16 | http://www.moxa.com/product/CN2610.htm]]'),(36,1654,'yes','[[Moxa%GPASS%CN2610-8 | http://www.moxa.com/product/CN2610.htm]]'),(36,1657,'yes','[[Moxa%GPASS%CN2650-16 | http://www.moxa.com/product/CN2610.htm]]'),(36,1656,'yes','[[Moxa%GPASS%CN2650-8 | http://www.moxa.com/product/CN2610.htm]]'),(36,1645,'yes','[[Moxa%GPASS%NPort 6150 | http://www.moxa.com/product/NPort_6150.htm]]'),(36,1658,'yes','[[Moxa%GPASS%NPort 6250 | http://www.moxa.com/product/NPort_6250.htm]]'),(36,1659,'yes','[[Moxa%GPASS%NPort 6450 | http://www.moxa.com/product/NPort_6450.htm]]'),(36,1647,'yes','[[Moxa%GPASS%NPort 6610-16 | http://www.moxa.com/product/NPort_6650.htm]]'),(36,1648,'yes','[[Moxa%GPASS%NPort 6610-32 | http://www.moxa.com/product/NPort_6650.htm]]'),(36,1646,'yes','[[Moxa%GPASS%NPort 6610-8 | http://www.moxa.com/product/NPort_6650.htm]]'),(36,1650,'yes','[[Moxa%GPASS%NPort 6650-16 | http://www.moxa.com/product/NPort_6650.htm]]'),(36,1651,'yes','[[Moxa%GPASS%NPort 6650-32 | http://www.moxa.com/product/NPort_6650.htm]]'),(36,1649,'yes','[[Moxa%GPASS%NPort 6650-8 | http://www.moxa.com/product/NPort_6650.htm]]'),(36,1660,'yes','[[Moxa%GPASS%NPort S8458 | http://www.moxa.com/product/NPort_S8458_Series.htm]]'),(36,2205,'yes','Raritan%GPASS%Dominion SX4'),(36,2206,'yes','Raritan%GPASS%Dominion SX8'),(36,2208,'yes','Raritan%GPASS%Dominion SXA-16'),(36,2209,'yes','Raritan%GPASS%Dominion SXA-16-DL'),(36,2210,'yes','Raritan%GPASS%Dominion SXA-16-DLM'),(36,2207,'yes','Raritan%GPASS%Dominion SXA-8'),(37,1673,'yes','Cisco Aironet IOS 12.3'),(37,1674,'yes','Cisco Aironet IOS 12.4'),(37,2476,'yes','OpenWrt 14'),(37,2477,'yes','OpenWrt 15'),(37,2478,'yes','RouterOS 6'),(38,1788,'yes','Cisco%GPASS%UCS Domain'),(38,2144,'yes','Cisco%GPASS%Wireless Controller'),(38,1789,'yes','Generic%GPASS%Switch stack'),(38,1790,'yes','VMware%GPASS%vSphere instance'),(9999,2153,'yes','[[Alcatel-Lucent%GPASS%1642 EMC | http://www.alcatel-lucent.com/products/1642-edge-multiplexer-compact]]'),(9999,482,'yes','[[Cronyx%GPASS%E1-DXC/S | http://www.cronyx.ru/hardware/e1dxc-s.html]]'),(9999,481,'yes','[[Cronyx%GPASS%E1-XL/S | http://www.cronyx.ru/hardware/e1xl-s.html]]'),(9999,486,'yes','[[Cronyx%GPASS%E1-XL/S-IP | http://www.cronyx.ru/hardware/e1xl-ip.html]]'),(9999,484,'yes','[[Cronyx%GPASS%FMUX-16-E3 | http://www.cronyx.ru/hardware/fmux16-e3.html]]'),(9999,483,'yes','[[Cronyx%GPASS%FMUX-4-E2 | http://www.cronyx.ru/hardware/fmux4-e2.html]]'),(9999,478,'yes','[[Cronyx%GPASS%FMUX/S-16E1 | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,479,'yes','[[Cronyx%GPASS%FMUX/S-16E1/ETS | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,480,'yes','[[Cronyx%GPASS%FMUX/S-16E1/M | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,472,'yes','[[Cronyx%GPASS%FMUX/S-4E1 | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,473,'yes','[[Cronyx%GPASS%FMUX/S-4E1/ETS | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,474,'yes','[[Cronyx%GPASS%FMUX/S-4E1/M | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,475,'yes','[[Cronyx%GPASS%FMUX/S-8E1 | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,476,'yes','[[Cronyx%GPASS%FMUX/S-8E1/ETS | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,477,'yes','[[Cronyx%GPASS%FMUX/S-8E1/M | http://www.cronyx.ru/hardware/fmux-ring.html]]'),(9999,485,'yes','[[Cronyx%GPASS%FMUX/SAT | http://www.cronyx.ru/hardware/fmux-sat.html]]'),(9999,490,'yes','[[RAD%GPASS%FCD-155E | http://www.rad.com/Article/0,6583,36276-Ethernet_over_SDH_SONET_ADM,00.html]]'),(9999,488,'yes','[[RAD%GPASS%FCD-E1M | http://www.rad.com/Article/0,6583,36723-E1_T1_Modular_Access_Multiplexer,00.html]]'),(9999,489,'yes','[[RAD%GPASS%FCD-T1M | http://www.rad.com/Article/0,6583,36723-E1_T1_Modular_Access_Multiplexer,00.html]]'),(9999,528,'yes','[[Tainet%GPASS%MUXpro 8216 | http://www.tainet.net/Product/muxpro820_8216.htm]]'),(9999,640,'yes','Raisecom%GPASS%ISCOM4300'),(9999,648,'yes','Raisecom%GPASS%OPCOM3100-155'),(9999,649,'yes','Raisecom%GPASS%OPCOM3101-155'),(9999,638,'yes','Raisecom%GPASS%RC702'),(9999,639,'yes','Raisecom%GPASS%RC702-GE'),(9999,672,'yes','Raisecom%GPASS%RC801-120B'),(9999,673,'yes','Raisecom%GPASS%RC801-240B'),(9999,674,'yes','Raisecom%GPASS%RC801-480B'),(9999,675,'yes','Raisecom%GPASS%RC803-120B'),(9999,676,'yes','Raisecom%GPASS%RC803-240B'),(9999,677,'yes','Raisecom%GPASS%RC803-480B'),(9999,678,'yes','Raisecom%GPASS%RC805-120B'),(9999,679,'yes','Raisecom%GPASS%RC805-240B'),(9999,680,'yes','Raisecom%GPASS%RC805-480B'),(9999,650,'yes','Raisecom%GPASS%RC831-120'),(9999,651,'yes','Raisecom%GPASS%RC831-120-BL'),(9999,652,'yes','Raisecom%GPASS%RC831-240'),(9999,653,'yes','Raisecom%GPASS%RC831-240E'),(9999,645,'yes','Raisecom%GPASS%RC953-8FE16E1'),(9999,641,'yes','Raisecom%GPASS%RC953-FE4E1'),(9999,643,'yes','Raisecom%GPASS%RC953-FE8E1'),(9999,642,'yes','Raisecom%GPASS%RC953-FX4E1'),(9999,644,'yes','Raisecom%GPASS%RC953-FX8E1'),(9999,647,'yes','Raisecom%GPASS%RC953-GESTM1'),(9999,646,'yes','Raisecom%GPASS%RC953E-3FE16E1'),(9999,666,'yes','Raisecom%GPASS%RCMS2104-120'),(9999,669,'yes','Raisecom%GPASS%RCMS2104-240'),(9999,667,'yes','Raisecom%GPASS%RCMS2304-120'),(9999,670,'yes','Raisecom%GPASS%RCMS2304-240'),(9999,668,'yes','Raisecom%GPASS%RCMS2504-120'),(9999,671,'yes','Raisecom%GPASS%RCMS2504-240'),(9999,655,'yes','Raisecom%GPASS%RCMS2801-120FE'),(9999,656,'yes','Raisecom%GPASS%RCMS2801-120FE-BL'),(9999,659,'yes','Raisecom%GPASS%RCMS2801-240EFE'),(9999,657,'yes','Raisecom%GPASS%RCMS2801-240FE'),(9999,658,'yes','Raisecom%GPASS%RCMS2801-240FE-BL'),(9999,654,'yes','Raisecom%GPASS%RCMS2801-480GE-BL'),(9999,660,'yes','Raisecom%GPASS%RCMS2811-120FE'),(9999,665,'yes','Raisecom%GPASS%RCMS2811-240EFE'),(9999,661,'yes','Raisecom%GPASS%RCMS2811-240FE'),(9999,662,'yes','Raisecom%GPASS%RCMS2811-240FE-BL'),(9999,663,'yes','Raisecom%GPASS%RCMS2811-480FE'),(9999,664,'yes','Raisecom%GPASS%RCMS2811-480FE-BL'),(9999,533,'yes','Tainet%GPASS%DSD-08A'),(9999,529,'yes','Tainet%GPASS%Mercury 3600+'),(9999,531,'yes','Tainet%GPASS%Mercury 3630'),(9999,532,'yes','Tainet%GPASS%Mercury 3630E'),(9999,530,'yes','Tainet%GPASS%Mercury 3820');
/*!40000 ALTER TABLE `Dictionary` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `EntityLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EntityLink` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_entity_type` enum('location','object','rack','row') COLLATE utf8_unicode_ci NOT NULL,
  `parent_entity_id` int(10) unsigned NOT NULL,
  `child_entity_type` enum('location','object','rack','row') COLLATE utf8_unicode_ci NOT NULL,
  `child_entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `EntityLink-unique` (`parent_entity_type`,`parent_entity_id`,`child_entity_type`,`child_entity_id`),
  KEY `EntityLink-compound` (`parent_entity_type`,`child_entity_type`,`child_entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `EntityLink` WRITE;
/*!40000 ALTER TABLE `EntityLink` DISABLE KEYS */;
INSERT INTO `EntityLink` VALUES (9,'location',975,'row',963),(10,'location',976,'row',964),(11,'location',977,'row',965),(12,'location',978,'row',966),(15,'object',982,'object',983),(13,'rack',968,'object',979),(14,'rack',969,'object',980),(6,'row',963,'rack',972),(8,'row',964,'rack',974),(4,'row',965,'rack',970),(5,'row',965,'rack',971),(7,'row',965,'rack',973),(1,'row',966,'rack',967),(2,'row',966,'rack',968),(3,'row',966,'rack',969);
/*!40000 ALTER TABLE `EntityLink` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`racktables`@`localhost`*/ /*!50003 TRIGGER `EntityLink-before-insert` BEFORE INSERT ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  # forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  # in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id;
    ELSE
      # some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    # lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    # only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`racktables`@`localhost`*/ /*!50003 TRIGGER `EntityLink-before-update` BEFORE UPDATE ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  # forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  # in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    ELSE
      # some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    # lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    # only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `File`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `File` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `mtime` datetime NOT NULL,
  `atime` datetime NOT NULL,
  `thumbnail` longblob,
  `contents` longblob NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `File` WRITE;
/*!40000 ALTER TABLE `File` DISABLE KEYS */;
/*!40000 ALTER TABLE `File` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `FileLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileLink` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `entity_type` enum('ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','row','user') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'object',
  `entity_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FileLink-unique` (`file_id`,`entity_type`,`entity_id`),
  KEY `FileLink-file_id` (`file_id`),
  CONSTRAINT `FileLink-File_fkey` FOREIGN KEY (`file_id`) REFERENCES `File` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `FileLink` WRITE;
/*!40000 ALTER TABLE `FileLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `FileLink` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4Address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4Address` (
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `reserved` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4Address` WRITE;
/*!40000 ALTER TABLE `IPv4Address` DISABLE KEYS */;
INSERT INTO `IPv4Address` VALUES (180879360,'','',NULL),(180879361,'','',NULL),(180879616,'network','','yes'),(180879617,'','',NULL),(180879618,'','',NULL),(180879675,'','',NULL),(180879676,'','',NULL),(180879677,'','',NULL),(180879678,'default gw','','no'),(180879679,'broadcast','','yes'),(180879680,'network','','yes'),(180879681,'','',NULL),(180879683,'','',NULL),(180879684,'','',NULL),(180879685,'','',NULL),(180879686,'','',NULL),(180879687,'','',NULL),(180879743,'broadcast','','yes'),(180879872,'network','','yes'),(180879935,'broadcast','','yes'),(180879936,'network','','yes'),(180879999,'broadcast','','yes'),(180880128,'network','','no'),(180880191,'broadcast','','no'),(180880192,'network','','yes'),(180880193,'','',NULL),(180880194,'','',NULL),(180880195,'','',NULL),(180880254,'','',NULL),(180880255,'broadcast','','yes'),(180880384,'network','','yes'),(180880447,'broadcast','','yes'),(180880448,'network','','yes'),(180880504,'for field engineer','','yes'),(180880511,'broadcast','','yes');
/*!40000 ALTER TABLE `IPv4Address` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4Allocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4Allocation` (
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` enum('regular','shared','virtual','router','point2point') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'regular',
  PRIMARY KEY (`object_id`,`ip`),
  KEY `ip` (`ip`),
  CONSTRAINT `IPv4Allocation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4Allocation` WRITE;
/*!40000 ALTER TABLE `IPv4Allocation` DISABLE KEYS */;
INSERT INTO `IPv4Allocation` VALUES (905,180879361,'se1/1','regular'),(905,180879362,'se1/0','regular'),(905,180879678,'fa2/0','router'),(906,180879617,'','regular'),(907,180879363,'se1/1','regular'),(907,180879364,'se1/0','regular'),(907,180879998,'','shared'),(908,180879360,'se1/0','regular'),(908,180879367,'se1/1','regular'),(908,180880446,'fa1/0','router'),(908,180880510,'fa2/0','router'),(909,180879365,'se1/1','regular'),(909,180879366,'se1/0','regular'),(909,180880254,'fa2/0','router'),(910,180879675,'','virtual'),(910,180879681,'eth0','regular'),(911,180879675,'','virtual'),(911,180879682,'eth0','regular'),(912,180879675,'','virtual'),(912,180879683,'eth0','regular'),(913,180879676,'','virtual'),(913,180879684,'eth0','regular'),(914,180879676,'','virtual'),(914,180879685,'eth0','regular'),(915,180879618,'telnet access','regular'),(915,180879675,'VIP1','regular'),(915,180879676,'VIP2','regular'),(915,180879677,'VIP3','regular'),(918,180879677,'','virtual'),(918,180879686,'bge0','regular'),(919,180879677,'','virtual'),(919,180879687,'bge0','regular'),(923,180880193,'eth0','regular'),(924,180880194,'eth0','regular'),(925,180880195,'eth0','regular'),(927,180879998,'','shared'),(928,180879877,'','regular'),(929,180879878,'','regular'),(932,180879973,'','regular'),(933,180879978,'','regular'),(934,180879974,'','regular'),(935,180879979,'','regular'),(936,180879975,'','regular'),(937,180879980,'','regular'),(938,180879976,'','regular'),(939,180879981,'','regular'),(940,180879977,'','regular'),(941,180879982,'','regular'),(956,180880449,'eth0','regular'),(957,180880450,'eth0','regular'),(958,180880451,'eth0','regular'),(959,180880452,'eth0','regular'),(960,180880453,'eth0','regular'),(961,180880385,'','regular'),(962,180880386,'','regular');
/*!40000 ALTER TABLE `IPv4Allocation` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4LB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4LB` (
  `object_id` int(10) unsigned DEFAULT NULL,
  `rspool_id` int(10) unsigned DEFAULT NULL,
  `vs_id` int(10) unsigned DEFAULT NULL,
  `prio` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  UNIQUE KEY `LB-VS` (`object_id`,`vs_id`),
  KEY `IPv4LB-FK-rspool_id` (`rspool_id`),
  KEY `IPv4LB-FK-vs_id` (`vs_id`),
  CONSTRAINT `IPv4LB-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `IPv4LB-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`),
  CONSTRAINT `IPv4LB-FK-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `IPv4VS` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4LB` WRITE;
/*!40000 ALTER TABLE `IPv4LB` DISABLE KEYS */;
INSERT INTO `IPv4LB` VALUES (928,1,1,NULL,NULL,NULL),(929,1,1,NULL,NULL,NULL),(929,2,2,NULL,NULL,NULL);
/*!40000 ALTER TABLE `IPv4LB` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4Log` WRITE;
/*!40000 ALTER TABLE `IPv4Log` DISABLE KEYS */;
/*!40000 ALTER TABLE `IPv4Log` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4NAT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4NAT` (
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `proto` enum('TCP','UDP','ALL') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TCP',
  `localip` int(10) unsigned NOT NULL DEFAULT '0',
  `localport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `remoteip` int(10) unsigned NOT NULL DEFAULT '0',
  `remoteport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `description` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`object_id`,`proto`,`localip`,`localport`,`remoteip`,`remoteport`),
  KEY `localip` (`localip`),
  KEY `remoteip` (`remoteip`),
  KEY `object_id` (`object_id`),
  CONSTRAINT `IPv4NAT-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4NAT` WRITE;
/*!40000 ALTER TABLE `IPv4NAT` DISABLE KEYS */;
INSERT INTO `IPv4NAT` VALUES (915,'TCP',180879675,80,180879681,80,''),(915,'TCP',180879675,80,180879682,80,''),(915,'TCP',180879675,80,180879683,80,''),(915,'TCP',180879677,443,180879686,443,''),(915,'TCP',180879677,443,180879687,443,''),(915,'UDP',180879676,53,180879684,53,''),(915,'UDP',180879676,53,180879685,53,'');
/*!40000 ALTER TABLE `IPv4NAT` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4Network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4Network` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `mask` int(10) unsigned NOT NULL DEFAULT '0',
  `name` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base-len` (`ip`,`mask`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4Network` WRITE;
/*!40000 ALTER TABLE `IPv4Network` DISABLE KEYS */;
INSERT INTO `IPv4Network` VALUES (96,180879616,26,'London network devices and VIPs',NULL),(97,180879680,26,'London HA server farm',NULL),(98,180879872,26,'New-York network devices',NULL),(99,180879936,26,'New-York servers',NULL),(102,180879360,31,'M-L P2P',NULL),(103,180879362,31,'L-NY P2P',NULL),(104,180879364,31,'NY-T P2P',NULL),(105,180879366,31,'T-M P2P',NULL),(106,180880384,26,'Moscow network devices',NULL),(107,180880448,26,'Moscow servers',NULL),(108,180880192,26,'Tokyo server farm',NULL);
/*!40000 ALTER TABLE `IPv4Network` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4RS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4RS` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inservice` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `rsip` varbinary(16) NOT NULL,
  `rsport` smallint(5) unsigned DEFAULT NULL,
  `rspool_id` int(10) unsigned DEFAULT NULL,
  `rsconfig` text COLLATE utf8_unicode_ci,
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pool-endpoint` (`rspool_id`,`rsip`,`rsport`),
  KEY `rsip` (`rsip`),
  CONSTRAINT `IPv4RS-FK` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4RS` WRITE;
/*!40000 ALTER TABLE `IPv4RS` DISABLE KEYS */;
INSERT INTO `IPv4RS` VALUES (1,'yes','\n\�e',80,1,NULL,NULL),(2,'yes','\n\�f',80,1,NULL,NULL),(3,'no','\n\�g',80,1,NULL,NULL),(4,'yes','\n\�h',80,1,NULL,NULL),(5,'yes','\n\�i',80,1,NULL,NULL),(6,'no','\n\�j',8080,2,NULL,NULL),(7,'yes','\n\�k',8080,2,NULL,NULL),(8,'yes','\n\�l',8080,2,NULL,NULL),(9,'yes','\n\�m',8080,2,NULL,NULL),(10,'yes','\n\�n',8080,2,NULL,NULL);
/*!40000 ALTER TABLE `IPv4RS` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4RSPool`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4RSPool` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4RSPool` WRITE;
/*!40000 ALTER TABLE `IPv4RSPool` DISABLE KEYS */;
INSERT INTO `IPv4RSPool` VALUES (1,'Apache servers',NULL,NULL),(2,'Resin servers',NULL,NULL);
/*!40000 ALTER TABLE `IPv4RSPool` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv4VS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv4VS` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vip` varbinary(16) NOT NULL,
  `vport` smallint(5) unsigned DEFAULT NULL,
  `proto` enum('TCP','UDP','MARK') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TCP',
  `name` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `vip` (`vip`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv4VS` WRITE;
/*!40000 ALTER TABLE `IPv4VS` DISABLE KEYS */;
INSERT INTO `IPv4VS` VALUES (1,'\n\�',80,'TCP','virtual web','lvs_sched wlc\ndelay_loop 3\nalpha\nomega\nquorum 3\nhysteresis 1\n\n# this is a comment\n# VS name is %VNAME%\n#\n','CHECK=%CHECK_TCP%\n'),(2,'\n\�',80,'TCP','virtual app','lvs_sched wlc\r\nlvs_method NAT\r\ndelay_loop 3\r\nalpha\r\nomega\r\nquorum 3\r\nhysteresis 1\r\n\r\n','HTTP_GET {\r\nurl {\r\npath /\r\nstatus_code 200\r\n}\r\nconnect_timeout 1\r\n}'),(3,'\0\0\0',NULL,'MARK','test fwmark service','lvs_sched wrr\nVIP=10.200.2.5\nMETHOD=TUN\nDELAY_LOOP=10','CHECK=%CHECK_TCP%');
/*!40000 ALTER TABLE `IPv4VS` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv6Address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv6Address` (
  `ip` binary(16) NOT NULL,
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `reserved` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv6Address` WRITE;
/*!40000 ALTER TABLE `IPv6Address` DISABLE KEYS */;
/*!40000 ALTER TABLE `IPv6Address` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv6Allocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv6Allocation` (
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` binary(16) NOT NULL,
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` enum('regular','shared','virtual','router','point2point') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'regular',
  PRIMARY KEY (`object_id`,`ip`),
  KEY `ip` (`ip`),
  CONSTRAINT `IPv6Allocation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv6Allocation` WRITE;
/*!40000 ALTER TABLE `IPv6Allocation` DISABLE KEYS */;
INSERT INTO `IPv6Allocation` VALUES (908,'�\0P\0�\�\0\0\0\0\0\0\0','fa1/0','router'),(919,'�\0P\0ޭ\0\0\0\0\0\0\0','bge1','router');
/*!40000 ALTER TABLE `IPv6Allocation` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv6Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv6Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` binary(16) NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv6Log` WRITE;
/*!40000 ALTER TABLE `IPv6Log` DISABLE KEYS */;
/*!40000 ALTER TABLE `IPv6Log` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `IPv6Network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPv6Network` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` binary(16) NOT NULL,
  `mask` int(10) unsigned NOT NULL,
  `last_ip` binary(16) NOT NULL,
  `name` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`,`mask`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `IPv6Network` WRITE;
/*!40000 ALTER TABLE `IPv6Network` DISABLE KEYS */;
INSERT INTO `IPv6Network` VALUES (1,'�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',7,'����������������','Local IPv6',NULL),(2,'�\0P\0\0\0\0\0\0\0\0\0\0\0',48,'�\0P\0����������','v6 Suballoc 1',NULL),(3,'�\0P\0\0\0\0\0\0\0\0\0\0\0',48,'�\0P\0����������','v6 Suballoc 2',NULL),(4,'�\0P\0ޭ\0\0\0\0\0\0\0\0',64,'�\0P\0ޭ��������','dead net',NULL),(5,'�\0P\0�\�\0\0\0\0\0\0\0\0',64,'�\0P\0�\���������','beef net',NULL);
/*!40000 ALTER TABLE `IPv6Network` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `LDAPCache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LDAPCache` (
  `presented_username` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `successful_hash` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `first_success` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_retry` timestamp NULL DEFAULT NULL,
  `displayed_name` char(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memberof` text COLLATE utf8_unicode_ci,
  UNIQUE KEY `presented_username` (`presented_username`),
  KEY `scanidx` (`presented_username`,`successful_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `LDAPCache` WRITE;
/*!40000 ALTER TABLE `LDAPCache` DISABLE KEYS */;
/*!40000 ALTER TABLE `LDAPCache` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Link` (
  `porta` int(10) unsigned NOT NULL DEFAULT '0',
  `portb` int(10) unsigned NOT NULL DEFAULT '0',
  `cable` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`porta`,`portb`),
  UNIQUE KEY `porta` (`porta`),
  UNIQUE KEY `portb` (`portb`),
  CONSTRAINT `Link-FK-a` FOREIGN KEY (`porta`) REFERENCES `Port` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Link-FK-b` FOREIGN KEY (`portb`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Link` WRITE;
/*!40000 ALTER TABLE `Link` DISABLE KEYS */;
INSERT INTO `Link` VALUES (3057,3071,NULL),(3058,3084,NULL),(3059,3069,NULL),(3062,3094,NULL),(3063,3079,NULL),(3070,3083,NULL),(3072,3080,NULL),(3073,3086,NULL),(3074,3088,NULL),(3075,3090,NULL),(3076,3092,NULL),(3077,3097,NULL),(3078,3095,NULL),(3082,3085,NULL),(3096,3219,NULL),(3098,3191,NULL),(3099,3112,NULL),(3101,3113,NULL),(3102,3114,NULL),(3103,3115,NULL),(3104,3116,NULL),(3105,3117,NULL),(3106,3118,NULL),(3119,3147,NULL),(3124,3126,NULL),(3127,3162,NULL),(3128,3158,NULL),(3130,3163,NULL),(3131,3148,NULL),(3133,3164,NULL),(3134,3143,NULL),(3136,3165,NULL),(3137,3151,NULL),(3139,3168,NULL),(3140,3154,NULL),(3142,3160,NULL),(3145,3171,NULL),(3159,3161,NULL),(3181,3186,NULL),(3182,3187,NULL),(3183,3190,NULL),(3184,3188,NULL),(3185,3189,NULL),(3214,3263,NULL),(3262,3265,NULL);
/*!40000 ALTER TABLE `Link` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`racktables`@`localhost`*/ /*!50003 TRIGGER `Link-before-insert` BEFORE INSERT ON `Link` FOR EACH ROW LinkTrigger:BEGIN
  DECLARE tmp, porta_type, portb_type, count INTEGER;

  IF NEW.porta = NEW.portb THEN
    # forbid connecting a port to itself
    SET NEW.porta = NULL;
    LEAVE LinkTrigger;
  ELSEIF NEW.porta > NEW.portb THEN
    # force porta < portb
    SET tmp = NEW.porta;
    SET NEW.porta = NEW.portb;
    SET NEW.portb = tmp;
  END IF; 

  # lock ports to prevent concurrent link establishment
  SELECT type INTO porta_type FROM Port WHERE id = NEW.porta FOR UPDATE;
  SELECT type INTO portb_type FROM Port WHERE id = NEW.portb FOR UPDATE;

  # only permit the link if ports are compatibile
  SELECT COUNT(*) INTO count FROM PortCompat WHERE (type1 = porta_type AND type2 = portb_type) OR (type1 = portb_type AND type2 = porta_type);
  IF count = 0 THEN
    SET NEW.porta = NULL;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`racktables`@`localhost`*/ /*!50003 TRIGGER `Link-before-update` BEFORE UPDATE ON `Link` FOR EACH ROW LinkTrigger:BEGIN
  DECLARE tmp, porta_type, portb_type, count INTEGER;

  IF NEW.porta = NEW.portb THEN
    # forbid connecting a port to itself
    SET NEW.porta = NULL;
    LEAVE LinkTrigger;
  ELSEIF NEW.porta > NEW.portb THEN
    # force porta < portb
    SET tmp = NEW.porta;
    SET NEW.porta = NEW.portb;
    SET NEW.portb = tmp;
  END IF; 

  # lock ports to prevent concurrent link establishment
  SELECT type INTO porta_type FROM Port WHERE id = NEW.porta FOR UPDATE;
  SELECT type INTO portb_type FROM Port WHERE id = NEW.portb FOR UPDATE;

  # only permit the link if ports are compatibile
  SELECT COUNT(*) INTO count FROM PortCompat WHERE (type1 = porta_type AND type2 = portb_type) OR (type1 = portb_type AND type2 = porta_type);
  IF count = 0 THEN
    SET NEW.porta = NULL;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `Location`;
/*!50001 DROP VIEW IF EXISTS `Location`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Location` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `has_problems`,
 1 AS `comment`,
 1 AS `parent_id`,
 1 AS `parent_name`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `Molecule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Molecule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Molecule` WRITE;
/*!40000 ALTER TABLE `Molecule` DISABLE KEYS */;
INSERT INTO `Molecule` VALUES (1);
/*!40000 ALTER TABLE `Molecule` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `MountOperation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MountOperation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_name` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `old_molecule_id` int(10) unsigned DEFAULT NULL,
  `new_molecule_id` int(10) unsigned DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`),
  KEY `MountOperation-FK-old_molecule_id` (`old_molecule_id`),
  KEY `MountOperation-FK-new_molecule_id` (`new_molecule_id`),
  CONSTRAINT `MountOperation-FK-new_molecule_id` FOREIGN KEY (`new_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE,
  CONSTRAINT `MountOperation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `MountOperation-FK-old_molecule_id` FOREIGN KEY (`old_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `MountOperation` WRITE;
/*!40000 ALTER TABLE `MountOperation` DISABLE KEYS */;
/*!40000 ALTER TABLE `MountOperation` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `MuninGraph`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MuninGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `caption` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph`),
  KEY `server_id` (`server_id`),
  KEY `graph` (`graph`),
  CONSTRAINT `MuninGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `MuninGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `MuninServer` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `MuninGraph` WRITE;
/*!40000 ALTER TABLE `MuninGraph` DISABLE KEYS */;
/*!40000 ALTER TABLE `MuninGraph` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `MuninServer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MuninServer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `base_url` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `MuninServer` WRITE;
/*!40000 ALTER TABLE `MuninServer` DISABLE KEYS */;
/*!40000 ALTER TABLE `MuninServer` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Object` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `objtype_id` int(10) unsigned NOT NULL DEFAULT '1',
  `asset_no` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_problems` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_no` (`asset_no`),
  KEY `id-tid` (`id`,`objtype_id`),
  KEY `type_id` (`objtype_id`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=984 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Object` WRITE;
/*!40000 ALTER TABLE `Object` DISABLE KEYS */;
INSERT INTO `Object` VALUES (905,'london router','bbrtr1',7,'net247','no',NULL),(906,'londonswitch1','',8,NULL,'no',NULL),(907,'New-York router 1','bbrtr2a',7,'net55','no',NULL),(908,'moscow router','bbrtr3',7,NULL,'no',NULL),(909,'tokyo router','bbrtr4',7,NULL,'no',NULL),(910,'London server 1','lserver01',4,'srv500','no',NULL),(911,'London server 2','lserver02',4,'srv501','no',NULL),(912,'London server 3','lserver03',4,'srv502','no',NULL),(913,'London server 4','lserver04',4,'srv503','yes','this one needs replacement'),(914,'London server 5','lserver05',4,'srv504','no',NULL),(915,'london LB','llb',8,'net1000','no',NULL),(916,'shared storage','',5,NULL,'no',NULL),(917,'london-NAS','',7,'net1001','no',NULL),(918,'London server 6','lserver06',4,'srv505','no',NULL),(919,'London server 7','lserver07',4,'srv506','no',NULL),(920,'backup library','lbackup',6,'misc200','no',NULL),(921,'lserver06 array','lserver06 array',5,NULL,'no',NULL),(922,'lserver07 array','lserver07 array',5,NULL,'no',NULL),(923,'Tokyo server 1','tserver01',4,'srv654','no',NULL),(924,'Tokyo server 2','tserver02',4,'srv848','no',NULL),(925,'Tokyo server 3','tserver03',4,'srv139','no',NULL),(926,'Tokyo switch','tswitch',8,'net385','no',NULL),(927,'New-York router 2','bbrtr2b',7,'net498','no',NULL),(928,'New-York IPVS LB A','nylba',4,'net554','no',NULL),(929,'New-York IPVS LB B','nylbb',4,'net555','no',NULL),(930,'New-York server switch A','nyswitcha',8,'net084','no',NULL),(931,'New-York server switch B','nyswitchb',8,'net486','no',NULL),(932,'New-York server 1A','nysrv1a',4,'srv287','no',NULL),(933,'New-York server 1B','nysrv1b',4,'srv288','no',NULL),(934,'New-York server 2A','nysrv2a',4,NULL,'no',NULL),(935,'New-York server 2B','nysrv2b',4,NULL,'no',NULL),(936,'New-York server 3A','nysrv3a',4,NULL,'no',NULL),(937,'New-York server 3B','nysrv3b',4,NULL,'no',NULL),(938,'New-York server 4A','nysrv4a',4,NULL,'no',NULL),(939,'New-York server 4B','nysrv4b',4,NULL,'no',NULL),(940,'New-York server 5A','nysrv5a',4,NULL,'no',NULL),(941,'New-York server 5B','nysrv5b',4,NULL,'no',NULL),(942,'wing A UPS',NULL,12,NULL,'no',NULL),(943,'wing B UPS',NULL,12,NULL,'no',NULL),(944,'network UPS',NULL,12,NULL,'no',NULL),(945,NULL,NULL,9,NULL,'no',NULL),(946,NULL,NULL,9,NULL,'no',NULL),(947,NULL,NULL,2,NULL,'no',NULL),(948,NULL,NULL,2,NULL,'no',NULL),(949,NULL,NULL,2,NULL,'no',NULL),(950,NULL,NULL,2,NULL,'no',NULL),(951,NULL,NULL,2,NULL,'no',NULL),(952,NULL,NULL,2,NULL,'no',NULL),(953,NULL,NULL,2,NULL,'no',NULL),(954,NULL,NULL,2,NULL,'no',NULL),(955,NULL,NULL,2,NULL,'no',NULL),(956,'mps1',NULL,4,NULL,'no',NULL),(957,'mps2',NULL,4,NULL,'no',NULL),(958,'mps3',NULL,4,NULL,'no',NULL),(959,'mps4',NULL,4,NULL,'no',NULL),(960,'mps5',NULL,4,NULL,'no',NULL),(961,'mskswitch',NULL,8,'sw0001','no',NULL),(962,'moscow kvm switch',NULL,445,'sw0002','no',NULL),(963,'Row 1',NULL,1561,NULL,'no',NULL),(964,'Row A',NULL,1561,NULL,'no',NULL),(965,'CF-4',NULL,1561,NULL,'no',NULL),(966,'Row 1',NULL,1561,NULL,'no',NULL),(967,'L01',NULL,1560,NULL,'no','test'),(968,'L02',NULL,1560,NULL,'no','network equipment mini-rack'),(969,'L03',NULL,1560,NULL,'no',NULL),(970,'NY100',NULL,1560,NULL,'no',NULL),(971,'NY101',NULL,1560,NULL,'no','server farm wing A'),(972,'M01',NULL,1560,NULL,'no',NULL),(973,'NY102',NULL,1560,NULL,'no','server farm wing B'),(974,'T01',NULL,1560,NULL,'no',NULL),(975,'Moscow',NULL,1562,NULL,'no',NULL),(976,'Tokyo',NULL,1562,NULL,'no',NULL),(977,'New-York',NULL,1562,NULL,'no',NULL),(978,'London',NULL,1562,NULL,'no',NULL),(979,'sw-a1',NULL,8,NULL,'no',NULL),(980,'sw-a2',NULL,8,NULL,'no',NULL),(981,'sw-d1',NULL,8,NULL,'no',NULL),(982,NULL,NULL,3,NULL,'no',NULL),(983,'London modem 1',NULL,13,NULL,'no',NULL);
/*!40000 ALTER TABLE `Object` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ObjectHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ObjectHistory` (
  `id` int(10) unsigned DEFAULT NULL,
  `name` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `objtype_id` int(10) unsigned DEFAULT NULL,
  `asset_no` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_problems` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `comment` text COLLATE utf8_unicode_ci,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_name` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `id` (`id`),
  CONSTRAINT `ObjectHistory-FK-object_id` FOREIGN KEY (`id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ObjectHistory` WRITE;
/*!40000 ALTER TABLE `ObjectHistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `ObjectHistory` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ObjectLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ObjectLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(10) unsigned NOT NULL,
  `user` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`),
  KEY `date` (`date`),
  CONSTRAINT `ObjectLog-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ObjectLog` WRITE;
/*!40000 ALTER TABLE `ObjectLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `ObjectLog` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ObjectParentCompat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ObjectParentCompat` (
  `parent_objtype_id` int(10) unsigned NOT NULL,
  `child_objtype_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `parent_child` (`parent_objtype_id`,`child_objtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ObjectParentCompat` WRITE;
/*!40000 ALTER TABLE `ObjectParentCompat` DISABLE KEYS */;
INSERT INTO `ObjectParentCompat` VALUES (3,13),(4,1504),(4,1507),(1397,1398),(1502,4),(1503,8),(1505,4),(1505,1504),(1505,1506),(1505,1507),(1506,4),(1506,1504),(1787,4),(1787,8),(1787,1502);
/*!40000 ALTER TABLE `ObjectParentCompat` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PatchCableConnector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PatchCableConnector` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'custom',
  `connector` char(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `connector_per_origin` (`connector`,`origin`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PatchCableConnector` WRITE;
/*!40000 ALTER TABLE `PatchCableConnector` DISABLE KEYS */;
INSERT INTO `PatchCableConnector` VALUES (999,'default','CX4/SFF-8470'),(2,'default','FC/APC'),(1,'default','FC/PC'),(4,'default','LC/APC'),(3,'default','LC/PC'),(6,'default','MPO-12/APC'),(5,'default','MPO-12/PC'),(8,'default','MPO-24/APC'),(7,'default','MPO-24/PC'),(10,'default','SC/APC'),(9,'default','SC/PC'),(14,'default','SFP-1000'),(15,'default','SFP+'),(12,'default','ST/APC'),(11,'default','ST/PC'),(13,'default','T568/8P8C/RJ45');
/*!40000 ALTER TABLE `PatchCableConnector` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PatchCableConnectorCompat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PatchCableConnectorCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `connector_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`connector_id`),
  KEY `connector_id` (`connector_id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-connector_id` FOREIGN KEY (`connector_id`) REFERENCES `PatchCableConnector` (`id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PatchCableConnectorCompat` WRITE;
/*!40000 ALTER TABLE `PatchCableConnectorCompat` DISABLE KEYS */;
INSERT INTO `PatchCableConnectorCompat` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2),(1,3),(2,3),(3,3),(4,3),(5,3),(6,3),(7,3),(8,3),(9,3),(10,3),(11,3),(12,3),(1,4),(2,4),(3,4),(4,4),(5,4),(6,4),(7,4),(8,4),(9,4),(10,4),(11,4),(12,4),(18,5),(19,5),(18,6),(19,6),(21,7),(22,7),(21,8),(22,8),(1,9),(2,9),(3,9),(4,9),(5,9),(6,9),(7,9),(8,9),(9,9),(10,9),(11,9),(12,9),(1,10),(2,10),(3,10),(4,10),(5,10),(6,10),(7,10),(8,10),(9,10),(10,10),(11,10),(12,10),(1,11),(2,11),(3,11),(4,11),(5,11),(6,11),(7,11),(8,11),(9,11),(10,11),(11,11),(12,11),(1,12),(2,12),(3,12),(4,12),(5,12),(6,12),(7,12),(8,12),(9,12),(10,12),(11,12),(12,12),(13,13),(14,13),(15,13),(16,13),(17,13),(999,13),(23,14),(24,15),(25,15),(26,15),(27,15),(20,999);
/*!40000 ALTER TABLE `PatchCableConnectorCompat` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PatchCableHeap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PatchCableHeap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pctype_id` int(10) unsigned NOT NULL,
  `end1_conn_id` int(10) unsigned NOT NULL,
  `end2_conn_id` int(10) unsigned NOT NULL,
  `amount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `length` decimal(5,2) unsigned NOT NULL DEFAULT '1.00',
  `description` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compat1` (`pctype_id`,`end1_conn_id`),
  KEY `compat2` (`pctype_id`,`end2_conn_id`),
  CONSTRAINT `PatchCableHeap-FK-compat1` FOREIGN KEY (`pctype_id`, `end1_conn_id`) REFERENCES `PatchCableConnectorCompat` (`pctype_id`, `connector_id`),
  CONSTRAINT `PatchCableHeap-FK-compat2` FOREIGN KEY (`pctype_id`, `end2_conn_id`) REFERENCES `PatchCableConnectorCompat` (`pctype_id`, `connector_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PatchCableHeap` WRITE;
/*!40000 ALTER TABLE `PatchCableHeap` DISABLE KEYS */;
INSERT INTO `PatchCableHeap` VALUES (1,2,9,9,97,2.00,''),(2,2,9,3,98,2.00,''),(3,13,13,13,100,1.00,'blue'),(4,13,13,13,100,1.00,'green'),(5,13,13,13,98,1.00,'yellow'),(6,13,13,13,49,2.00,'gray'),(7,27,15,15,10,0.50,'direct attach'),(8,25,15,15,20,2.00,'direct attach'),(9,24,15,15,15,7.00,'direct attach'),(10,20,999,999,5,2.00,''),(11,18,5,5,9,5.00,'fiber ribbon'),(12,22,7,7,11,10.00,'fiber ribbon'),(13,11,1,11,5,0.50,'converter'),(14,11,1,9,4,1.00,'converter');
/*!40000 ALTER TABLE `PatchCableHeap` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PatchCableHeapLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PatchCableHeapLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heap_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `message` char(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `heap_id-date` (`heap_id`,`date`),
  CONSTRAINT `PatchCableHeapLog-FK-heap_id` FOREIGN KEY (`heap_id`) REFERENCES `PatchCableHeap` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PatchCableHeapLog` WRITE;
/*!40000 ALTER TABLE `PatchCableHeapLog` DISABLE KEYS */;
INSERT INTO `PatchCableHeapLog` VALUES (1,14,'2014-06-15 00:10:24','admin','amount set to 5'),(2,13,'2014-06-15 00:10:28','admin','amount set to 5'),(3,2,'2014-06-15 00:10:34','admin','amount set to 100'),(4,1,'2014-06-15 00:10:42','admin','amount set to 100'),(5,3,'2014-06-15 00:10:49','admin','amount set to 100'),(6,6,'2014-06-15 00:10:56','admin','amount set to 50'),(7,4,'2014-06-15 00:10:59','admin','amount set to 100'),(8,5,'2014-06-15 00:11:01','admin','amount set to 100'),(9,12,'2014-06-15 00:11:07','admin','amount set to 10'),(10,11,'2014-06-15 00:11:09','admin','amount set to 10'),(11,10,'2014-06-15 00:11:12','admin','amount set to 5'),(12,7,'2014-06-15 00:11:16','admin','amount set to 2'),(13,8,'2014-06-15 00:11:28','admin','amount set to 20'),(14,9,'2014-06-15 00:11:34','admin','amount set to 15'),(15,7,'2014-06-15 00:14:22','admin','amount adjusted by 1'),(16,7,'2014-06-15 00:14:24','admin','amount adjusted by 1'),(17,7,'2014-06-15 00:14:25','admin','amount adjusted by 1'),(18,7,'2014-06-15 00:14:26','admin','amount adjusted by 1'),(19,7,'2014-06-15 00:14:27','admin','amount adjusted by 1'),(20,7,'2014-06-15 00:14:29','admin','amount adjusted by 1'),(21,7,'2014-06-15 00:14:30','admin','amount adjusted by 1'),(22,7,'2014-06-15 00:14:31','admin','amount adjusted by 1'),(23,6,'2014-06-15 00:14:35','admin','amount adjusted by -1'),(24,12,'2014-06-15 00:14:38','admin','amount adjusted by 1'),(25,11,'2014-06-15 00:14:40','admin','amount adjusted by -1'),(26,1,'2014-06-15 00:14:42','admin','amount adjusted by -1'),(27,1,'2014-06-15 00:14:44','admin','amount adjusted by -1'),(28,1,'2014-06-15 00:14:45','admin','amount adjusted by -1'),(29,2,'2014-06-15 00:14:47','admin','amount adjusted by -1'),(30,2,'2014-06-15 00:14:50','admin','amount adjusted by -1'),(31,5,'2014-06-15 00:14:51','admin','amount adjusted by -1'),(32,5,'2014-06-15 00:14:52','admin','amount adjusted by -1'),(33,14,'2014-06-15 00:14:57','admin','amount adjusted by -1');
/*!40000 ALTER TABLE `PatchCableHeapLog` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PatchCableOIFCompat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PatchCableOIFCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`oif_id`),
  KEY `oif_id` (`oif_id`),
  CONSTRAINT `PatchCableOIFCompat-FK-oif_id` FOREIGN KEY (`oif_id`) REFERENCES `PortOuterInterface` (`id`),
  CONSTRAINT `PatchCableOIFCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PatchCableOIFCompat` WRITE;
/*!40000 ALTER TABLE `PatchCableOIFCompat` DISABLE KEYS */;
INSERT INTO `PatchCableOIFCompat` VALUES (13,18),(14,18),(15,18),(16,18),(17,18),(999,18),(13,19),(14,19),(15,19),(16,19),(17,19),(13,24),(14,24),(15,24),(16,24),(17,24),(1,30),(2,30),(3,30),(4,30),(5,34),(6,34),(5,35),(6,35),(5,36),(6,36),(1,37),(2,37),(3,37),(4,37),(5,38),(6,38),(1,39),(2,39),(3,39),(4,39),(5,39),(6,39),(20,40),(23,1077),(24,1084),(25,1084),(26,1084),(27,1084),(11,1088),(12,1088),(11,1089),(12,1089),(11,1090),(12,1090),(11,1091),(12,1091),(1,1195),(2,1195),(3,1195),(4,1195),(1,1196),(2,1196),(3,1196),(4,1196),(5,1197),(6,1197),(11,1198),(12,1198),(11,1199),(12,1199),(5,1200),(6,1200),(5,1201),(6,1201),(1,1202),(2,1202),(3,1202),(4,1202),(1,1203),(2,1203),(3,1203),(4,1203),(5,1204),(6,1204),(5,1205),(6,1205),(11,1206),(12,1206),(11,1207),(12,1207),(14,1642),(15,1642),(16,1642),(17,1642),(5,1660),(6,1660),(5,1662),(6,1662),(18,1663),(19,1663),(5,1664),(6,1664),(21,1669),(22,1669),(5,1670),(6,1670),(5,1671),(6,1671),(18,1672),(19,1672),(5,1675),(6,1675),(5,1676),(6,1676);
/*!40000 ALTER TABLE `PatchCableOIFCompat` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PatchCableType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PatchCableType` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'custom',
  `pctype` char(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pctype_per_origin` (`pctype`,`origin`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PatchCableType` WRITE;
/*!40000 ALTER TABLE `PatchCableType` DISABLE KEYS */;
INSERT INTO `PatchCableType` VALUES (24,'default','10Gb/s 24AWG twinax'),(25,'default','10Gb/s 26AWG twinax'),(26,'default','10Gb/s 28AWG twinax'),(27,'default','10Gb/s 30AWG twinax'),(20,'default','10Gb/s CX4 coax'),(18,'default','12-fiber OM3'),(19,'default','12-fiber OM4'),(23,'default','1Gb/s 50cm shielded'),(21,'default','24-fiber OM3'),(22,'default','24-fiber OM4'),(999,'default','Cat.3 TP'),(13,'default','Cat.5 TP'),(14,'default','Cat.6 TP'),(15,'default','Cat.6a TP'),(16,'default','Cat.7 TP'),(17,'default','Cat.7a TP'),(1,'default','duplex OM1'),(2,'default','duplex OM2'),(3,'default','duplex OM3'),(4,'default','duplex OM4'),(5,'default','duplex OS1'),(6,'default','duplex OS2'),(7,'default','simplex OM1'),(8,'default','simplex OM2'),(9,'default','simplex OM3'),(10,'default','simplex OM4'),(11,'default','simplex OS1'),(12,'default','simplex OS2');
/*!40000 ALTER TABLE `PatchCableType` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Port`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Port` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `iif_id` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  `l2address` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reservation_comment` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `object_iif_oif_name` (`object_id`,`iif_id`,`type`,`name`),
  KEY `type` (`type`),
  KEY `comment` (`reservation_comment`),
  KEY `l2address` (`l2address`),
  KEY `Port-FK-iif-oif` (`iif_id`,`type`),
  CONSTRAINT `Port-FK-iif-oif` FOREIGN KEY (`iif_id`, `type`) REFERENCES `PortInterfaceCompat` (`iif_id`, `oif_id`),
  CONSTRAINT `Port-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3275 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Port` WRITE;
/*!40000 ALTER TABLE `Port` DISABLE KEYS */;
INSERT INTO `Port` VALUES (3057,905,'se1/0',1,32,NULL,NULL,''),(3058,905,'se1/1',1,32,NULL,NULL,''),(3059,905,'fa2/0',1,19,'00000000A001',NULL,''),(3060,905,'fa2/1',1,19,'00000000A002','ISP uplink',''),(3062,906,'gi2',1,24,'00000000B002',NULL,'2'),(3063,906,'gi1',1,24,'00000000B001',NULL,'1'),(3064,906,'gi3',1,24,'00000000B003',NULL,'3'),(3065,906,'gi4',1,24,'00000000B004',NULL,'4'),(3066,906,'gi5',1,24,'00000000B005',NULL,'5'),(3067,906,'gi6',1,24,'00000000B006',NULL,'6'),(3068,906,'gi7',1,24,'00000000B007',NULL,'7'),(3069,906,'gi8',1,24,'00000000B008',NULL,'8'),(3070,907,'se1/0',1,32,NULL,NULL,''),(3071,907,'se1/1',1,32,NULL,NULL,''),(3072,915,'e1',1,19,NULL,NULL,'1'),(3073,915,'e2',1,19,NULL,NULL,'2'),(3074,915,'e3',1,19,NULL,NULL,'3'),(3075,915,'e4',1,19,NULL,NULL,'4'),(3076,915,'e5',1,19,NULL,NULL,'5'),(3077,915,'e6',1,19,NULL,NULL,'6'),(3078,915,'e7',1,19,NULL,NULL,'7'),(3079,915,'e8',1,19,NULL,NULL,'8'),(3080,910,'eth0',1,24,NULL,NULL,'1'),(3081,910,'eth1',1,24,NULL,NULL,'2'),(3082,909,'se1/0',1,32,NULL,NULL,''),(3083,909,'se1/1',1,32,NULL,NULL,''),(3084,908,'se1/0',1,32,NULL,NULL,''),(3085,908,'se1/1',1,32,NULL,NULL,''),(3086,911,'eth0',1,24,NULL,NULL,'1'),(3087,911,'eth1',1,24,NULL,NULL,'2'),(3088,912,'eth0',1,24,NULL,NULL,'1'),(3089,912,'eth1',1,24,NULL,NULL,'2'),(3090,913,'eth0',1,24,NULL,NULL,'1'),(3091,913,'eth1',1,24,NULL,NULL,'2'),(3092,914,'eth0',1,24,NULL,NULL,'1'),(3093,914,'eth1',1,24,NULL,NULL,'2'),(3094,917,'fa0/0',1,19,NULL,NULL,''),(3095,919,'bge0',1,24,NULL,NULL,''),(3096,919,'bge1',1,24,NULL,NULL,''),(3097,918,'bge0',1,24,NULL,NULL,''),(3098,918,'bge1',1,24,NULL,NULL,''),(3099,909,'fa2/0',1,19,NULL,NULL,''),(3100,909,'fa2/1',1,19,NULL,'ISP uplink',''),(3101,926,'fa1',1,19,NULL,NULL,'1'),(3102,926,'fa2',1,19,NULL,NULL,'2'),(3103,926,'fa3',1,19,NULL,NULL,'3'),(3104,926,'fa4',1,19,NULL,NULL,'4'),(3105,926,'fa5',1,19,NULL,NULL,'5'),(3106,926,'fa6',1,19,NULL,NULL,'6'),(3107,926,'fa7',1,19,NULL,NULL,'7'),(3108,926,'fa8',1,19,NULL,NULL,'8'),(3109,926,'fa9',1,19,NULL,NULL,'9'),(3110,926,'fa10',1,19,NULL,NULL,'10'),(3111,926,'fa11',1,19,NULL,NULL,'11'),(3112,926,'fa12',1,19,NULL,NULL,'12'),(3113,923,'eth0',1,24,NULL,NULL,'1'),(3114,923,'eth1',1,24,NULL,NULL,'2'),(3115,924,'eth0',1,24,NULL,NULL,'1'),(3116,924,'eth1',1,24,NULL,NULL,'2'),(3117,925,'eth0',1,24,NULL,NULL,'1'),(3118,925,'eth1',1,24,NULL,NULL,'2'),(3119,908,'fa2/0',1,19,NULL,NULL,''),(3120,908,'fa2/1',1,19,NULL,'ISP uplink',''),(3121,907,'fa2/0',1,19,NULL,NULL,''),(3122,907,'fa2/1',1,19,NULL,NULL,''),(3123,927,'gi3/0',3,1202,NULL,'ISP uplink',''),(3124,927,'gi4/0',3,1202,NULL,NULL,''),(3125,907,'gi3/0',3,1202,NULL,'ISP uplink',''),(3126,907,'gi4/0',3,1202,NULL,NULL,''),(3127,956,'kvm',1,33,NULL,NULL,''),(3128,956,'eth0',1,24,NULL,NULL,''),(3129,956,'eth1',1,24,NULL,NULL,''),(3130,957,'kvm',1,33,NULL,NULL,''),(3131,957,'eth0',1,24,NULL,NULL,''),(3132,957,'eth1',1,24,NULL,NULL,''),(3133,958,'kvm',1,33,NULL,NULL,''),(3134,958,'eth0',1,24,NULL,NULL,''),(3135,958,'eth1',1,24,NULL,NULL,''),(3136,959,'kvm',1,33,NULL,NULL,''),(3137,959,'eth0',1,24,NULL,NULL,''),(3138,959,'eth1',1,24,NULL,NULL,''),(3139,960,'kvm',1,33,NULL,NULL,''),(3140,960,'eth0',1,24,NULL,NULL,''),(3141,960,'eth1',1,24,NULL,NULL,''),(3142,908,'con0',1,29,NULL,NULL,'console'),(3143,961,'1',1,24,'01040104AA00',NULL,''),(3144,961,'2',1,24,'01040104AA01','for field engineer',''),(3145,961,'3',1,24,'01040104AA02',NULL,''),(3146,961,'4',1,24,'01040104AA03',NULL,''),(3147,961,'5',1,24,'01040104AA04',NULL,''),(3148,961,'6',1,24,'01040104AA05',NULL,''),(3149,961,'7',1,24,'01040104AA06',NULL,''),(3150,961,'8',1,24,'01040104AA07',NULL,''),(3151,961,'9',1,24,'01040104AA08',NULL,''),(3152,961,'10',1,24,'01040104AA09',NULL,''),(3153,961,'11',1,24,'01040104AA0A',NULL,''),(3154,961,'12',1,24,'01040104AA0B',NULL,''),(3155,961,'13',1,24,'01040104AA0C',NULL,''),(3156,961,'14',1,24,'01040104AA0D',NULL,''),(3157,961,'15',1,24,'01040104AA0E',NULL,''),(3158,961,'16',1,24,'01040104AA0F',NULL,''),(3159,961,'con',1,681,NULL,NULL,'console'),(3160,956,'ttyS0',1,681,NULL,NULL,'serial A'),(3161,956,'ttyS1',1,681,NULL,NULL,'serial B'),(3162,962,'tail1',1,446,NULL,NULL,''),(3163,962,'tail2',1,446,NULL,NULL,''),(3164,962,'tail3',1,446,NULL,NULL,''),(3165,962,'tail4',1,446,NULL,NULL,''),(3166,962,'tail5',1,446,NULL,NULL,''),(3167,962,'tail6',1,446,NULL,NULL,''),(3168,962,'tail7',1,446,NULL,NULL,''),(3169,962,'tail8',1,446,NULL,NULL,''),(3170,962,'head',1,33,NULL,'monitor connected',''),(3171,962,'net',1,19,'020002003333',NULL,''),(3178,927,'fa1/0',1,19,NULL,NULL,''),(3179,908,'fa1/0',1,19,NULL,NULL,''),(3180,955,'in',1,16,NULL,'from local distribution',''),(3181,955,'out1',1,1322,NULL,NULL,''),(3182,955,'out2',1,1322,NULL,NULL,''),(3183,955,'out3',1,1322,NULL,NULL,''),(3184,955,'out4',1,1322,NULL,NULL,''),(3185,955,'out5',1,1322,NULL,NULL,''),(3186,923,'ps',1,16,NULL,NULL,''),(3187,924,'ps',1,16,NULL,NULL,''),(3188,925,'ps',1,16,NULL,NULL,''),(3189,926,'ps',1,16,NULL,NULL,''),(3190,909,'ps',1,16,NULL,NULL,''),(3191,979,'gi0/1',1,24,NULL,NULL,''),(3192,979,'gi0/2',1,24,NULL,NULL,''),(3193,979,'gi0/3',1,24,NULL,NULL,''),(3194,979,'gi0/4',1,24,NULL,NULL,''),(3195,979,'gi0/5',1,24,NULL,NULL,''),(3196,979,'gi0/6',1,24,NULL,NULL,''),(3197,979,'gi0/7',1,24,NULL,NULL,''),(3198,979,'gi0/8',1,24,NULL,NULL,''),(3199,979,'gi0/9',1,24,NULL,NULL,''),(3200,979,'gi0/10',1,24,NULL,NULL,''),(3201,979,'gi0/11',1,24,NULL,NULL,''),(3202,979,'gi0/12',1,24,NULL,NULL,''),(3203,979,'gi0/13',1,24,NULL,NULL,''),(3204,979,'gi0/14',1,24,NULL,NULL,''),(3205,979,'gi0/15',1,24,NULL,NULL,''),(3206,979,'gi0/16',1,24,NULL,NULL,''),(3207,979,'gi0/17',1,24,NULL,NULL,''),(3208,979,'gi0/18',1,24,NULL,NULL,''),(3209,979,'gi0/19',1,24,NULL,NULL,''),(3210,979,'gi0/20',1,24,NULL,NULL,''),(3211,979,'gi0/21',1,24,NULL,NULL,''),(3212,979,'gi0/22',1,24,NULL,NULL,''),(3213,979,'gi0/23',1,24,NULL,NULL,''),(3214,979,'gi0/24',1,24,NULL,NULL,''),(3215,980,'gi0/0/1',1,24,NULL,NULL,''),(3216,980,'gi0/0/2',1,24,NULL,NULL,''),(3217,980,'gi0/0/3',1,24,NULL,NULL,''),(3218,980,'gi0/0/4',1,24,NULL,NULL,''),(3219,980,'gi0/0/5',1,24,NULL,NULL,''),(3220,980,'gi0/0/6',1,24,NULL,NULL,''),(3221,980,'gi0/0/7',1,24,NULL,NULL,''),(3222,980,'gi0/0/8',1,24,NULL,NULL,''),(3223,980,'gi0/0/9',1,24,NULL,NULL,''),(3224,980,'gi0/0/10',1,24,NULL,NULL,''),(3225,980,'gi0/0/11',1,24,NULL,NULL,''),(3226,980,'gi0/0/12',1,24,NULL,NULL,''),(3227,980,'gi0/0/13',1,24,NULL,NULL,''),(3228,980,'gi0/0/14',1,24,NULL,NULL,''),(3229,980,'gi0/0/15',1,24,NULL,NULL,''),(3230,980,'gi0/0/16',1,24,NULL,NULL,''),(3231,980,'gi0/0/17',1,24,NULL,NULL,''),(3232,980,'gi0/0/18',1,24,NULL,NULL,''),(3233,980,'gi0/0/19',1,24,NULL,NULL,''),(3234,980,'gi0/0/20',1,24,NULL,NULL,''),(3235,980,'gi0/0/21',1,24,NULL,NULL,''),(3236,980,'gi0/0/22',1,24,NULL,NULL,''),(3237,980,'gi0/0/23',1,24,NULL,NULL,''),(3238,980,'gi0/0/24',1,24,NULL,NULL,''),(3239,980,'gi0/0/25',1,24,NULL,NULL,''),(3240,980,'gi0/0/26',1,24,NULL,NULL,''),(3241,980,'gi0/0/27',1,24,NULL,NULL,''),(3242,980,'gi0/0/28',1,24,NULL,NULL,''),(3243,980,'gi0/0/29',1,24,NULL,NULL,''),(3244,980,'gi0/0/30',1,24,NULL,NULL,''),(3245,980,'gi0/0/31',1,24,NULL,NULL,''),(3246,980,'gi0/0/32',1,24,NULL,NULL,''),(3247,980,'gi0/0/33',1,24,NULL,NULL,''),(3248,980,'gi0/0/34',1,24,NULL,NULL,''),(3249,980,'gi0/0/35',1,24,NULL,NULL,''),(3250,980,'gi0/0/36',1,24,NULL,NULL,''),(3251,980,'gi0/0/37',1,24,NULL,NULL,''),(3252,980,'gi0/0/38',1,24,NULL,NULL,''),(3253,980,'gi0/0/39',1,24,NULL,NULL,''),(3254,980,'gi0/0/40',1,24,NULL,NULL,''),(3255,980,'gi0/0/41',1,24,NULL,NULL,''),(3256,980,'gi0/0/42',1,24,NULL,NULL,''),(3257,980,'gi0/0/43',1,24,NULL,NULL,''),(3258,980,'gi0/0/44',1,24,NULL,NULL,''),(3259,980,'gi0/0/45',1,24,NULL,NULL,''),(3260,980,'gi0/0/46',1,24,NULL,NULL,''),(3261,980,'gi0/0/47',1,24,NULL,NULL,''),(3262,980,'gi0/0/48',1,24,NULL,NULL,''),(3263,981,'gi0/1',1,24,NULL,NULL,''),(3264,981,'gi0/2',1,24,NULL,NULL,''),(3265,981,'gi0/3',1,24,NULL,NULL,''),(3266,981,'gi0/4',1,24,NULL,NULL,''),(3267,981,'gi0/5',1,24,NULL,NULL,''),(3268,981,'gi0/6',1,24,NULL,NULL,''),(3269,981,'gi0/7',1,24,NULL,NULL,''),(3270,981,'gi0/8',1,24,NULL,NULL,''),(3271,981,'gi0/9',1,24,NULL,NULL,''),(3272,981,'gi0/10',1,24,NULL,NULL,''),(3273,981,'gi0/11',1,24,NULL,NULL,''),(3274,981,'gi0/12',1,24,NULL,NULL,'');
/*!40000 ALTER TABLE `Port` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`racktables`@`localhost`*/ /*!50003 TRIGGER `Port-before-insert` BEFORE INSERT ON `Port` FOR EACH ROW PortTrigger:BEGIN
  IF (NEW.`l2address` IS NOT NULL AND (SELECT COUNT(*) FROM `Port` WHERE `l2address` = NEW.`l2address` AND `object_id` != NEW.`object_id`) > 0) THEN
    CALL `Port-l2address-already-exists-on-another-object`;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`racktables`@`localhost`*/ /*!50003 TRIGGER `Port-before-update` BEFORE UPDATE ON `Port` FOR EACH ROW PortTrigger:BEGIN
  IF (NEW.`l2address` IS NOT NULL AND (SELECT COUNT(*) FROM `Port` WHERE `l2address` = NEW.`l2address` AND `object_id` != NEW.`object_id`) > 0) THEN
    CALL `Port-l2address-already-exists-on-another-object`;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `PortAllowedVLAN`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortAllowedVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `PortAllowedVLAN-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `PortVLANMode` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `PortAllowedVLAN-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortAllowedVLAN` WRITE;
/*!40000 ALTER TABLE `PortAllowedVLAN` DISABLE KEYS */;
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/1',1),(980,'gi0/0/10',1),(980,'gi0/0/11',1),(980,'gi0/0/12',1),(980,'gi0/0/13',1),(980,'gi0/0/14',1),(980,'gi0/0/15',1),(980,'gi0/0/16',1),(980,'gi0/0/17',1),(980,'gi0/0/18',1),(980,'gi0/0/19',1),(980,'gi0/0/2',1),(980,'gi0/0/20',1),(980,'gi0/0/21',1),(980,'gi0/0/22',1),(980,'gi0/0/23',1),(980,'gi0/0/24',1),(980,'gi0/0/25',1),(980,'gi0/0/26',1),(980,'gi0/0/27',1),(980,'gi0/0/28',1),(980,'gi0/0/29',1),(980,'gi0/0/3',1),(980,'gi0/0/30',1),(980,'gi0/0/31',1),(980,'gi0/0/32',1),(980,'gi0/0/33',1),(980,'gi0/0/34',1),(980,'gi0/0/35',1),(980,'gi0/0/36',1),(980,'gi0/0/37',1),(980,'gi0/0/38',1),(980,'gi0/0/39',1),(980,'gi0/0/40',1),(980,'gi0/0/41',1),(980,'gi0/0/42',1),(980,'gi0/0/43',1),(980,'gi0/0/44',1),(980,'gi0/0/45',1),(980,'gi0/0/46',1),(980,'gi0/0/47',1),(980,'gi0/0/6',1),(980,'gi0/0/7',1),(980,'gi0/0/8',1),(980,'gi0/0/9',1),(981,'gi0/10',1),(981,'gi0/11',1),(981,'gi0/12',1),(981,'gi0/2',1),(981,'gi0/4',1),(981,'gi0/5',1),(981,'gi0/6',1),(981,'gi0/7',1),(981,'gi0/8',1),(981,'gi0/9',1),(979,'gi0/13',3),(979,'gi0/24',3),(980,'gi0/0/4',3),(980,'gi0/0/48',3),(981,'gi0/1',3),(981,'gi0/3',3),(980,'gi0/0/48',5),(980,'gi0/0/5',5),(981,'gi0/3',5),(979,'gi0/1',7),(979,'gi0/2',7),(979,'gi0/24',7),(981,'gi0/1',7),(979,'gi0/1',8),(979,'gi0/2',8),(979,'gi0/24',8),(981,'gi0/1',8),(979,'gi0/1',9),(979,'gi0/10',9),(979,'gi0/11',9),(979,'gi0/12',9),(979,'gi0/14',9),(979,'gi0/15',9),(979,'gi0/16',9),(979,'gi0/17',9),(979,'gi0/18',9),(979,'gi0/19',9),(979,'gi0/2',9),(979,'gi0/20',9),(979,'gi0/21',9),(979,'gi0/22',9),(979,'gi0/23',9),(979,'gi0/24',9),(979,'gi0/3',9),(979,'gi0/4',9),(979,'gi0/5',9),(979,'gi0/6',9),(979,'gi0/7',9),(979,'gi0/8',9),(979,'gi0/9',9),(981,'gi0/1',9),(980,'gi0/0/1',11),(980,'gi0/0/48',11),(981,'gi0/3',11),(980,'gi0/0/1',12),(980,'gi0/0/48',12),(981,'gi0/3',12),(980,'gi0/0/1',13),(980,'gi0/0/48',13),(981,'gi0/3',13);
/*!40000 ALTER TABLE `PortAllowedVLAN` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PortCompat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortCompat` (
  `type1` int(10) unsigned NOT NULL DEFAULT '0',
  `type2` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `type1_2` (`type1`,`type2`),
  KEY `type2` (`type2`),
  CONSTRAINT `PortCompat-FK-oif_id1` FOREIGN KEY (`type1`) REFERENCES `PortOuterInterface` (`id`),
  CONSTRAINT `PortCompat-FK-oif_id2` FOREIGN KEY (`type2`) REFERENCES `PortOuterInterface` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortCompat` WRITE;
/*!40000 ALTER TABLE `PortCompat` DISABLE KEYS */;
INSERT INTO `PortCompat` VALUES (1322,16),(17,17),(18,18),(19,18),(24,18),(18,19),(19,19),(24,19),(18,24),(19,24),(24,24),(29,29),(681,29),(682,29),(30,30),(32,32),(446,33),(34,34),(35,35),(36,36),(37,37),(38,38),(39,39),(40,40),(41,41),(42,42),(439,439),(33,446),(29,681),(681,681),(682,681),(29,682),(681,682),(682,682),(1077,1077),(1084,1084),(1087,1087),(1089,1088),(1088,1089),(1091,1090),(1090,1091),(1195,1195),(1196,1196),(1197,1197),(1199,1198),(1198,1199),(1200,1200),(1201,1201),(1202,1202),(1203,1203),(1204,1204),(1205,1205),(1207,1206),(1206,1207),(1209,1209),(1210,1210),(1211,1211),(1212,1212),(1213,1213),(1214,1214),(1215,1215),(1216,1216),(1217,1217),(1218,1218),(1219,1219),(1220,1220),(1221,1221),(1222,1222),(1223,1223),(1224,1224),(1225,1225),(1226,1226),(1227,1227),(1228,1228),(1229,1229),(1230,1230),(1231,1231),(1232,1232),(1233,1233),(1234,1234),(1235,1235),(1236,1236),(1237,1237),(1238,1238),(1239,1239),(1240,1240),(1241,1241),(1242,1242),(1243,1243),(1244,1244),(1245,1245),(1246,1246),(1247,1247),(1248,1248),(1249,1249),(1250,1250),(1251,1251),(1252,1252),(1253,1253),(1254,1254),(1255,1255),(1256,1256),(1257,1257),(1258,1258),(1259,1259),(1260,1260),(1261,1261),(1262,1262),(1263,1263),(1264,1264),(1265,1265),(1266,1266),(1267,1267),(1268,1268),(1269,1269),(1270,1270),(1271,1271),(1272,1272),(1273,1273),(1274,1274),(1275,1275),(1276,1276),(1277,1277),(1278,1278),(1279,1279),(1280,1280),(1281,1281),(1282,1282),(1283,1283),(1284,1284),(1285,1285),(1286,1286),(1287,1287),(1288,1288),(1289,1289),(1290,1290),(1291,1291),(1292,1292),(1293,1293),(1294,1294),(1295,1295),(1296,1296),(1297,1297),(1298,1298),(1299,1299),(1300,1300),(1316,1316),(16,1322),(1399,1399),(1424,1424),(1425,1425),(1426,1426),(1427,1427),(1428,1428),(1429,1429),(1430,1430),(1431,1431),(1432,1432),(1433,1433),(1434,1434),(1435,1435),(1436,1436),(1437,1437),(1438,1438),(1439,1439),(1440,1440),(1441,1441),(1442,1442),(1443,1443),(1444,1444),(1445,1445),(1446,1446),(1447,1447),(1448,1448),(1449,1449),(1450,1450),(1451,1451),(1452,1452),(1453,1453),(1454,1454),(1455,1455),(1456,1456),(1457,1457),(1458,1458),(1459,1459),(1460,1460),(1461,1461),(1462,1462),(1463,1463),(1464,1464),(1465,1465),(1466,1466),(1469,1469),(1588,1588),(1589,1588),(1590,1588),(1588,1589),(1589,1589),(1590,1589),(1588,1590),(1589,1590),(1590,1590),(1591,1591),(1603,1603),(1642,1642),(1660,1660),(1661,1661),(1662,1662),(1663,1663),(1664,1664),(1668,1668),(1669,1669),(1670,1670),(1671,1671),(1672,1672),(1673,1673),(1674,1674),(1675,1675),(1676,1676),(1677,1677),(1678,1678),(1999,1999);
/*!40000 ALTER TABLE `PortCompat` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PortInnerInterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortInnerInterface` (
  `id` int(10) unsigned NOT NULL,
  `iif_name` char(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iif_name` (`iif_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortInnerInterface` WRITE;
/*!40000 ALTER TABLE `PortInnerInterface` DISABLE KEYS */;
INSERT INTO `PortInnerInterface` VALUES (11,'CFP'),(12,'CFP2'),(13,'CPAK'),(14,'CXP'),(3,'GBIC'),(1,'hardwired'),(10,'QSFP+'),(15,'QSFP28'),(2,'SFP-100'),(4,'SFP-1000'),(9,'SFP+'),(6,'X2'),(5,'XENPAK'),(8,'XFP'),(7,'XPAK');
/*!40000 ALTER TABLE `PortInnerInterface` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PortInterfaceCompat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortInterfaceCompat` (
  `iif_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `pair` (`iif_id`,`oif_id`),
  KEY `PortInterfaceCompat-FK-oif_id` (`oif_id`),
  CONSTRAINT `PortInterfaceCompat-FK-iif_id` FOREIGN KEY (`iif_id`) REFERENCES `PortInnerInterface` (`id`),
  CONSTRAINT `PortInterfaceCompat-FK-oif_id` FOREIGN KEY (`oif_id`) REFERENCES `PortOuterInterface` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortInterfaceCompat` WRITE;
/*!40000 ALTER TABLE `PortInterfaceCompat` DISABLE KEYS */;
INSERT INTO `PortInterfaceCompat` VALUES (1,16),(1,19),(1,24),(3,24),(4,24),(1,29),(5,30),(6,30),(7,30),(8,30),(9,30),(1,31),(1,32),(1,33),(3,34),(4,34),(5,35),(6,35),(7,35),(8,35),(9,35),(5,36),(6,36),(7,36),(8,36),(9,36),(5,37),(6,37),(7,37),(8,37),(9,37),(5,38),(6,38),(7,38),(8,38),(9,38),(5,39),(6,39),(7,39),(8,39),(9,39),(5,40),(6,40),(7,40),(8,40),(9,40),(3,42),(4,42),(1,446),(1,681),(1,682),(4,1077),(3,1078),(5,1079),(6,1080),(7,1081),(8,1082),(9,1084),(4,1088),(4,1089),(4,1090),(4,1091),(2,1195),(2,1196),(2,1197),(2,1198),(2,1199),(2,1200),(2,1201),(3,1202),(4,1202),(3,1203),(4,1203),(3,1204),(4,1204),(3,1205),(4,1205),(3,1206),(4,1206),(3,1207),(4,1207),(2,1208),(1,1322),(1,1399),(1,1469),(10,1588),(15,1588),(12,1589),(13,1590),(14,1591),(10,1660),(15,1660),(10,1662),(15,1662),(10,1663),(15,1663),(10,1664),(15,1664),(11,1668),(11,1669),(12,1669),(13,1669),(11,1670),(12,1670),(13,1670),(15,1670),(11,1671),(12,1671),(13,1671),(15,1671),(11,1672),(12,1672),(13,1672),(15,1672),(11,1673),(12,1673),(13,1673),(15,1673),(11,1674),(12,1674),(13,1674),(15,1674),(11,1675),(12,1675),(13,1675),(11,1676),(12,1676),(13,1676),(14,1677),(14,1678);
/*!40000 ALTER TABLE `PortInterfaceCompat` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PortLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `port_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `port_id-date` (`port_id`,`date`),
  CONSTRAINT `PortLog_ibfk_1` FOREIGN KEY (`port_id`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortLog` WRITE;
/*!40000 ALTER TABLE `PortLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `PortLog` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PortNativeVLAN`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortNativeVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `PortNativeVLAN-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `PortAllowedVLAN` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortNativeVLAN` WRITE;
/*!40000 ALTER TABLE `PortNativeVLAN` DISABLE KEYS */;
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/1',9),(979,'gi0/10',9),(979,'gi0/11',9),(979,'gi0/12',9),(979,'gi0/13',3),(979,'gi0/14',9),(979,'gi0/15',9),(979,'gi0/16',9),(979,'gi0/17',9),(979,'gi0/18',9),(979,'gi0/19',9),(979,'gi0/2',9),(979,'gi0/20',9),(979,'gi0/21',9),(979,'gi0/22',9),(979,'gi0/23',9),(979,'gi0/3',9),(979,'gi0/4',9),(979,'gi0/5',9),(979,'gi0/6',9),(979,'gi0/7',9),(979,'gi0/8',9),(979,'gi0/9',9),(980,'gi0/0/1',1),(980,'gi0/0/10',1),(980,'gi0/0/11',1),(980,'gi0/0/12',1),(980,'gi0/0/13',1),(980,'gi0/0/14',1),(980,'gi0/0/15',1),(980,'gi0/0/16',1),(980,'gi0/0/17',1),(980,'gi0/0/18',1),(980,'gi0/0/19',1),(980,'gi0/0/2',1),(980,'gi0/0/20',1),(980,'gi0/0/21',1),(980,'gi0/0/22',1),(980,'gi0/0/23',1),(980,'gi0/0/24',1),(980,'gi0/0/25',1),(980,'gi0/0/26',1),(980,'gi0/0/27',1),(980,'gi0/0/28',1),(980,'gi0/0/29',1),(980,'gi0/0/3',1),(980,'gi0/0/30',1),(980,'gi0/0/31',1),(980,'gi0/0/32',1),(980,'gi0/0/33',1),(980,'gi0/0/34',1),(980,'gi0/0/35',1),(980,'gi0/0/36',1),(980,'gi0/0/37',1),(980,'gi0/0/38',1),(980,'gi0/0/39',1),(980,'gi0/0/4',3),(980,'gi0/0/40',1),(980,'gi0/0/41',1),(980,'gi0/0/42',1),(980,'gi0/0/43',1),(980,'gi0/0/44',1),(980,'gi0/0/45',1),(980,'gi0/0/46',1),(980,'gi0/0/47',1),(980,'gi0/0/5',5),(980,'gi0/0/6',1),(980,'gi0/0/7',1),(980,'gi0/0/8',1),(980,'gi0/0/9',1),(981,'gi0/10',1),(981,'gi0/11',1),(981,'gi0/12',1),(981,'gi0/2',1),(981,'gi0/4',1),(981,'gi0/5',1),(981,'gi0/6',1),(981,'gi0/7',1),(981,'gi0/8',1),(981,'gi0/9',1);
/*!40000 ALTER TABLE `PortNativeVLAN` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PortOuterInterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortOuterInterface` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oif_name` char(48) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oif_name` (`oif_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortOuterInterface` WRITE;
/*!40000 ALTER TABLE `PortOuterInterface` DISABLE KEYS */;
INSERT INTO `PortOuterInterface` VALUES (1206,'1000Base-BX10-D'),(1207,'1000Base-BX10-U'),(1088,'1000Base-BX40-D'),(1089,'1000Base-BX40-U'),(1090,'1000Base-BX80-D'),(1091,'1000Base-BX80-U'),(1209,'1000Base-CWDM80-1470 (gray)'),(1210,'1000Base-CWDM80-1490 (violet)'),(1211,'1000Base-CWDM80-1510 (blue)'),(1212,'1000Base-CWDM80-1530 (green)'),(1213,'1000Base-CWDM80-1550 (yellow)'),(1214,'1000Base-CWDM80-1570 (orange)'),(1215,'1000Base-CWDM80-1590 (red)'),(1216,'1000Base-CWDM80-1610 (brown)'),(1424,'1000Base-CX'),(1258,'1000Base-DWDM80-28.77 (ITU 61)'),(1257,'1000Base-DWDM80-29.55 (ITU 60)'),(1256,'1000Base-DWDM80-30.33 (ITU 59)'),(1255,'1000Base-DWDM80-31.12 (ITU 58)'),(1254,'1000Base-DWDM80-31.90 (ITU 57)'),(1253,'1000Base-DWDM80-32.68 (ITU 56)'),(1252,'1000Base-DWDM80-33.47 (ITU 55)'),(1251,'1000Base-DWDM80-34.25 (ITU 54)'),(1250,'1000Base-DWDM80-35.04 (ITU 53)'),(1249,'1000Base-DWDM80-35.82 (ITU 52)'),(1248,'1000Base-DWDM80-36.61 (ITU 51)'),(1247,'1000Base-DWDM80-37.40 (ITU 50)'),(1246,'1000Base-DWDM80-38.19 (ITU 49)'),(1245,'1000Base-DWDM80-38.98 (ITU 48)'),(1244,'1000Base-DWDM80-39.77 (ITU 47)'),(1243,'1000Base-DWDM80-40.56 (ITU 46)'),(1242,'1000Base-DWDM80-41.35 (ITU 45)'),(1241,'1000Base-DWDM80-42.14 (ITU 44)'),(1240,'1000Base-DWDM80-42.94 (ITU 43)'),(1239,'1000Base-DWDM80-43.73 (ITU 42)'),(1238,'1000Base-DWDM80-44.53 (ITU 41)'),(1237,'1000Base-DWDM80-45.32 (ITU 40)'),(1236,'1000Base-DWDM80-46.12 (ITU 39)'),(1235,'1000Base-DWDM80-46.92 (ITU 38)'),(1234,'1000Base-DWDM80-47.72 (ITU 37)'),(1233,'1000Base-DWDM80-48.51 (ITU 36)'),(1232,'1000Base-DWDM80-49.32 (ITU 35)'),(1231,'1000Base-DWDM80-50.12 (ITU 34)'),(1230,'1000Base-DWDM80-50.92 (ITU 33)'),(1229,'1000Base-DWDM80-51.72 (ITU 32)'),(1228,'1000Base-DWDM80-52.52 (ITU 31)'),(1227,'1000Base-DWDM80-53.33 (ITU 30)'),(1226,'1000Base-DWDM80-54.13 (ITU 29)'),(1225,'1000Base-DWDM80-54.94 (ITU 28)'),(1224,'1000Base-DWDM80-55.75 (ITU 27)'),(1223,'1000Base-DWDM80-56.55 (ITU 26)'),(1222,'1000Base-DWDM80-57.36 (ITU 25)'),(1221,'1000Base-DWDM80-58.17 (ITU 24)'),(1220,'1000Base-DWDM80-58.98 (ITU 23)'),(1219,'1000Base-DWDM80-59.79 (ITU 22)'),(1218,'1000Base-DWDM80-60.61 (ITU 21)'),(1217,'1000Base-DWDM80-61.42 (ITU 20)'),(42,'1000Base-EX'),(1204,'1000Base-LX'),(1205,'1000Base-LX10'),(1202,'1000Base-SX'),(1203,'1000Base-SX+'),(24,'1000Base-T'),(1087,'1000Base-T (Dell 1855)'),(1316,'1000Base-T (Dell M1000e)'),(1603,'1000Base-T (HP c-Class)'),(34,'1000Base-ZX'),(1198,'100Base-BX10-D'),(1199,'100Base-BX10-U'),(1200,'100Base-EX'),(1195,'100Base-FX'),(1197,'100Base-LX10'),(1196,'100Base-SX'),(19,'100Base-TX'),(1604,'100Base-TX (HP c-Class)'),(1201,'100Base-ZX'),(1678,'100GBase-CR10'),(1677,'100GBase-CR4'),(1676,'100GBase-ER10'),(1671,'100GBase-ER4'),(1674,'100GBase-KP4'),(1673,'100GBase-KR4'),(1675,'100GBase-LR10'),(1670,'100GBase-LR4'),(1669,'100GBase-SR10'),(1672,'100GBase-SR4'),(18,'10Base-T'),(17,'10Base2'),(40,'10GBase-CX4'),(35,'10GBase-ER'),(1466,'10GBase-ER-DWDM40-28.77 (ITU 61)'),(1465,'10GBase-ER-DWDM40-29.55 (ITU 60)'),(1464,'10GBase-ER-DWDM40-30.33 (ITU 59)'),(1463,'10GBase-ER-DWDM40-31.12 (ITU 58)'),(1462,'10GBase-ER-DWDM40-31.90 (ITU 57)'),(1461,'10GBase-ER-DWDM40-32.68 (ITU 56)'),(1460,'10GBase-ER-DWDM40-33.47 (ITU 55)'),(1459,'10GBase-ER-DWDM40-34.25 (ITU 54)'),(1458,'10GBase-ER-DWDM40-35.04 (ITU 53)'),(1457,'10GBase-ER-DWDM40-35.82 (ITU 52)'),(1456,'10GBase-ER-DWDM40-36.61 (ITU 51)'),(1455,'10GBase-ER-DWDM40-37.40 (ITU 50)'),(1454,'10GBase-ER-DWDM40-38.19 (ITU 49)'),(1453,'10GBase-ER-DWDM40-38.98 (ITU 48)'),(1452,'10GBase-ER-DWDM40-39.77 (ITU 47)'),(1451,'10GBase-ER-DWDM40-40.56 (ITU 46)'),(1450,'10GBase-ER-DWDM40-41.35 (ITU 45)'),(1449,'10GBase-ER-DWDM40-42.14 (ITU 44)'),(1448,'10GBase-ER-DWDM40-42.94 (ITU 43)'),(1447,'10GBase-ER-DWDM40-43.73 (ITU 42)'),(1446,'10GBase-ER-DWDM40-44.53 (ITU 41)'),(1445,'10GBase-ER-DWDM40-45.32 (ITU 40)'),(1444,'10GBase-ER-DWDM40-46.12 (ITU 39)'),(1443,'10GBase-ER-DWDM40-46.92 (ITU 38)'),(1442,'10GBase-ER-DWDM40-47.72 (ITU 37)'),(1441,'10GBase-ER-DWDM40-48.51 (ITU 36)'),(1440,'10GBase-ER-DWDM40-49.32 (ITU 35)'),(1439,'10GBase-ER-DWDM40-50.12 (ITU 34)'),(1438,'10GBase-ER-DWDM40-50.92 (ITU 33)'),(1437,'10GBase-ER-DWDM40-51.72 (ITU 32)'),(1436,'10GBase-ER-DWDM40-52.52 (ITU 31)'),(1435,'10GBase-ER-DWDM40-53.33 (ITU 30)'),(1434,'10GBase-ER-DWDM40-54.13 (ITU 29)'),(1433,'10GBase-ER-DWDM40-54.94 (ITU 28)'),(1432,'10GBase-ER-DWDM40-55.75 (ITU 27)'),(1431,'10GBase-ER-DWDM40-56.55 (ITU 26)'),(1430,'10GBase-ER-DWDM40-57.36 (ITU 25)'),(1429,'10GBase-ER-DWDM40-58.17 (ITU 24)'),(1428,'10GBase-ER-DWDM40-58.98 (ITU 23)'),(1427,'10GBase-ER-DWDM40-59.79 (ITU 22)'),(1426,'10GBase-ER-DWDM40-60.61 (ITU 21)'),(1425,'10GBase-ER-DWDM40-61.42 (ITU 20)'),(1999,'10GBase-KR'),(41,'10GBase-KX4'),(36,'10GBase-LR'),(37,'10GBase-LRM'),(39,'10GBase-LX4'),(30,'10GBase-SR'),(1642,'10GBase-T'),(38,'10GBase-ZR'),(1300,'10GBase-ZR-DWDM80-28.77 (ITU 61)'),(1299,'10GBase-ZR-DWDM80-29.55 (ITU 60)'),(1298,'10GBase-ZR-DWDM80-30.33 (ITU 59)'),(1297,'10GBase-ZR-DWDM80-31.12 (ITU 58)'),(1296,'10GBase-ZR-DWDM80-31.90 (ITU 57)'),(1295,'10GBase-ZR-DWDM80-32.68 (ITU 56)'),(1294,'10GBase-ZR-DWDM80-33.47 (ITU 55)'),(1293,'10GBase-ZR-DWDM80-34.25 (ITU 54)'),(1292,'10GBase-ZR-DWDM80-35.04 (ITU 53)'),(1291,'10GBase-ZR-DWDM80-35.82 (ITU 52)'),(1290,'10GBase-ZR-DWDM80-36.61 (ITU 51)'),(1289,'10GBase-ZR-DWDM80-37.40 (ITU 50)'),(1288,'10GBase-ZR-DWDM80-38.19 (ITU 49)'),(1287,'10GBase-ZR-DWDM80-38.98 (ITU 48)'),(1286,'10GBase-ZR-DWDM80-39.77 (ITU 47)'),(1285,'10GBase-ZR-DWDM80-40.56 (ITU 46)'),(1284,'10GBase-ZR-DWDM80-41.35 (ITU 45)'),(1283,'10GBase-ZR-DWDM80-42.14 (ITU 44)'),(1282,'10GBase-ZR-DWDM80-42.94 (ITU 43)'),(1281,'10GBase-ZR-DWDM80-43.73 (ITU 42)'),(1280,'10GBase-ZR-DWDM80-44.53 (ITU 41)'),(1279,'10GBase-ZR-DWDM80-45.32 (ITU 40)'),(1278,'10GBase-ZR-DWDM80-46.12 (ITU 39)'),(1277,'10GBase-ZR-DWDM80-46.92 (ITU 38)'),(1276,'10GBase-ZR-DWDM80-47.72 (ITU 37)'),(1275,'10GBase-ZR-DWDM80-48.51 (ITU 36)'),(1274,'10GBase-ZR-DWDM80-49.32 (ITU 35)'),(1273,'10GBase-ZR-DWDM80-50.12 (ITU 34)'),(1272,'10GBase-ZR-DWDM80-50.92 (ITU 33)'),(1271,'10GBase-ZR-DWDM80-51.72 (ITU 32)'),(1270,'10GBase-ZR-DWDM80-52.52 (ITU 31)'),(1269,'10GBase-ZR-DWDM80-53.33 (ITU 30)'),(1268,'10GBase-ZR-DWDM80-54.13 (ITU 29)'),(1267,'10GBase-ZR-DWDM80-54.94 (ITU 28)'),(1266,'10GBase-ZR-DWDM80-55.75 (ITU 27)'),(1265,'10GBase-ZR-DWDM80-56.55 (ITU 26)'),(1264,'10GBase-ZR-DWDM80-57.36 (ITU 25)'),(1263,'10GBase-ZR-DWDM80-58.17 (ITU 24)'),(1262,'10GBase-ZR-DWDM80-58.98 (ITU 23)'),(1261,'10GBase-ZR-DWDM80-59.79 (ITU 22)'),(1260,'10GBase-ZR-DWDM80-60.61 (ITU 21)'),(1259,'10GBase-ZR-DWDM80-61.42 (ITU 20)'),(1662,'40GBase-ER4'),(1660,'40GBase-FR'),(1661,'40GBase-KR4'),(1664,'40GBase-LR4'),(1663,'40GBase-SR4'),(16,'AC-in'),(1322,'AC-out'),(1399,'DC'),(439,'dry contact'),(1668,'empty CFP'),(1589,'empty CFP2'),(1590,'empty CPAK'),(1591,'empty CXP'),(1078,'empty GBIC'),(1588,'empty QSFP'),(1208,'empty SFP-100'),(1077,'empty SFP-1000'),(1084,'empty SFP+'),(1080,'empty X2'),(1079,'empty XENPAK'),(1082,'empty XFP'),(1081,'empty XPAK'),(446,'KVM (console)'),(33,'KVM (host)'),(682,'RS-232 (DB-25)'),(681,'RS-232 (DB-9)'),(29,'RS-232 (RJ-45)'),(32,'sync serial'),(440,'unknown'),(31,'virtual bridge'),(1469,'virtual port');
/*!40000 ALTER TABLE `PortOuterInterface` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `PortVLANMode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PortVLANMode` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `vlan_mode` enum('access','trunk') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'access',
  PRIMARY KEY (`object_id`,`port_name`),
  CONSTRAINT `PortVLANMode-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `PortVLANMode` WRITE;
/*!40000 ALTER TABLE `PortVLANMode` DISABLE KEYS */;
INSERT INTO `PortVLANMode` VALUES (979,'gi0/1','trunk'),(979,'gi0/10','access'),(979,'gi0/11','access'),(979,'gi0/12','access'),(979,'gi0/13','access'),(979,'gi0/14','access'),(979,'gi0/15','access'),(979,'gi0/16','access'),(979,'gi0/17','access'),(979,'gi0/18','access'),(979,'gi0/19','access'),(979,'gi0/2','trunk'),(979,'gi0/20','access'),(979,'gi0/21','access'),(979,'gi0/22','access'),(979,'gi0/23','access'),(979,'gi0/24','trunk'),(979,'gi0/3','access'),(979,'gi0/4','access'),(979,'gi0/5','access'),(979,'gi0/6','access'),(979,'gi0/7','access'),(979,'gi0/8','access'),(979,'gi0/9','access'),(980,'gi0/0/1','trunk'),(980,'gi0/0/10','access'),(980,'gi0/0/11','access'),(980,'gi0/0/12','access'),(980,'gi0/0/13','access'),(980,'gi0/0/14','access'),(980,'gi0/0/15','access'),(980,'gi0/0/16','access'),(980,'gi0/0/17','access'),(980,'gi0/0/18','access'),(980,'gi0/0/19','access'),(980,'gi0/0/2','access'),(980,'gi0/0/20','access'),(980,'gi0/0/21','access'),(980,'gi0/0/22','access'),(980,'gi0/0/23','access'),(980,'gi0/0/24','access'),(980,'gi0/0/25','access'),(980,'gi0/0/26','access'),(980,'gi0/0/27','access'),(980,'gi0/0/28','access'),(980,'gi0/0/29','access'),(980,'gi0/0/3','access'),(980,'gi0/0/30','access'),(980,'gi0/0/31','access'),(980,'gi0/0/32','access'),(980,'gi0/0/33','access'),(980,'gi0/0/34','access'),(980,'gi0/0/35','access'),(980,'gi0/0/36','access'),(980,'gi0/0/37','access'),(980,'gi0/0/38','access'),(980,'gi0/0/39','access'),(980,'gi0/0/4','access'),(980,'gi0/0/40','access'),(980,'gi0/0/41','access'),(980,'gi0/0/42','access'),(980,'gi0/0/43','access'),(980,'gi0/0/44','access'),(980,'gi0/0/45','access'),(980,'gi0/0/46','access'),(980,'gi0/0/47','access'),(980,'gi0/0/48','trunk'),(980,'gi0/0/5','access'),(980,'gi0/0/6','access'),(980,'gi0/0/7','access'),(980,'gi0/0/8','access'),(980,'gi0/0/9','access'),(981,'gi0/1','trunk'),(981,'gi0/10','trunk'),(981,'gi0/11','trunk'),(981,'gi0/12','trunk'),(981,'gi0/2','trunk'),(981,'gi0/3','trunk'),(981,'gi0/4','trunk'),(981,'gi0/5','trunk'),(981,'gi0/6','trunk'),(981,'gi0/7','trunk'),(981,'gi0/8','trunk'),(981,'gi0/9','trunk');
/*!40000 ALTER TABLE `PortVLANMode` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Rack`;
/*!50001 DROP VIEW IF EXISTS `Rack`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Rack` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `asset_no`,
 1 AS `has_problems`,
 1 AS `comment`,
 1 AS `height`,
 1 AS `sort_order`,
 1 AS `thumb_data`,
 1 AS `row_id`,
 1 AS `row_name`,
 1 AS `location_id`,
 1 AS `location_name`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `RackObject`;
/*!50001 DROP VIEW IF EXISTS `RackObject`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `RackObject` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `label`,
 1 AS `objtype_id`,
 1 AS `asset_no`,
 1 AS `has_problems`,
 1 AS `comment`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `RackSpace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RackSpace` (
  `rack_id` int(10) unsigned NOT NULL DEFAULT '0',
  `unit_no` int(10) unsigned NOT NULL DEFAULT '0',
  `atom` enum('front','interior','rear') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'interior',
  `state` enum('A','U','T') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `object_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`rack_id`,`unit_no`,`atom`),
  KEY `RackSpace_object_id` (`object_id`),
  CONSTRAINT `RackSpace-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `RackSpace-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `RackSpace` WRITE;
/*!40000 ALTER TABLE `RackSpace` DISABLE KEYS */;
INSERT INTO `RackSpace` VALUES (967,1,'rear','T',949),(967,2,'front','T',910),(967,2,'interior','T',910),(967,2,'rear','T',910),(967,4,'front','T',911),(967,4,'interior','T',911),(967,4,'rear','T',911),(967,6,'front','T',912),(967,6,'interior','T',912),(967,6,'rear','T',912),(967,8,'front','T',913),(967,8,'interior','T',913),(967,8,'rear','T',913),(967,10,'front','T',914),(967,10,'interior','T',914),(967,10,'rear','T',914),(967,17,'front','T',982),(967,17,'interior','T',982),(967,17,'rear','T',982),(967,22,'front','T',916),(967,22,'interior','T',916),(967,22,'rear','T',916),(967,23,'front','T',916),(967,23,'interior','T',916),(967,23,'rear','T',916),(967,35,'front','T',981),(967,35,'interior','T',981),(968,1,'front','T',917),(968,1,'interior','T',917),(968,1,'rear','A',NULL),(968,2,'front','T',917),(968,2,'interior','T',917),(968,2,'rear','A',NULL),(968,3,'rear','A',NULL),(968,4,'front','T',915),(968,4,'interior','T',915),(968,4,'rear','A',NULL),(968,5,'front','T',915),(968,5,'interior','T',915),(968,5,'rear','A',NULL),(968,6,'front','T',952),(968,6,'rear','A',NULL),(968,7,'front','T',906),(968,7,'interior','T',906),(968,7,'rear','A',NULL),(968,8,'rear','A',NULL),(968,9,'front','T',905),(968,9,'interior','T',905),(968,9,'rear','A',NULL),(968,10,'front','T',905),(968,10,'interior','T',905),(968,10,'rear','A',NULL),(968,11,'front','T',905),(968,11,'interior','T',905),(968,11,'rear','A',NULL),(968,12,'rear','A',NULL),(969,2,'rear','T',947),(969,3,'front','T',918),(969,3,'interior','T',918),(969,3,'rear','T',918),(969,4,'front','T',918),(969,4,'interior','T',918),(969,4,'rear','T',918),(969,5,'front','T',918),(969,5,'interior','T',918),(969,5,'rear','T',918),(969,6,'front','T',918),(969,6,'interior','T',918),(969,6,'rear','T',918),(969,7,'front','T',918),(969,7,'interior','T',918),(969,7,'rear','T',918),(969,9,'front','T',919),(969,9,'interior','T',919),(969,9,'rear','T',919),(969,10,'front','T',919),(969,10,'interior','T',919),(969,10,'rear','T',919),(969,11,'front','T',919),(969,11,'interior','T',919),(969,11,'rear','T',919),(969,12,'front','T',919),(969,12,'interior','T',919),(969,12,'rear','T',919),(969,13,'front','T',919),(969,13,'interior','T',919),(969,13,'rear','T',919),(969,14,'rear','T',950),(969,15,'front','T',921),(969,15,'interior','T',921),(969,15,'rear','T',921),(969,16,'front','T',921),(969,16,'interior','T',921),(969,16,'rear','T',921),(969,17,'front','T',922),(969,17,'interior','T',922),(969,17,'rear','T',922),(969,18,'front','T',922),(969,18,'interior','T',922),(969,18,'rear','T',922),(969,30,'front','T',920),(969,30,'interior','T',920),(969,30,'rear','T',920),(969,31,'front','T',920),(969,31,'interior','T',920),(969,31,'rear','T',920),(969,32,'front','T',920),(969,32,'interior','T',920),(969,32,'rear','T',920),(969,33,'front','T',920),(969,33,'interior','T',920),(969,33,'rear','T',920),(969,34,'front','T',920),(969,34,'interior','T',920),(969,34,'rear','T',920),(969,35,'front','T',920),(969,35,'interior','T',920),(969,35,'rear','T',920),(969,36,'front','T',920),(969,36,'interior','T',920),(969,36,'rear','T',920),(969,37,'front','T',920),(969,37,'interior','T',920),(969,37,'rear','T',920),(970,1,'front','T',944),(970,1,'interior','T',944),(970,1,'rear','T',944),(970,2,'front','T',944),(970,2,'interior','T',944),(970,2,'rear','T',944),(970,9,'rear','T',948),(970,10,'interior','T',927),(970,10,'rear','T',927),(970,11,'interior','T',927),(970,11,'rear','T',927),(970,12,'interior','T',927),(970,12,'rear','T',927),(970,13,'rear','T',945),(970,14,'interior','T',907),(970,14,'rear','T',907),(970,15,'interior','T',907),(970,15,'rear','T',907),(970,16,'interior','T',907),(970,16,'rear','T',907),(971,1,'front','T',942),(971,1,'interior','T',942),(971,1,'rear','T',942),(971,2,'front','T',942),(971,2,'interior','T',942),(971,2,'rear','T',942),(971,3,'front','T',942),(971,3,'interior','T',942),(971,3,'rear','T',942),(971,4,'front','T',942),(971,4,'interior','T',942),(971,4,'rear','T',942),(971,5,'front','T',942),(971,5,'interior','T',942),(971,5,'rear','T',942),(971,6,'front','U',NULL),(971,6,'interior','U',NULL),(971,6,'rear','U',NULL),(971,7,'front','T',932),(971,7,'interior','T',932),(971,7,'rear','T',932),(971,9,'front','T',934),(971,9,'interior','T',934),(971,9,'rear','T',934),(971,11,'front','T',936),(971,11,'interior','T',936),(971,11,'rear','T',936),(971,13,'front','T',938),(971,13,'interior','T',938),(971,13,'rear','T',938),(971,15,'front','T',940),(971,15,'interior','T',940),(971,15,'rear','T',940),(971,17,'rear','T',951),(971,37,'interior','T',930),(971,37,'rear','T',930),(971,38,'interior','T',930),(971,38,'rear','T',930),(971,40,'interior','T',928),(971,40,'rear','T',928),(971,41,'interior','T',928),(971,41,'rear','T',928),(972,2,'front','T',956),(972,2,'interior','T',956),(972,2,'rear','T',956),(972,4,'front','T',957),(972,4,'interior','T',957),(972,4,'rear','T',957),(972,6,'front','T',958),(972,6,'interior','T',958),(972,6,'rear','T',958),(972,8,'front','T',959),(972,8,'interior','T',959),(972,8,'rear','T',959),(972,10,'front','T',960),(972,10,'interior','T',960),(972,10,'rear','T',960),(972,18,'interior','T',908),(972,18,'rear','T',908),(972,19,'interior','T',908),(972,19,'rear','T',908),(972,20,'front','T',953),(972,20,'interior','T',908),(972,20,'rear','T',908),(972,34,'interior','T',962),(972,34,'rear','T',962),(972,35,'interior','T',961),(972,35,'rear','T',961),(973,1,'front','T',943),(973,1,'interior','T',943),(973,1,'rear','T',943),(973,2,'front','T',943),(973,2,'interior','T',943),(973,2,'rear','T',943),(973,3,'front','T',943),(973,3,'interior','T',943),(973,3,'rear','T',943),(973,4,'front','T',943),(973,4,'interior','T',943),(973,4,'rear','T',943),(973,5,'front','T',943),(973,5,'interior','T',943),(973,5,'rear','T',943),(973,6,'front','U',NULL),(973,6,'interior','U',NULL),(973,6,'rear','U',NULL),(973,7,'front','T',933),(973,7,'interior','T',933),(973,7,'rear','T',933),(973,9,'front','T',935),(973,9,'interior','T',935),(973,9,'rear','T',935),(973,11,'front','T',937),(973,11,'interior','T',937),(973,11,'rear','T',937),(973,13,'front','T',939),(973,13,'interior','T',939),(973,13,'rear','T',939),(973,15,'front','T',941),(973,15,'interior','T',941),(973,15,'rear','T',941),(973,17,'rear','T',954),(973,37,'interior','T',931),(973,37,'rear','T',931),(973,38,'interior','T',931),(973,38,'rear','T',931),(973,40,'interior','T',929),(973,40,'rear','T',929),(973,41,'interior','T',929),(973,41,'rear','T',929),(974,1,'front','T',923),(974,1,'interior','T',923),(974,1,'rear','T',923),(974,3,'front','T',924),(974,3,'interior','T',924),(974,3,'rear','T',924),(974,5,'front','T',925),(974,5,'interior','T',925),(974,5,'rear','T',925),(974,9,'rear','T',955),(974,11,'interior','T',926),(974,11,'rear','T',926),(974,13,'interior','T',909),(974,13,'rear','T',909),(974,14,'interior','T',909),(974,14,'rear','T',909),(974,15,'interior','T',909),(974,15,'rear','T',909),(974,16,'rear','T',946);
/*!40000 ALTER TABLE `RackSpace` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `RackThumbnail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RackThumbnail` (
  `rack_id` int(10) unsigned NOT NULL,
  `thumb_data` blob,
  UNIQUE KEY `rack_id` (`rack_id`),
  CONSTRAINT `RackThumbnail-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `RackThumbnail` WRITE;
/*!40000 ALTER TABLE `RackThumbnail` DISABLE KEYS */;
/*!40000 ALTER TABLE `RackThumbnail` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Row`;
/*!50001 DROP VIEW IF EXISTS `Row`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Row` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `location_id`,
 1 AS `location_name`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `Script`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Script` (
  `script_name` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `script_text` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`script_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Script` WRITE;
/*!40000 ALTER TABLE `Script` DISABLE KEYS */;
INSERT INTO `Script` VALUES ('DefaultRSConfig','CONNECT_TIMEOUT=1\nCONNECT_PORT=%RSPORT%\nCHECK_TCP=`TCP_CHECK {\n	connect_port %CONNECT_PORT%\n	connect_timeout %CONNECT_TIMEOUT% \n}\'\n%CHECK%\n'),('DefaultVSConfig','METHOD=NAT\nlvs_method %METHOD%\n'),('RackCode','allow {$userid_1}'),('RackCodeCache','YTozOntzOjY6InJlc3VsdCI7czozOiJBQ0siO3M6NzoiQUJJX3ZlciI7aToyO3M6NDoibG9hZCI7YToxOntpOjA7YTo0OntzOjQ6InR5cGUiO3M6MTA6IlNZTlRfR1JBTlQiO3M6OToiY29uZGl0aW9uIjthOjM6e3M6NDoidHlwZSI7czo3OiJMRVhfVEFHIjtzOjQ6ImxvYWQiO3M6OToiJHVzZXJpZF8xIjtzOjY6ImxpbmVubyI7aToxO31zOjg6ImRlY2lzaW9uIjtiOjE7czo2OiJsaW5lbm8iO2k6MTt9fX0=');
/*!40000 ALTER TABLE `Script` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TagStorage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TagStorage` (
  `entity_realm` enum('file','ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','user','vst') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'object',
  `entity_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tag_is_assignable` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `user` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `entity_tag` (`entity_realm`,`entity_id`,`tag_id`),
  KEY `entity_id` (`entity_id`),
  KEY `TagStorage-FK-tag_id` (`tag_id`),
  KEY `tag_id-tag_is_assignable` (`tag_id`,`tag_is_assignable`),
  CONSTRAINT `TagStorage-FK-TagTree` FOREIGN KEY (`tag_id`, `tag_is_assignable`) REFERENCES `TagTree` (`id`, `is_assignable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TagStorage` WRITE;
/*!40000 ALTER TABLE `TagStorage` DISABLE KEYS */;
INSERT INTO `TagStorage` VALUES ('ipv4net',96,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',97,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',98,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',99,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',102,5,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',102,11,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',103,5,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',103,11,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',104,5,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',104,11,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',105,5,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',105,11,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',106,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',107,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv4net',108,12,'yes','john_doe','2012-06-01 00:00:00'),('ipvs',1,2,'yes','admin','2013-06-23 23:48:52'),('ipv6net',1,10,'yes','john_doe','2012-06-01 00:00:00'),('ipv6net',2,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv6net',3,12,'yes','john_doe','2012-06-01 00:00:00'),('ipv6net',4,18,'yes','john_doe','2012-06-01 00:00:00'),('ipv6net',5,18,'yes','john_doe','2012-06-01 00:00:00'),('object',905,1,'yes','john_doe','2012-06-01 00:00:00'),('object',908,2,'yes','john_doe','2012-06-01 00:00:00'),('object',909,1,'yes','john_doe','2012-06-01 00:00:00'),('object',928,9,'yes','john_doe','2012-06-01 00:00:00'),('object',929,9,'yes','john_doe','2012-06-01 00:00:00'),('object',956,2,'yes','john_doe','2012-06-01 00:00:00'),('object',957,2,'yes','john_doe','2012-06-01 00:00:00'),('object',958,2,'yes','john_doe','2012-06-01 00:00:00'),('object',959,2,'yes','john_doe','2012-06-01 00:00:00'),('object',960,2,'yes','john_doe','2012-06-01 00:00:00'),('object',961,2,'yes','john_doe','2012-06-01 00:00:00'),('object',962,2,'yes','john_doe','2012-06-01 00:00:00'),('object',979,12,'yes','john_doe','2012-06-01 00:00:00'),('object',980,12,'yes','john_doe','2012-06-01 00:00:00'),('object',981,12,'yes','john_doe','2012-06-01 00:00:00'),('rack',967,7,'yes','john_doe','2012-06-01 00:00:00'),('rack',968,8,'yes','john_doe','2012-06-01 00:00:00'),('rack',969,7,'yes','john_doe','2012-06-01 00:00:00'),('rack',970,8,'yes','john_doe','2012-06-01 00:00:00'),('rack',971,7,'yes','john_doe','2012-06-01 00:00:00'),('rack',972,7,'yes','john_doe','2012-06-01 00:00:00'),('rack',973,7,'yes','john_doe','2012-06-01 00:00:00'),('rack',974,8,'yes','john_doe','2012-06-01 00:00:00'),('vst',1,22,'yes','john_doe','2012-06-01 00:00:00'),('vst',2,22,'yes','john_doe','2012-06-01 00:00:00'),('vst',3,23,'yes','john_doe','2012-06-01 00:00:00');
/*!40000 ALTER TABLE `TagStorage` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TagTree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TagTree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `is_assignable` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `tag` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  KEY `TagTree-K-parent_id` (`parent_id`),
  KEY `id-is_assignable` (`id`,`is_assignable`),
  CONSTRAINT `TagTree-K-parent_id` FOREIGN KEY (`parent_id`) REFERENCES `TagTree` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TagTree` WRITE;
/*!40000 ALTER TABLE `TagTree` DISABLE KEYS */;
INSERT INTO `TagTree` VALUES (1,NULL,'yes','testing'),(2,NULL,'yes','production'),(5,10,'yes','WAN link'),(6,NULL,'yes','racks'),(7,6,'yes','tall racks'),(8,6,'yes','low racks'),(9,NULL,'yes','load balancer'),(10,NULL,'yes','network'),(11,10,'yes','small network'),(12,10,'yes','medium network'),(16,NULL,'yes','XKCD'),(17,16,'yes','romance'),(18,16,'yes','sarcasm'),(19,16,'yes','math'),(20,16,'yes','language'),(21,NULL,'yes','vlan template'),(22,21,'yes','access switch template'),(23,21,'yes','distribution switch template');
/*!40000 ALTER TABLE `TagTree` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `UserAccount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserAccount` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` char(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_password_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_realname` char(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `UserAccount` WRITE;
/*!40000 ALTER TABLE `UserAccount` DISABLE KEYS */;
INSERT INTO `UserAccount` VALUES (1,'admin','d033e22ae348aeb5660fc2140aec35850c4da997','RackTables Administrator');
/*!40000 ALTER TABLE `UserAccount` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `UserConfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserConfig` (
  `varname` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `varvalue` text COLLATE utf8_unicode_ci NOT NULL,
  `user` char(64) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `user_varname` (`user`,`varname`),
  KEY `varname` (`varname`),
  CONSTRAINT `UserConfig-FK-varname` FOREIGN KEY (`varname`) REFERENCES `Config` (`varname`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `UserConfig` WRITE;
/*!40000 ALTER TABLE `UserConfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `UserConfig` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANDescription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANDescription` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vlan_type` enum('ondemand','compulsory','alien') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ondemand',
  `vlan_descr` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `VLANDescription-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VLANDescription-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANDescription` WRITE;
/*!40000 ALTER TABLE `VLANDescription` DISABLE KEYS */;
INSERT INTO `VLANDescription` VALUES (1,1,'compulsory','default'),(1,2,'ondemand','второй'),(1,3,'ondemand','третий'),(1,4,'ondemand','четвёртый'),(1,5,'ondemand','пятый'),(1,6,'ondemand','шестой'),(1,7,'ondemand','седьмой'),(1,8,'ondemand','восьмой'),(1,9,'ondemand','девятый'),(1,10,'ondemand','десятый'),(1,11,'ondemand','одиннадцатый'),(1,12,'ondemand','двенадцатый'),(1,13,'ondemand','тринадцатый'),(1,14,'ondemand','четырнадцатый'),(1,15,'ondemand','пятнадцатый'),(2,1,'compulsory','default'),(2,2,'ondemand','second'),(2,3,'ondemand','third'),(2,4,'ondemand','fourth'),(2,5,'ondemand','fifth'),(2,6,'ondemand','sixth'),(2,7,'ondemand','seventh'),(2,8,'ondemand','eighth'),(2,9,'ondemand','ninth'),(2,10,'ondemand','tenth'),(2,11,'ondemand','eleventh'),(2,12,'ondemand','twelfth'),(2,13,'ondemand','thirteenth'),(2,14,'ondemand','fourteenth'),(2,15,'ondemand','fifteenth'),(3,1,'compulsory','default'),(3,2,'ondemand','другий'),(3,3,'ondemand','третій'),(3,4,'ondemand','четвертий'),(3,5,'ondemand','п\'ятий'),(3,6,'ondemand','шостий'),(3,7,'ondemand','сьомий'),(3,8,'ondemand','восьмий'),(3,9,'ondemand','дев\'ятий'),(3,10,'ondemand','десятий'),(3,11,'ondemand','одинадцятий'),(3,12,'ondemand','дванадцятий'),(3,13,'ondemand','тринадцятий'),(3,14,'ondemand','чотирнадцятий'),(3,15,'ondemand','п\'ятнадцятий'),(4,1,'compulsory','default'),(4,2,'ondemand','第二'),(4,3,'ondemand','第三'),(4,4,'ondemand','第四'),(4,5,'ondemand','第五'),(4,6,'ondemand','第六'),(4,7,'ondemand','第七'),(4,8,'ondemand','第八'),(4,9,'ondemand','第九'),(4,10,'ondemand','第十'),(4,11,'ondemand','第十一'),(4,12,'ondemand','第十二'),(4,13,'ondemand','第十三'),(4,14,'ondemand','第十四'),(4,15,'ondemand','第十五');
/*!40000 ALTER TABLE `VLANDescription` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANDomain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANDomain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned DEFAULT NULL,
  `description` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`),
  KEY `VLANDomain-FK-group_id` (`group_id`),
  CONSTRAINT `VLANDomain-FK-group_id` FOREIGN KEY (`group_id`) REFERENCES `VLANDomain` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANDomain` WRITE;
/*!40000 ALTER TABLE `VLANDomain` DISABLE KEYS */;
INSERT INTO `VLANDomain` VALUES (1,NULL,'русский'),(2,NULL,'English'),(3,NULL,'українська'),(4,NULL,'日本語');
/*!40000 ALTER TABLE `VLANDomain` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANIPv4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANIPv4` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv4net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain-vlan` (`ipv4net_id`,`domain_id`,`vlan_id`),
  KEY `VLANIPv4-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv4-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv4-FK-ipv4net_id` FOREIGN KEY (`ipv4net_id`) REFERENCES `IPv4Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANIPv4` WRITE;
/*!40000 ALTER TABLE `VLANIPv4` DISABLE KEYS */;
/*!40000 ALTER TABLE `VLANIPv4` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANIPv6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANIPv6` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv6net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain-vlan` (`ipv6net_id`,`domain_id`,`vlan_id`),
  KEY `VLANIPv6-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv6-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv6-FK-ipv6net_id` FOREIGN KEY (`ipv6net_id`) REFERENCES `IPv6Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANIPv6` WRITE;
/*!40000 ALTER TABLE `VLANIPv6` DISABLE KEYS */;
/*!40000 ALTER TABLE `VLANIPv6` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANSTRule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANSTRule` (
  `vst_id` int(10) unsigned NOT NULL,
  `rule_no` int(10) unsigned NOT NULL,
  `port_pcre` char(255) COLLATE utf8_unicode_ci NOT NULL,
  `port_role` enum('access','trunk','anymode','uplink','downlink','none') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `wrt_vlans` text COLLATE utf8_unicode_ci,
  `description` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `vst-rule` (`vst_id`,`rule_no`),
  CONSTRAINT `VLANSTRule-FK-vst_id` FOREIGN KEY (`vst_id`) REFERENCES `VLANSwitchTemplate` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANSTRule` WRITE;
/*!40000 ALTER TABLE `VLANSTRule` DISABLE KEYS */;
INSERT INTO `VLANSTRule` VALUES (1,100,'#[^\\d]24#','uplink','','uplink'),(1,999,'#.#','anymode','','user-defined'),(2,100,'#[^\\d]48#','uplink','','uplink'),(2,999,'#.#','anymode','','user-defined'),(3,999,'/./','downlink','','access switches');
/*!40000 ALTER TABLE `VLANSTRule` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANSwitch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANSwitch` (
  `object_id` int(10) unsigned NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `mutex_rev` int(10) unsigned NOT NULL DEFAULT '0',
  `out_of_sync` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `last_errno` int(10) unsigned NOT NULL DEFAULT '0',
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_push_started` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_push_finished` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_error_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `object_id` (`object_id`),
  KEY `domain_id` (`domain_id`),
  KEY `template_id` (`template_id`),
  KEY `out_of_sync` (`out_of_sync`),
  KEY `last_errno` (`last_errno`),
  CONSTRAINT `VLANSwitch-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`),
  CONSTRAINT `VLANSwitch-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `VLANSwitch-FK-template_id` FOREIGN KEY (`template_id`) REFERENCES `VLANSwitchTemplate` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANSwitch` WRITE;
/*!40000 ALTER TABLE `VLANSwitch` DISABLE KEYS */;
INSERT INTO `VLANSwitch` VALUES (979,2,1,4,'no',0,'2012-09-09 15:11:57','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(980,2,2,4,'no',0,'2012-09-09 15:16:22','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(981,2,3,1,'no',0,'2012-09-09 15:16:53','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `VLANSwitch` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANSwitchTemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANSwitchTemplate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mutex_rev` int(10) NOT NULL,
  `description` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `saved_by` char(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANSwitchTemplate` WRITE;
/*!40000 ALTER TABLE `VLANSwitchTemplate` DISABLE KEYS */;
INSERT INTO `VLANSwitchTemplate` VALUES (1,1,'24 ports switch','admin'),(2,1,'48 ports switch','admin'),(3,1,'distribution switch','admin');
/*!40000 ALTER TABLE `VLANSwitchTemplate` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VLANValidID`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VLANValidID` (
  `vlan_id` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`vlan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VLANValidID` WRITE;
/*!40000 ALTER TABLE `VLANValidID` DISABLE KEYS */;
INSERT INTO `VLANValidID` VALUES (1),(2),(3),(4),(5),(6),(7),(8),(9),(10),(11),(12),(13),(14),(15),(16),(17),(18),(19),(20),(21),(22),(23),(24),(25),(26),(27),(28),(29),(30),(31),(32),(33),(34),(35),(36),(37),(38),(39),(40),(41),(42),(43),(44),(45),(46),(47),(48),(49),(50),(51),(52),(53),(54),(55),(56),(57),(58),(59),(60),(61),(62),(63),(64),(65),(66),(67),(68),(69),(70),(71),(72),(73),(74),(75),(76),(77),(78),(79),(80),(81),(82),(83),(84),(85),(86),(87),(88),(89),(90),(91),(92),(93),(94),(95),(96),(97),(98),(99),(100),(101),(102),(103),(104),(105),(106),(107),(108),(109),(110),(111),(112),(113),(114),(115),(116),(117),(118),(119),(120),(121),(122),(123),(124),(125),(126),(127),(128),(129),(130),(131),(132),(133),(134),(135),(136),(137),(138),(139),(140),(141),(142),(143),(144),(145),(146),(147),(148),(149),(150),(151),(152),(153),(154),(155),(156),(157),(158),(159),(160),(161),(162),(163),(164),(165),(166),(167),(168),(169),(170),(171),(172),(173),(174),(175),(176),(177),(178),(179),(180),(181),(182),(183),(184),(185),(186),(187),(188),(189),(190),(191),(192),(193),(194),(195),(196),(197),(198),(199),(200),(201),(202),(203),(204),(205),(206),(207),(208),(209),(210),(211),(212),(213),(214),(215),(216),(217),(218),(219),(220),(221),(222),(223),(224),(225),(226),(227),(228),(229),(230),(231),(232),(233),(234),(235),(236),(237),(238),(239),(240),(241),(242),(243),(244),(245),(246),(247),(248),(249),(250),(251),(252),(253),(254),(255),(256),(257),(258),(259),(260),(261),(262),(263),(264),(265),(266),(267),(268),(269),(270),(271),(272),(273),(274),(275),(276),(277),(278),(279),(280),(281),(282),(283),(284),(285),(286),(287),(288),(289),(290),(291),(292),(293),(294),(295),(296),(297),(298),(299),(300),(301),(302),(303),(304),(305),(306),(307),(308),(309),(310),(311),(312),(313),(314),(315),(316),(317),(318),(319),(320),(321),(322),(323),(324),(325),(326),(327),(328),(329),(330),(331),(332),(333),(334),(335),(336),(337),(338),(339),(340),(341),(342),(343),(344),(345),(346),(347),(348),(349),(350),(351),(352),(353),(354),(355),(356),(357),(358),(359),(360),(361),(362),(363),(364),(365),(366),(367),(368),(369),(370),(371),(372),(373),(374),(375),(376),(377),(378),(379),(380),(381),(382),(383),(384),(385),(386),(387),(388),(389),(390),(391),(392),(393),(394),(395),(396),(397),(398),(399),(400),(401),(402),(403),(404),(405),(406),(407),(408),(409),(410),(411),(412),(413),(414),(415),(416),(417),(418),(419),(420),(421),(422),(423),(424),(425),(426),(427),(428),(429),(430),(431),(432),(433),(434),(435),(436),(437),(438),(439),(440),(441),(442),(443),(444),(445),(446),(447),(448),(449),(450),(451),(452),(453),(454),(455),(456),(457),(458),(459),(460),(461),(462),(463),(464),(465),(466),(467),(468),(469),(470),(471),(472),(473),(474),(475),(476),(477),(478),(479),(480),(481),(482),(483),(484),(485),(486),(487),(488),(489),(490),(491),(492),(493),(494),(495),(496),(497),(498),(499),(500),(501),(502),(503),(504),(505),(506),(507),(508),(509),(510),(511),(512),(513),(514),(515),(516),(517),(518),(519),(520),(521),(522),(523),(524),(525),(526),(527),(528),(529),(530),(531),(532),(533),(534),(535),(536),(537),(538),(539),(540),(541),(542),(543),(544),(545),(546),(547),(548),(549),(550),(551),(552),(553),(554),(555),(556),(557),(558),(559),(560),(561),(562),(563),(564),(565),(566),(567),(568),(569),(570),(571),(572),(573),(574),(575),(576),(577),(578),(579),(580),(581),(582),(583),(584),(585),(586),(587),(588),(589),(590),(591),(592),(593),(594),(595),(596),(597),(598),(599),(600),(601),(602),(603),(604),(605),(606),(607),(608),(609),(610),(611),(612),(613),(614),(615),(616),(617),(618),(619),(620),(621),(622),(623),(624),(625),(626),(627),(628),(629),(630),(631),(632),(633),(634),(635),(636),(637),(638),(639),(640),(641),(642),(643),(644),(645),(646),(647),(648),(649),(650),(651),(652),(653),(654),(655),(656),(657),(658),(659),(660),(661),(662),(663),(664),(665),(666),(667),(668),(669),(670),(671),(672),(673),(674),(675),(676),(677),(678),(679),(680),(681),(682),(683),(684),(685),(686),(687),(688),(689),(690),(691),(692),(693),(694),(695),(696),(697),(698),(699),(700),(701),(702),(703),(704),(705),(706),(707),(708),(709),(710),(711),(712),(713),(714),(715),(716),(717),(718),(719),(720),(721),(722),(723),(724),(725),(726),(727),(728),(729),(730),(731),(732),(733),(734),(735),(736),(737),(738),(739),(740),(741),(742),(743),(744),(745),(746),(747),(748),(749),(750),(751),(752),(753),(754),(755),(756),(757),(758),(759),(760),(761),(762),(763),(764),(765),(766),(767),(768),(769),(770),(771),(772),(773),(774),(775),(776),(777),(778),(779),(780),(781),(782),(783),(784),(785),(786),(787),(788),(789),(790),(791),(792),(793),(794),(795),(796),(797),(798),(799),(800),(801),(802),(803),(804),(805),(806),(807),(808),(809),(810),(811),(812),(813),(814),(815),(816),(817),(818),(819),(820),(821),(822),(823),(824),(825),(826),(827),(828),(829),(830),(831),(832),(833),(834),(835),(836),(837),(838),(839),(840),(841),(842),(843),(844),(845),(846),(847),(848),(849),(850),(851),(852),(853),(854),(855),(856),(857),(858),(859),(860),(861),(862),(863),(864),(865),(866),(867),(868),(869),(870),(871),(872),(873),(874),(875),(876),(877),(878),(879),(880),(881),(882),(883),(884),(885),(886),(887),(888),(889),(890),(891),(892),(893),(894),(895),(896),(897),(898),(899),(900),(901),(902),(903),(904),(905),(906),(907),(908),(909),(910),(911),(912),(913),(914),(915),(916),(917),(918),(919),(920),(921),(922),(923),(924),(925),(926),(927),(928),(929),(930),(931),(932),(933),(934),(935),(936),(937),(938),(939),(940),(941),(942),(943),(944),(945),(946),(947),(948),(949),(950),(951),(952),(953),(954),(955),(956),(957),(958),(959),(960),(961),(962),(963),(964),(965),(966),(967),(968),(969),(970),(971),(972),(973),(974),(975),(976),(977),(978),(979),(980),(981),(982),(983),(984),(985),(986),(987),(988),(989),(990),(991),(992),(993),(994),(995),(996),(997),(998),(999),(1000),(1001),(1002),(1003),(1004),(1005),(1006),(1007),(1008),(1009),(1010),(1011),(1012),(1013),(1014),(1015),(1016),(1017),(1018),(1019),(1020),(1021),(1022),(1023),(1024),(1025),(1026),(1027),(1028),(1029),(1030),(1031),(1032),(1033),(1034),(1035),(1036),(1037),(1038),(1039),(1040),(1041),(1042),(1043),(1044),(1045),(1046),(1047),(1048),(1049),(1050),(1051),(1052),(1053),(1054),(1055),(1056),(1057),(1058),(1059),(1060),(1061),(1062),(1063),(1064),(1065),(1066),(1067),(1068),(1069),(1070),(1071),(1072),(1073),(1074),(1075),(1076),(1077),(1078),(1079),(1080),(1081),(1082),(1083),(1084),(1085),(1086),(1087),(1088),(1089),(1090),(1091),(1092),(1093),(1094),(1095),(1096),(1097),(1098),(1099),(1100),(1101),(1102),(1103),(1104),(1105),(1106),(1107),(1108),(1109),(1110),(1111),(1112),(1113),(1114),(1115),(1116),(1117),(1118),(1119),(1120),(1121),(1122),(1123),(1124),(1125),(1126),(1127),(1128),(1129),(1130),(1131),(1132),(1133),(1134),(1135),(1136),(1137),(1138),(1139),(1140),(1141),(1142),(1143),(1144),(1145),(1146),(1147),(1148),(1149),(1150),(1151),(1152),(1153),(1154),(1155),(1156),(1157),(1158),(1159),(1160),(1161),(1162),(1163),(1164),(1165),(1166),(1167),(1168),(1169),(1170),(1171),(1172),(1173),(1174),(1175),(1176),(1177),(1178),(1179),(1180),(1181),(1182),(1183),(1184),(1185),(1186),(1187),(1188),(1189),(1190),(1191),(1192),(1193),(1194),(1195),(1196),(1197),(1198),(1199),(1200),(1201),(1202),(1203),(1204),(1205),(1206),(1207),(1208),(1209),(1210),(1211),(1212),(1213),(1214),(1215),(1216),(1217),(1218),(1219),(1220),(1221),(1222),(1223),(1224),(1225),(1226),(1227),(1228),(1229),(1230),(1231),(1232),(1233),(1234),(1235),(1236),(1237),(1238),(1239),(1240),(1241),(1242),(1243),(1244),(1245),(1246),(1247),(1248),(1249),(1250),(1251),(1252),(1253),(1254),(1255),(1256),(1257),(1258),(1259),(1260),(1261),(1262),(1263),(1264),(1265),(1266),(1267),(1268),(1269),(1270),(1271),(1272),(1273),(1274),(1275),(1276),(1277),(1278),(1279),(1280),(1281),(1282),(1283),(1284),(1285),(1286),(1287),(1288),(1289),(1290),(1291),(1292),(1293),(1294),(1295),(1296),(1297),(1298),(1299),(1300),(1301),(1302),(1303),(1304),(1305),(1306),(1307),(1308),(1309),(1310),(1311),(1312),(1313),(1314),(1315),(1316),(1317),(1318),(1319),(1320),(1321),(1322),(1323),(1324),(1325),(1326),(1327),(1328),(1329),(1330),(1331),(1332),(1333),(1334),(1335),(1336),(1337),(1338),(1339),(1340),(1341),(1342),(1343),(1344),(1345),(1346),(1347),(1348),(1349),(1350),(1351),(1352),(1353),(1354),(1355),(1356),(1357),(1358),(1359),(1360),(1361),(1362),(1363),(1364),(1365),(1366),(1367),(1368),(1369),(1370),(1371),(1372),(1373),(1374),(1375),(1376),(1377),(1378),(1379),(1380),(1381),(1382),(1383),(1384),(1385),(1386),(1387),(1388),(1389),(1390),(1391),(1392),(1393),(1394),(1395),(1396),(1397),(1398),(1399),(1400),(1401),(1402),(1403),(1404),(1405),(1406),(1407),(1408),(1409),(1410),(1411),(1412),(1413),(1414),(1415),(1416),(1417),(1418),(1419),(1420),(1421),(1422),(1423),(1424),(1425),(1426),(1427),(1428),(1429),(1430),(1431),(1432),(1433),(1434),(1435),(1436),(1437),(1438),(1439),(1440),(1441),(1442),(1443),(1444),(1445),(1446),(1447),(1448),(1449),(1450),(1451),(1452),(1453),(1454),(1455),(1456),(1457),(1458),(1459),(1460),(1461),(1462),(1463),(1464),(1465),(1466),(1467),(1468),(1469),(1470),(1471),(1472),(1473),(1474),(1475),(1476),(1477),(1478),(1479),(1480),(1481),(1482),(1483),(1484),(1485),(1486),(1487),(1488),(1489),(1490),(1491),(1492),(1493),(1494),(1495),(1496),(1497),(1498),(1499),(1500),(1501),(1502),(1503),(1504),(1505),(1506),(1507),(1508),(1509),(1510),(1511),(1512),(1513),(1514),(1515),(1516),(1517),(1518),(1519),(1520),(1521),(1522),(1523),(1524),(1525),(1526),(1527),(1528),(1529),(1530),(1531),(1532),(1533),(1534),(1535),(1536),(1537),(1538),(1539),(1540),(1541),(1542),(1543),(1544),(1545),(1546),(1547),(1548),(1549),(1550),(1551),(1552),(1553),(1554),(1555),(1556),(1557),(1558),(1559),(1560),(1561),(1562),(1563),(1564),(1565),(1566),(1567),(1568),(1569),(1570),(1571),(1572),(1573),(1574),(1575),(1576),(1577),(1578),(1579),(1580),(1581),(1582),(1583),(1584),(1585),(1586),(1587),(1588),(1589),(1590),(1591),(1592),(1593),(1594),(1595),(1596),(1597),(1598),(1599),(1600),(1601),(1602),(1603),(1604),(1605),(1606),(1607),(1608),(1609),(1610),(1611),(1612),(1613),(1614),(1615),(1616),(1617),(1618),(1619),(1620),(1621),(1622),(1623),(1624),(1625),(1626),(1627),(1628),(1629),(1630),(1631),(1632),(1633),(1634),(1635),(1636),(1637),(1638),(1639),(1640),(1641),(1642),(1643),(1644),(1645),(1646),(1647),(1648),(1649),(1650),(1651),(1652),(1653),(1654),(1655),(1656),(1657),(1658),(1659),(1660),(1661),(1662),(1663),(1664),(1665),(1666),(1667),(1668),(1669),(1670),(1671),(1672),(1673),(1674),(1675),(1676),(1677),(1678),(1679),(1680),(1681),(1682),(1683),(1684),(1685),(1686),(1687),(1688),(1689),(1690),(1691),(1692),(1693),(1694),(1695),(1696),(1697),(1698),(1699),(1700),(1701),(1702),(1703),(1704),(1705),(1706),(1707),(1708),(1709),(1710),(1711),(1712),(1713),(1714),(1715),(1716),(1717),(1718),(1719),(1720),(1721),(1722),(1723),(1724),(1725),(1726),(1727),(1728),(1729),(1730),(1731),(1732),(1733),(1734),(1735),(1736),(1737),(1738),(1739),(1740),(1741),(1742),(1743),(1744),(1745),(1746),(1747),(1748),(1749),(1750),(1751),(1752),(1753),(1754),(1755),(1756),(1757),(1758),(1759),(1760),(1761),(1762),(1763),(1764),(1765),(1766),(1767),(1768),(1769),(1770),(1771),(1772),(1773),(1774),(1775),(1776),(1777),(1778),(1779),(1780),(1781),(1782),(1783),(1784),(1785),(1786),(1787),(1788),(1789),(1790),(1791),(1792),(1793),(1794),(1795),(1796),(1797),(1798),(1799),(1800),(1801),(1802),(1803),(1804),(1805),(1806),(1807),(1808),(1809),(1810),(1811),(1812),(1813),(1814),(1815),(1816),(1817),(1818),(1819),(1820),(1821),(1822),(1823),(1824),(1825),(1826),(1827),(1828),(1829),(1830),(1831),(1832),(1833),(1834),(1835),(1836),(1837),(1838),(1839),(1840),(1841),(1842),(1843),(1844),(1845),(1846),(1847),(1848),(1849),(1850),(1851),(1852),(1853),(1854),(1855),(1856),(1857),(1858),(1859),(1860),(1861),(1862),(1863),(1864),(1865),(1866),(1867),(1868),(1869),(1870),(1871),(1872),(1873),(1874),(1875),(1876),(1877),(1878),(1879),(1880),(1881),(1882),(1883),(1884),(1885),(1886),(1887),(1888),(1889),(1890),(1891),(1892),(1893),(1894),(1895),(1896),(1897),(1898),(1899),(1900),(1901),(1902),(1903),(1904),(1905),(1906),(1907),(1908),(1909),(1910),(1911),(1912),(1913),(1914),(1915),(1916),(1917),(1918),(1919),(1920),(1921),(1922),(1923),(1924),(1925),(1926),(1927),(1928),(1929),(1930),(1931),(1932),(1933),(1934),(1935),(1936),(1937),(1938),(1939),(1940),(1941),(1942),(1943),(1944),(1945),(1946),(1947),(1948),(1949),(1950),(1951),(1952),(1953),(1954),(1955),(1956),(1957),(1958),(1959),(1960),(1961),(1962),(1963),(1964),(1965),(1966),(1967),(1968),(1969),(1970),(1971),(1972),(1973),(1974),(1975),(1976),(1977),(1978),(1979),(1980),(1981),(1982),(1983),(1984),(1985),(1986),(1987),(1988),(1989),(1990),(1991),(1992),(1993),(1994),(1995),(1996),(1997),(1998),(1999),(2000),(2001),(2002),(2003),(2004),(2005),(2006),(2007),(2008),(2009),(2010),(2011),(2012),(2013),(2014),(2015),(2016),(2017),(2018),(2019),(2020),(2021),(2022),(2023),(2024),(2025),(2026),(2027),(2028),(2029),(2030),(2031),(2032),(2033),(2034),(2035),(2036),(2037),(2038),(2039),(2040),(2041),(2042),(2043),(2044),(2045),(2046),(2047),(2048),(2049),(2050),(2051),(2052),(2053),(2054),(2055),(2056),(2057),(2058),(2059),(2060),(2061),(2062),(2063),(2064),(2065),(2066),(2067),(2068),(2069),(2070),(2071),(2072),(2073),(2074),(2075),(2076),(2077),(2078),(2079),(2080),(2081),(2082),(2083),(2084),(2085),(2086),(2087),(2088),(2089),(2090),(2091),(2092),(2093),(2094),(2095),(2096),(2097),(2098),(2099),(2100),(2101),(2102),(2103),(2104),(2105),(2106),(2107),(2108),(2109),(2110),(2111),(2112),(2113),(2114),(2115),(2116),(2117),(2118),(2119),(2120),(2121),(2122),(2123),(2124),(2125),(2126),(2127),(2128),(2129),(2130),(2131),(2132),(2133),(2134),(2135),(2136),(2137),(2138),(2139),(2140),(2141),(2142),(2143),(2144),(2145),(2146),(2147),(2148),(2149),(2150),(2151),(2152),(2153),(2154),(2155),(2156),(2157),(2158),(2159),(2160),(2161),(2162),(2163),(2164),(2165),(2166),(2167),(2168),(2169),(2170),(2171),(2172),(2173),(2174),(2175),(2176),(2177),(2178),(2179),(2180),(2181),(2182),(2183),(2184),(2185),(2186),(2187),(2188),(2189),(2190),(2191),(2192),(2193),(2194),(2195),(2196),(2197),(2198),(2199),(2200),(2201),(2202),(2203),(2204),(2205),(2206),(2207),(2208),(2209),(2210),(2211),(2212),(2213),(2214),(2215),(2216),(2217),(2218),(2219),(2220),(2221),(2222),(2223),(2224),(2225),(2226),(2227),(2228),(2229),(2230),(2231),(2232),(2233),(2234),(2235),(2236),(2237),(2238),(2239),(2240),(2241),(2242),(2243),(2244),(2245),(2246),(2247),(2248),(2249),(2250),(2251),(2252),(2253),(2254),(2255),(2256),(2257),(2258),(2259),(2260),(2261),(2262),(2263),(2264),(2265),(2266),(2267),(2268),(2269),(2270),(2271),(2272),(2273),(2274),(2275),(2276),(2277),(2278),(2279),(2280),(2281),(2282),(2283),(2284),(2285),(2286),(2287),(2288),(2289),(2290),(2291),(2292),(2293),(2294),(2295),(2296),(2297),(2298),(2299),(2300),(2301),(2302),(2303),(2304),(2305),(2306),(2307),(2308),(2309),(2310),(2311),(2312),(2313),(2314),(2315),(2316),(2317),(2318),(2319),(2320),(2321),(2322),(2323),(2324),(2325),(2326),(2327),(2328),(2329),(2330),(2331),(2332),(2333),(2334),(2335),(2336),(2337),(2338),(2339),(2340),(2341),(2342),(2343),(2344),(2345),(2346),(2347),(2348),(2349),(2350),(2351),(2352),(2353),(2354),(2355),(2356),(2357),(2358),(2359),(2360),(2361),(2362),(2363),(2364),(2365),(2366),(2367),(2368),(2369),(2370),(2371),(2372),(2373),(2374),(2375),(2376),(2377),(2378),(2379),(2380),(2381),(2382),(2383),(2384),(2385),(2386),(2387),(2388),(2389),(2390),(2391),(2392),(2393),(2394),(2395),(2396),(2397),(2398),(2399),(2400),(2401),(2402),(2403),(2404),(2405),(2406),(2407),(2408),(2409),(2410),(2411),(2412),(2413),(2414),(2415),(2416),(2417),(2418),(2419),(2420),(2421),(2422),(2423),(2424),(2425),(2426),(2427),(2428),(2429),(2430),(2431),(2432),(2433),(2434),(2435),(2436),(2437),(2438),(2439),(2440),(2441),(2442),(2443),(2444),(2445),(2446),(2447),(2448),(2449),(2450),(2451),(2452),(2453),(2454),(2455),(2456),(2457),(2458),(2459),(2460),(2461),(2462),(2463),(2464),(2465),(2466),(2467),(2468),(2469),(2470),(2471),(2472),(2473),(2474),(2475),(2476),(2477),(2478),(2479),(2480),(2481),(2482),(2483),(2484),(2485),(2486),(2487),(2488),(2489),(2490),(2491),(2492),(2493),(2494),(2495),(2496),(2497),(2498),(2499),(2500),(2501),(2502),(2503),(2504),(2505),(2506),(2507),(2508),(2509),(2510),(2511),(2512),(2513),(2514),(2515),(2516),(2517),(2518),(2519),(2520),(2521),(2522),(2523),(2524),(2525),(2526),(2527),(2528),(2529),(2530),(2531),(2532),(2533),(2534),(2535),(2536),(2537),(2538),(2539),(2540),(2541),(2542),(2543),(2544),(2545),(2546),(2547),(2548),(2549),(2550),(2551),(2552),(2553),(2554),(2555),(2556),(2557),(2558),(2559),(2560),(2561),(2562),(2563),(2564),(2565),(2566),(2567),(2568),(2569),(2570),(2571),(2572),(2573),(2574),(2575),(2576),(2577),(2578),(2579),(2580),(2581),(2582),(2583),(2584),(2585),(2586),(2587),(2588),(2589),(2590),(2591),(2592),(2593),(2594),(2595),(2596),(2597),(2598),(2599),(2600),(2601),(2602),(2603),(2604),(2605),(2606),(2607),(2608),(2609),(2610),(2611),(2612),(2613),(2614),(2615),(2616),(2617),(2618),(2619),(2620),(2621),(2622),(2623),(2624),(2625),(2626),(2627),(2628),(2629),(2630),(2631),(2632),(2633),(2634),(2635),(2636),(2637),(2638),(2639),(2640),(2641),(2642),(2643),(2644),(2645),(2646),(2647),(2648),(2649),(2650),(2651),(2652),(2653),(2654),(2655),(2656),(2657),(2658),(2659),(2660),(2661),(2662),(2663),(2664),(2665),(2666),(2667),(2668),(2669),(2670),(2671),(2672),(2673),(2674),(2675),(2676),(2677),(2678),(2679),(2680),(2681),(2682),(2683),(2684),(2685),(2686),(2687),(2688),(2689),(2690),(2691),(2692),(2693),(2694),(2695),(2696),(2697),(2698),(2699),(2700),(2701),(2702),(2703),(2704),(2705),(2706),(2707),(2708),(2709),(2710),(2711),(2712),(2713),(2714),(2715),(2716),(2717),(2718),(2719),(2720),(2721),(2722),(2723),(2724),(2725),(2726),(2727),(2728),(2729),(2730),(2731),(2732),(2733),(2734),(2735),(2736),(2737),(2738),(2739),(2740),(2741),(2742),(2743),(2744),(2745),(2746),(2747),(2748),(2749),(2750),(2751),(2752),(2753),(2754),(2755),(2756),(2757),(2758),(2759),(2760),(2761),(2762),(2763),(2764),(2765),(2766),(2767),(2768),(2769),(2770),(2771),(2772),(2773),(2774),(2775),(2776),(2777),(2778),(2779),(2780),(2781),(2782),(2783),(2784),(2785),(2786),(2787),(2788),(2789),(2790),(2791),(2792),(2793),(2794),(2795),(2796),(2797),(2798),(2799),(2800),(2801),(2802),(2803),(2804),(2805),(2806),(2807),(2808),(2809),(2810),(2811),(2812),(2813),(2814),(2815),(2816),(2817),(2818),(2819),(2820),(2821),(2822),(2823),(2824),(2825),(2826),(2827),(2828),(2829),(2830),(2831),(2832),(2833),(2834),(2835),(2836),(2837),(2838),(2839),(2840),(2841),(2842),(2843),(2844),(2845),(2846),(2847),(2848),(2849),(2850),(2851),(2852),(2853),(2854),(2855),(2856),(2857),(2858),(2859),(2860),(2861),(2862),(2863),(2864),(2865),(2866),(2867),(2868),(2869),(2870),(2871),(2872),(2873),(2874),(2875),(2876),(2877),(2878),(2879),(2880),(2881),(2882),(2883),(2884),(2885),(2886),(2887),(2888),(2889),(2890),(2891),(2892),(2893),(2894),(2895),(2896),(2897),(2898),(2899),(2900),(2901),(2902),(2903),(2904),(2905),(2906),(2907),(2908),(2909),(2910),(2911),(2912),(2913),(2914),(2915),(2916),(2917),(2918),(2919),(2920),(2921),(2922),(2923),(2924),(2925),(2926),(2927),(2928),(2929),(2930),(2931),(2932),(2933),(2934),(2935),(2936),(2937),(2938),(2939),(2940),(2941),(2942),(2943),(2944),(2945),(2946),(2947),(2948),(2949),(2950),(2951),(2952),(2953),(2954),(2955),(2956),(2957),(2958),(2959),(2960),(2961),(2962),(2963),(2964),(2965),(2966),(2967),(2968),(2969),(2970),(2971),(2972),(2973),(2974),(2975),(2976),(2977),(2978),(2979),(2980),(2981),(2982),(2983),(2984),(2985),(2986),(2987),(2988),(2989),(2990),(2991),(2992),(2993),(2994),(2995),(2996),(2997),(2998),(2999),(3000),(3001),(3002),(3003),(3004),(3005),(3006),(3007),(3008),(3009),(3010),(3011),(3012),(3013),(3014),(3015),(3016),(3017),(3018),(3019),(3020),(3021),(3022),(3023),(3024),(3025),(3026),(3027),(3028),(3029),(3030),(3031),(3032),(3033),(3034),(3035),(3036),(3037),(3038),(3039),(3040),(3041),(3042),(3043),(3044),(3045),(3046),(3047),(3048),(3049),(3050),(3051),(3052),(3053),(3054),(3055),(3056),(3057),(3058),(3059),(3060),(3061),(3062),(3063),(3064),(3065),(3066),(3067),(3068),(3069),(3070),(3071),(3072),(3073),(3074),(3075),(3076),(3077),(3078),(3079),(3080),(3081),(3082),(3083),(3084),(3085),(3086),(3087),(3088),(3089),(3090),(3091),(3092),(3093),(3094),(3095),(3096),(3097),(3098),(3099),(3100),(3101),(3102),(3103),(3104),(3105),(3106),(3107),(3108),(3109),(3110),(3111),(3112),(3113),(3114),(3115),(3116),(3117),(3118),(3119),(3120),(3121),(3122),(3123),(3124),(3125),(3126),(3127),(3128),(3129),(3130),(3131),(3132),(3133),(3134),(3135),(3136),(3137),(3138),(3139),(3140),(3141),(3142),(3143),(3144),(3145),(3146),(3147),(3148),(3149),(3150),(3151),(3152),(3153),(3154),(3155),(3156),(3157),(3158),(3159),(3160),(3161),(3162),(3163),(3164),(3165),(3166),(3167),(3168),(3169),(3170),(3171),(3172),(3173),(3174),(3175),(3176),(3177),(3178),(3179),(3180),(3181),(3182),(3183),(3184),(3185),(3186),(3187),(3188),(3189),(3190),(3191),(3192),(3193),(3194),(3195),(3196),(3197),(3198),(3199),(3200),(3201),(3202),(3203),(3204),(3205),(3206),(3207),(3208),(3209),(3210),(3211),(3212),(3213),(3214),(3215),(3216),(3217),(3218),(3219),(3220),(3221),(3222),(3223),(3224),(3225),(3226),(3227),(3228),(3229),(3230),(3231),(3232),(3233),(3234),(3235),(3236),(3237),(3238),(3239),(3240),(3241),(3242),(3243),(3244),(3245),(3246),(3247),(3248),(3249),(3250),(3251),(3252),(3253),(3254),(3255),(3256),(3257),(3258),(3259),(3260),(3261),(3262),(3263),(3264),(3265),(3266),(3267),(3268),(3269),(3270),(3271),(3272),(3273),(3274),(3275),(3276),(3277),(3278),(3279),(3280),(3281),(3282),(3283),(3284),(3285),(3286),(3287),(3288),(3289),(3290),(3291),(3292),(3293),(3294),(3295),(3296),(3297),(3298),(3299),(3300),(3301),(3302),(3303),(3304),(3305),(3306),(3307),(3308),(3309),(3310),(3311),(3312),(3313),(3314),(3315),(3316),(3317),(3318),(3319),(3320),(3321),(3322),(3323),(3324),(3325),(3326),(3327),(3328),(3329),(3330),(3331),(3332),(3333),(3334),(3335),(3336),(3337),(3338),(3339),(3340),(3341),(3342),(3343),(3344),(3345),(3346),(3347),(3348),(3349),(3350),(3351),(3352),(3353),(3354),(3355),(3356),(3357),(3358),(3359),(3360),(3361),(3362),(3363),(3364),(3365),(3366),(3367),(3368),(3369),(3370),(3371),(3372),(3373),(3374),(3375),(3376),(3377),(3378),(3379),(3380),(3381),(3382),(3383),(3384),(3385),(3386),(3387),(3388),(3389),(3390),(3391),(3392),(3393),(3394),(3395),(3396),(3397),(3398),(3399),(3400),(3401),(3402),(3403),(3404),(3405),(3406),(3407),(3408),(3409),(3410),(3411),(3412),(3413),(3414),(3415),(3416),(3417),(3418),(3419),(3420),(3421),(3422),(3423),(3424),(3425),(3426),(3427),(3428),(3429),(3430),(3431),(3432),(3433),(3434),(3435),(3436),(3437),(3438),(3439),(3440),(3441),(3442),(3443),(3444),(3445),(3446),(3447),(3448),(3449),(3450),(3451),(3452),(3453),(3454),(3455),(3456),(3457),(3458),(3459),(3460),(3461),(3462),(3463),(3464),(3465),(3466),(3467),(3468),(3469),(3470),(3471),(3472),(3473),(3474),(3475),(3476),(3477),(3478),(3479),(3480),(3481),(3482),(3483),(3484),(3485),(3486),(3487),(3488),(3489),(3490),(3491),(3492),(3493),(3494),(3495),(3496),(3497),(3498),(3499),(3500),(3501),(3502),(3503),(3504),(3505),(3506),(3507),(3508),(3509),(3510),(3511),(3512),(3513),(3514),(3515),(3516),(3517),(3518),(3519),(3520),(3521),(3522),(3523),(3524),(3525),(3526),(3527),(3528),(3529),(3530),(3531),(3532),(3533),(3534),(3535),(3536),(3537),(3538),(3539),(3540),(3541),(3542),(3543),(3544),(3545),(3546),(3547),(3548),(3549),(3550),(3551),(3552),(3553),(3554),(3555),(3556),(3557),(3558),(3559),(3560),(3561),(3562),(3563),(3564),(3565),(3566),(3567),(3568),(3569),(3570),(3571),(3572),(3573),(3574),(3575),(3576),(3577),(3578),(3579),(3580),(3581),(3582),(3583),(3584),(3585),(3586),(3587),(3588),(3589),(3590),(3591),(3592),(3593),(3594),(3595),(3596),(3597),(3598),(3599),(3600),(3601),(3602),(3603),(3604),(3605),(3606),(3607),(3608),(3609),(3610),(3611),(3612),(3613),(3614),(3615),(3616),(3617),(3618),(3619),(3620),(3621),(3622),(3623),(3624),(3625),(3626),(3627),(3628),(3629),(3630),(3631),(3632),(3633),(3634),(3635),(3636),(3637),(3638),(3639),(3640),(3641),(3642),(3643),(3644),(3645),(3646),(3647),(3648),(3649),(3650),(3651),(3652),(3653),(3654),(3655),(3656),(3657),(3658),(3659),(3660),(3661),(3662),(3663),(3664),(3665),(3666),(3667),(3668),(3669),(3670),(3671),(3672),(3673),(3674),(3675),(3676),(3677),(3678),(3679),(3680),(3681),(3682),(3683),(3684),(3685),(3686),(3687),(3688),(3689),(3690),(3691),(3692),(3693),(3694),(3695),(3696),(3697),(3698),(3699),(3700),(3701),(3702),(3703),(3704),(3705),(3706),(3707),(3708),(3709),(3710),(3711),(3712),(3713),(3714),(3715),(3716),(3717),(3718),(3719),(3720),(3721),(3722),(3723),(3724),(3725),(3726),(3727),(3728),(3729),(3730),(3731),(3732),(3733),(3734),(3735),(3736),(3737),(3738),(3739),(3740),(3741),(3742),(3743),(3744),(3745),(3746),(3747),(3748),(3749),(3750),(3751),(3752),(3753),(3754),(3755),(3756),(3757),(3758),(3759),(3760),(3761),(3762),(3763),(3764),(3765),(3766),(3767),(3768),(3769),(3770),(3771),(3772),(3773),(3774),(3775),(3776),(3777),(3778),(3779),(3780),(3781),(3782),(3783),(3784),(3785),(3786),(3787),(3788),(3789),(3790),(3791),(3792),(3793),(3794),(3795),(3796),(3797),(3798),(3799),(3800),(3801),(3802),(3803),(3804),(3805),(3806),(3807),(3808),(3809),(3810),(3811),(3812),(3813),(3814),(3815),(3816),(3817),(3818),(3819),(3820),(3821),(3822),(3823),(3824),(3825),(3826),(3827),(3828),(3829),(3830),(3831),(3832),(3833),(3834),(3835),(3836),(3837),(3838),(3839),(3840),(3841),(3842),(3843),(3844),(3845),(3846),(3847),(3848),(3849),(3850),(3851),(3852),(3853),(3854),(3855),(3856),(3857),(3858),(3859),(3860),(3861),(3862),(3863),(3864),(3865),(3866),(3867),(3868),(3869),(3870),(3871),(3872),(3873),(3874),(3875),(3876),(3877),(3878),(3879),(3880),(3881),(3882),(3883),(3884),(3885),(3886),(3887),(3888),(3889),(3890),(3891),(3892),(3893),(3894),(3895),(3896),(3897),(3898),(3899),(3900),(3901),(3902),(3903),(3904),(3905),(3906),(3907),(3908),(3909),(3910),(3911),(3912),(3913),(3914),(3915),(3916),(3917),(3918),(3919),(3920),(3921),(3922),(3923),(3924),(3925),(3926),(3927),(3928),(3929),(3930),(3931),(3932),(3933),(3934),(3935),(3936),(3937),(3938),(3939),(3940),(3941),(3942),(3943),(3944),(3945),(3946),(3947),(3948),(3949),(3950),(3951),(3952),(3953),(3954),(3955),(3956),(3957),(3958),(3959),(3960),(3961),(3962),(3963),(3964),(3965),(3966),(3967),(3968),(3969),(3970),(3971),(3972),(3973),(3974),(3975),(3976),(3977),(3978),(3979),(3980),(3981),(3982),(3983),(3984),(3985),(3986),(3987),(3988),(3989),(3990),(3991),(3992),(3993),(3994),(3995),(3996),(3997),(3998),(3999),(4000),(4001),(4002),(4003),(4004),(4005),(4006),(4007),(4008),(4009),(4010),(4011),(4012),(4013),(4014),(4015),(4016),(4017),(4018),(4019),(4020),(4021),(4022),(4023),(4024),(4025),(4026),(4027),(4028),(4029),(4030),(4031),(4032),(4033),(4034),(4035),(4036),(4037),(4038),(4039),(4040),(4041),(4042),(4043),(4044),(4045),(4046),(4047),(4048),(4049),(4050),(4051),(4052),(4053),(4054),(4055),(4056),(4057),(4058),(4059),(4060),(4061),(4062),(4063),(4064),(4065),(4066),(4067),(4068),(4069),(4070),(4071),(4072),(4073),(4074),(4075),(4076),(4077),(4078),(4079),(4080),(4081),(4082),(4083),(4084),(4085),(4086),(4087),(4088),(4089),(4090),(4091),(4092),(4093),(4094);
/*!40000 ALTER TABLE `VLANValidID` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VS` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VS` WRITE;
/*!40000 ALTER TABLE `VS` DISABLE KEYS */;
INSERT INTO `VS` VALUES (1,'service1',NULL,NULL);
/*!40000 ALTER TABLE `VS` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VSEnabledIPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VSEnabledIPs` (
  `object_id` int(10) unsigned NOT NULL,
  `vs_id` int(10) unsigned NOT NULL,
  `vip` varbinary(16) NOT NULL,
  `rspool_id` int(10) unsigned NOT NULL,
  `prio` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`object_id`,`vs_id`,`vip`,`rspool_id`),
  KEY `vip` (`vip`),
  KEY `VSEnabledIPs-FK-vs_id-vip` (`vs_id`,`vip`),
  KEY `VSEnabledIPs-FK-rspool_id` (`rspool_id`),
  CONSTRAINT `VSEnabledIPs-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledIPs-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledIPs-FK-vs_id-vip` FOREIGN KEY (`vs_id`, `vip`) REFERENCES `VSIPs` (`vs_id`, `vip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VSEnabledIPs` WRITE;
/*!40000 ALTER TABLE `VSEnabledIPs` DISABLE KEYS */;
INSERT INTO `VSEnabledIPs` VALUES (928,1,'\n\�9',1,'100',NULL,NULL),(928,1,'\n\�:',1,'200',NULL,NULL),(928,1,'�\0P\0\0\0\0\0\0\0\0\0\09',1,NULL,NULL,NULL),(929,1,'\n\�9',1,'200',NULL,NULL),(929,1,'\n\�:',1,'100',NULL,NULL),(929,1,'�\0P\0\0\0\0\0\0\0\0\0\09',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `VSEnabledIPs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VSEnabledPorts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VSEnabledPorts` (
  `object_id` int(10) unsigned NOT NULL,
  `vs_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP','MARK') COLLATE utf8_unicode_ci NOT NULL,
  `vport` int(10) unsigned NOT NULL,
  `rspool_id` int(10) unsigned NOT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`object_id`,`vs_id`,`proto`,`vport`,`rspool_id`),
  KEY `VSEnabledPorts-FK-vs_id-proto-vport` (`vs_id`,`proto`,`vport`),
  KEY `VSEnabledPorts-FK-rspool_id` (`rspool_id`),
  CONSTRAINT `VSEnabledPorts-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledPorts-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledPorts-FK-vs_id-proto-vport` FOREIGN KEY (`vs_id`, `proto`, `vport`) REFERENCES `VSPorts` (`vs_id`, `proto`, `vport`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VSEnabledPorts` WRITE;
/*!40000 ALTER TABLE `VSEnabledPorts` DISABLE KEYS */;
INSERT INTO `VSEnabledPorts` VALUES (928,1,'TCP',80,1,NULL,NULL),(928,1,'TCP',443,1,NULL,NULL),(929,1,'TCP',80,1,NULL,NULL),(929,1,'TCP',443,1,NULL,NULL);
/*!40000 ALTER TABLE `VSEnabledPorts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VSIPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VSIPs` (
  `vs_id` int(10) unsigned NOT NULL,
  `vip` varbinary(16) NOT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`vs_id`,`vip`),
  KEY `vip` (`vip`),
  CONSTRAINT `VSIPs-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VSIPs` WRITE;
/*!40000 ALTER TABLE `VSIPs` DISABLE KEYS */;
INSERT INTO `VSIPs` VALUES (1,'\n\�9',NULL,NULL),(1,'\n\�:',NULL,NULL),(1,'�\0P\0\0\0\0\0\0\0\0\0\09',NULL,NULL);
/*!40000 ALTER TABLE `VSIPs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `VSPorts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VSPorts` (
  `vs_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP','MARK') COLLATE utf8_unicode_ci NOT NULL,
  `vport` int(10) unsigned NOT NULL,
  `vsconfig` text COLLATE utf8_unicode_ci,
  `rsconfig` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`vs_id`,`proto`,`vport`),
  KEY `proto-vport` (`proto`,`vport`),
  CONSTRAINT `VS-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `VSPorts` WRITE;
/*!40000 ALTER TABLE `VSPorts` DISABLE KEYS */;
INSERT INTO `VSPorts` VALUES (1,'TCP',80,NULL,'CHECK_HTTP {\n  url /ping\n  status_code 200\n}'),(1,'TCP',443,NULL,'CHECK_SSL {\n url /ping\n status_code 200\n}');
/*!40000 ALTER TABLE `VSPorts` ENABLE KEYS */;
UNLOCK TABLES;
/*!50001 DROP VIEW IF EXISTS `Location`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`racktables`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `Location` AS select `O`.`id` AS `id`,`O`.`name` AS `name`,`O`.`has_problems` AS `has_problems`,`O`.`comment` AS `comment`,`P`.`id` AS `parent_id`,`P`.`name` AS `parent_name` from (`Object` `O` left join (`Object` `P` join `EntityLink` `EL` on(((`EL`.`parent_entity_id` = `P`.`id`) and (`P`.`objtype_id` = 1562) and (`EL`.`parent_entity_type` = 'location') and (`EL`.`child_entity_type` = 'location')))) on((`EL`.`child_entity_id` = `O`.`id`))) where (`O`.`objtype_id` = 1562) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `Rack`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`racktables`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `Rack` AS select `O`.`id` AS `id`,`O`.`name` AS `name`,`O`.`asset_no` AS `asset_no`,`O`.`has_problems` AS `has_problems`,`O`.`comment` AS `comment`,`AV_H`.`uint_value` AS `height`,`AV_S`.`uint_value` AS `sort_order`,`RT`.`thumb_data` AS `thumb_data`,`R`.`id` AS `row_id`,`R`.`name` AS `row_name`,`L`.`id` AS `location_id`,`L`.`name` AS `location_name` from (((((((`Object` `O` left join `AttributeValue` `AV_H` on(((`O`.`id` = `AV_H`.`object_id`) and (`AV_H`.`attr_id` = 27)))) left join `AttributeValue` `AV_S` on(((`O`.`id` = `AV_S`.`object_id`) and (`AV_S`.`attr_id` = 29)))) left join `RackThumbnail` `RT` on((`O`.`id` = `RT`.`rack_id`))) left join `EntityLink` `RL` on(((`O`.`id` = `RL`.`child_entity_id`) and (`RL`.`parent_entity_type` = 'row') and (`RL`.`child_entity_type` = 'rack')))) join `Object` `R` on((`R`.`id` = `RL`.`parent_entity_id`))) left join `EntityLink` `LL` on(((`R`.`id` = `LL`.`child_entity_id`) and (`LL`.`parent_entity_type` = 'location') and (`LL`.`child_entity_type` = 'row')))) left join `Object` `L` on((`L`.`id` = `LL`.`parent_entity_id`))) where (`O`.`objtype_id` = 1560) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `RackObject`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`racktables`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `RackObject` AS select `Object`.`id` AS `id`,`Object`.`name` AS `name`,`Object`.`label` AS `label`,`Object`.`objtype_id` AS `objtype_id`,`Object`.`asset_no` AS `asset_no`,`Object`.`has_problems` AS `has_problems`,`Object`.`comment` AS `comment` from `Object` where (`Object`.`objtype_id` not in (1560,1561,1562)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `Row`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`racktables`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `Row` AS select `O`.`id` AS `id`,`O`.`name` AS `name`,`L`.`id` AS `location_id`,`L`.`name` AS `location_name` from ((`Object` `O` left join `EntityLink` `EL` on(((`O`.`id` = `EL`.`child_entity_id`) and (`EL`.`parent_entity_type` = 'location') and (`EL`.`child_entity_type` = 'row')))) left join `Object` `L` on(((`EL`.`parent_entity_id` = `L`.`id`) and (`L`.`objtype_id` = 1562)))) where (`O`.`objtype_id` = 1561) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

