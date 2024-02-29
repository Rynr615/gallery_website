-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gallery_web
-- ------------------------------------------------------
-- Server version	10.4.27-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `albums`
--

DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `albums` (
  `albumID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `thumbnail_album` varchar(255) NOT NULL DEFAULT 'album_default.svg',
  PRIMARY KEY (`albumID`),
  KEY `userID` (`userID`),
  CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `albums`
--

LOCK TABLES `albums` WRITE;
/*!40000 ALTER TABLE `albums` DISABLE KEYS */;
INSERT INTO `albums` VALUES (1,1,'First Album','','2024-02-21 13:03:46','album_default.jpg'),(2,3,'Album ke 1','Test 1 edit','2024-02-22 08:41:31','1708479145_items-11.jpg'),(3,1,'Second Album','dfafdasf','2024-02-25 11:17:10','1708859830_1708479145_items-11.jpg'),(4,1,'JKT Member','','2024-02-26 01:41:32','album_default.jpg');
/*!40000 ALTER TABLE `albums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `photoID` int(11) DEFAULT NULL,
  `commentText` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`commentID`),
  KEY `userID` (`userID`),
  KEY `photoID` (`photoID`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`photoID`) REFERENCES `photos` (`photoID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (3,3,4,'test 1 edit','2024-02-22 08:35:36'),(15,5,29,'akwokwok edit','2024-02-28 09:19:48');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likes` (
  `likeID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `photoID` int(11) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`likeID`),
  KEY `userID` (`userID`),
  KEY `photoID` (`photoID`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`photoID`) REFERENCES `photos` (`photoID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (2,1,26,'2024-02-27 05:21:07');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photos` (
  `photoID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `albumID` int(11) DEFAULT 0,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(255) NOT NULL,
  PRIMARY KEY (`photoID`),
  KEY `userID` (`userID`),
  KEY `albumID` (`albumID`),
  CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  CONSTRAINT `photos_ibfk_2` FOREIGN KEY (`albumID`) REFERENCES `albums` (`albumID`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photos`
--

LOCK TABLES `photos` WRITE;
/*!40000 ALTER TABLE `photos` DISABLE KEYS */;
INSERT INTO `photos` VALUES (4,1,NULL,'Yoimiya','','1708522392_items-1.jpg','2024-02-21 13:33:12',''),(6,1,3,NULL,NULL,'1708861458_items-9.jpg','2024-02-25 11:44:18',''),(13,1,NULL,'Test gtw keberapa','akowkwok','1708868938_1708479145_items-11.jpg','2024-02-25 13:48:58',''),(14,1,NULL,'Test gtw keberapa','akowkwok','1708868938_1708522392_items-1.jpg','2024-02-25 13:48:58',''),(15,1,NULL,'Test testo','dafadfa','1708868963_1708479145_items-11.jpg','2024-02-25 13:49:23',''),(16,1,NULL,'Test testo','dafadfa','1708868963_1708519400_items-6.jpg','2024-02-25 13:49:23',''),(17,1,NULL,'Test testo','dafadfa','1708868963_1708519933_items-4.png','2024-02-25 13:49:23',''),(18,1,1,NULL,NULL,'1708869439_1708479145_items-11.jpg','2024-02-25 13:57:19',''),(19,1,3,NULL,NULL,'1708869439_1708519400_items-6.jpg','2024-02-26 13:18:27',''),(20,1,1,NULL,NULL,'1708869439_1708519933_items-4.png','2024-02-25 13:57:19',''),(21,1,4,NULL,NULL,'1708912114_jessica_chandra.jpg','2024-02-26 01:48:34',''),(22,1,4,NULL,NULL,'1708912114_azizi_asadel.jpg','2024-02-26 01:48:34',''),(23,1,4,NULL,NULL,'1708912114_amanda_sukma.jpg','2024-02-26 01:48:34',''),(24,1,4,NULL,NULL,'1708912114_marsha_lenathea.jpg','2024-02-26 01:48:34',''),(25,1,4,NULL,NULL,'1708912114_freya_jayawardana.jpg','2024-02-26 01:48:34',''),(26,1,NULL,'test report','','1709003739_items-8.jpg','2024-02-27 03:15:39',''),(28,1,NULL,'maomao','','1709085152_1708861458_items-9.jpg','2024-02-28 02:15:00','Idol'),(29,1,1,'Waifu','Lorem ipsum dolor sit, amet consectetur adipisicing elit. Hic voluptates qui minus explicabo doloremque sed reiciendis ratione id inventore quasi!','1709087479_items-3.jpg','2024-02-28 02:35:37','Game'),(30,1,1,'Waifu','mehweewh','1709087479_items-4.png','2024-02-28 02:31:19','Game'),(31,1,1,'Waifu','mehweewh','1709087479_items-5.jpg','2024-02-28 02:31:19','Game');
/*!40000 ALTER TABLE `photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `reportID` int(11) NOT NULL AUTO_INCREMENT,
  `reportType` varchar(50) NOT NULL,
  `photoID` int(11) DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `additionalInfo` text DEFAULT NULL,
  `reportedBy` int(11) NOT NULL,
  `reportedUser` int(11) NOT NULL,
  `reportedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reportID`),
  KEY `fk_report_user` (`reportedBy`),
  KEY `fk_report_photo` (`photoID`),
  KEY `fk_reported_user` (`reportedUser`),
  CONSTRAINT `fk_report_photo` FOREIGN KEY (`photoID`) REFERENCES `photos` (`photoID`),
  CONSTRAINT `fk_report_user` FOREIGN KEY (`reportedBy`) REFERENCES `users` (`userID`),
  CONSTRAINT `fk_reported_user` FOREIGN KEY (`reportedUser`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (6,'spam',26,'too cute','',1,0,'2024-02-27 11:56:00'),(7,'spam',26,'too cute','',1,0,'2024-02-27 11:57:23'),(8,'spam',26,'apalah','',2,0,'2024-02-27 12:00:36'),(9,'spam',26,'wkkw','',2,1,'2024-02-27 12:02:59'),(13,'nudity',29,'too hot','',1,1,'2024-02-28 02:38:20');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reset_password`
--

DROP TABLE IF EXISTS `reset_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reset_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `reset_code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reset_password`
--

LOCK TABLES `reset_password` WRITE;
/*!40000 ALTER TABLE `reset_password` DISABLE KEYS */;
INSERT INTO `reset_password` VALUES (1,'ryanyanuar184@gmail.com','171456');
/*!40000 ALTER TABLE `reset_password` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `access_level` varchar(50) DEFAULT 'user',
  `last_login` datetime DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_photo` varchar(255) DEFAULT 'default_profile.svg',
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ryan Yanuar Pradana','Ryn','$2y$10$jV/2X2Ft3CoWZ9hJExamIeYii9pEhFnIdGAf9fs.EX.xlTHm6YWqq','ryanyanuarpradana@gmai.com','super_admin','2024-02-28 07:01:58','2024-02-20 17:20:41','1709085397profile_photo_1.jpg'),(2,'','raihanrei','$2y$10$xS8NUCRafzGRh2EEP46aO.4NrKh/WdXka7BT1gcGSMS9NV.NNWWc6','raihanrei@gmail.com','user','2024-02-27 14:00:42','2024-02-21 06:42:50','default_profile.svg'),(3,'','fxthir','$2y$10$kR52EIBOS9dqoo1SpXK7J.acVAIvQ.dHeiCtjbeNh5/p5EgI0gSvG','muhammadabdulfathir@gmail.com','user','2024-02-26 09:55:54','2024-02-22 02:33:34','default_profile.svg'),(4,'','reynaldi','$2y$10$qLeyJMP/l2e.ItonRGp1seRZSG1ZtnCvpE07sffkqIXkEISAjVQxa','rynldh@gmail.com','user','2024-02-26 03:54:14','2024-02-25 20:54:14','default_profile.svg'),(5,'','Ryan','$2y$10$h2x.Na0iES1Z.R1iI7C6oezfTFe1hHjNLGttZ9cj6VFulKuT4Z1E.','ryanyanuar184@gmail.com','user','2024-02-28 10:49:55','2024-02-28 03:13:53','default_profile.svg');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-29  7:47:59
