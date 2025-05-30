-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 73.214.12.104    Database: dealership
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `sedan`
--

DROP TABLE IF EXISTS `sedan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sedan` (
  `idsuv` int NOT NULL,
  `make` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `year` int NOT NULL,
  `drivetrain` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `transmission` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `mileage` int NOT NULL,
  `engine` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `condition` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `color` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `vin` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idsuv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sedan`
--

LOCK TABLES `sedan` WRITE;
/*!40000 ALTER TABLE `sedan` DISABLE KEYS */;
/*!40000 ALTER TABLE `sedan` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-17 12:34:42
