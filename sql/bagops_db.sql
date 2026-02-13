/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: bagops_db
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

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
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assignment_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_assign_event` (`event_id`),
  KEY `fk_assign_user` (`user_id`),
  CONSTRAINT `fk_assign_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_assign_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_doc_event` (`event_id`),
  CONSTRAINT `fk_doc_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `risk_level` varchar(30) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES
(1,'Tahun Baru Masehi','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-01-01 00:00:00','2026-01-01 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(2,'Hari Jadi Kab. Samosir','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-01-01 00:00:00','2026-01-01 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(3,'Isra Mi\'raj 1446 H','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-01-07 00:00:00','2026-01-07 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(4,'Hari Gizi dan Makanan','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-01-16 00:00:00','2026-01-16 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(5,'Tahun Baru Imlek 2676','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-01-25 00:00:00','2026-01-25 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(6,'Hari Raya Nyepi 1947 Saka','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-03-19 00:00:00','2026-03-19 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(7,'Hari Raya Idul Fitri','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-03-21 00:00:00','2026-03-21 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(8,'Jumat Agung','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-03-21 00:00:00','2026-03-21 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(9,'Hari Paskah','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-04-05 00:00:00','2026-04-05 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(10,'Hari Jadi Provinsi Sumut','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-04-15 00:00:00','2026-04-15 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(11,'Hari Buruh','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-05-01 00:00:00','2026-05-01 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(12,'Hari Pendidikan Nasional','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-05-02 00:00:00','2026-05-02 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(13,'HUT Perum Bulog','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-05-10 00:00:00','2026-05-10 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(14,'Kenaikan Yesus Kristus','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-05-14 00:00:00','2026-05-14 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(15,'Idul Adha','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-05-27 00:00:00','2026-05-27 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(16,'Hari Raya Waisak','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-05-31 00:00:00','2026-05-31 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(17,'Hari Anti Tembakau Internasional','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-05-31 00:00:00','2026-05-31 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(18,'Hari Lahir Pancasila','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-06-01 00:00:00','2026-06-01 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(19,'Hari Pasar Modal Indonesia','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-06-03 00:00:00','2026-06-03 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(20,'Trail of the King (Lari Lintas Alam)','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-06-12 00:00:00','2026-06-12 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(21,'Tahun Baru Islam','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-06-16 00:00:00','2026-06-16 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(22,'HUT Polri','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-07-01 00:00:00','2026-07-01 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(23,'HUT BNI','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-07-05 00:00:00','2026-07-05 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(24,'HUT Koperasi','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-07-12 00:00:00','2026-07-12 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(25,'HUT RI','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-08-17 00:00:00','2026-08-17 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(26,'Maulid Nabi Muhammad SAW','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-08-25 00:00:00','2026-08-25 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(27,'Samosir International Choir Competition','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-09-24 00:00:00','2026-09-24 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(28,'HUT Bank Mandiri','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-10-02 00:00:00','2026-10-02 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(29,'HUT TNI','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-10-05 00:00:00','2026-10-05 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(30,'HUT HKBP','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-10-07 00:00:00','2026-10-07 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(31,'Hari Pangan Sedunia','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-10-16 00:00:00','2026-10-16 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(32,'HUT Bank Sumut','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-11-04 00:00:00','2026-11-04 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(33,'Hari Anti Korupsi Sedunia','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-12-09 00:00:00','2026-12-09 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(34,'HUT BRI','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-12-16 00:00:00','2026-12-16 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02'),
(35,'Hari Raya Natal','kamtibmas','Wilayah Polres Samosir',NULL,NULL,'2026-12-25 00:00:00','2026-12-25 23:59:00','medium','Kalender Kamtibmas 2026','2026-02-13 06:27:02','2026-02-13 06:27:02');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `send_at` datetime NOT NULL,
  `channel` varchar(30) DEFAULT 'in-app',
  `message` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notif_event` (`event_id`),
  CONSTRAINT `fk_notif_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(1,1,'2025-12-29 08:00:00','in-app','Reminder Tahun Baru Masehi (H-3)','pending','2026-02-13 06:27:02'),
(2,1,'2025-12-31 08:00:00','in-app','Reminder Tahun Baru Masehi (H-1)','pending','2026-02-13 06:27:02'),
(3,2,'2025-12-29 08:00:00','in-app','Reminder Hari Jadi Kab. Samosir (H-3)','pending','2026-02-13 06:27:02'),
(4,2,'2025-12-31 08:00:00','in-app','Reminder Hari Jadi Kab. Samosir (H-1)','pending','2026-02-13 06:27:02'),
(5,3,'2026-01-04 08:00:00','in-app','Reminder Isra Mi\'raj 1446 H (H-3)','pending','2026-02-13 06:27:02'),
(6,3,'2026-01-06 08:00:00','in-app','Reminder Isra Mi\'raj 1446 H (H-1)','pending','2026-02-13 06:27:02'),
(7,4,'2026-01-13 08:00:00','in-app','Reminder Hari Gizi dan Makanan (H-3)','pending','2026-02-13 06:27:02'),
(8,4,'2026-01-15 08:00:00','in-app','Reminder Hari Gizi dan Makanan (H-1)','pending','2026-02-13 06:27:02'),
(9,5,'2026-01-22 08:00:00','in-app','Reminder Tahun Baru Imlek 2676 (H-3)','pending','2026-02-13 06:27:02'),
(10,5,'2026-01-24 08:00:00','in-app','Reminder Tahun Baru Imlek 2676 (H-1)','pending','2026-02-13 06:27:02'),
(11,6,'2026-03-16 08:00:00','in-app','Reminder Hari Raya Nyepi 1947 Saka (H-3)','pending','2026-02-13 06:27:02'),
(12,6,'2026-03-18 08:00:00','in-app','Reminder Hari Raya Nyepi 1947 Saka (H-1)','pending','2026-02-13 06:27:02'),
(13,7,'2026-03-18 08:00:00','in-app','Reminder Hari Raya Idul Fitri (H-3)','pending','2026-02-13 06:27:02'),
(14,7,'2026-03-20 08:00:00','in-app','Reminder Hari Raya Idul Fitri (H-1)','pending','2026-02-13 06:27:02'),
(15,8,'2026-03-18 08:00:00','in-app','Reminder Jumat Agung (H-3)','pending','2026-02-13 06:27:02'),
(16,8,'2026-03-20 08:00:00','in-app','Reminder Jumat Agung (H-1)','pending','2026-02-13 06:27:02'),
(17,9,'2026-04-02 08:00:00','in-app','Reminder Hari Paskah (H-3)','pending','2026-02-13 06:27:02'),
(18,9,'2026-04-04 08:00:00','in-app','Reminder Hari Paskah (H-1)','pending','2026-02-13 06:27:02'),
(19,10,'2026-04-12 08:00:00','in-app','Reminder Hari Jadi Provinsi Sumut (H-3)','pending','2026-02-13 06:27:02'),
(20,10,'2026-04-14 08:00:00','in-app','Reminder Hari Jadi Provinsi Sumut (H-1)','pending','2026-02-13 06:27:02'),
(21,11,'2026-04-28 08:00:00','in-app','Reminder Hari Buruh (H-3)','pending','2026-02-13 06:27:02'),
(22,11,'2026-04-30 08:00:00','in-app','Reminder Hari Buruh (H-1)','pending','2026-02-13 06:27:02'),
(23,12,'2026-04-29 08:00:00','in-app','Reminder Hari Pendidikan Nasional (H-3)','pending','2026-02-13 06:27:02'),
(24,12,'2026-05-01 08:00:00','in-app','Reminder Hari Pendidikan Nasional (H-1)','pending','2026-02-13 06:27:02'),
(25,13,'2026-05-07 08:00:00','in-app','Reminder HUT Perum Bulog (H-3)','pending','2026-02-13 06:27:02'),
(26,13,'2026-05-09 08:00:00','in-app','Reminder HUT Perum Bulog (H-1)','pending','2026-02-13 06:27:02'),
(27,14,'2026-05-11 08:00:00','in-app','Reminder Kenaikan Yesus Kristus (H-3)','pending','2026-02-13 06:27:02'),
(28,14,'2026-05-13 08:00:00','in-app','Reminder Kenaikan Yesus Kristus (H-1)','pending','2026-02-13 06:27:02'),
(29,15,'2026-05-24 08:00:00','in-app','Reminder Idul Adha (H-3)','pending','2026-02-13 06:27:02'),
(30,15,'2026-05-26 08:00:00','in-app','Reminder Idul Adha (H-1)','pending','2026-02-13 06:27:02'),
(31,16,'2026-05-28 08:00:00','in-app','Reminder Hari Raya Waisak (H-3)','pending','2026-02-13 06:27:02'),
(32,16,'2026-05-30 08:00:00','in-app','Reminder Hari Raya Waisak (H-1)','pending','2026-02-13 06:27:02'),
(33,17,'2026-05-28 08:00:00','in-app','Reminder Hari Anti Tembakau Internasional (H-3)','pending','2026-02-13 06:27:02'),
(34,17,'2026-05-30 08:00:00','in-app','Reminder Hari Anti Tembakau Internasional (H-1)','pending','2026-02-13 06:27:02'),
(35,18,'2026-05-29 08:00:00','in-app','Reminder Hari Lahir Pancasila (H-3)','pending','2026-02-13 06:27:02'),
(36,18,'2026-05-31 08:00:00','in-app','Reminder Hari Lahir Pancasila (H-1)','pending','2026-02-13 06:27:02'),
(37,19,'2026-05-31 08:00:00','in-app','Reminder Hari Pasar Modal Indonesia (H-3)','pending','2026-02-13 06:27:02'),
(38,19,'2026-06-02 08:00:00','in-app','Reminder Hari Pasar Modal Indonesia (H-1)','pending','2026-02-13 06:27:02'),
(39,20,'2026-06-09 08:00:00','in-app','Reminder Trail of the King (Lari Lintas Alam) (H-3)','pending','2026-02-13 06:27:02'),
(40,20,'2026-06-11 08:00:00','in-app','Reminder Trail of the King (Lari Lintas Alam) (H-1)','pending','2026-02-13 06:27:02'),
(41,21,'2026-06-13 08:00:00','in-app','Reminder Tahun Baru Islam (H-3)','pending','2026-02-13 06:27:02'),
(42,21,'2026-06-15 08:00:00','in-app','Reminder Tahun Baru Islam (H-1)','pending','2026-02-13 06:27:02'),
(43,22,'2026-06-28 08:00:00','in-app','Reminder HUT Polri (H-3)','pending','2026-02-13 06:27:02'),
(44,22,'2026-06-30 08:00:00','in-app','Reminder HUT Polri (H-1)','pending','2026-02-13 06:27:02'),
(45,23,'2026-07-02 08:00:00','in-app','Reminder HUT BNI (H-3)','pending','2026-02-13 06:27:02'),
(46,23,'2026-07-04 08:00:00','in-app','Reminder HUT BNI (H-1)','pending','2026-02-13 06:27:02'),
(47,24,'2026-07-09 08:00:00','in-app','Reminder HUT Koperasi (H-3)','pending','2026-02-13 06:27:02'),
(48,24,'2026-07-11 08:00:00','in-app','Reminder HUT Koperasi (H-1)','pending','2026-02-13 06:27:02'),
(49,25,'2026-08-14 08:00:00','in-app','Reminder HUT RI (H-3)','pending','2026-02-13 06:27:02'),
(50,25,'2026-08-16 08:00:00','in-app','Reminder HUT RI (H-1)','pending','2026-02-13 06:27:02'),
(51,26,'2026-08-22 08:00:00','in-app','Reminder Maulid Nabi Muhammad SAW (H-3)','pending','2026-02-13 06:27:02'),
(52,26,'2026-08-24 08:00:00','in-app','Reminder Maulid Nabi Muhammad SAW (H-1)','pending','2026-02-13 06:27:02'),
(53,27,'2026-09-21 08:00:00','in-app','Reminder Samosir International Choir Competition (H-3)','pending','2026-02-13 06:27:02'),
(54,27,'2026-09-23 08:00:00','in-app','Reminder Samosir International Choir Competition (H-1)','pending','2026-02-13 06:27:02'),
(55,28,'2026-09-29 08:00:00','in-app','Reminder HUT Bank Mandiri (H-3)','pending','2026-02-13 06:27:02'),
(56,28,'2026-10-01 08:00:00','in-app','Reminder HUT Bank Mandiri (H-1)','pending','2026-02-13 06:27:02'),
(57,29,'2026-10-02 08:00:00','in-app','Reminder HUT TNI (H-3)','pending','2026-02-13 06:27:02'),
(58,29,'2026-10-04 08:00:00','in-app','Reminder HUT TNI (H-1)','pending','2026-02-13 06:27:02'),
(59,30,'2026-10-04 08:00:00','in-app','Reminder HUT HKBP (H-3)','pending','2026-02-13 06:27:02'),
(60,30,'2026-10-06 08:00:00','in-app','Reminder HUT HKBP (H-1)','pending','2026-02-13 06:27:02'),
(61,31,'2026-10-13 08:00:00','in-app','Reminder Hari Pangan Sedunia (H-3)','pending','2026-02-13 06:27:02'),
(62,31,'2026-10-15 08:00:00','in-app','Reminder Hari Pangan Sedunia (H-1)','pending','2026-02-13 06:27:02'),
(63,32,'2026-11-01 08:00:00','in-app','Reminder HUT Bank Sumut (H-3)','pending','2026-02-13 06:27:02'),
(64,32,'2026-11-03 08:00:00','in-app','Reminder HUT Bank Sumut (H-1)','pending','2026-02-13 06:27:02'),
(65,33,'2026-12-06 08:00:00','in-app','Reminder Hari Anti Korupsi Sedunia (H-3)','pending','2026-02-13 06:27:02'),
(66,33,'2026-12-08 08:00:00','in-app','Reminder Hari Anti Korupsi Sedunia (H-1)','pending','2026-02-13 06:27:02'),
(67,34,'2026-12-13 08:00:00','in-app','Reminder HUT BRI (H-3)','pending','2026-02-13 06:27:02'),
(68,34,'2026-12-15 08:00:00','in-app','Reminder HUT BRI (H-1)','pending','2026-02-13 06:27:02'),
(69,35,'2026-12-22 08:00:00','in-app','Reminder Hari Raya Natal (H-3)','pending','2026-02-13 06:27:02'),
(70,35,'2026-12-24 08:00:00','in-app','Reminder Hari Raya Natal (H-1)','pending','2026-02-13 06:27:02');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `renops`
--

DROP TABLE IF EXISTS `renops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `renops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `doc_no` varchar(100) DEFAULT NULL,
  `command_basis` text DEFAULT NULL,
  `intel_summary` text DEFAULT NULL,
  `objectives` text DEFAULT NULL,
  `forces` text DEFAULT NULL,
  `comms_plan` text DEFAULT NULL,
  `contingency_plan` text DEFAULT NULL,
  `logistics_plan` text DEFAULT NULL,
  `coordination` text DEFAULT NULL,
  `attachments` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_renops_event` (`event_id`),
  CONSTRAINT `fk_renops_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `renops`
--

LOCK TABLES `renops` WRITE;
/*!40000 ALTER TABLE `renops` DISABLE KEYS */;
/*!40000 ALTER TABLE `renops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'draft',
  `summary` text DEFAULT NULL,
  `metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metrics`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_report_event` (`event_id`),
  CONSTRAINT `fk_report_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `rank` varchar(50) DEFAULT NULL,
  `position` varchar(120) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `urut` int(11) DEFAULT NULL,
  `nrp` varchar(30) DEFAULT NULL,
  `ket` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=512 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(2,'RINA SRY NIRWANA TARIGAN, S.I.K., M.H.','AKBP','KAPOLRES SAMOSIR ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'84031648',''),
(3,'BRISTON AGUS MUNTECARLO, S.T., S.I.K.','KOMPOL','WAKAPOLRES',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'83081648',''),
(4,'EDUAR, S.H.','KOMPOL','KABAG OPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'68100259',''),
(5,'PATRI SIHALOHO','AIPDA','PS. PAUR SUBBAGBINOPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'82080038',''),
(6,'AGUNG NUGRAHA NADAP-DAP','BRIPDA','BA MIN BAG OPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'02120141',''),
(7,'ALDI PRANATA GINTING','BRIPDA','BA MIN BAG OPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'03010386',''),
(8,'HENDRIKSON SILALAHI','BRIPDA','BA MIN BAG OPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'02040489',''),
(9,'TOHONAN SITOHANG','BRIPDA','BA MIN BAG OPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'02071119',''),
(10,'GILANG SUTOYO','BRIPDA','BA MIN BAG OPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'03101364',''),
(11,'FERNANDO SILALAHI, A.Md.','-','ASN BAG OPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'198112262024211002','P3K/ BKO POLDA'),
(12,'HENDRI SIAGIAN, S.H.','IPDA','KA SPKT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'76030248',''),
(13,'DENI MUSTIKA SUKMANA, S.E.','IPDA','PAMAPTA 1',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'87070134',''),
(14,'JAMIL MUNTHE, S.H., M.H.','IPDA','PAMAPTA 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'85081770',''),
(15,'BULET MARS SWANTO LBN. BATU, S.H.','IPDA','PAMAPTA 3',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'87030020',''),
(16,'RAMADHAN PUTRA, S.H.','BRIPTU','BAMIN PAMAPTA 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'96010872',''),
(17,'ABEDNEGO TARIGAN','BRIPTU','BAMIN PAMAPTA 3',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'98090415',''),
(18,'EDY SUSANTO PARDEDE','BRIPTU','BAMIN PAMAPTA 1',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'00010166',''),
(19,'BOBBY ANGGARA PUTRA SIREGAR','BRIPDA','BAMIN PAMAPTA 1',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'98010470',''),
(20,'GABRIEL PAULIMA NADEAK','BRIPDA','BAMIN PAMAPTA 1',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'01070820','OP CALL CENTRE'),
(21,'ANDRE OWEN PURBA','BRIPDA','BAMIN PAMAPTA 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'02091526','OP CALL CENTRE'),
(22,'EDWARD FERDINAND SIDABUTAR','BRIPDA','BAMIN PAMAPTA 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'04070159','OP CALL CENTRE'),
(23,'BIMA SANTO HUTAGAOL','BRIPDA','BAMIN PAMAPTA 3',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',12,'03060873','OP CALL CENTRE'),
(24,'KRISTIAN M. H. NABABAN','BRIPDA','BAMIN PAMAPTA 3',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',13,'03121291','OP CALL CENTRE'),
(25,'SURUNG SAGALA','IPDA','PAURSUBBAGPROGAR',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'72100484',''),
(26,'ZAKHARIA S. I. SIMANJUNTAK, S.H.  ','BRIPTU','BA MIN BAG REN',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'96090857',''),
(27,'GRENIEL WIARTO SIHITE','BRIPDA','BA MIN BAG REN',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'03080202',''),
(28,'TARMIZI LUBIS, S.H.','AKP','PS. KABAG SDM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'73010107',''),
(29,'REYMESTA AMBARITA, S.Kom.','PENDA','PAURSUBBAGBINKAR',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'`198111252014122004',''),
(30,'LAMTIO SINAGA, S.H.','BRIGPOL','BA MIN BAG SDM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'97090248',''),
(31,'DODI KURNIADI','BRIPTU','BA MIN BAG SDM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'97120490',''),
(32,'EFRANTA SAPUTRA SITEPU','BRIPDA','BA MIN BAG SDM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'05070285',''),
(33,'RADOS. S. TOGATOROP,S.H.','AIPDA','BA POLRES SAMOSIR',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'86070985','DIK SIP'),
(34,'REYSON YOHANNES SIMBOLON','BRIPDA','ADC KAPOLRES',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'00080579',''),
(35,'ANDRE TARUNA SIMBOLON','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'02090891',''),
(36,'YOLANDA NAULIVIA ARITONANG','BRIPDA','ADC KAPOLRES',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'03081525',''),
(37,'SYAUQI LUTFI LUBIS, S.H., M.H.','BRIGPOL','BA POLRES SAMOSIR',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'95080918',''),
(38,'DANIEL BRANDO SIDABUKKE','BRIGPOL','BA POLRES SAMOSIR',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'97050575',''),
(39,'SUTRISNO BUTAR-BUTAR, S.H.','BRIPTU','BA POLRES SAMOSIR',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'98010119',''),
(40,'LEONARDO SINAGA','AIPDA','BA POLRES SAMOSIR',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'81110363','BELUM MENGHADAP'),
(41,'AWALUDDIN','IPDA','Plt. KASUBBAGBEKPAL',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'76040221',''),
(42,'EFRON SARWEDY SINAGA, S.H.','BRIPTU','BA MIN BAG LOG',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'97050588',''),
(43,'PRIADI MAROJAHAN HUTABARAT','BRIPTU','BA MIN BAG LOG',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'00010095',''),
(44,'CHRIST JERICHO SAPUTRA TAMPUBOLON ','BRIPDA','BA MIN BAG LOG',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'03070263',''),
(45,'EFRI PANDI','AIPDA','PS. KASIUM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'86100287',''),
(46,'YOGI ADE PRATAMA SITOHANG','BRIPDA','BINTARA SIUM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'04010804',''),
(47,'PENGEJAPEN, S.H.','BRIGPOL','PS. KASIKEU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'93100676',''),
(48,'MUHARRAM SYAHRI, S.H.','BRIPTU','BINTARA SIKEU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'97050876',''),
(49,'M.FATHUR RAHMAN, S.H.','BRIPTU','BINTARA SIKEU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'97100685',''),
(50,'HESKIEL WANDANA MELIALA','BRIPDA','BINTARA SIKEU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'03070010',''),
(51,'DANIEL RICARDO SARAGIH','BRIPDA','BINTARA SIKEU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'03040138',''),
(52,'NENENG GUSNIARTI','PENATA','KASIDOKKES',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'197008291993032002',''),
(53,'EDDY SURANTA SARAGIH','BRIPKA','BA SIDOKKES',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'84040532',''),
(54,'BILMAR SITUMORANG','AIPTU','Plt. KASIWAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'75060617',''),
(55,'YOHANES EDI SUPRIATNO, S.H., M.H.','BRIGPOL','BINTARA SIWAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'94080815',''),
(56,'AGUSTIAWAN SINAGA','BRIGPOL','BINTARA SIWAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'94080892',''),
(57,'LISTER BROUN SITORUS','BRIGPOL','BINTARA SITIK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'93060444',''),
(58,'ANDREAS D. S. SITANGGANG','BRIPDA','BINTARA SITIK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'00070791',''),
(59,'JACKSON SIDABUTAR','BRIPDA','BINTARA SITIK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'01101139',''),
(60,'PARIMPUNAN SIREGAR','IPDA','KASUBSIBANKUM ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'73050261',''),
(61,'DANIEL E. LUMBANTORUAN, S.H.','BRIGPOL','BINTARA SIKUM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'95030599',''),
(62,'DENNI BOYKE H. SIREGAR, S.H.','IPDA','PS. KASIPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'76120670',''),
(63,'BENNI ARDINAL, S.H., M.H.','AIPDA','PS. KANIT PROPOS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'81010202',''),
(64,'AGUSTINUS SINAGA','AIPDA','PS. KANIT PAMINAL',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'85081088',''),
(65,'RAMBO CISLER NADEAK','BRIPKA','BINTARA SIPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'86081359',''),
(66,'PERY RAPEN YONES PARDOSI, S.H.','BRIGPOL','BINTARA SIPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'95030796',''),
(67,'DWI HETRIANDY, S.H. ','BRIGPOL','BINTARA SIPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'97070014',''),
(68,'TRY WIBOWO','BRIPTU','BINTARA SIPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'97120554',''),
(69,'SIMON TIGRIS SIAGIAN','BRIPTU','BINTARA SIPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'00080343',''),
(70,'FIRIAN JOSUA SITORUS','BRIPDA','BINTARA SIPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'01080575',''),
(71,'DION MAR\'YANSEN SILITONGA','BRIGPOL','BA PEMBINAAN',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'87030647',''),
(72,'CLAUDIUS HARIS PARDEDE','BRIGPOL','BA PEMBINAAN',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'89080105',''),
(73,'RADIAMAN SIMARMATA','AKP','KASIHUMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'70010290',''),
(74,'GUNAWAN SITUMORANG','BRIGPOL','BINTARA SIHUMAS ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'93030551',''),
(75,'DANIEL BAHTERA SINAGA','BRIPTU','BINTARA SIHUMAS ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'98091488',''),
(76,'HORAS LARIUS SITUMORANG','IPDA','KAURBINOPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'75120560',''),
(77,'JEFTA OCTAVIANUS NICO SIANTURI','BRIGPOL','BINTARA SAT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'95090650',''),
(78,'SAHAT MARULI TUA SINAGA, S.H.','BRIGPOL','BINTARA SAT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'94091146',''),
(79,'RONAL PARTOGI SITUMORANG','BRIPDA','BINTARA SAT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'04020118',''),
(80,'DONAL P. SITANGGANG, S.H., M.H.','IPTU','PS. KASAT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'82070670',''),
(81,'MUHAMMAD YUNUS LUBIS, S.H.','IPDA','KAURBINOPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'85050489',''),
(82,'MARBETA S. SIANIPAR, S.H.','AIPDA','PS. KAURMINTU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'80070348',''),
(83,'SITARDA AKABRI SIBUEA','AIPDA','PS. KANIT 3',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'87080112',''),
(84,'CINTER ROKHY SINAGA','BRIPKA','PS. KANIT 1',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'87051430',''),
(85,'VANDU P. MARPAUNG','BRIPKA','PS. KANIT 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'90080088',''),
(86,'ALFONSIUS GULTOM, S.H. ','BRIGPOL','BINTARA SAT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'93080556',''),
(87,'TRIFIKO P. NAINGGOLAN, S.H.','BRIPTU','BINTARA SATINTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'97040848',''),
(88,'ANDRI AFRIJAL SIMARMATA','BRIPTU','BINTARA SATINTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'98110618',''),
(89,'DIEN VAROSCY I. SITUMORANG','BRIPDA','BINTARA SATINTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'02030032',''),
(90,'ARDY TRIANO MALAU','BRIPDA','BINTARA SATINTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'02120339',''),
(91,'JUNEDI SAGALA','BRIPDA','BINTARA SATINTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',12,'02040459',''),
(92,'GABRIEL SEBASTIAN SIREGAR','BRIPDA','BINTARA SATINTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',13,'02101010',''),
(93,'RIO F. T ERENST PANJAITAN','BRIPDA','BINTARA SATINTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',14,'04020209',''),
(94,'AGHEO HARMANA JOUSTRA SINURAYA','BRIPDA','BINTARA SAT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',15,'04080118',''),
(95,'SAMUEL RINALDI PAKPAHAN','BRIPDA','BINTARA SAT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',16,'04010804',''),
(96,'RAYMONTIUS HAROMUNTE','BRIPDA','BINTARA SAT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',17,'04040520',''),
(97,'EDWARD SIDAURUK, S.E., M.M.','AKP','KASAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'79120994',''),
(98,'DARMONO SAMOSIR, S.H. ','IPDA','KANITIDIK 3',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'76020196',''),
(99,'ROYANTO PURBA, S.H.','IPDA','KANITIDIK 4',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'83010825',''),
(100,'SUHADIYANTO, S.H.','IPDA','KANITIDIK 1',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'83120602',''),
(101,'KUICAN SIMANJUNTAK','BRIPKA','KANITIDIK 5',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'88060535',''),
(102,'MARTIN HABENSONY ARITONANG','AIPTU','PS. KANITIDIK 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'79030434',''),
(103,'HENRY SIPAKKAR','AIPTU','PS. KANIT IDENTIFIKASI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'83060084',''),
(104,'CHANDRA HUTAPEA','BRIPKA','PS. KAURMINTU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'87011165',''),
(105,'CHANDRA BARIMBING','BRIPKA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'89030401',''),
(106,'DEDY SAOLOAN SIGALINGGING','BRIPKA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'87041596',''),
(107,'ISWAN LUKITO','BRIPKA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'82050798',''),
(108,'RONI HANSVERI BANJARNAHOR','BRIGPOL','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',12,'95030238',''),
(109,'RODEN SUANDI TURNIP','BRIGPOL','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',13,'94020506',''),
(110,'SAPUTRA, S.H.','BRIGPOL','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',14,'94121145',''),
(111,'DIAN LESTARI GULTOM, S.H.','BRIGPOL','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',15,'95100554',''),
(112,'ARGIO SIMBOLON','BRIGPOL','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',16,'95110886',''),
(113,'EKO DAHANA PARDEDE, S.H.','BRIGPOL','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',17,'97070616',''),
(114,'GIDEON AFRIADI LUMBAN RAJA','BRIPTU','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',18,'97040728',''),
(115,'FACHRUL REZA SILALAHI','BRIPTU','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',19,'98090397',''),
(116,'RIDHOTUA F. SITANGGANG','BRIPTU','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',20,'00030346',''),
(117,'NICHO FERNANDO SARAGIH','BRIPTU','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',21,'00110362',''),
(118,'ADI P.S. MARBUN','BRIPTU','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',22,'00090499',''),
(119,'PRIYATAMA ABDILLAH HARAHAP','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',23,'01120358',''),
(120,'RIZKI AFRIZAL SIMANJUNTAK','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',24,'01070839',''),
(121,'MIDUK YUDIANTO SINAGA','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',25,'01060553',''),
(122,'FRAN\'S ALEXANDER SIANIPAR ','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',26,'02110342',''),
(123,'RAFFLES SIJABAT','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',27,'01110817',''),
(124,'HERIANTA TARIGAN','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',28,'01091201',''),
(125,'RICKY AGATHA GINTING','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',29,'03030809',''),
(126,'CHRISTIAN PROSPEROUS SIMANUNGKALIT','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',30,'03020368',''),
(127,'PINIEL RAJAGUKGUK','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',31,'04020196',''),
(128,'REZA SIREGAR','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',32,'03090568',''),
(129,'ANDRE YEHEZKIEL HUTABARAT','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',33,'04060050',''),
(130,'RAYMOND VAN HEZEKIEL SIAHAAN','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',34,'04031206',''),
(131,'M. ALAMSYAH PRAYOGA TAMBUNAN','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',35,'05080602',''),
(132,'IRVAN SYAPUTRA MALAU','BRIPDA','BINTARA SAT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',36,'04090567',''),
(133,'FERRY ARIANDY, S.H., M.H','AKP','KASATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'79060034',''),
(134,'ALVIUS KRISTIAN GINTING, S.H.','IPDA','KAURBINOPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'88100591',''),
(135,'BENNY SITUMORANG, S.H. ','BRIPKA','PS.KANIT IDIK 1',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'89010155',''),
(136,'EKO PUTRA DAMANIK, S.H.','BRIGPOL','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'93050797',''),
(137,'MAY FRANSISCO SIAGIAN, S.H.','BRIGPOL','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'91050361',''),
(138,'ROBERTO MANALU','BRIPTU','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'94090839',''),
(139,'M. RONALD FAHROZI HARAHAP, S.H.','BRIPTU','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'98110378',''),
(140,'HERIANTO EFENDI, S.H.','BRIPTU','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'97020694',''),
(141,'TEDDI PARNASIPAN TOGATOROP','BRIPDA','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'02120224',''),
(142,'ONDIHON SIMBOLON','BRIPDA','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'02090838',''),
(143,'IVAN SIGOP SIHOMBING','BRIPDA','BINTARA SATRESNARKOBA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'05080131',''),
(144,'NANDI BUTAR-BUTAR, S.H.','AKP','KASAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'80080676',''),
(145,'BARTO ANTONIUS SIMALANGO','AIPTU','PS. KAURBINOPS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'80050867',''),
(146,'HASUDUNGAN SILITONGA','AIPDA','PS. KANIT DALMAS 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'73040390',''),
(147,'JHONNY LEONARDO SILALAHI','BRIPKA','PS. KANIT TURJAWALI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'85090954',''),
(148,'ASRIL','BRIPKA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'83081051',''),
(149,'INDIRWAN FRIDERICK, S.H. ','BRIGPOL','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'94110350',''),
(150,'EGIDIUM BRAUN SILITONGA','BRIGPOL','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'93100793','A'),
(151,'DINAMIKA JAYA NEGARA SITANGGANG','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'97100701','B'),
(152,'ZULKIFLI NASUTION','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'02051553','C'),
(153,'WIRA HARZITA','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'05051087',''),
(154,'RAHMAT ANDRIAN TAMBUNAN','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'06100189',''),
(155,'JONATAN DWI SAPUTRA PARAPAT','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',12,'07080045',''),
(156,'PERDANA NIKOLA SEMBIRING','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',13,'04051595',''),
(157,'PETRUS SURIA HUGALUNG','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',14,'04081205',''),
(158,'RAFAEL ARSANLILO SINULINGGA','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',15,'06010414',''),
(159,'RAJASPER SIRINGORINGO','BRIPDA','BINTARA SAT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',16,'06090021',''),
(160,'TANGIO HAOJAHAN SITANGGANG, S.H.','IPTU','KASAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'72100604',''),
(161,'MARUBA NAINGGOLAN','AIPTU','PS. KANITPAMWASTER',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'80100836',''),
(162,'ROY HARIS ST. SIMAREMARE','AIPDA','PS. KANITPAMWISATA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'85030645',''),
(163,'M. DENY WAHYU','AIPDA','PS. PANIT PAMWASTER',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'80050898',''),
(164,'HENRI F. SIANIPAR','AIPTU','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'83050202',''),
(165,'BUYUNG ANDRYANTO','BRIPKA','PS. KAURMINTU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'85121325',''),
(166,'RIANTO SITANGGANG','BRIGPOL','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'91110130',''),
(167,'ROY NANDA SEMBIRING KEMBAREN','BRIGPOL','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'94090948',''),
(168,'CANDRA SILALAHI, S.H.','BRIGPOL','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'96031057',''),
(169,'HORAS J.M. ARITONANG ','BRIPDA','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'01060884',''),
(170,'YUNUS SAMDIO SIDABUTAR ','BRIPDA','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'02100599',''),
(171,'RAINHEART SITANGGANG ','BRIPDA','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',12,'03010565',''),
(172,'BONIFASIUS NAINGGOLAN','BRIPDA','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',13,'02011312',''),
(173,'RAY YONDO SIAHAAN ','BRIPDA','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',14,'00080816',''),
(174,'REDY EZRA JONATHAN','BRIPDA','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',15,'03040947',''),
(175,'CHARLY H. ARITONANG','BRIPDA','BINTARA SAT PAMOBVIT',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',16,'04100485',''),
(176,'NATANAIL SURBAKTI, S.H','AKP','KASAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'79120800',''),
(177,'JUSUF KETAREN','IPDA','KANITREGIDENT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'75080942',''),
(178,'ARON PERANGIN-ANGIN','AIPTU','PS. KANITGAKKUM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'80070492',''),
(179,'HERON GINTING','BRIPKA','PS. KANITTURJAWALI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'79060704',''),
(180,'JEFRI KHADAFI SIREGAR, S.H.','BRIPKA','PS. KANITKAMSEL ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'86030733',''),
(181,'HERIANTO TURNIP','BRIPKA','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'89070031',''),
(182,'ROY GRIMSLAY, S.H.','BRIGPOL','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'93020749',''),
(183,'BAGUS DWI PRAKOSO, S.H.','BRIGPOL','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'93090673','SABTU'),
(184,'ICASANDRI MONANZA BR GINTING','BRIGPOL','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'97040353','MINGGU'),
(185,'DIKI FEBRIAN SITORUS','BRIPTU','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'95021078','SENIN'),
(186,'MARCHLANDA SITOHANG','BRIPTU','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'96031061','SELASA'),
(187,'JULIVER SIDABUTAR','BRIPTU','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',12,'01080438',''),
(188,'FATHURROZI TINDAON','BRIPDA','BINTARA SAT LANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',13,'01120281',''),
(189,'BENY BOY CHRISTIAN SIAHAAN','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',14,'02111012',''),
(190,'RADOT NOVALDO PANDAPOTAN PURBA','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',15,'02111051',''),
(191,'DIDI HOT BAGAS SITORUS','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',16,'96061331',''),
(192,'MUHAMMAD ZIDHAN RIFALDI','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',17,'05030251',''),
(193,'DANI INDRA PERMANA SINAGA','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',18,'04050615',''),
(194,'HEZKIEL CAPRI SITINDAON','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',19,'05010048',''),
(195,'BONARIS TSUYOKO DITASANI SINAGA','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',20,'04030824',''),
(196,'ARY ANJAS SARAGIH','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',21,'05010014',''),
(197,'GABRIEL VERY JUNIOR SITOHANG','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',22,'04030805',''),
(198,'FIRMAN BAHTERA','BRIPDA','BINTARA SATLANTAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',23,'02121477',''),
(199,'SULAIMAN PANGARIBUAN, S.H','AKP','KASAT POLAIRUD',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'68120522',''),
(200,'EFENDI M.  SIREGAR','AIPDA','PS. KANITPATROLI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'83080822',''),
(201,'ROMEL LINDUNG SIAHAAN','AIPDA','PS. KAURMINTU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'73120275',''),
(202,'FRANS HOTMAN MANURUNG, S.H.','BRIPKA','BINTARA SATPOLAIRUD',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'90060273',''),
(203,'ANTONIUS SIPAYUNG','BRIGPOL','BINTARA SATPOLAIRUD',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'77070919',''),
(204,'SAUT H. SIAHAAN','AIPDA','PS. KASAT TAHTI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'82051018',''),
(205,'FERNANDO SIMBOLON','BRIPTU','BINTARA SAT TAHTI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'98050496',''),
(206,'KURNIA PERMANA','BRIPTU','BINTARA SAT TAHTI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'98030531',''),
(207,'STEVEN IMANUEL SITUMEANG','BRIPDA','BINTARA SAT TAHTI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'05090232',''),
(208,'RAHMAT KURNIAWAN','IPTU','PS. KAPOLSEK HARIAN BOHO',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'69090552',''),
(209,'MARUKKIL J.M. PASARIBU ','AIPTU','PS. KANIT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'79090296',''),
(210,'LANTRO LANDELINUS SAGALA','AIPDA','PS. KANIT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'82070930',''),
(211,'ANDY DEDY SIHOMBING, S.H.','BRIPKA','PS. KANIT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'87120701',''),
(212,'RANGGA HATTA','BRIPKA','PS.KANIT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'86021428',''),
(213,'ARDIANSYAH BUTAR-BUTAR','BRIPKA','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'80120573',''),
(214,'ADRYANTO SINAGA, S.H.','BRIGPOL','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'96120123',''),
(215,'BROLIN ADFRIALDI HALOHO','BRIGPOL','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'94040538',''),
(216,'SUGIANTO ERIK SIBORO','BRIGPOL','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'95110806',''),
(217,'RISKO SIMBOLON','BRIPDA','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'01020739',''),
(218,'MAXON NAINGGOLAN','AKP','KAPOLSEK PALIPI',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'70050412',''),
(219,'H. SWANDI SINAGA','AIPTU','PS. KA SPKT 1 ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'78040213',''),
(220,'HARATUA GULTOM','AIPTU','PS. KASIUM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'77030463',''),
(221,'ASA MELKI HUTABARAT','AIPDA','PS. KANIT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'76120606',''),
(222,'JARIAHMAN SARAGIH','AIPDA','PS. KANIT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'78100741',''),
(223,'MUHAMMAD SYAFEI RAMADHAN','AIPDA','PS. KANIT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'87041134',''),
(224,'RIJALUL FIKRI SINAGA','BRIPKA','PS. KANIT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'86121371',''),
(225,'TEGUH SYAHPUTRA','BRIPKA','PS. KA SPKT 2',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'85071450',''),
(226,'RUDYANTO LUMBANRAJA','BRIPKA','BINTARA  POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'85041500',''),
(227,'ZULPAN SYAHPUTRA DAMANIK','BRIPTU','BINTARA  POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'96031075',''),
(228,'RAMADAN SIREGAR, S.H.','IPTU','PS. KAPOLSEK SIMANINDO',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'83061022',''),
(229,'WIDODO KABAN, S.H.','IPDA','KANIT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'86071792',''),
(230,'GUNTAR TAMBUNAN','AIPTU','PS. KA SPKT 1 ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'75120864',''),
(231,'JEFRI RICARDO SAMOSIR','AIPTU','PS. KANITPROPAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'82040124',''),
(232,'JUITO SUPANOTO PERANGIN-ANGIN','AIPDA','PS. KANIT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'84020306',''),
(233,'YOPPHY RHODEAR MUNTHE ','AIPDA','PS. KA SPKT 3',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'83080042',''),
(234,'TUMBUR SITOHANG','AIPDA','PS. KANIT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'86010311',''),
(235,'DONI SURIANTO PURBA, S.H.','BRIPKA','PS. KASIUM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'84110202',''),
(236,'PATAR F. ANRI SIAHAAN','BRIPKA','PS. KANIT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'89020409',''),
(237,'KURNIAWAN, S.H.','BRIGPOL','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'94090490',''),
(238,'ASHARI BUTAR-BUTAR, S.H.','BRIGPOL','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',11,'95060432',''),
(239,'MARLAN SILALAHI','KOMPOL','KAPOLSEK ONANRUNGGU',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'68020268',''),
(240,'HERMAWADI ','AIPDA','PS. KANIT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'82050839',''),
(241,'BISSAR LUMBANTUNGKUP','AIPDA','PS. KANIT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'84091124',''),
(242,'BONAR JUBEL SIBARANI','BRIPKA','PS. KANIT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'70090340',''),
(243,'RAMLES SITANGGANG','BRIPKA','PS. KANIT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'77020642',''),
(244,'LUHUT SIRINGO-RINGO','BRIGPOL','BINTARA POLSEK ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'83031377',''),
(245,'ANRIAN SIGALINGGING','BRIPDA','BINTARA POLSEK ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'03100001',''),
(246,'BONATUA LUMBANTUNGKUP','BRIPDA','BINTARA POLSEK ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'99110755',''),
(247,'ANDRE SUGIARTO MARPAUNG','BRIPDA','BINTARA POLSEK ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'03050116',''),
(248,'ERWIN KEVIN GULTOM','BRIPDA','BINTARA POLSEK ',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'04030125',''),
(249,'BANGUN TUA DALIMUNTHE','AKP','KAPOLSEK PANGURURAN',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',1,'70020298',''),
(250,'LANCASTER ARIANTO CANDY PASARIBU, S.H.','AIPTU','PS. KANIT RESKRIM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',2,'81050713',''),
(251,'RUDY SETYAWAN','AIPTU','PS. KANIT INTELKAM',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',3,'80090905',''),
(252,'MANGATUR TUA TINDAON','AIPDA','PS. KANIT BINMAS',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',4,'80080892','A'),
(253,'RENO HOTMARULI TUA MANIK, S.H.','BRIPKA','PS. KANIT SAMAPTA',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',5,'87110154','B'),
(254,'HERBINTUPA SITANGGANG ','BRIGPOL','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',6,'79020443','C'),
(255,'IBRAHIM TARIGAN','BRIGPOL','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',7,'85121751',''),
(256,'AGUNG NUGRAHA HARIANJA, S.H. ','BRIPTU','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',8,'98090406',''),
(257,'DANI PUTRA RUMAHORBO','BRIPTU','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',9,'98091274',''),
(258,'KRISMAN JULU GULTOM','BRIPDA','BINTARA POLSEK',NULL,'user','2026-02-13 06:12:45','2026-02-13 06:12:45',10,'01060198','');
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

-- Dump completed on 2026-02-13 13:33:44
