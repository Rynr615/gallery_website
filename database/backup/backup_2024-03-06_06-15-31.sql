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
  `acces_level` varchar(255) NOT NULL,
  PRIMARY KEY (`albumID`),
  KEY `userID` (`userID`),
  CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `albums`
--

LOCK TABLES `albums` WRITE;
/*!40000 ALTER TABLE `albums` DISABLE KEYS */;
INSERT INTO `albums` VALUES (1,1,'Genshin Impact','Game yang baik','2024-03-06 01:49:21','1709634209_items-5.jpg','public'),(2,2,'Pemandangan','','2024-03-06 03:18:07','album_default.jpg','public'),(3,3,'Animek','Simpenan','2024-03-06 01:49:29','items-17.jpg','public'),(5,2,'test','','2024-03-06 01:43:40','album_default.jpg','public');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,2,1,'Jos gandos','2024-03-05 10:32:44'),(2,1,7,'Kiritooo','2024-03-05 10:37:58'),(3,3,5,'Kerennyoo','2024-03-05 10:44:46'),(4,4,6,'test test','2024-03-06 03:01:12'),(5,2,5,'mantap banh','2024-03-06 04:47:44');
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
  `type` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`likeID`),
  KEY `userID` (`userID`),
  KEY `photoID` (`photoID`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`photoID`) REFERENCES `photos` (`photoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
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
  `acces_level` varchar(255) NOT NULL,
  PRIMARY KEY (`photoID`),
  KEY `userID` (`userID`),
  KEY `albumID` (`albumID`),
  CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  CONSTRAINT `photos_ibfk_2` FOREIGN KEY (`albumID`) REFERENCES `albums` (`albumID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photos`
--

LOCK TABLES `photos` WRITE;
/*!40000 ALTER TABLE `photos` DISABLE KEYS */;
INSERT INTO `photos` VALUES (1,1,1,'Genshin Impek','Mantapkan banh','1709634339_items-1.jpg','2024-03-06 02:05:32','Game','public'),(2,1,1,'Genshin Impek','Mantapkan banh','1709634339_items-2.jpg','2024-03-06 02:05:32','Game','public'),(3,1,1,'Genshin Impek','Mantapkan banh','1709634339_items-3.jpg','2024-03-06 02:05:32','Game','public'),(4,1,1,'Genshin Impek','Mantapkan banh','1709634339_items-4.png','2024-03-06 02:05:32','Game','public'),(5,2,2,'Alam','','1709634694_items-12.jpg','2024-03-06 02:05:32','Nature','public'),(6,2,2,'Alam','','1709634694_items-13.jpg','2024-03-06 02:05:32','Nature','public'),(7,3,3,'SAwO','Kirito','1709634953_items-14.jpg','2024-03-06 02:05:32','Anime','public'),(8,3,3,'SAwO','Kirito','1709634953_items-15.jpg','2024-03-06 02:05:32','Anime','public'),(9,3,3,'SAwO','Kirito','1709634953_items-16.jpg','2024-03-06 02:05:32','Anime','public'),(10,2,5,'test','','1709689974_Haerin.full.319794.jpg','2024-03-06 02:03:17','Food','private');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_album`
--

DROP TABLE IF EXISTS `reports_album`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_album` (
  `reportID` int(11) NOT NULL AUTO_INCREMENT,
  `reportType` varchar(50) NOT NULL,
  `albumID` int(11) DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `additionalInfo` text DEFAULT NULL,
  `reportedBy` int(11) NOT NULL,
  `reportedUser` int(11) NOT NULL,
  `reportedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reportID`),
  KEY `FK_reports_album_albumID` (`albumID`),
  KEY `fk_reportedBy` (`reportedBy`),
  KEY `fk_reportedUser` (`reportedUser`),
  CONSTRAINT `FK_reports_album_albumID` FOREIGN KEY (`albumID`) REFERENCES `albums` (`albumID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reportedBy` FOREIGN KEY (`reportedBy`) REFERENCES `users` (`userID`),
  CONSTRAINT `fk_reportedUser` FOREIGN KEY (`reportedUser`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_album`
--

LOCK TABLES `reports_album` WRITE;
/*!40000 ALTER TABLE `reports_album` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_album` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reset_password`
--

LOCK TABLES `reset_password` WRITE;
/*!40000 ALTER TABLE `reset_password` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ryan Yanuar Pradana','Ryn','$2y$10$afL11FP0XsdbsoViMln3T.Zz0WrhUCRZ8ThcH7KfqvAt9GgGsm.Ci','ryanyanuarpradana@gmail.com','super_admin','2024-03-06 03:14:20','2024-03-02 18:26:17','1709634519profile_photo_1.jpg'),(2,'Muhammad Abdul Fathir','fxthir','$2y$10$uHom.NeGhhwsNYBGGl4DQu9CO45jmk/71W7De.N6QXSZ6sTGd6Tm.','muhammadabdoelfathir@gmail.com','user','2024-03-06 04:13:17','2024-03-05 04:10:17','1709634743profile_photo_2.jpg'),(3,'Rendi Raihanrai','raihanrei','$2y$10$wAACMFQtBHGhnQiy9UvNkel/ZR0mzOCltsfcile0kwu/MdTE7JKAa','raihanrai.rendi@gmail.com','admin','2024-03-06 03:59:56','2024-03-05 04:22:31','1709635016profile_photo_3.jpg'),(4,'','reynaldi','$2y$10$nNDrEnJfGsLk9tEehyww8uUcDcmF4mD3AT.XWSC9C6/Y4cnX3.y0W','rynldhi@gmail.com','user','2024-03-06 04:00:59','2024-03-05 20:29:30','default_profile.svg');
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

-- Dump completed on 2024-03-06 12:15:33
