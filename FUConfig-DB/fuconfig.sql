-- MySQL dump 10.13  Distrib 5.7.25, for Linux (x86_64)
--
-- Host: localhost    Database: fuconfig
-- ------------------------------------------------------
-- Server version	5.7.25-0ubuntu0.18.04.2-log

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
-- Table structure for table `directories`
--

DROP TABLE IF EXISTS `directories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `directories` (
  `directory_id` int(11) NOT NULL AUTO_INCREMENT,
  `directory_name` varchar(200) NOT NULL,
  `directory_filename` varchar(200) DEFAULT NULL,
  `default` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`directory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COMMENT='			';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `directories`
--

LOCK TABLES `directories` WRITE;
/*!40000 ALTER TABLE `directories` DISABLE KEYS */;
INSERT INTO `directories` VALUES (1,'Unlisted',NULL,0),(2,'Theme Camp Directory','campdirectory.xml',1),(3,'FUT Corporate','FUTcorpdirectory.xml',0),(4,'Volunteer Departments','departmentdirectory.xml',0),(5,'Art Cars','artcarsdirectory.xml',0),(6,'Pay Phones','payphonedirectory.xml',0),(7,'Public Address','PAdirectory.xml',0),(8,'Ranger','rangersdirectory.xml',0);
/*!40000 ALTER TABLE `directories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `number_types`
--

DROP TABLE IF EXISTS `number_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `number_types` (
  `number_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `number_type_name` varchar(45) NOT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  `number_type_system_name` varchar(45) NOT NULL,
  `exclude_from_delete` tinyint(4) NOT NULL DEFAULT '0',
  `number_type_app_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`number_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `number_types`
--

LOCK TABLES `number_types` WRITE;
/*!40000 ALTER TABLE `number_types` DISABLE KEYS */;
INSERT INTO `number_types` VALUES (1,'Line',1,'line',0,'SCCP'),(2,'Speed Dial',0,'speeddial',0,'SCCP'),(3,'System Custom',0,'custom',1,'SCCP'),(4,'Sip',0,'sip',0,'SIP'),(5,'Random',0,'speeddial',0,'SCCP');
/*!40000 ALTER TABLE `number_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `numbers`
--

DROP TABLE IF EXISTS `numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `numbers` (
  `number_id` int(11) NOT NULL AUTO_INCREMENT,
  `callerid` varchar(255) NOT NULL,
  `number` varchar(45) NOT NULL,
  `directory_id` int(11) NOT NULL,
  `number_type_id` int(11) NOT NULL,
  `todelete_number` tinyint(4) NOT NULL DEFAULT '0',
  `altered_number` tinyint(4) NOT NULL DEFAULT '0',
  `added_number` tinyint(4) NOT NULL DEFAULT '0',
  `sccpline_id` varchar(45) DEFAULT NULL,
  `sip_user` varchar(45) DEFAULT NULL,
  `sip_pass` varchar(45) DEFAULT NULL,
  `sippeer_id` varchar(45) DEFAULT NULL,
  `password_index` int(11) DEFAULT '1',
  PRIMARY KEY (`number_id`),
  KEY `numbers_to_directory_fk_idx` (`directory_id`),
  KEY `numbers_to_number_types_fk_idx` (`number_type_id`),
  CONSTRAINT `numbers_to_directory_fk` FOREIGN KEY (`directory_id`) REFERENCES `directories` (`directory_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `numbers_to_number_types_fk` FOREIGN KEY (`number_type_id`) REFERENCES `number_types` (`number_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `numbers`
--

LOCK TABLES `numbers` WRITE;
/*!40000 ALTER TABLE `numbers` DISABLE KEYS */;
INSERT INTO `numbers` VALUES (34,'Random','726366',2,5,0,1,1,NULL,NULL,NULL,NULL,1),(98,'SCCPTEST','5454',2,1,0,0,0,'5454',NULL,NULL,NULL,1),(99,'12','12',2,4,0,0,0,NULL,'899a6','899a6','232',1),(100,'14','14',2,4,0,0,0,NULL,'899a61','899a61','233',2),(101,'35','35',2,1,0,0,0,'35','','',NULL,1),(102,'36','36',2,1,0,0,0,'36','','',NULL,1),(103,'Sales','1',2,1,0,0,0,'1','','',NULL,1),(104,'V022','1022',2,1,0,0,0,'1022',NULL,NULL,NULL,1),(105,'V029-Change','1030',2,1,0,0,0,'1030',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orgs`
--

DROP TABLE IF EXISTS `orgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orgs` (
  `org_id` int(11) NOT NULL AUTO_INCREMENT,
  `org_name` varchar(255) DEFAULT NULL,
  `org_contactname` varchar(255) DEFAULT NULL,
  `org_contactemail` varchar(255) DEFAULT NULL,
  `org_contactphone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orgs`
--

LOCK TABLES `orgs` WRITE;
/*!40000 ALTER TABLE `orgs` DISABLE KEYS */;
INSERT INTO `orgs` VALUES (13,'Test Camp','','',''),(35,'Test Camp 2','','',''),(36,'Test Camp 3','','','');
/*!40000 ALTER TABLE `orgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_data`
--

DROP TABLE IF EXISTS `phone_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_data` (
  `phone_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_id` int(11) NOT NULL,
  `phone_data_name` varchar(255) DEFAULT NULL,
  `phone_data_contents` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`phone_data_id`),
  KEY `phones_to_phone_data_fk_idx` (`phone_id`),
  CONSTRAINT `phones_to_phone_data_fk` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`phone_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_data`
--

LOCK TABLES `phone_data` WRITE;
/*!40000 ALTER TABLE `phone_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_inventory`
--

DROP TABLE IF EXISTS `phone_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_inventory` (
  `phone_inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_inventory_tag` varchar(45) NOT NULL,
  `phone_inventory_serial` varchar(15) NOT NULL,
  `phone_inventory_type_id` int(11) DEFAULT NULL,
  `phone_inventory_model_id` int(11) DEFAULT NULL,
  `phone_inventory_available` tinyint(4) NOT NULL DEFAULT '1',
  `sip_username1` varchar(45) DEFAULT NULL,
  `sip_password1` varchar(45) DEFAULT NULL,
  `sip_username2` varchar(45) DEFAULT NULL,
  `sip_password2` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`phone_inventory_id`),
  UNIQUE KEY `phone_inventory_tag_UNIQUE` (`phone_inventory_tag`),
  KEY `phone_inventory_type_fk_idx` (`phone_inventory_type_id`),
  KEY `phone_inventory_to_phone_model_fk_idx` (`phone_inventory_model_id`),
  KEY `phone_inventory_tag_INDEX` (`phone_inventory_tag`),
  CONSTRAINT `phone_inventory_to_phone_model_fk` FOREIGN KEY (`phone_inventory_model_id`) REFERENCES `phone_models` (`phone_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phone_inventory_type_fk` FOREIGN KEY (`phone_inventory_type_id`) REFERENCES `phone_types` (`phone_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_inventory`
--

LOCK TABLES `phone_inventory` WRITE;
/*!40000 ALTER TABLE `phone_inventory` DISABLE KEYS */;
INSERT INTO `phone_inventory` VALUES (2,'V002','SEP0011BB0DE4B3',1,1,1,NULL,NULL,NULL,NULL),(4,'V003','SEP000DED0881A3',1,1,1,NULL,NULL,NULL,NULL),(5,'V004','SEP000F8F59C5D3',1,1,1,NULL,NULL,NULL,NULL),(6,'V005','SEP001E7A242E6C',1,1,1,NULL,NULL,NULL,NULL),(7,'V008','SEP000BBE7C9576',1,1,1,NULL,NULL,NULL,NULL),(8,'V034','SEP0011BB0DDBC3',1,1,1,NULL,NULL,NULL,NULL),(9,'V013','SEP000DED22F2FF',1,1,1,NULL,NULL,NULL,NULL),(10,'V010','SEP0014A8D486EF',1,1,1,NULL,NULL,NULL,NULL),(11,'V011','SEP0013C4FBBFA6',1,1,1,NULL,NULL,NULL,NULL),(12,'V012','SEP0014F2FAFD04',1,1,1,NULL,NULL,NULL,NULL),(14,'V014','SEP000F8F59C5D3',1,1,1,NULL,NULL,NULL,NULL),(15,'V015','SEP011BB0DE0F8',1,1,1,NULL,NULL,NULL,NULL),(16,'V022','SEP0015626A523F',1,1,1,NULL,NULL,NULL,NULL),(17,'V023','SEP00156247F803',1,1,1,NULL,NULL,NULL,NULL),(18,'V024','SEP001562869F5B',1,1,1,NULL,NULL,NULL,NULL),(19,'V025','SEP0015F914028C',1,1,1,NULL,NULL,NULL,NULL),(20,'V026','SEP001646806BB8',1,1,1,NULL,NULL,NULL,NULL),(21,'V027','SEP0016474B01A9',1,1,1,NULL,NULL,NULL,NULL),(22,'V028','SEP0015F914025B',1,1,1,NULL,NULL,NULL,NULL),(23,'V029','SEP0015F915297A',1,1,1,NULL,NULL,NULL,NULL),(24,'V031','SEP0011BB0DE105',1,1,1,NULL,NULL,NULL,NULL),(25,'V032','SEP0013C4E08A2B',1,1,1,NULL,NULL,NULL,NULL),(26,'V033','SEP0011BB0DE195',1,1,1,NULL,NULL,NULL,NULL),(27,'V035','SEP0013C4D1790C',1,1,1,NULL,NULL,NULL,NULL),(28,'V036','SEP00156228A41C',1,1,1,NULL,NULL,NULL,NULL),(29,'V037','SEP00156286AA23',1,1,1,NULL,NULL,NULL,NULL),(30,'V039','SEP0014A90FFFF8',1,1,1,NULL,NULL,NULL,NULL),(31,'V040','SEP001562869F9E',1,1,1,NULL,NULL,NULL,NULL),(32,'V030','SEPD0574CF79C98',1,6,1,'','','',''),(33,'V041','SEP0015F9B3928E',1,2,0,NULL,NULL,NULL,NULL),(34,'V042','SEP0016C8C3CCFE',1,2,0,NULL,NULL,NULL,NULL),(35,'V043','SEP0016C8C3CE4A',1,2,0,NULL,NULL,NULL,NULL),(36,'V044','SEP0016464BB150',1,2,0,NULL,NULL,NULL,NULL),(37,'V045','SEP001646776B5D',1,2,0,NULL,NULL,NULL,NULL),(38,'V046','SEP0011BB0DE191',1,1,1,NULL,NULL,NULL,NULL),(39,'V047','SEP001562695B25',1,1,1,NULL,NULL,NULL,NULL),(40,'V048','SEP000DBCCC6C63',1,1,1,NULL,NULL,NULL,NULL),(41,'V049','SEP0015629F897C',1,1,1,NULL,NULL,NULL,NULL),(42,'V050','SEP0011BB0DE47A',1,1,1,NULL,NULL,NULL,NULL),(43,'V051','SEP00156286A007',1,1,1,NULL,NULL,NULL,NULL),(44,'V052','SEP0013C4FBBF9E',1,1,1,NULL,NULL,NULL,NULL),(45,'V053','SEP0011BB0DE10B',1,1,1,NULL,NULL,NULL,NULL),(46,'V054','SEP0015626A4FCF',1,1,1,NULL,NULL,NULL,NULL),(47,'V055','SEP0015626A569D',1,1,1,NULL,NULL,NULL,NULL),(48,'V056','SEP00131AE5C78F',1,1,1,NULL,NULL,NULL,NULL),(49,'V057','SEP0013C4FBBB1E',1,1,1,NULL,NULL,NULL,NULL),(50,'V058','SEP00156286B037',1,1,1,NULL,NULL,NULL,NULL),(51,'V059','SEP0015626A51FD',1,1,1,NULL,NULL,NULL,NULL),(52,'V060','SEP0015626A5531',1,1,1,NULL,NULL,NULL,NULL),(53,'V061','SEP0011BB0DE246',1,1,1,NULL,NULL,NULL,NULL),(54,'V062','SEP0011BB0DE1D8',1,1,1,NULL,NULL,NULL,NULL),(55,'V063','SEP001562767D9D',1,1,1,NULL,NULL,NULL,NULL),(56,'V006','SEP0016C8C3C9BB',1,2,1,NULL,NULL,NULL,NULL),(57,'V007','SEP001562788A1C',1,1,1,NULL,NULL,NULL,NULL),(58,'V009','SEP000DED47D711',1,1,1,NULL,NULL,NULL,NULL),(60,'v064','SEP0011BB0DE0F8',1,1,1,NULL,NULL,NULL,NULL),(73,'test','test',2,4,1,NULL,NULL,NULL,NULL),(74,'ATA-15','000F9F18EBFC',2,5,1,'8ebfc','8ebfc','8ebfc1','8ebfc1'),(75,'ATA-08','000F9F1899A6',2,5,1,'899a6','899a6','899a61','899a61'),(76,'V001','SEPD0574CF794A2',1,3,1,'','','','');
/*!40000 ALTER TABLE `phone_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_models`
--

DROP TABLE IF EXISTS `phone_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_models` (
  `phone_model_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_model_name` varchar(45) NOT NULL,
  `phone_model_max_numbers` int(11) NOT NULL DEFAULT '1',
  `phone_model_type_id` int(11) NOT NULL,
  `xml_config_filename` varchar(200) DEFAULT 'SEPdefault.cnf.xml',
  `phone_model_system_name` varchar(45) DEFAULT NULL,
  `add_button_list` tinyint(4) DEFAULT '0',
  `button_list_max` int(11) DEFAULT '28',
  `page_size` int(11) DEFAULT '12',
  PRIMARY KEY (`phone_model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_models`
--

LOCK TABLES `phone_models` WRITE;
/*!40000 ALTER TABLE `phone_models` DISABLE KEYS */;
INSERT INTO `phone_models` VALUES (1,'7940',2,1,'SEPdefault.cnf.xml','7940',0,28,12),(2,'7960',6,1,'SEPdefault.cnf.xml','7960',0,28,12),(3,'7965+Addon(48)',6,1,'cisco7965default.cnf.xml','7965',1,48,12),(4,'SIP Softphone',1,2,'',NULL,0,28,12),(5,'SIP 2 Line ATA',2,2,'',NULL,0,28,12),(6,'7965/6',6,1,'cisco7965default.cnf.xml','7965',0,28,12);
/*!40000 ALTER TABLE `phone_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_number_assignment`
--

DROP TABLE IF EXISTS `phone_number_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_number_assignment` (
  `phone_number_assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `number_id` int(11) DEFAULT NULL,
  `phone_id` int(11) DEFAULT NULL,
  `number_type_id` int(11) DEFAULT '1',
  `buttonconfig_id` int(11) DEFAULT NULL,
  `todelete_assignment` tinyint(4) NOT NULL DEFAULT '0',
  `added_assignment` tinyint(4) NOT NULL DEFAULT '0',
  `password_index` int(11) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`phone_number_assignment_id`),
  UNIQUE KEY `phone_number_assignment_phone_number_pair` (`number_id`,`phone_id`),
  KEY `phone_number_assignment_phones_fk_idx` (`phone_id`),
  KEY `phone_number_assignment_number_fk_idx` (`number_id`),
  CONSTRAINT `phone_number_assignment_number_fk` FOREIGN KEY (`number_id`) REFERENCES `numbers` (`number_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phone_number_assignment_phones_fk` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`phone_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_number_assignment`
--

LOCK TABLES `phone_number_assignment` WRITE;
/*!40000 ALTER TABLE `phone_number_assignment` DISABLE KEYS */;
INSERT INTO `phone_number_assignment` VALUES (177,98,63,1,NULL,0,1,1,1),(178,34,63,5,NULL,0,1,1,2),(179,99,64,4,NULL,0,1,1,1),(180,100,64,4,NULL,0,1,1,1),(181,101,65,1,NULL,0,1,1,1),(182,102,65,1,NULL,0,1,1,2),(183,98,65,2,NULL,0,1,1,3),(184,103,66,1,NULL,0,1,1,1),(185,104,67,1,NULL,0,1,1,1),(186,34,67,5,NULL,0,1,1,2),(187,105,68,1,NULL,0,1,1,1),(188,34,68,5,NULL,0,1,1,2);
/*!40000 ALTER TABLE `phone_number_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_types`
--

DROP TABLE IF EXISTS `phone_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_types` (
  `phone_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_type_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`phone_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_types`
--

LOCK TABLES `phone_types` WRITE;
/*!40000 ALTER TABLE `phone_types` DISABLE KEYS */;
INSERT INTO `phone_types` VALUES (1,'SCCP'),(2,'SIP');
/*!40000 ALTER TABLE `phone_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phones`
--

DROP TABLE IF EXISTS `phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phones` (
  `phone_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_type_id` int(11) NOT NULL,
  `phone_model_id` int(11) NOT NULL,
  `phone_org_id` int(11) DEFAULT NULL,
  `phone_primary_number_id` int(11) DEFAULT NULL,
  `phone_is_inventory` tinyint(4) NOT NULL DEFAULT '0',
  `phone_inventory_id` int(11) DEFAULT NULL,
  `phone_serial` varchar(45) NOT NULL,
  `phone_is_deployed` tinyint(4) NOT NULL DEFAULT '0',
  `altered` tinyint(4) NOT NULL DEFAULT '0',
  `todelete_phone` tinyint(4) NOT NULL DEFAULT '0',
  `added` tinyint(4) NOT NULL DEFAULT '0',
  `errored` tinyint(4) NOT NULL DEFAULT '0',
  `sccpdevice_id` int(11) DEFAULT NULL,
  `sip_username1` varchar(45) DEFAULT NULL,
  `sip_password1` varchar(45) DEFAULT NULL,
  `sip_username2` varchar(45) DEFAULT NULL,
  `sip_password2` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`phone_id`),
  UNIQUE KEY `phone_serial_UNIQUE` (`phone_serial`),
  KEY `phones_phone_types_fk_idx` (`phone_type_id`),
  KEY `phones_orges_fk_idx` (`phone_org_id`),
  KEY `phones_to_numbers_fk_idx` (`phone_primary_number_id`),
  KEY `phones_to_phone_inventory_fk_idx` (`phone_inventory_id`),
  KEY `phones_to_phone_models_fk_idx` (`phone_model_id`),
  CONSTRAINT `phones_orges_fk` FOREIGN KEY (`phone_org_id`) REFERENCES `orgs` (`org_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phones_to_numbers_fk` FOREIGN KEY (`phone_primary_number_id`) REFERENCES `numbers` (`number_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phones_to_phone_inventory_fk` FOREIGN KEY (`phone_inventory_id`) REFERENCES `phone_inventory` (`phone_inventory_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phones_to_phone_models_fk` FOREIGN KEY (`phone_model_id`) REFERENCES `phone_models` (`phone_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phones_to_phone_type_fk` FOREIGN KEY (`phone_type_id`) REFERENCES `phone_types` (`phone_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phones`
--

LOCK TABLES `phones` WRITE;
/*!40000 ALTER TABLE `phones` DISABLE KEYS */;
INSERT INTO `phones` VALUES (63,1,1,13,NULL,1,11,'SEP0013C4FBBFA6',0,0,0,0,0,655,NULL,NULL,NULL,NULL),(64,2,5,13,NULL,1,75,'000F9F1899A6',0,0,0,0,0,NULL,'899a6','899a6','899a61','899a61'),(65,1,6,35,NULL,1,32,'SEPD0574CF79C98',0,0,0,0,0,656,'','','',''),(66,1,3,35,NULL,1,76,'SEPD0574CF794A2',0,0,0,0,0,657,'','','',''),(67,1,1,36,NULL,1,16,'SEP0015626A523F',0,0,0,0,0,658,NULL,NULL,NULL,NULL),(68,1,1,36,NULL,1,23,'SEP0015F915297A',0,0,0,0,0,659,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `phones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routers`
--

DROP TABLE IF EXISTS `routers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routers` (
  `router_id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `version` int(11) NOT NULL DEFAULT '1',
  `channel_24` int(11) NOT NULL DEFAULT '1',
  `channel_5` int(11) NOT NULL DEFAULT '36',
  `org_id` int(11) DEFAULT NULL,
  `router_is_deployed` int(11) NOT NULL DEFAULT '0',
  `channel_24_current` int(11) DEFAULT NULL,
  `channel_5_current` int(11) DEFAULT NULL,
  `available` int(4) NOT NULL DEFAULT '1',
  `enclosed` varchar(200) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`router_id`),
  UNIQUE KEY `number_unique` (`number`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routers`
--

LOCK TABLES `routers` WRITE;
/*!40000 ALTER TABLE `routers` DISABLE KEYS */;
INSERT INTO `routers` VALUES (1,50,1,6,149,NULL,0,6,149,1,'',''),(2,1,1,6,44,NULL,0,6,44,0,'No','home router'),(3,2,1,6,149,13,0,NULL,NULL,1,'No',''),(4,3,1,11,44,NULL,0,NULL,NULL,1,'Payphone','ATA23'),(5,4,1,1,36,36,0,1,36,1,'No','Internet gateway, no cilent SSID'),(6,6,1,1,36,NULL,0,NULL,NULL,1,'Payphone - Rotary','ATA17 and PA'),(7,7,1,11,44,NULL,0,NULL,NULL,1,'No',NULL),(8,9,1,1,157,NULL,0,NULL,NULL,1,'Yes',NULL),(9,10,1,6,149,NULL,0,NULL,NULL,1,'No',NULL),(10,11,1,6,44,NULL,0,NULL,NULL,1,'Yes','Tim Keller?'),(11,12,1,6,44,NULL,0,NULL,NULL,1,'DTMF control box','DTMF Control board, Effigy'),(12,13,1,11,157,NULL,0,NULL,NULL,1,'No',NULL),(13,14,1,1,36,36,0,1,36,1,'No',''),(14,16,1,1,149,NULL,0,NULL,NULL,1,'No','Tim Keller?'),(15,17,1,11,157,NULL,0,NULL,NULL,1,'No','bad usb port'),(16,18,1,1,36,NULL,0,NULL,NULL,1,'No',NULL),(17,19,1,1,157,NULL,0,NULL,NULL,1,'No',NULL),(18,21,1,11,157,NULL,0,NULL,NULL,1,'POE',NULL),(19,22,1,6,44,NULL,0,NULL,NULL,1,'No',NULL),(20,23,1,6,157,NULL,0,NULL,NULL,1,'Yes',NULL),(21,24,1,1,149,NULL,0,NULL,NULL,1,'Yes',NULL),(22,25,1,6,149,NULL,0,NULL,NULL,1,'Yes',NULL),(23,26,1,1,157,NULL,0,NULL,NULL,1,'No',NULL),(24,27,1,11,44,NULL,0,NULL,NULL,1,'POE',NULL),(25,28,1,6,149,NULL,0,NULL,NULL,1,'No',NULL),(26,29,1,6,44,NULL,0,NULL,NULL,1,'POE',NULL),(27,30,1,11,44,NULL,0,NULL,NULL,1,'No',NULL),(28,31,1,1,36,NULL,0,NULL,NULL,1,'POE',NULL),(29,32,1,1,44,NULL,0,NULL,NULL,1,'Yes',NULL),(46,33,1,1,149,NULL,0,NULL,NULL,1,'POE',NULL),(47,34,1,1,36,NULL,0,NULL,NULL,0,'No','Config issues, formerly PA'),(48,35,1,1,36,NULL,0,NULL,NULL,1,'Payphone','ATA29 and PA (baresip autostart)'),(49,36,1,6,36,NULL,0,NULL,NULL,1,'Payphone','ATA25 (baresip autostart BAD)'),(50,37,1,6,157,NULL,0,NULL,NULL,1,'POE - short pole',NULL),(51,38,1,1,157,NULL,0,NULL,NULL,1,'Yes',NULL),(52,39,1,6,157,NULL,0,NULL,NULL,1,'Yes',NULL),(53,46,1,11,44,NULL,0,NULL,NULL,1,'POE unenclosed',NULL),(67,45,1,1,157,NULL,0,NULL,NULL,1,'Payphone','ATA24'),(68,47,1,11,44,NULL,0,NULL,NULL,1,'No','Tim Keller?'),(69,48,1,6,44,NULL,0,NULL,NULL,1,'No','Tim Keller?'),(70,49,1,6,44,NULL,0,NULL,NULL,1,'No',NULL),(71,60,1,1,157,NULL,0,NULL,NULL,1,'No',NULL),(72,51,1,1,36,NULL,0,NULL,NULL,1,'No',NULL),(73,52,1,6,149,NULL,0,NULL,NULL,1,'No',NULL),(74,53,1,6,149,NULL,0,NULL,NULL,1,'No',NULL),(75,54,1,1,36,35,0,NULL,NULL,1,'No',''),(76,55,1,6,149,NULL,0,NULL,NULL,1,'No',NULL),(77,57,1,6,44,NULL,0,NULL,NULL,1,'No','Tim Keller?'),(78,58,1,1,149,NULL,0,NULL,NULL,0,'No','need downgrade'),(79,59,1,1,149,NULL,0,NULL,NULL,0,'No','need downgrade'),(80,61,1,1,36,NULL,0,NULL,NULL,1,'Short Pole - 12V battery',NULL),(81,62,1,1,44,NULL,0,NULL,NULL,1,'No',NULL),(82,63,1,6,36,NULL,0,NULL,NULL,1,'Payphone','ATA20'),(83,64,1,6,157,NULL,0,NULL,NULL,1,'No',NULL),(84,65,1,6,157,NULL,0,NULL,NULL,1,'No',NULL),(85,66,1,6,157,NULL,0,NULL,NULL,1,'No',NULL),(86,67,1,6,36,NULL,0,NULL,NULL,1,'Tall Pole - 12V battery',NULL),(87,68,1,6,44,NULL,0,NULL,NULL,1,'No',NULL),(88,69,1,6,44,NULL,0,NULL,NULL,1,'No',NULL),(89,70,1,1,36,NULL,0,NULL,NULL,1,'No',NULL);
/*!40000 ALTER TABLE `routers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-07  6:00:49
