-- MySQL dump 10.11
--
-- Host: localhost    Database: pub_main
-- ------------------------------------------------------
-- Server version	5.1.26-rc

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
-- Table structure for table `proj`
--

DROP TABLE IF EXISTS `proj`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `proj` (
  `p_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `u_id` int(10) unsigned NOT NULL DEFAULT '0',
  `p_cname` varchar(128) NOT NULL DEFAULT '',
  `p_ename` varchar(128) NOT NULL DEFAULT '',
  `db_server` varchar(128) DEFAULT NULL,
  `db_name` varchar(128) DEFAULT NULL,
  `db_user` varchar(128) DEFAULT NULL,
  `db_pwd` varchar(128) DEFAULT NULL,
  `db_port` int(10) NOT NULL DEFAULT '0',
  `db_sock` varchar(128) DEFAULT NULL,
  `db_default` int(10) NOT NULL DEFAULT '0',
  `docu_flag` int(10) NOT NULL DEFAULT '0',
  `note` text,
  `ptype` int(10) NOT NULL DEFAULT '0',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `validation` int(10) NOT NULL DEFAULT '0',
  `createdt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `p_cname` (`p_cname`),
  KEY `i_uid` (`u_id`),
  KEY `i_ptype` (`ptype`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `proj`
--

LOCK TABLES `proj` WRITE;
/*!40000 ALTER TABLE `proj` DISABLE KEYS */;
INSERT INTO `proj` VALUES (1,1,'比华利美容在线','',NULL,'web_1_1',NULL,NULL,0,NULL,1,0,NULL,0,'http://223.4.180.41/',0,'2012-08-01 11:38:52','2012-08-01 12:54:00'),(2,1,'北青汽车网','',NULL,'web_2_1',NULL,NULL,0,NULL,1,0,NULL,0,'http://sample11380008632.ghc.net/',0,'2013-09-24 15:44:34','2013-10-10 09:41:16');
/*!40000 ALTER TABLE `proj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pub_queue`
--

DROP TABLE IF EXISTS `pub_queue`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pub_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `t_id` int(10) unsigned NOT NULL DEFAULT '0',
  `d_id` int(10) unsigned NOT NULL DEFAULT '0',
  `createdt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pub` (`p_id`,`t_id`,`d_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pub_queue`
--

LOCK TABLES `pub_queue` WRITE;
/*!40000 ALTER TABLE `pub_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `pub_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL DEFAULT '',
  `login` varchar(30) NOT NULL DEFAULT '',
  `passwd` varchar(100) DEFAULT NULL,
  `type` int(10) NOT NULL DEFAULT '4',
  `allproj` int(10) NOT NULL DEFAULT '0',
  `priv` blob NOT NULL,
  `note` blob NOT NULL,
  `linkman` varchar(255) NOT NULL DEFAULT '',
  `gender` enum('m','w','u') NOT NULL DEFAULT 'u',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `fax` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `zip` varchar(255) NOT NULL DEFAULT '',
  `f_times` int(10) unsigned DEFAULT '0',
  `s_times` int(10) unsigned DEFAULT '0',
  `lastlogin` datetime DEFAULT NULL,
  `c_id` int(10) unsigned NOT NULL DEFAULT '0',
  `m_id` int(10) unsigned NOT NULL DEFAULT '0',
  `createdt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userID_2` (`login`),
  KEY `userID` (`login`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Administrator','admin','pub54321',0,0,'','','','u','','','','','','',12,134,'2013-10-17 20:26:43',0,0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'北青网开发','bqauto','123456',2,0,'','','','u','','','','','','',0,9,'2013-10-17 20:04:21',1,1,'2013-10-10 09:38:33','2013-10-10 09:38:40');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_priv`
--

DROP TABLE IF EXISTS `user_priv`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_priv` (
  `u_id` int(10) NOT NULL DEFAULT '0',
  `p_id` int(10) NOT NULL DEFAULT '0',
  KEY `userid` (`u_id`),
  KEY `projid` (`p_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user_priv`
--

LOCK TABLES `user_priv` WRITE;
/*!40000 ALTER TABLE `user_priv` DISABLE KEYS */;
INSERT INTO `user_priv` VALUES (2,2);
/*!40000 ALTER TABLE `user_priv` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-17 13:12:47
