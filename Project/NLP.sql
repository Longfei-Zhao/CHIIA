-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: localhost    Database: NLP
-- ------------------------------------------------------
-- Server version	5.7.24-0ubuntu0.16.04.1

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
-- Table structure for table `NLP_ARTICLE`
--

DROP TABLE IF EXISTS `NLP_ARTICLE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NLP_ARTICLE` (
  `articleID` int(11) NOT NULL AUTO_INCREMENT,
  `ID` varchar(1024) NOT NULL,
  `title` varchar(1024) NOT NULL,
  `author` varchar(1024) NOT NULL,
  `date` date DEFAULT NULL,
  `content` longtext NOT NULL,
  `url` varchar(1024) DEFAULT NULL,
  `crawldate` date NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  `assign` tinyint(4) DEFAULT '0',
  `labeledby` varchar(255) DEFAULT NULL,
  `source` varchar(1024) NOT NULL,
  `labeledtime` date DEFAULT NULL,
  `likelyhood` float DEFAULT NULL,
  `AN` text,
  `ART` text,
  `CLM` text,
  `CT` text,
  `CX` text,
  `CR` text,
  `DE` text,
  `CO` text,
  `ED` text,
  `FDS` text,
  `HD` text,
  `HL` text,
  `HLP` text,
  `LA` text,
  `LP` text,
  `PG` text,
  `RF` text,
  `RE` text,
  `SE` text,
  `SC` text,
  `SN` text,
  `NS` text,
  `VOL` text,
  `WC` text,
  `PD` text,
  `CY` text,
  `ET` text,
  `AU` text,
  `PUB` text,
  `IPC` text,
  `IPD` text,
  `TD` longtext,
  PRIMARY KEY (`articleID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=36517 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NLP_JOBLIST`
--

DROP TABLE IF EXISTS `NLP_JOBLIST`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NLP_JOBLIST` (
  `userID` int(11) NOT NULL,
  `articleID` int(11) NOT NULL,
  `assignedDate` date DEFAULT NULL,
  PRIMARY KEY (`userID`,`articleID`),
  KEY `articleID` (`articleID`),
  CONSTRAINT `NLP_JOBLIST_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `NLP_USER` (`userID`),
  CONSTRAINT `NLP_JOBLIST_ibfk_2` FOREIGN KEY (`articleID`) REFERENCES `NLP_ARTICLE` (`articleID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NLP_ML`
--

DROP TABLE IF EXISTS `NLP_ML`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NLP_ML` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `log` longtext,
  `TERMFREQ` text,
  `accuracy` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NLP_SPIDER`
--

DROP TABLE IF EXISTS `NLP_SPIDER`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NLP_SPIDER` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `source` varchar(1024) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `term` varchar(1024) NOT NULL,
  `progress` float DEFAULT NULL,
  `checkpoint` varchar(1024) DEFAULT NULL,
  `log` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NLP_USER`
--

DROP TABLE IF EXISTS `NLP_USER`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NLP_USER` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `authority` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-10-31 10:34:39
