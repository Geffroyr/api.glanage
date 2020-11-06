-- MySQL dump 10.13  Distrib 8.0.22, for Linux (x86_64)
--
-- Host: localhost    Database: glanage
-- ------------------------------------------------------
-- Server version	8.0.22-0ubuntu0.20.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `legume`
--

DROP TABLE IF EXISTS `legume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `legume` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legume`
--

LOCK TABLES `legume` WRITE;
/*!40000 ALTER TABLE `legume` DISABLE KEYS */;
INSERT INTO `legume` VALUES (1,'Abricot'),(2,'Ail'),(3,'Airelle'),(4,'Amande'),(5,'Ananas'),(6,'Artichaut'),(7,'Asperge blanche'),(8,'Asperge verte'),(9,'Aubergine'),(10,'Avocat'),(11,'Banane'),(12,'Bette'),(13,'Betterave rouge'),(14,'Brocoli'),(15,'Carotte'),(16,'Cassis'),(17,'Catalonia'),(18,'Céleri'),(19,'Céleri branche'),(20,'Céleri rave'),(21,'Cerise'),(22,'Châtaigne'),(23,'Chou blanc'),(24,'Chou de Bruxelles'),(25,'Chou frisé'),(26,'Chou Romanesco'),(27,'Chou rouge'),(28,'Chou-chinois'),(29,'Chou-fleur'),(30,'Chou-rave'),(31,'Cimadi Rapa'),(32,'Citron'),(33,'Citrouille'),(34,'Clémentine'),(35,'Coing'),(36,'Concombre'),(37,'Courge'),(38,'Courgette'),(39,'Cresson'),(40,'Datte'),(41,'Endive'),(42,'Epinard'),(43,'Fenouil'),(44,'Figue fraîche'),(45,'Fraise'),(46,'Fraise des bois'),(47,'Framboise'),(48,'Fruit de la passion'),(49,'Grenade'),(50,'Groseille'),(51,'Groseille à maquereau'),(52,'Haricot'),(53,'Kaki'),(54,'Kiwi'),(55,'Kumquat'),(56,'Laitue romaine'),(57,'Litchi'),(58,'Mâche'),(59,'Maïs'),(60,'Mandarine'),(61,'Mangue'),(62,'Marron'),(63,'Melon'),(64,'Mirabelle'),(65,'Mûre'),(66,'Myrtille'),(67,'Navet'),(68,'Nectarine'),(69,'Noisette'),(70,'Noix'),(71,'Oignon'),(72,'Orange'),(73,'Orange sanguine'),(74,'Pamplemousse'),(75,'Panais'),(76,'Papaye'),(77,'Pastèque'),(78,'Pâtisson'),(79,'Pêche'),(80,'Petit oignon blanc'),(81,'Petit pois'),(82,'Poire'),(83,'Poireau'),(84,'Pois mange-tout'),(85,'Poivron'),(86,'Pomme'),(87,'Pomme de terre'),(88,'Potimarron'),(89,'Potiron'),(90,'Prune'),(91,'Quetsche'),(92,'Radis'),(93,'Radis long'),(94,'Raisin'),(95,'Reine-claude'),(96,'Rhubarbe'),(97,'Salsifis'),(98,'Tomate'),(99,'Tomate charnue'),(100,'Tomate Peretti'),(101,'Topinambour');
/*!40000 ALTER TABLE `legume` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-11-06 19:28:24
