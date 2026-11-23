-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: All_InOne_By_Eva
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `anniversaires_traites`
--

DROP TABLE IF EXISTS `anniversaires_traites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anniversaires_traites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `annee` smallint(5) unsigned NOT NULL,
  `offre` varchar(255) DEFAULT NULL,
  `traite_par` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `anniversaires_traites_client_id_annee_unique` (`client_id`,`annee`),
  KEY `anniversaires_traites_traite_par_foreign` (`traite_par`),
  CONSTRAINT `anniversaires_traites_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `anniversaires_traites_traite_par_foreign` FOREIGN KEY (`traite_par`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anniversaires_traites`
--

LOCK TABLES `anniversaires_traites` WRITE;
/*!40000 ALTER TABLE `anniversaires_traites` DISABLE KEYS */;
/*!40000 ALTER TABLE `anniversaires_traites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campagnes_messages`
--

DROP TABLE IF EXISTS `campagnes_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campagnes_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `modele_message_id` bigint(20) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `statut_suivi` varchar(255) NOT NULL DEFAULT 'preparee',
  `cree_par` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campagnes_messages_modele_message_id_foreign` (`modele_message_id`),
  KEY `campagnes_messages_cree_par_foreign` (`cree_par`),
  CONSTRAINT `campagnes_messages_cree_par_foreign` FOREIGN KEY (`cree_par`) REFERENCES `users` (`id`),
  CONSTRAINT `campagnes_messages_modele_message_id_foreign` FOREIGN KEY (`modele_message_id`) REFERENCES `modeles_messages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campagnes_messages`
--

LOCK TABLES `campagnes_messages` WRITE;
/*!40000 ALTER TABLE `campagnes_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `campagnes_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_depenses`
--

DROP TABLE IF EXISTS `categories_depenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_depenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_depenses_nom_unique` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_depenses`
--

LOCK TABLES `categories_depenses` WRITE;
/*!40000 ALTER TABLE `categories_depenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories_depenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_services`
--

DROP TABLE IF EXISTS `categories_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `ordre` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_services_nom_unique` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_services`
--

LOCK TABLES `categories_services` WRITE;
/*!40000 ALTER TABLE `categories_services` DISABLE KEYS */;
INSERT INTO `categories_services` VALUES (5,'coiffure homme',1,'2026-11-09 15:12:37','2026-11-09 15:12:37'),(6,'coiffure femme',2,'2026-11-09 15:13:02','2026-11-09 15:13:02'),(7,'coiffure enfants',3,'2026-11-09 15:13:27','2026-11-09 15:13:27'),(8,'tresse',4,'2026-11-09 15:14:00','2026-11-09 15:14:00'),(9,'colorations',5,'2026-11-09 15:14:13','2026-11-09 15:14:13'),(10,'soins capillaire',6,'2026-11-09 15:14:28','2026-11-09 15:14:28'),(11,'massage',7,'2026-11-09 15:14:56','2026-11-09 15:14:56'),(12,'manicure',8,'2026-11-09 15:15:07','2026-11-09 15:15:07'),(13,'pedicure',9,'2026-11-09 15:15:18','2026-11-09 15:15:18'),(14,'soins du visage',10,'2026-11-09 15:15:39','2026-11-09 15:15:39'),(15,'forfaits combiner',11,'2026-11-09 15:16:07','2026-11-09 15:16:07');
/*!40000 ALTER TABLE `categories_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `preference_communication` varchar(20) NOT NULL DEFAULT 'whatsapp',
  `consentement_promo` tinyint(1) NOT NULL DEFAULT 0,
  `consentement_modifie_le` timestamp NULL DEFAULT NULL,
  `preferences_prestations` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_telephone_unique` (`telephone`),
  KEY `clients_nom_prenom_index` (`nom`,`prenom`),
  KEY `clients_statut_index` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Mame Diarra','Sy','221748802298',NULL,'hosea.wilkinson@example.org',NULL,'2025-11-13','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(2,'Rokhaya','Sy','221791565672',NULL,NULL,'2006-01-08','2026-01-09','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(3,'Mariama','Gueye','221772900707',NULL,'adrain36@example.org','1995-10-26','2026-05-01','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(4,'Astou','Niang','221773111611',NULL,NULL,'1968-04-09','2026-04-04','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(5,'Ibrahima','Seck','221734294837',NULL,NULL,NULL,'2026-01-07','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(6,'Babacar','Sow','221717383425',NULL,'sfadel@example.com','1982-09-08','2026-01-06','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(7,'Cheikh','Sow','221728856678',NULL,NULL,'1989-06-19','2025-11-26','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(8,'Mamadou','Gueye','221702219612',NULL,NULL,'1973-06-26','2026-01-05','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(9,'Ousmane','Cissé','221784352823',NULL,NULL,'1996-10-28','2026-03-07','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(10,'Coumba','Seck','221747546298',NULL,'vicenta.anderson@example.org','1980-01-13','2026-04-15','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(11,'Aïssatou','Sy','221768328416',NULL,NULL,'1978-07-31','2026-01-12','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(12,'Mariama','Ndiaye','221722765782',NULL,NULL,'1987-08-30','2025-12-16','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(13,'Aïssatou','Sy','221731030468',NULL,'elinore10@example.com','1970-05-07','2026-03-02','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(14,'Mamadou','Niang','221745409785',NULL,NULL,'1967-06-30','2026-02-17','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(15,'Seynabou','Sow','221770901017',NULL,NULL,'1983-03-10','2026-03-06','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(16,'Ousmane','Sy','221762369869',NULL,NULL,'1997-08-24','2026-01-27','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(17,'Adama','Faye','221788610617',NULL,NULL,'1980-03-09','2026-02-10','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(18,'Ndeye','Kane','221791848706',NULL,NULL,'1975-01-07','2025-11-05','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(19,'Adama','Mbaye','221767478747',NULL,'baylee40@example.com','2002-02-15','2025-11-21','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(20,'Ndeye','Seck','221752876313',NULL,'joberbrunner@example.org',NULL,'2026-01-19','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(21,'Ousmane','Fall','221758065566',NULL,NULL,'2008-03-19','2026-02-05','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(22,'Aïssatou','Diallo','221709250458',NULL,NULL,'1997-12-20','2026-01-26','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(23,'Aïssatou','Thiam','221729394292',NULL,NULL,'1981-11-14','2026-02-05','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(24,'Astou','Diop','221766989258',NULL,'collins.emelia@example.net','1994-05-18','2026-03-01','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(25,'Babacar','Gueye','221735727708',NULL,NULL,'1980-10-28','2026-03-27','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(26,'Ousmane','Kane','221780079392',NULL,NULL,NULL,'2025-12-10','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(27,'Rokhaya','Cissé','221701873820',NULL,NULL,'1983-02-23','2026-02-06','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(28,'Rokhaya','Seck','221773395664',NULL,NULL,'2009-10-05','2026-01-10','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(29,'Ibrahima','Niang','221726885830',NULL,'emohr@example.com',NULL,'2025-11-17','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(30,'Ibrahima','Diop','221717779355',NULL,NULL,'1970-01-30','2026-02-12','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(31,'Rokhaya','Mbaye','221714414131',NULL,NULL,'1974-10-29','2026-03-26','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(32,'Khady','Thiam','221753969363',NULL,NULL,'1980-05-19','2026-04-20','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(33,'Cheikh','Sy','221724254441',NULL,NULL,NULL,'2026-03-20','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(34,'Aïssatou','Ndiaye','221793358537',NULL,NULL,'1982-12-30','2026-02-21','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(35,'Mamadou','Gueye','221750257911',NULL,NULL,'1990-04-02','2026-01-14','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(36,'Adama','Sy','221788565991',NULL,'pierce53@example.org','1977-10-06','2026-04-26','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(37,'Ibrahima','Diallo','221716021820',NULL,'welch.tianna@example.org','1980-10-25','2026-10-15','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(38,'Khady','Fall','221753171301',NULL,NULL,'1979-09-26','2026-10-29','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(39,'Babacar','Diallo','221752879530',NULL,NULL,'1989-07-21','2026-11-01','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04'),(40,'Ibrahima','Fall','221753496390',NULL,NULL,'2004-06-16','2026-10-25','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-03 18:37:04','2026-11-03 18:37:04');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depenses`
--

DROP TABLE IF EXISTS `depenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categorie_depense_id` bigint(20) unsigned NOT NULL,
  `montant` int(10) unsigned NOT NULL,
  `date_depense` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `justificatif_path` varchar(255) DEFAULT NULL,
  `enregistre_par` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `depenses_categorie_depense_id_foreign` (`categorie_depense_id`),
  KEY `depenses_enregistre_par_foreign` (`enregistre_par`),
  KEY `depenses_date_depense_index` (`date_depense`),
  CONSTRAINT `depenses_categorie_depense_id_foreign` FOREIGN KEY (`categorie_depense_id`) REFERENCES `categories_depenses` (`id`),
  CONSTRAINT `depenses_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depenses`
--

LOCK TABLES `depenses` WRITE;
/*!40000 ALTER TABLE `depenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `depenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `destinataires_campagnes`
--

DROP TABLE IF EXISTS `destinataires_campagnes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `destinataires_campagnes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campagne_message_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `message_personnalise` text NOT NULL,
  `ouvert_le` timestamp NULL DEFAULT NULL,
  `suivi_manuel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `destinataires_campagnes_campagne_message_id_client_id_unique` (`campagne_message_id`,`client_id`),
  KEY `destinataires_campagnes_client_id_foreign` (`client_id`),
  CONSTRAINT `destinataires_campagnes_campagne_message_id_foreign` FOREIGN KEY (`campagne_message_id`) REFERENCES `campagnes_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `destinataires_campagnes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destinataires_campagnes`
--

LOCK TABLES `destinataires_campagnes` WRITE;
/*!40000 ALTER TABLE `destinataires_campagnes` DISABLE KEYS */;
/*!40000 ALTER TABLE `destinataires_campagnes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilites`
--

DROP TABLE IF EXISTS `disponibilites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disponibilites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employe_id` bigint(20) unsigned NOT NULL,
  `jour_semaine` tinyint(3) unsigned NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disponibilites_employe_id_jour_semaine_unique` (`employe_id`,`jour_semaine`),
  CONSTRAINT `disponibilites_employe_id_foreign` FOREIGN KEY (`employe_id`) REFERENCES `employes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilites`
--

LOCK TABLES `disponibilites` WRITE;
/*!40000 ALTER TABLE `disponibilites` DISABLE KEYS */;
INSERT INTO `disponibilites` VALUES (1,1,1,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(2,1,2,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(3,1,3,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(4,1,4,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(5,1,5,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(6,1,6,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(7,2,1,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(8,2,2,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(9,2,3,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(10,2,4,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(11,2,5,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(12,2,6,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(13,3,1,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(14,3,2,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(15,3,3,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(16,3,4,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(17,3,5,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04'),(18,3,6,'08:00:00','22:00:00','2026-11-03 18:37:04','2026-11-03 18:37:04');
/*!40000 ALTER TABLE `disponibilites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employe_service`
--

DROP TABLE IF EXISTS `employe_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employe_service` (
  `employe_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`employe_id`,`service_id`),
  KEY `employe_service_service_id_foreign` (`service_id`),
  CONSTRAINT `employe_service_employe_id_foreign` FOREIGN KEY (`employe_id`) REFERENCES `employes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employe_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employe_service`
--

LOCK TABLES `employe_service` WRITE;
/*!40000 ALTER TABLE `employe_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `employe_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employes`
--

DROP TABLE IF EXISTS `employes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `fonction` varchar(255) NOT NULL,
  `specialites` varchar(255) DEFAULT NULL,
  `taux_commission` decimal(5,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employes_user_id_unique` (`user_id`),
  CONSTRAINT `employes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employes`
--

LOCK TABLES `employes` WRITE;
/*!40000 ALTER TABLE `employes` DISABLE KEYS */;
INSERT INTO `employes` VALUES (1,3,'Fatou','Sarr','771997415','Coiffeuse','Tresses, coloration',10.00,1,'2026-11-03 18:37:04','2026-11-03 18:37:04'),(2,NULL,'Moussa','Ba','779083202','Coiffeur barbier','Coupes hommes et enfants',10.00,1,'2026-11-03 18:37:04','2026-11-03 18:37:04'),(3,NULL,'Aminata','Fall','772401942','Esthéticienne','Massages, soins du visage, onglerie',10.00,1,'2026-11-03 18:37:04','2026-11-03 18:37:04');
/*!40000 ALTER TABLE `employes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forfait_service`
--

DROP TABLE IF EXISTS `forfait_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forfait_service` (
  `forfait_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`forfait_id`,`service_id`),
  KEY `forfait_service_service_id_foreign` (`service_id`),
  CONSTRAINT `forfait_service_forfait_id_foreign` FOREIGN KEY (`forfait_id`) REFERENCES `forfaits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forfait_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forfait_service`
--

LOCK TABLES `forfait_service` WRITE;
/*!40000 ALTER TABLE `forfait_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `forfait_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forfaits`
--

DROP TABLE IF EXISTS `forfaits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forfaits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forfaits`
--

LOCK TABLES `forfaits` WRITE;
/*!40000 ALTER TABLE `forfaits` DISABLE KEYS */;
/*!40000 ALTER TABLE `forfaits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historique_actions`
--

DROP TABLE IF EXISTS `historique_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historique_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `auditable_type` varchar(255) DEFAULT NULL,
  `auditable_id` bigint(20) unsigned DEFAULT NULL,
  `anciennes_valeurs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`anciennes_valeurs`)),
  `nouvelles_valeurs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nouvelles_valeurs`)),
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `historique_actions_user_id_foreign` (`user_id`),
  KEY `historique_actions_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `historique_actions_created_at_index` (`created_at`),
  CONSTRAINT `historique_actions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historique_actions`
--

LOCK TABLES `historique_actions` WRITE;
/*!40000 ALTER TABLE `historique_actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `historique_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_all_in_one_eva_schema',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modeles_messages`
--

DROP TABLE IF EXISTS `modeles_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modeles_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modeles_messages_type_unique` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modeles_messages`
--

LOCK TABLES `modeles_messages` WRITE;
/*!40000 ALTER TABLE `modeles_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `modeles_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modes_paiement`
--

DROP TABLE IF EXISTS `modes_paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modes_paiement` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modes_paiement_libelle_unique` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modes_paiement`
--

LOCK TABLES `modes_paiement` WRITE;
/*!40000 ALTER TABLE `modes_paiement` DISABLE KEYS */;
/*!40000 ALTER TABLE `modes_paiement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mouvements_points`
--

DROP TABLE IF EXISTS `mouvements_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mouvements_points` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `paiement_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `points` int(11) NOT NULL,
  `motif` varchar(255) NOT NULL,
  `enregistre_par` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `mouvements_points_paiement_id_unique` (`paiement_id`),
  KEY `mouvements_points_client_id_foreign` (`client_id`),
  KEY `mouvements_points_enregistre_par_foreign` (`enregistre_par`),
  CONSTRAINT `mouvements_points_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `mouvements_points_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`),
  CONSTRAINT `mouvements_points_paiement_id_foreign` FOREIGN KEY (`paiement_id`) REFERENCES `paiements` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mouvements_points`
--

LOCK TABLES `mouvements_points` WRITE;
/*!40000 ALTER TABLE `mouvements_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `mouvements_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mouvements_stock`
--

DROP TABLE IF EXISTS `mouvements_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mouvements_stock` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `produit_id` bigint(20) unsigned NOT NULL,
  `type` varchar(20) NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `stock_apres` decimal(10,2) NOT NULL,
  `motif` varchar(255) NOT NULL,
  `rendez_vous_id` bigint(20) unsigned DEFAULT NULL,
  `enregistre_par` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mouvements_stock_produit_id_foreign` (`produit_id`),
  KEY `mouvements_stock_rendez_vous_id_foreign` (`rendez_vous_id`),
  KEY `mouvements_stock_enregistre_par_foreign` (`enregistre_par`),
  KEY `mouvements_stock_created_at_index` (`created_at`),
  CONSTRAINT `mouvements_stock_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`),
  CONSTRAINT `mouvements_stock_produit_id_foreign` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`),
  CONSTRAINT `mouvements_stock_rendez_vous_id_foreign` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mouvements_stock`
--

LOCK TABLES `mouvements_stock` WRITE;
/*!40000 ALTER TABLE `mouvements_stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `mouvements_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paiements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `rendez_vous_id` bigint(20) unsigned DEFAULT NULL,
  `mode_paiement_id` bigint(20) unsigned NOT NULL,
  `montant_total` int(10) unsigned NOT NULL,
  `montant_paye` int(10) unsigned NOT NULL,
  `reste_a_payer` int(10) unsigned NOT NULL,
  `paye_le` datetime NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `enregistre_par` bigint(20) unsigned NOT NULL,
  `annule_le` timestamp NULL DEFAULT NULL,
  `annule_par` bigint(20) unsigned DEFAULT NULL,
  `motif_annulation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paiements_client_id_foreign` (`client_id`),
  KEY `paiements_rendez_vous_id_foreign` (`rendez_vous_id`),
  KEY `paiements_mode_paiement_id_foreign` (`mode_paiement_id`),
  KEY `paiements_enregistre_par_foreign` (`enregistre_par`),
  KEY `paiements_annule_par_foreign` (`annule_par`),
  KEY `paiements_paye_le_index` (`paye_le`),
  KEY `paiements_annule_le_index` (`annule_le`),
  CONSTRAINT `paiements_annule_par_foreign` FOREIGN KEY (`annule_par`) REFERENCES `users` (`id`),
  CONSTRAINT `paiements_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `paiements_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`),
  CONSTRAINT `paiements_mode_paiement_id_foreign` FOREIGN KEY (`mode_paiement_id`) REFERENCES `modes_paiement` (`id`),
  CONSTRAINT `paiements_rendez_vous_id_foreign` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements`
--

LOCK TABLES `paiements` WRITE;
/*!40000 ALTER TABLE `paiements` DISABLE KEYS */;
/*!40000 ALTER TABLE `paiements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametres_salon`
--

DROP TABLE IF EXISTS `parametres_salon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametres_salon` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cle` varchar(255) NOT NULL,
  `valeur` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parametres_salon_cle_unique` (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametres_salon`
--

LOCK TABLES `parametres_salon` WRITE;
/*!40000 ALTER TABLE `parametres_salon` DISABLE KEYS */;
/*!40000 ALTER TABLE `parametres_salon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produits`
--

DROP TABLE IF EXISTS `produits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `quantite` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unite` varchar(20) NOT NULL,
  `prix_achat` int(10) unsigned NOT NULL,
  `prix_vente` int(10) unsigned DEFAULT NULL,
  `seuil_alerte` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fournisseur` varchar(255) DEFAULT NULL,
  `date_entree` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produits`
--

LOCK TABLES `produits` WRITE;
/*!40000 ALTER TABLE `produits` DISABLE KEYS */;
/*!40000 ALTER TABLE `produits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recompenses`
--

DROP TABLE IF EXISTS `recompenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recompenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `type` varchar(30) NOT NULL,
  `seuil_points` int(10) unsigned NOT NULL,
  `valeur` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recompenses`
--

LOCK TABLES `recompenses` WRITE;
/*!40000 ALTER TABLE `recompenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `recompenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rendez_vous`
--

DROP TABLE IF EXISTS `rendez_vous`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rendez_vous` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `employe_id` bigint(20) unsigned NOT NULL,
  `debut` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `zone` varchar(20) NOT NULL DEFAULT 'mixte',
  `prix_prevu` int(10) unsigned NOT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'en_attente',
  `commentaire` text DEFAULT NULL,
  `cree_par` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rendez_vous_client_id_foreign` (`client_id`),
  KEY `rendez_vous_cree_par_foreign` (`cree_par`),
  KEY `rendez_vous_employe_id_debut_fin_index` (`employe_id`,`debut`,`fin`),
  KEY `rendez_vous_statut_index` (`statut`),
  CONSTRAINT `rendez_vous_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `rendez_vous_cree_par_foreign` FOREIGN KEY (`cree_par`) REFERENCES `users` (`id`),
  CONSTRAINT `rendez_vous_employe_id_foreign` FOREIGN KEY (`employe_id`) REFERENCES `employes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendez_vous`
--

LOCK TABLES `rendez_vous` WRITE;
/*!40000 ALTER TABLE `rendez_vous` DISABLE KEYS */;
/*!40000 ALTER TABLE `rendez_vous` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rendez_vous_service`
--

DROP TABLE IF EXISTS `rendez_vous_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rendez_vous_service` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rendez_vous_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned DEFAULT NULL,
  `forfait_id` bigint(20) unsigned DEFAULT NULL,
  `libelle` varchar(255) NOT NULL,
  `prix` int(10) unsigned NOT NULL,
  `duree_minutes` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rendez_vous_service_rendez_vous_id_foreign` (`rendez_vous_id`),
  KEY `rendez_vous_service_service_id_foreign` (`service_id`),
  KEY `rendez_vous_service_forfait_id_foreign` (`forfait_id`),
  CONSTRAINT `rendez_vous_service_forfait_id_foreign` FOREIGN KEY (`forfait_id`) REFERENCES `forfaits` (`id`),
  CONSTRAINT `rendez_vous_service_rendez_vous_id_foreign` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rendez_vous_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendez_vous_service`
--

LOCK TABLES `rendez_vous_service` WRITE;
/*!40000 ALTER TABLE `rendez_vous_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `rendez_vous_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Administrateur','2026-11-03 18:36:20','2026-11-03 18:36:20'),(2,'receptionniste','Réceptionniste','2026-11-03 18:36:21','2026-11-03 18:36:21'),(3,'professionnel','Professionnel','2026-11-03 18:36:24','2026-11-03 18:36:24');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_produit`
--

DROP TABLE IF EXISTS `service_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_produit` (
  `service_id` bigint(20) unsigned NOT NULL,
  `produit_id` bigint(20) unsigned NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  PRIMARY KEY (`service_id`,`produit_id`),
  KEY `service_produit_produit_id_foreign` (`produit_id`),
  CONSTRAINT `service_produit_produit_id_foreign` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_produit_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_produit`
--

LOCK TABLES `service_produit` WRITE;
/*!40000 ALTER TABLE `service_produit` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_produit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categorie_service_id` bigint(20) unsigned NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` int(10) unsigned NOT NULL,
  `duree_minutes` smallint(5) unsigned NOT NULL,
  `zone` varchar(20) NOT NULL DEFAULT 'mixte',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_categorie_service_id_foreign` (`categorie_service_id`),
  KEY `services_is_active_index` (`is_active`),
  CONSTRAINT `services_categorie_service_id_foreign` FOREIGN KEY (`categorie_service_id`) REFERENCES `categories_services` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (5,10,'eehvdjhee','sdfghjklm',100000,111,'mixte',1,'2026-11-09 16:06:00','2026-11-09 16:08:40'),(6,5,'thiiap sa feeling','zertyuiopkjvc',50000,12000,'mixte',1,'2026-11-09 16:06:52','2026-11-09 16:06:52');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('PBrqOXJfS7XPZm3mhXRYLGfHgIN0kZjAdXnYLY5j',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ3Mks1N2ZoSjhmMkc0YlEybDJ3QmZQT1R2Uk5vd0V3VFdnRmxnbnI0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3ByZXN0YXRpb25zIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9jbGllbnRzIiwicm91dGUiOiJjbGllbnRzLmluZGV4In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6IjdjNTYxMjlhMDdkNDE1ZDc4ZWE1MDFjYzhkMGJlOTE4NjU3NTViODhkMzY3NzZlMjVkMDIzZjUwMWE2MjBmMWYifQ==',1794243610);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Eva Diallo','admin@allinone.test',NULL,'$2y$12$r5OP2SZrnSxVRCGWoH7e8eh5HtCol0Wkk5cPiKA3E0Yu3y6SacxNa',1,NULL,'2026-11-03 18:37:03','2026-11-03 18:37:03'),(2,2,'Awa Ndiaye','accueil@allinone.test',NULL,'$2y$12$uVgrCaPZeY7LEIWyp4n.4u7Rgy79noyNALx3e32kGTSB4uHRTJn9m',1,NULL,'2026-11-03 18:37:03','2026-11-03 18:37:03'),(3,3,'Fatou Sarr','fatou@allinone.test',NULL,'$2y$12$XtDrOL0e.FfKTqzJcOjJau5W2zAIP6dajl.ZlWwe2uvocecuH3WIm',1,NULL,'2026-11-03 18:37:04','2026-11-03 18:37:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisations_recompenses`
--

DROP TABLE IF EXISTS `utilisations_recompenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisations_recompenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `recompense_id` bigint(20) unsigned NOT NULL,
  `mouvement_points_id` bigint(20) unsigned NOT NULL,
  `enregistre_par` bigint(20) unsigned NOT NULL,
  `utilisee_le` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `utilisations_recompenses_client_id_foreign` (`client_id`),
  KEY `utilisations_recompenses_recompense_id_foreign` (`recompense_id`),
  KEY `utilisations_recompenses_mouvement_points_id_foreign` (`mouvement_points_id`),
  KEY `utilisations_recompenses_enregistre_par_foreign` (`enregistre_par`),
  CONSTRAINT `utilisations_recompenses_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `utilisations_recompenses_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`),
  CONSTRAINT `utilisations_recompenses_mouvement_points_id_foreign` FOREIGN KEY (`mouvement_points_id`) REFERENCES `mouvements_points` (`id`),
  CONSTRAINT `utilisations_recompenses_recompense_id_foreign` FOREIGN KEY (`recompense_id`) REFERENCES `recompenses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisations_recompenses`
--

LOCK TABLES `utilisations_recompenses` WRITE;
/*!40000 ALTER TABLE `utilisations_recompenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `utilisations_recompenses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-09 17:10:32
