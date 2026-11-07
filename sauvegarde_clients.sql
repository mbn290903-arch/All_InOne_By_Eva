-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: all_in_one_eva
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
INSERT INTO `cache` VALUES ('laravel-cache-parametres_salon','a:4:{s:13:\"pause_minutes\";s:1:\"0\";s:23:\"fidelite_fcfa_par_point\";s:4:\"1000\";s:19:\"inactivite_semaines\";s:1:\"6\";s:18:\"offre_anniversaire\";N;}',2109322039);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_depenses`
--

LOCK TABLES `categories_depenses` WRITE;
/*!40000 ALTER TABLE `categories_depenses` DISABLE KEYS */;
INSERT INTO `categories_depenses` VALUES (1,'Achat de produits','2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'Loyer','2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,'Électricité','2026-11-06 10:47:17','2026-11-06 10:47:17'),(4,'Eau','2026-11-06 10:47:17','2026-11-06 10:47:17'),(5,'Salaires et commissions','2026-11-06 10:47:17','2026-11-06 10:47:17'),(6,'Entretien du matériel','2026-11-06 10:47:17','2026-11-06 10:47:17'),(7,'Autres dépenses','2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_services`
--

LOCK TABLES `categories_services` WRITE;
/*!40000 ALTER TABLE `categories_services` DISABLE KEYS */;
INSERT INTO `categories_services` VALUES (1,'Coiffure hommes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'Coiffure femmes',2,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,'Coiffure enfants',3,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(4,'Tresses',4,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(5,'Coloration',5,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(6,'Soins capillaires',6,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(7,'Massages',7,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(8,'Manucure',8,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(9,'Pédicure',9,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(10,'Soins du visage',10,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(11,'Forfaits combinés',11,'2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Mamadou','Mbaye','221780743415',NULL,NULL,'2010-03-09','2026-04-17','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(2,'Ousmane','Faye','221791910547',NULL,NULL,NULL,'2026-03-30','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(3,'Mame Diarra','Seck','221711441055',NULL,NULL,'1979-01-24','2026-01-19','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(4,'Khady','Sow','221734420256',NULL,'dolly.dickinson@example.net','1993-08-25','2026-01-13','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(5,'Seynabou','Gueye','221708148610',NULL,NULL,'2010-10-28','2025-11-30','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(6,'Mamadou','Sow','221749346552',NULL,'jortiz@example.org','1981-08-24','2025-12-29','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(7,'Mame Diarra','Faye','221729832887',NULL,'orland.bogan@example.net','1999-12-15','2026-04-04','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(8,'Cheikh','Niang','221748415266',NULL,'luis21@example.net','1982-04-18','2026-02-03','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(9,'Astou','Cissé','221790270362',NULL,NULL,'1986-09-14','2025-12-26','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(10,'Seynabou','Sy','221798236108',NULL,'emilio.macejkovic@example.net','2004-09-13','2026-03-10','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(11,'Babacar','Kane','221761261854',NULL,NULL,'1993-11-26','2025-11-17','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(12,'Cheikh','Sy','221709207650',NULL,NULL,'2007-06-16','2026-01-29','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(13,'Cheikh','Sow','221791754802',NULL,NULL,'1988-04-28','2026-01-03','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(14,'Mariama','Ndiaye','221701681886',NULL,'rbecker@example.net','1966-11-26','2026-03-07','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(15,'Mariama','Diop','221742719353',NULL,NULL,'1993-05-14','2026-02-09','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(16,'Astou','Thiam','221793455853',NULL,NULL,'1997-02-26','2026-02-01','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(17,'Cheikh','Niang','221778762897',NULL,NULL,'1981-02-12','2025-12-12','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(18,'Mame Diarra','Seck','221714637428',NULL,NULL,'2006-11-17','2026-01-16','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(19,'Aïssatou','Fall','221779927616',NULL,'uriah.kautzer@example.net','2008-10-22','2026-03-12','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(20,'Binta','Cissé','221720707042',NULL,'melvin.waters@example.com','1991-08-06','2025-11-23','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(21,'Coumba','Cissé','221766510290',NULL,'kian.nikolaus@example.net','1990-12-26','2026-01-27','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(22,'Mame Diarra','Mbaye','221704185547',NULL,NULL,'1968-04-13','2026-03-06','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(23,'Adama','Ndiaye','221767931847',NULL,NULL,NULL,'2026-02-15','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(24,'Khady','Thiam','221780294662',NULL,NULL,'2009-09-22','2026-03-15','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(25,'Cheikh','Sow','221742153049',NULL,NULL,NULL,'2026-03-05','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(26,'Babacar','Ndiaye','221796279584',NULL,NULL,'1985-01-10','2026-02-15','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(27,'Ibrahima','Kane','221795904673',NULL,NULL,'1968-12-30','2026-01-24','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(28,'Adama','Kane','221786691454',NULL,NULL,'1972-08-12','2025-11-17','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(29,'Coumba','Kane','221739873857',NULL,NULL,'1969-02-19','2026-04-19','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(30,'Mamadou','Diallo','221744648674',NULL,NULL,NULL,'2026-01-27','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(31,'Aïssatou','Thiam','221736389392',NULL,NULL,'1986-10-17','2026-02-12','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(32,'Ousmane','Seck','221783572169',NULL,NULL,'1991-08-30','2026-01-03','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(33,'Ousmane','Cissé','221736915463',NULL,NULL,'1992-01-27','2026-02-04','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(34,'Ndeye','Sow','221761450970',NULL,'willy.lowe@example.net','1973-02-11','2026-03-02','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(35,'Cheikh','Diallo','221796169580',NULL,NULL,'2008-08-13','2026-02-24','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(36,'Adama','Kane','221723935709',NULL,NULL,'1992-11-30','2025-11-29','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(37,'Coumba','Kane','221701555457',NULL,'umayert@example.net','2003-10-01','2026-10-31','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(38,'Babacar','Ndiaye','221763688759',NULL,NULL,'1969-10-02','2026-10-27','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(39,'Ousmane','Kane','221714427790',NULL,NULL,'1986-02-07','2026-10-28','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(40,'Mamadou','Thiam','221781902513',NULL,NULL,'2010-03-16','2026-10-29','whatsapp',1,NULL,NULL,NULL,'actif','2026-11-06 10:47:19','2026-11-06 10:47:19'),(41,'Kara','Niamana','707203310','707203310',NULL,'2025-10-10','2026-11-06','whatsapp',0,NULL,NULL,NULL,'actif','2026-11-06 11:10:27','2026-11-06 11:10:27');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depenses`
--

LOCK TABLES `depenses` WRITE;
/*!40000 ALTER TABLE `depenses` DISABLE KEYS */;
INSERT INTO `depenses` VALUES (1,2,250000,'2026-11-05','Loyer du local',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(2,3,38000,'2026-11-13','Facture Senelec',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(3,2,250000,'2026-10-05','Loyer du local',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(4,3,56000,'2026-10-13','Facture Senelec',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(5,2,250000,'2026-09-05','Loyer du local',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(6,3,46000,'2026-09-13','Facture Senelec',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(7,2,250000,'2026-08-05','Loyer du local',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(8,3,45000,'2026-08-13','Facture Senelec',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(9,2,250000,'2026-07-05','Loyer du local',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(10,3,56000,'2026-07-13','Facture Senelec',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(11,2,250000,'2026-06-05','Loyer du local',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(12,3,38000,'2026-06-13','Facture Senelec',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21');
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
INSERT INTO `disponibilites` VALUES (1,1,1,'08:00:00','22:00:00','2026-11-06 10:47:18','2026-11-06 10:47:18'),(2,1,2,'08:00:00','22:00:00','2026-11-06 10:47:18','2026-11-06 10:47:18'),(3,1,3,'08:00:00','22:00:00','2026-11-06 10:47:18','2026-11-06 10:47:18'),(4,1,4,'08:00:00','22:00:00','2026-11-06 10:47:18','2026-11-06 10:47:18'),(5,1,5,'08:00:00','22:00:00','2026-11-06 10:47:18','2026-11-06 10:47:18'),(6,1,6,'08:00:00','22:00:00','2026-11-06 10:47:18','2026-11-06 10:47:18'),(7,2,1,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(8,2,2,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(9,2,3,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(10,2,4,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(11,2,5,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(12,2,6,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(13,3,1,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(14,3,2,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(15,3,3,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(16,3,4,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(17,3,5,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19'),(18,3,6,'08:00:00','22:00:00','2026-11-06 10:47:19','2026-11-06 10:47:19');
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
INSERT INTO `employe_service` VALUES (1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(2,1),(2,2),(2,5),(3,13),(3,14),(3,15),(3,16),(3,17),(3,18),(3,19);
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
INSERT INTO `employes` VALUES (1,3,'Fatou','Sarr','773066008','Coiffeuse','Tresses, coloration',10.00,1,'2026-11-06 10:47:18','2026-11-06 10:47:18'),(2,NULL,'Moussa','Ba','774566722','Coiffeur barbier','Coupes hommes et enfants',10.00,1,'2026-11-06 10:47:18','2026-11-06 10:47:18'),(3,NULL,'Aminata','Fall','772119996','Esthéticienne','Massages, soins du visage, onglerie',10.00,1,'2026-11-06 10:47:19','2026-11-06 10:47:19');
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
INSERT INTO `forfait_service` VALUES (1,13),(1,18),(2,15),(2,17);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forfaits`
--

LOCK TABLES `forfaits` WRITE;
/*!40000 ALTER TABLE `forfaits` DISABLE KEYS */;
INSERT INTO `forfaits` VALUES (1,'Duo détente','Massage relaxant et soin du visage',30000,1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'Mains & pieds','Manucure et pédicure',11000,1,'2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historique_actions`
--

LOCK TABLES `historique_actions` WRITE;
/*!40000 ALTER TABLE `historique_actions` DISABLE KEYS */;
INSERT INTO `historique_actions` VALUES (1,1,'rdv.cree','App\\Models\\RendezVous',1,NULL,'{\"debut\":\"2026-06-06T12:00:00.000000Z\",\"fin\":\"2026-06-06T12:20:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(2,1,'paiement.cree','App\\Models\\Paiement',1,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:19'),(3,1,'rdv.statut','App\\Models\\RendezVous',1,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(4,1,'rdv.cree','App\\Models\\RendezVous',2,NULL,'{\"debut\":\"2026-10-03T17:00:00.000000Z\",\"fin\":\"2026-10-03T17:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(5,1,'paiement.cree','App\\Models\\Paiement',2,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:19'),(6,1,'rdv.statut','App\\Models\\RendezVous',2,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(7,1,'rdv.cree','App\\Models\\RendezVous',3,NULL,'{\"debut\":\"2026-06-20T09:30:00.000000Z\",\"fin\":\"2026-06-20T10:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":15000}','127.0.0.1','2026-11-06 10:47:19'),(8,1,'rdv.statut','App\\Models\\RendezVous',3,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(9,1,'rdv.cree','App\\Models\\RendezVous',4,NULL,'{\"debut\":\"2026-10-20T15:30:00.000000Z\",\"fin\":\"2026-10-20T16:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(10,1,'paiement.cree','App\\Models\\Paiement',3,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:19'),(11,1,'rdv.statut','App\\Models\\RendezVous',4,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(12,1,'rdv.cree','App\\Models\\RendezVous',5,NULL,'{\"debut\":\"2026-10-31T13:00:00.000000Z\",\"fin\":\"2026-10-31T13:45:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(13,1,'paiement.cree','App\\Models\\Paiement',4,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:19'),(14,1,'rdv.statut','App\\Models\\RendezVous',5,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(15,1,'rdv.cree','App\\Models\\RendezVous',6,NULL,'{\"debut\":\"2026-10-05T12:30:00.000000Z\",\"fin\":\"2026-10-05T12:50:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(16,1,'paiement.cree','App\\Models\\Paiement',5,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:19'),(17,1,'rdv.statut','App\\Models\\RendezVous',6,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(18,1,'rdv.cree','App\\Models\\RendezVous',7,NULL,'{\"debut\":\"2026-06-22T16:30:00.000000Z\",\"fin\":\"2026-06-22T17:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:19'),(19,1,'paiement.cree','App\\Models\\Paiement',6,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:19'),(20,1,'rdv.statut','App\\Models\\RendezVous',7,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(21,1,'rdv.cree','App\\Models\\RendezVous',8,NULL,'{\"debut\":\"2026-10-29T18:00:00.000000Z\",\"fin\":\"2026-10-29T18:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:19'),(22,1,'rdv.statut','App\\Models\\RendezVous',8,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(23,1,'rdv.cree','App\\Models\\RendezVous',9,NULL,'{\"debut\":\"2026-10-22T09:30:00.000000Z\",\"fin\":\"2026-10-22T10:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:19'),(24,1,'paiement.cree','App\\Models\\Paiement',7,NULL,'{\"montant_paye\":7000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:19'),(25,1,'rdv.statut','App\\Models\\RendezVous',9,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(26,1,'rdv.cree','App\\Models\\RendezVous',10,NULL,'{\"debut\":\"2026-10-01T12:00:00.000000Z\",\"fin\":\"2026-10-01T12:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(27,1,'paiement.cree','App\\Models\\Paiement',8,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:19'),(28,1,'rdv.statut','App\\Models\\RendezVous',10,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(29,1,'rdv.cree','App\\Models\\RendezVous',11,NULL,'{\"debut\":\"2026-09-21T14:00:00.000000Z\",\"fin\":\"2026-09-21T14:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(30,1,'paiement.cree','App\\Models\\Paiement',9,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:19'),(31,1,'rdv.statut','App\\Models\\RendezVous',11,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(32,1,'rdv.cree','App\\Models\\RendezVous',12,NULL,'{\"debut\":\"2026-11-12T09:30:00.000000Z\",\"fin\":\"2026-11-12T10:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:19'),(33,1,'rdv.statut','App\\Models\\RendezVous',12,'{\"statut\":\"en_attente\"}','{\"statut\":\"confirme\"}','127.0.0.1','2026-11-06 10:47:19'),(34,1,'rdv.cree','App\\Models\\RendezVous',13,NULL,'{\"debut\":\"2026-06-02T10:30:00.000000Z\",\"fin\":\"2026-06-02T11:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(35,1,'paiement.cree','App\\Models\\Paiement',10,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:19'),(36,1,'rdv.statut','App\\Models\\RendezVous',13,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(37,1,'rdv.cree','App\\Models\\RendezVous',14,NULL,'{\"debut\":\"2026-07-02T11:00:00.000000Z\",\"fin\":\"2026-07-02T11:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:19'),(38,1,'rdv.statut','App\\Models\\RendezVous',14,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(39,1,'rdv.cree','App\\Models\\RendezVous',15,NULL,'{\"debut\":\"2026-11-12T14:30:00.000000Z\",\"fin\":\"2026-11-12T14:50:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(40,1,'rdv.cree','App\\Models\\RendezVous',16,NULL,'{\"debut\":\"2026-09-16T09:00:00.000000Z\",\"fin\":\"2026-09-16T10:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":15000}','127.0.0.1','2026-11-06 10:47:19'),(41,1,'rdv.statut','App\\Models\\RendezVous',16,'{\"statut\":\"en_attente\"}','{\"statut\":\"annule\"}','127.0.0.1','2026-11-06 10:47:19'),(42,1,'rdv.cree','App\\Models\\RendezVous',17,NULL,'{\"debut\":\"2026-06-15T14:00:00.000000Z\",\"fin\":\"2026-06-15T14:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":8000}','127.0.0.1','2026-11-06 10:47:19'),(43,1,'paiement.cree','App\\Models\\Paiement',11,NULL,'{\"montant_paye\":8000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:19'),(44,1,'rdv.statut','App\\Models\\RendezVous',17,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(45,1,'rdv.cree','App\\Models\\RendezVous',18,NULL,'{\"debut\":\"2026-11-02T16:00:00.000000Z\",\"fin\":\"2026-11-02T16:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:19'),(46,1,'paiement.cree','App\\Models\\Paiement',12,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:19'),(47,1,'rdv.statut','App\\Models\\RendezVous',18,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(48,1,'rdv.cree','App\\Models\\RendezVous',19,NULL,'{\"debut\":\"2026-09-07T17:30:00.000000Z\",\"fin\":\"2026-09-07T18:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(49,1,'paiement.cree','App\\Models\\Paiement',13,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:19'),(50,1,'rdv.statut','App\\Models\\RendezVous',19,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(51,1,'rdv.cree','App\\Models\\RendezVous',20,NULL,'{\"debut\":\"2026-10-12T10:30:00.000000Z\",\"fin\":\"2026-10-12T10:50:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(52,1,'paiement.cree','App\\Models\\Paiement',14,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:19'),(53,1,'rdv.statut','App\\Models\\RendezVous',20,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(54,1,'rdv.cree','App\\Models\\RendezVous',21,NULL,'{\"debut\":\"2026-11-10T12:30:00.000000Z\",\"fin\":\"2026-11-10T13:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":30000}','127.0.0.1','2026-11-06 10:47:19'),(55,1,'rdv.cree','App\\Models\\RendezVous',22,NULL,'{\"debut\":\"2026-07-20T17:30:00.000000Z\",\"fin\":\"2026-07-20T18:15:00.000000Z\",\"employe_id\":3,\"prix_prevu\":12000}','127.0.0.1','2026-11-06 10:47:19'),(56,1,'rdv.statut','App\\Models\\RendezVous',22,'{\"statut\":\"en_attente\"}','{\"statut\":\"annule\"}','127.0.0.1','2026-11-06 10:47:19'),(57,1,'rdv.cree','App\\Models\\RendezVous',23,NULL,'{\"debut\":\"2026-05-20T10:30:00.000000Z\",\"fin\":\"2026-05-20T11:15:00.000000Z\",\"employe_id\":3,\"prix_prevu\":8000}','127.0.0.1','2026-11-06 10:47:19'),(58,1,'paiement.cree','App\\Models\\Paiement',15,NULL,'{\"montant_paye\":8000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:19'),(59,1,'rdv.statut','App\\Models\\RendezVous',23,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(60,1,'rdv.cree','App\\Models\\RendezVous',24,NULL,'{\"debut\":\"2026-08-01T13:30:00.000000Z\",\"fin\":\"2026-08-01T13:50:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(61,1,'rdv.statut','App\\Models\\RendezVous',24,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(62,1,'rdv.cree','App\\Models\\RendezVous',25,NULL,'{\"debut\":\"2026-06-27T09:00:00.000000Z\",\"fin\":\"2026-06-27T09:45:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(63,1,'paiement.cree','App\\Models\\Paiement',16,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:19'),(64,1,'rdv.statut','App\\Models\\RendezVous',25,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(65,1,'rdv.cree','App\\Models\\RendezVous',26,NULL,'{\"debut\":\"2026-09-19T12:30:00.000000Z\",\"fin\":\"2026-09-19T13:15:00.000000Z\",\"employe_id\":3,\"prix_prevu\":12000}','127.0.0.1','2026-11-06 10:47:19'),(66,1,'paiement.cree','App\\Models\\Paiement',17,NULL,'{\"montant_paye\":12000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:19'),(67,1,'rdv.statut','App\\Models\\RendezVous',26,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(68,1,'rdv.cree','App\\Models\\RendezVous',27,NULL,'{\"debut\":\"2026-08-10T09:30:00.000000Z\",\"fin\":\"2026-08-10T10:15:00.000000Z\",\"employe_id\":3,\"prix_prevu\":12000}','127.0.0.1','2026-11-06 10:47:19'),(69,1,'paiement.cree','App\\Models\\Paiement',18,NULL,'{\"montant_paye\":12000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:19'),(70,1,'rdv.statut','App\\Models\\RendezVous',27,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(71,1,'rdv.cree','App\\Models\\RendezVous',28,NULL,'{\"debut\":\"2026-06-13T12:30:00.000000Z\",\"fin\":\"2026-06-13T13:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:19'),(72,1,'paiement.cree','App\\Models\\Paiement',19,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:19'),(73,1,'rdv.statut','App\\Models\\RendezVous',28,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(74,1,'rdv.cree','App\\Models\\RendezVous',29,NULL,'{\"debut\":\"2026-06-24T15:30:00.000000Z\",\"fin\":\"2026-06-24T15:50:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(75,1,'paiement.cree','App\\Models\\Paiement',20,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:19'),(76,1,'rdv.statut','App\\Models\\RendezVous',29,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(77,1,'rdv.cree','App\\Models\\RendezVous',30,NULL,'{\"debut\":\"2026-06-15T12:30:00.000000Z\",\"fin\":\"2026-06-15T13:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":15000}','127.0.0.1','2026-11-06 10:47:19'),(78,1,'paiement.cree','App\\Models\\Paiement',21,NULL,'{\"montant_paye\":15000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:19'),(79,1,'rdv.statut','App\\Models\\RendezVous',30,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(80,1,'rdv.cree','App\\Models\\RendezVous',31,NULL,'{\"debut\":\"2026-07-02T15:00:00.000000Z\",\"fin\":\"2026-07-02T15:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:19'),(81,1,'paiement.cree','App\\Models\\Paiement',22,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:19'),(82,1,'rdv.statut','App\\Models\\RendezVous',31,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(83,1,'rdv.cree','App\\Models\\RendezVous',32,NULL,'{\"debut\":\"2026-06-03T15:30:00.000000Z\",\"fin\":\"2026-06-03T15:50:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(84,1,'rdv.statut','App\\Models\\RendezVous',32,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(85,1,'rdv.cree','App\\Models\\RendezVous',33,NULL,'{\"debut\":\"2026-05-20T18:00:00.000000Z\",\"fin\":\"2026-05-20T18:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:19'),(86,1,'rdv.statut','App\\Models\\RendezVous',33,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(87,1,'rdv.cree','App\\Models\\RendezVous',34,NULL,'{\"debut\":\"2026-10-09T13:30:00.000000Z\",\"fin\":\"2026-10-09T14:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:19'),(88,1,'paiement.cree','App\\Models\\Paiement',23,NULL,'{\"montant_paye\":7000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:19'),(89,1,'rdv.statut','App\\Models\\RendezVous',34,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:19'),(90,1,'rdv.cree','App\\Models\\RendezVous',35,NULL,'{\"debut\":\"2026-11-09T12:00:00.000000Z\",\"fin\":\"2026-11-09T12:20:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:19'),(91,1,'rdv.statut','App\\Models\\RendezVous',35,'{\"statut\":\"en_attente\"}','{\"statut\":\"confirme\"}','127.0.0.1','2026-11-06 10:47:20'),(92,1,'rdv.cree','App\\Models\\RendezVous',36,NULL,'{\"debut\":\"2026-07-08T13:30:00.000000Z\",\"fin\":\"2026-07-08T13:50:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:20'),(93,1,'paiement.cree','App\\Models\\Paiement',24,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(94,1,'rdv.statut','App\\Models\\RendezVous',36,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(95,1,'rdv.cree','App\\Models\\RendezVous',37,NULL,'{\"debut\":\"2026-09-23T18:00:00.000000Z\",\"fin\":\"2026-09-23T18:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:20'),(96,1,'paiement.cree','App\\Models\\Paiement',25,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(97,1,'rdv.statut','App\\Models\\RendezVous',37,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(98,1,'rdv.cree','App\\Models\\RendezVous',38,NULL,'{\"debut\":\"2026-11-14T15:30:00.000000Z\",\"fin\":\"2026-11-14T16:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(99,1,'rdv.statut','App\\Models\\RendezVous',38,'{\"statut\":\"en_attente\"}','{\"statut\":\"confirme\"}','127.0.0.1','2026-11-06 10:47:20'),(100,1,'rdv.cree','App\\Models\\RendezVous',39,NULL,'{\"debut\":\"2026-10-26T11:30:00.000000Z\",\"fin\":\"2026-10-26T11:50:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:20'),(101,1,'paiement.cree','App\\Models\\Paiement',26,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:20'),(102,1,'rdv.statut','App\\Models\\RendezVous',39,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(103,1,'rdv.cree','App\\Models\\RendezVous',40,NULL,'{\"debut\":\"2026-07-25T17:00:00.000000Z\",\"fin\":\"2026-07-25T18:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:20'),(104,1,'paiement.cree','App\\Models\\Paiement',27,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:20'),(105,1,'rdv.statut','App\\Models\\RendezVous',40,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(106,1,'rdv.cree','App\\Models\\RendezVous',41,NULL,'{\"debut\":\"2026-06-19T11:00:00.000000Z\",\"fin\":\"2026-06-19T11:20:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:20'),(107,1,'rdv.statut','App\\Models\\RendezVous',41,'{\"statut\":\"en_attente\"}','{\"statut\":\"annule\"}','127.0.0.1','2026-11-06 10:47:20'),(108,1,'rdv.cree','App\\Models\\RendezVous',42,NULL,'{\"debut\":\"2026-06-20T11:00:00.000000Z\",\"fin\":\"2026-06-20T11:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:20'),(109,1,'paiement.cree','App\\Models\\Paiement',28,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(110,1,'rdv.statut','App\\Models\\RendezVous',42,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(111,1,'rdv.cree','App\\Models\\RendezVous',43,NULL,'{\"debut\":\"2026-10-05T12:30:00.000000Z\",\"fin\":\"2026-10-05T13:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:20'),(112,1,'paiement.cree','App\\Models\\Paiement',29,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(113,1,'rdv.statut','App\\Models\\RendezVous',43,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(114,1,'rdv.cree','App\\Models\\RendezVous',44,NULL,'{\"debut\":\"2026-06-13T15:30:00.000000Z\",\"fin\":\"2026-06-13T16:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":15000}','127.0.0.1','2026-11-06 10:47:20'),(115,1,'paiement.cree','App\\Models\\Paiement',30,NULL,'{\"montant_paye\":15000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(116,1,'rdv.statut','App\\Models\\RendezVous',44,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(117,1,'rdv.cree','App\\Models\\RendezVous',45,NULL,'{\"debut\":\"2026-11-06T17:30:00.000000Z\",\"fin\":\"2026-11-06T18:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(118,1,'rdv.cree','App\\Models\\RendezVous',46,NULL,'{\"debut\":\"2026-10-06T12:30:00.000000Z\",\"fin\":\"2026-10-06T13:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(119,1,'paiement.cree','App\\Models\\Paiement',31,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(120,1,'rdv.statut','App\\Models\\RendezVous',46,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(121,1,'rdv.cree','App\\Models\\RendezVous',47,NULL,'{\"debut\":\"2026-06-08T15:30:00.000000Z\",\"fin\":\"2026-06-08T16:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:20'),(122,1,'paiement.cree','App\\Models\\Paiement',32,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(123,1,'rdv.statut','App\\Models\\RendezVous',47,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(124,1,'rdv.cree','App\\Models\\RendezVous',48,NULL,'{\"debut\":\"2026-07-11T15:30:00.000000Z\",\"fin\":\"2026-07-11T16:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(125,1,'paiement.cree','App\\Models\\Paiement',33,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(126,1,'rdv.statut','App\\Models\\RendezVous',48,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(127,1,'rdv.cree','App\\Models\\RendezVous',49,NULL,'{\"debut\":\"2026-06-22T09:00:00.000000Z\",\"fin\":\"2026-06-22T09:20:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:20'),(128,1,'rdv.statut','App\\Models\\RendezVous',49,'{\"statut\":\"en_attente\"}','{\"statut\":\"annule\"}','127.0.0.1','2026-11-06 10:47:20'),(129,1,'rdv.cree','App\\Models\\RendezVous',50,NULL,'{\"debut\":\"2026-10-17T15:30:00.000000Z\",\"fin\":\"2026-10-17T16:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":30000}','127.0.0.1','2026-11-06 10:47:20'),(130,1,'paiement.cree','App\\Models\\Paiement',34,NULL,'{\"montant_paye\":30000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:20'),(131,1,'rdv.statut','App\\Models\\RendezVous',50,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(132,1,'rdv.cree','App\\Models\\RendezVous',51,NULL,'{\"debut\":\"2026-06-12T10:00:00.000000Z\",\"fin\":\"2026-06-12T10:45:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(133,1,'rdv.statut','App\\Models\\RendezVous',51,'{\"statut\":\"en_attente\"}','{\"statut\":\"annule\"}','127.0.0.1','2026-11-06 10:47:20'),(134,1,'rdv.cree','App\\Models\\RendezVous',52,NULL,'{\"debut\":\"2026-08-12T17:30:00.000000Z\",\"fin\":\"2026-08-12T18:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(135,1,'paiement.cree','App\\Models\\Paiement',35,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(136,1,'rdv.statut','App\\Models\\RendezVous',52,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(137,1,'rdv.cree','App\\Models\\RendezVous',53,NULL,'{\"debut\":\"2026-07-27T09:30:00.000000Z\",\"fin\":\"2026-07-27T10:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(138,1,'paiement.cree','App\\Models\\Paiement',36,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(139,1,'rdv.statut','App\\Models\\RendezVous',53,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(140,1,'rdv.cree','App\\Models\\RendezVous',54,NULL,'{\"debut\":\"2026-07-10T18:00:00.000000Z\",\"fin\":\"2026-07-10T18:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(141,1,'paiement.cree','App\\Models\\Paiement',37,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:20'),(142,1,'rdv.statut','App\\Models\\RendezVous',54,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(143,1,'rdv.cree','App\\Models\\RendezVous',55,NULL,'{\"debut\":\"2026-10-22T13:30:00.000000Z\",\"fin\":\"2026-10-22T14:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":30000}','127.0.0.1','2026-11-06 10:47:20'),(144,1,'paiement.cree','App\\Models\\Paiement',38,NULL,'{\"montant_paye\":30000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(145,1,'rdv.statut','App\\Models\\RendezVous',55,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(146,1,'rdv.cree','App\\Models\\RendezVous',56,NULL,'{\"debut\":\"2026-10-23T12:30:00.000000Z\",\"fin\":\"2026-10-23T13:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":15000}','127.0.0.1','2026-11-06 10:47:20'),(147,1,'paiement.cree','App\\Models\\Paiement',39,NULL,'{\"montant_paye\":15000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(148,1,'rdv.statut','App\\Models\\RendezVous',56,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(149,1,'rdv.cree','App\\Models\\RendezVous',57,NULL,'{\"debut\":\"2026-10-14T11:00:00.000000Z\",\"fin\":\"2026-10-14T12:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:20'),(150,1,'paiement.cree','App\\Models\\Paiement',40,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(151,1,'rdv.statut','App\\Models\\RendezVous',57,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(152,1,'rdv.cree','App\\Models\\RendezVous',58,NULL,'{\"debut\":\"2026-06-03T17:30:00.000000Z\",\"fin\":\"2026-06-03T17:50:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:20'),(153,1,'paiement.cree','App\\Models\\Paiement',41,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(154,1,'rdv.statut','App\\Models\\RendezVous',58,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(155,1,'rdv.cree','App\\Models\\RendezVous',59,NULL,'{\"debut\":\"2026-06-25T12:30:00.000000Z\",\"fin\":\"2026-06-25T13:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(156,1,'paiement.cree','App\\Models\\Paiement',42,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(157,1,'rdv.statut','App\\Models\\RendezVous',59,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(158,1,'rdv.cree','App\\Models\\RendezVous',60,NULL,'{\"debut\":\"2026-06-29T17:30:00.000000Z\",\"fin\":\"2026-06-29T18:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:20'),(159,1,'paiement.cree','App\\Models\\Paiement',43,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:20'),(160,1,'rdv.statut','App\\Models\\RendezVous',60,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(161,1,'rdv.cree','App\\Models\\RendezVous',61,NULL,'{\"debut\":\"2026-06-29T15:00:00.000000Z\",\"fin\":\"2026-06-29T15:20:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:20'),(162,1,'paiement.cree','App\\Models\\Paiement',44,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(163,1,'rdv.statut','App\\Models\\RendezVous',61,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(164,1,'rdv.cree','App\\Models\\RendezVous',62,NULL,'{\"debut\":\"2026-10-06T17:30:00.000000Z\",\"fin\":\"2026-10-06T18:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(165,1,'paiement.cree','App\\Models\\Paiement',45,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(166,1,'rdv.statut','App\\Models\\RendezVous',62,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(167,1,'rdv.cree','App\\Models\\RendezVous',63,NULL,'{\"debut\":\"2026-06-19T14:30:00.000000Z\",\"fin\":\"2026-06-19T15:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:20'),(168,1,'paiement.cree','App\\Models\\Paiement',46,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(169,1,'rdv.statut','App\\Models\\RendezVous',63,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(170,1,'rdv.cree','App\\Models\\RendezVous',64,NULL,'{\"debut\":\"2026-08-12T13:00:00.000000Z\",\"fin\":\"2026-08-12T13:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(171,1,'paiement.cree','App\\Models\\Paiement',47,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(172,1,'rdv.statut','App\\Models\\RendezVous',64,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(173,1,'rdv.cree','App\\Models\\RendezVous',65,NULL,'{\"debut\":\"2026-06-17T14:00:00.000000Z\",\"fin\":\"2026-06-17T14:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":12000}','127.0.0.1','2026-11-06 10:47:20'),(174,1,'paiement.cree','App\\Models\\Paiement',48,NULL,'{\"montant_paye\":12000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(175,1,'rdv.statut','App\\Models\\RendezVous',65,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(176,1,'rdv.cree','App\\Models\\RendezVous',66,NULL,'{\"debut\":\"2026-08-18T13:30:00.000000Z\",\"fin\":\"2026-08-18T14:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:20'),(177,1,'paiement.cree','App\\Models\\Paiement',49,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(178,1,'rdv.statut','App\\Models\\RendezVous',66,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(179,1,'rdv.cree','App\\Models\\RendezVous',67,NULL,'{\"debut\":\"2026-10-28T12:30:00.000000Z\",\"fin\":\"2026-10-28T12:50:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:20'),(180,1,'paiement.cree','App\\Models\\Paiement',50,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(181,1,'rdv.statut','App\\Models\\RendezVous',67,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(182,1,'rdv.cree','App\\Models\\RendezVous',68,NULL,'{\"debut\":\"2026-05-20T11:30:00.000000Z\",\"fin\":\"2026-05-20T12:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:20'),(183,1,'paiement.cree','App\\Models\\Paiement',51,NULL,'{\"montant_paye\":7000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(184,1,'rdv.statut','App\\Models\\RendezVous',68,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(185,1,'rdv.cree','App\\Models\\RendezVous',69,NULL,'{\"debut\":\"2026-09-25T18:00:00.000000Z\",\"fin\":\"2026-09-25T18:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":8000}','127.0.0.1','2026-11-06 10:47:20'),(186,1,'paiement.cree','App\\Models\\Paiement',52,NULL,'{\"montant_paye\":8000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(187,1,'rdv.statut','App\\Models\\RendezVous',69,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(188,1,'rdv.cree','App\\Models\\RendezVous',70,NULL,'{\"debut\":\"2026-08-28T13:00:00.000000Z\",\"fin\":\"2026-08-28T14:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:20'),(189,1,'paiement.cree','App\\Models\\Paiement',53,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(190,1,'rdv.statut','App\\Models\\RendezVous',70,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(191,1,'rdv.cree','App\\Models\\RendezVous',71,NULL,'{\"debut\":\"2026-09-03T09:30:00.000000Z\",\"fin\":\"2026-09-03T10:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(192,1,'rdv.statut','App\\Models\\RendezVous',71,'{\"statut\":\"en_attente\"}','{\"statut\":\"annule\"}','127.0.0.1','2026-11-06 10:47:20'),(193,1,'rdv.cree','App\\Models\\RendezVous',72,NULL,'{\"debut\":\"2026-10-05T16:30:00.000000Z\",\"fin\":\"2026-10-05T17:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(194,1,'paiement.cree','App\\Models\\Paiement',54,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(195,1,'rdv.statut','App\\Models\\RendezVous',72,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(196,1,'rdv.cree','App\\Models\\RendezVous',73,NULL,'{\"debut\":\"2026-06-13T09:30:00.000000Z\",\"fin\":\"2026-06-13T10:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:20'),(197,1,'paiement.cree','App\\Models\\Paiement',55,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(198,1,'rdv.statut','App\\Models\\RendezVous',73,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(199,1,'rdv.cree','App\\Models\\RendezVous',74,NULL,'{\"debut\":\"2026-10-13T17:00:00.000000Z\",\"fin\":\"2026-10-13T17:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(200,1,'paiement.cree','App\\Models\\Paiement',56,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(201,1,'rdv.statut','App\\Models\\RendezVous',74,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(202,1,'rdv.cree','App\\Models\\RendezVous',75,NULL,'{\"debut\":\"2026-07-31T18:30:00.000000Z\",\"fin\":\"2026-07-31T19:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:20'),(203,1,'paiement.cree','App\\Models\\Paiement',57,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:20'),(204,1,'rdv.statut','App\\Models\\RendezVous',75,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(205,1,'rdv.cree','App\\Models\\RendezVous',76,NULL,'{\"debut\":\"2026-11-13T16:00:00.000000Z\",\"fin\":\"2026-11-13T16:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":12000}','127.0.0.1','2026-11-06 10:47:20'),(206,1,'rdv.cree','App\\Models\\RendezVous',77,NULL,'{\"debut\":\"2026-11-07T11:00:00.000000Z\",\"fin\":\"2026-11-07T11:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(207,1,'rdv.cree','App\\Models\\RendezVous',78,NULL,'{\"debut\":\"2026-06-13T09:00:00.000000Z\",\"fin\":\"2026-06-13T09:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(208,1,'paiement.cree','App\\Models\\Paiement',58,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:20'),(209,1,'rdv.statut','App\\Models\\RendezVous',78,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(210,1,'rdv.cree','App\\Models\\RendezVous',79,NULL,'{\"debut\":\"2026-07-11T09:30:00.000000Z\",\"fin\":\"2026-07-11T10:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:20'),(211,1,'paiement.cree','App\\Models\\Paiement',59,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:20'),(212,1,'rdv.statut','App\\Models\\RendezVous',79,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:20'),(213,1,'rdv.cree','App\\Models\\RendezVous',80,NULL,'{\"debut\":\"2026-06-04T16:30:00.000000Z\",\"fin\":\"2026-06-04T17:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:21'),(214,1,'rdv.statut','App\\Models\\RendezVous',80,'{\"statut\":\"en_attente\"}','{\"statut\":\"annule\"}','127.0.0.1','2026-11-06 10:47:21'),(215,1,'rdv.cree','App\\Models\\RendezVous',81,NULL,'{\"debut\":\"2026-07-22T13:00:00.000000Z\",\"fin\":\"2026-07-22T13:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(216,1,'paiement.cree','App\\Models\\Paiement',60,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(217,1,'rdv.statut','App\\Models\\RendezVous',81,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(218,1,'rdv.cree','App\\Models\\RendezVous',82,NULL,'{\"debut\":\"2026-07-17T14:30:00.000000Z\",\"fin\":\"2026-07-17T14:50:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:21'),(219,1,'paiement.cree','App\\Models\\Paiement',61,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:21'),(220,1,'rdv.statut','App\\Models\\RendezVous',82,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(221,1,'rdv.cree','App\\Models\\RendezVous',83,NULL,'{\"debut\":\"2026-06-25T09:00:00.000000Z\",\"fin\":\"2026-06-25T09:45:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(222,1,'paiement.cree','App\\Models\\Paiement',62,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:21'),(223,1,'rdv.statut','App\\Models\\RendezVous',83,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(224,1,'rdv.cree','App\\Models\\RendezVous',84,NULL,'{\"debut\":\"2026-09-11T16:30:00.000000Z\",\"fin\":\"2026-09-11T17:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(225,1,'paiement.cree','App\\Models\\Paiement',63,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:21'),(226,1,'rdv.statut','App\\Models\\RendezVous',84,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(227,1,'rdv.cree','App\\Models\\RendezVous',85,NULL,'{\"debut\":\"2026-10-19T17:00:00.000000Z\",\"fin\":\"2026-10-19T17:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(228,1,'paiement.cree','App\\Models\\Paiement',64,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:21'),(229,1,'rdv.statut','App\\Models\\RendezVous',85,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(230,1,'rdv.cree','App\\Models\\RendezVous',86,NULL,'{\"debut\":\"2026-09-29T17:30:00.000000Z\",\"fin\":\"2026-09-29T18:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":20000}','127.0.0.1','2026-11-06 10:47:21'),(231,1,'paiement.cree','App\\Models\\Paiement',65,NULL,'{\"montant_paye\":20000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:21'),(232,1,'rdv.statut','App\\Models\\RendezVous',86,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(233,1,'rdv.cree','App\\Models\\RendezVous',87,NULL,'{\"debut\":\"2026-10-28T09:30:00.000000Z\",\"fin\":\"2026-10-28T10:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(234,1,'paiement.cree','App\\Models\\Paiement',66,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(235,1,'rdv.statut','App\\Models\\RendezVous',87,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(236,1,'rdv.cree','App\\Models\\RendezVous',88,NULL,'{\"debut\":\"2026-09-12T15:00:00.000000Z\",\"fin\":\"2026-09-12T15:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:21'),(237,1,'paiement.cree','App\\Models\\Paiement',67,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:21'),(238,1,'rdv.statut','App\\Models\\RendezVous',88,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(239,1,'rdv.cree','App\\Models\\RendezVous',89,NULL,'{\"debut\":\"2026-10-12T12:30:00.000000Z\",\"fin\":\"2026-10-12T13:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(240,1,'paiement.cree','App\\Models\\Paiement',68,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:21'),(241,1,'rdv.statut','App\\Models\\RendezVous',89,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(242,1,'rdv.cree','App\\Models\\RendezVous',90,NULL,'{\"debut\":\"2026-09-24T18:30:00.000000Z\",\"fin\":\"2026-09-24T18:50:00.000000Z\",\"employe_id\":2,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:21'),(243,1,'rdv.statut','App\\Models\\RendezVous',90,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(244,1,'rdv.cree','App\\Models\\RendezVous',91,NULL,'{\"debut\":\"2026-06-01T18:30:00.000000Z\",\"fin\":\"2026-06-01T18:50:00.000000Z\",\"employe_id\":1,\"prix_prevu\":2000}','127.0.0.1','2026-11-06 10:47:21'),(245,1,'paiement.cree','App\\Models\\Paiement',69,NULL,'{\"montant_paye\":2000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:21'),(246,1,'rdv.statut','App\\Models\\RendezVous',91,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(247,1,'rdv.cree','App\\Models\\RendezVous',92,NULL,'{\"debut\":\"2026-05-27T09:00:00.000000Z\",\"fin\":\"2026-05-27T09:30:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:21'),(248,1,'paiement.cree','App\\Models\\Paiement',70,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:21'),(249,1,'rdv.statut','App\\Models\\RendezVous',92,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(250,1,'rdv.cree','App\\Models\\RendezVous',93,NULL,'{\"debut\":\"2026-08-14T17:00:00.000000Z\",\"fin\":\"2026-08-14T17:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":8000}','127.0.0.1','2026-11-06 10:47:21'),(251,1,'paiement.cree','App\\Models\\Paiement',71,NULL,'{\"montant_paye\":8000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:21'),(252,1,'rdv.statut','App\\Models\\RendezVous',93,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(253,1,'rdv.cree','App\\Models\\RendezVous',94,NULL,'{\"debut\":\"2026-10-24T10:30:00.000000Z\",\"fin\":\"2026-10-24T11:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":15000}','127.0.0.1','2026-11-06 10:47:21'),(254,1,'paiement.cree','App\\Models\\Paiement',72,NULL,'{\"montant_paye\":15000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(255,1,'rdv.statut','App\\Models\\RendezVous',94,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(256,1,'rdv.cree','App\\Models\\RendezVous',95,NULL,'{\"debut\":\"2026-08-05T17:00:00.000000Z\",\"fin\":\"2026-08-05T17:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":8000}','127.0.0.1','2026-11-06 10:47:21'),(257,1,'paiement.cree','App\\Models\\Paiement',73,NULL,'{\"montant_paye\":8000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(258,1,'rdv.statut','App\\Models\\RendezVous',95,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(259,1,'rdv.cree','App\\Models\\RendezVous',96,NULL,'{\"debut\":\"2026-08-25T17:30:00.000000Z\",\"fin\":\"2026-08-25T18:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:21'),(260,1,'paiement.cree','App\\Models\\Paiement',74,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(261,1,'rdv.statut','App\\Models\\RendezVous',96,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(262,1,'rdv.cree','App\\Models\\RendezVous',97,NULL,'{\"debut\":\"2026-09-07T18:00:00.000000Z\",\"fin\":\"2026-09-07T18:45:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:21'),(263,1,'paiement.cree','App\\Models\\Paiement',75,NULL,'{\"montant_paye\":7000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:21'),(264,1,'rdv.statut','App\\Models\\RendezVous',97,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(265,1,'rdv.cree','App\\Models\\RendezVous',98,NULL,'{\"debut\":\"2026-08-20T12:30:00.000000Z\",\"fin\":\"2026-08-20T13:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:21'),(266,1,'paiement.cree','App\\Models\\Paiement',76,NULL,'{\"montant_paye\":7000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:21'),(267,1,'rdv.statut','App\\Models\\RendezVous',98,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(268,1,'rdv.cree','App\\Models\\RendezVous',99,NULL,'{\"debut\":\"2026-08-19T12:30:00.000000Z\",\"fin\":\"2026-08-19T13:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(269,1,'paiement.cree','App\\Models\\Paiement',77,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:21'),(270,1,'rdv.statut','App\\Models\\RendezVous',99,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(271,1,'rdv.cree','App\\Models\\RendezVous',100,NULL,'{\"debut\":\"2026-10-24T12:30:00.000000Z\",\"fin\":\"2026-10-24T13:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:21'),(272,1,'rdv.statut','App\\Models\\RendezVous',100,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(273,1,'rdv.cree','App\\Models\\RendezVous',101,NULL,'{\"debut\":\"2026-09-10T10:30:00.000000Z\",\"fin\":\"2026-09-10T11:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:21'),(274,1,'paiement.cree','App\\Models\\Paiement',78,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(275,1,'rdv.statut','App\\Models\\RendezVous',101,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(276,1,'rdv.cree','App\\Models\\RendezVous',102,NULL,'{\"debut\":\"2026-08-21T11:00:00.000000Z\",\"fin\":\"2026-08-21T11:45:00.000000Z\",\"employe_id\":3,\"prix_prevu\":12000}','127.0.0.1','2026-11-06 10:47:21'),(277,1,'paiement.cree','App\\Models\\Paiement',79,NULL,'{\"montant_paye\":12000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(278,1,'rdv.statut','App\\Models\\RendezVous',102,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(279,1,'rdv.cree','App\\Models\\RendezVous',103,NULL,'{\"debut\":\"2026-08-04T10:30:00.000000Z\",\"fin\":\"2026-08-04T11:00:00.000000Z\",\"employe_id\":3,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(280,1,'paiement.cree','App\\Models\\Paiement',80,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(281,1,'rdv.statut','App\\Models\\RendezVous',103,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(282,1,'rdv.cree','App\\Models\\RendezVous',104,NULL,'{\"debut\":\"2026-09-11T18:30:00.000000Z\",\"fin\":\"2026-09-11T19:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:21'),(283,1,'paiement.cree','App\\Models\\Paiement',81,NULL,'{\"montant_paye\":7000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:21'),(284,1,'rdv.statut','App\\Models\\RendezVous',104,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(285,1,'rdv.cree','App\\Models\\RendezVous',105,NULL,'{\"debut\":\"2026-06-22T12:30:00.000000Z\",\"fin\":\"2026-06-22T13:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(286,1,'paiement.cree','App\\Models\\Paiement',82,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":1}','127.0.0.1','2026-11-06 10:47:21'),(287,1,'rdv.statut','App\\Models\\RendezVous',105,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(288,1,'rdv.cree','App\\Models\\RendezVous',106,NULL,'{\"debut\":\"2026-09-11T10:00:00.000000Z\",\"fin\":\"2026-09-11T10:45:00.000000Z\",\"employe_id\":1,\"prix_prevu\":7000}','127.0.0.1','2026-11-06 10:47:21'),(289,1,'paiement.cree','App\\Models\\Paiement',83,NULL,'{\"montant_paye\":7000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:21'),(290,1,'rdv.statut','App\\Models\\RendezVous',106,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(291,1,'rdv.cree','App\\Models\\RendezVous',107,NULL,'{\"debut\":\"2026-08-13T16:30:00.000000Z\",\"fin\":\"2026-08-13T17:00:00.000000Z\",\"employe_id\":2,\"prix_prevu\":3000}','127.0.0.1','2026-11-06 10:47:21'),(292,1,'paiement.cree','App\\Models\\Paiement',84,NULL,'{\"montant_paye\":3000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:21'),(293,1,'rdv.statut','App\\Models\\RendezVous',107,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(294,1,'rdv.cree','App\\Models\\RendezVous',108,NULL,'{\"debut\":\"2026-07-02T09:30:00.000000Z\",\"fin\":\"2026-07-02T10:15:00.000000Z\",\"employe_id\":3,\"prix_prevu\":12000}','127.0.0.1','2026-11-06 10:47:21'),(295,1,'paiement.cree','App\\Models\\Paiement',85,NULL,'{\"montant_paye\":12000,\"reste_a_payer\":0,\"mode_paiement_id\":2}','127.0.0.1','2026-11-06 10:47:21'),(296,1,'rdv.statut','App\\Models\\RendezVous',108,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(297,1,'rdv.cree','App\\Models\\RendezVous',109,NULL,'{\"debut\":\"2026-06-06T09:30:00.000000Z\",\"fin\":\"2026-06-06T10:15:00.000000Z\",\"employe_id\":1,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(298,1,'paiement.cree','App\\Models\\Paiement',86,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":4}','127.0.0.1','2026-11-06 10:47:21'),(299,1,'rdv.statut','App\\Models\\RendezVous',109,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(300,1,'rdv.cree','App\\Models\\RendezVous',110,NULL,'{\"debut\":\"2026-08-05T12:30:00.000000Z\",\"fin\":\"2026-08-05T13:30:00.000000Z\",\"employe_id\":3,\"prix_prevu\":15000}','127.0.0.1','2026-11-06 10:47:21'),(301,1,'paiement.cree','App\\Models\\Paiement',87,NULL,'{\"montant_paye\":15000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:21'),(302,1,'rdv.statut','App\\Models\\RendezVous',110,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21'),(303,1,'rdv.cree','App\\Models\\RendezVous',111,NULL,'{\"debut\":\"2026-10-29T10:30:00.000000Z\",\"fin\":\"2026-10-29T11:15:00.000000Z\",\"employe_id\":2,\"prix_prevu\":5000}','127.0.0.1','2026-11-06 10:47:21'),(304,1,'paiement.cree','App\\Models\\Paiement',88,NULL,'{\"montant_paye\":5000,\"reste_a_payer\":0,\"mode_paiement_id\":3}','127.0.0.1','2026-11-06 10:47:21'),(305,1,'rdv.statut','App\\Models\\RendezVous',111,'{\"statut\":\"en_attente\"}','{\"statut\":\"termine\"}','127.0.0.1','2026-11-06 10:47:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modeles_messages`
--

LOCK TABLES `modeles_messages` WRITE;
/*!40000 ALTER TABLE `modeles_messages` DISABLE KEYS */;
INSERT INTO `modeles_messages` VALUES (1,'anniversaire','Message d\'anniversaire','Bonjour [Prénom] ! Toute l\'équipe d\'All in One by Eva te souhaite un merveilleux anniversaire. Pour célébrer cette journée spéciale, nous serions ravis de t\'accueillir à nouveau au salon. À très bientôt !','2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'promotion','Message de promotion','Bonjour [Prénom] ! All in One by Eva a une nouvelle offre beauté à te faire découvrir. Contacte-nous pour connaître les détails et réserver ton prochain moment de bien-être.','2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,'rappel_rdv','Rappel de rendez-vous','Bonjour [Prénom] ! Nous te rappelons ton rendez-vous chez All in One by Eva prévu le [date] à [heure] pour [prestation]. Nous avons hâte de t\'accueillir.','2026-11-06 10:47:17','2026-11-06 10:47:17'),(4,'fidelisation','Message de fidélisation','Bonjour [Prénom] ! Cela fait quelque temps que nous ne t\'avons pas accueilli chez All in One by Eva. Nous serions heureux de te retrouver pour un nouveau moment de détente et de beauté.','2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modes_paiement`
--

LOCK TABLES `modes_paiement` WRITE;
/*!40000 ALTER TABLE `modes_paiement` DISABLE KEYS */;
INSERT INTO `modes_paiement` VALUES (1,'Espèces',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'Wave',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,'Orange Money',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(4,'Carte bancaire',1,'2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mouvements_points`
--

LOCK TABLES `mouvements_points` WRITE;
/*!40000 ALTER TABLE `mouvements_points` DISABLE KEYS */;
INSERT INTO `mouvements_points` VALUES (1,31,1,'gain',2,'Rendez-vous du 06/06/2026',NULL,'2026-11-06 10:47:19'),(2,19,2,'gain',5,'Rendez-vous du 03/10/2026',NULL,'2026-11-06 10:47:19'),(3,35,3,'gain',5,'Rendez-vous du 20/10/2026',NULL,'2026-11-06 10:47:19'),(4,6,4,'gain',5,'Rendez-vous du 31/10/2026',NULL,'2026-11-06 10:47:19'),(5,10,5,'gain',2,'Rendez-vous du 05/10/2026',NULL,'2026-11-06 10:47:19'),(6,30,6,'gain',3,'Rendez-vous du 22/06/2026',NULL,'2026-11-06 10:47:19'),(7,24,7,'gain',7,'Rendez-vous du 22/10/2026',NULL,'2026-11-06 10:47:19'),(8,34,8,'gain',5,'Rendez-vous du 01/10/2026',NULL,'2026-11-06 10:47:19'),(9,18,9,'gain',5,'Rendez-vous du 21/09/2026',NULL,'2026-11-06 10:47:19'),(10,27,10,'gain',5,'Rendez-vous du 02/06/2026',NULL,'2026-11-06 10:47:19'),(11,14,11,'gain',8,'Rendez-vous du 15/06/2026',NULL,'2026-11-06 10:47:19'),(12,23,12,'gain',3,'Rendez-vous du 02/11/2026',NULL,'2026-11-06 10:47:19'),(13,31,13,'gain',5,'Rendez-vous du 07/09/2026',NULL,'2026-11-06 10:47:19'),(14,21,14,'gain',2,'Rendez-vous du 12/10/2026',NULL,'2026-11-06 10:47:19'),(15,36,15,'gain',8,'Rendez-vous du 20/05/2026',NULL,'2026-11-06 10:47:19'),(16,14,16,'gain',5,'Rendez-vous du 27/06/2026',NULL,'2026-11-06 10:47:19'),(17,31,17,'gain',12,'Rendez-vous du 19/09/2026',NULL,'2026-11-06 10:47:19'),(18,14,18,'gain',12,'Rendez-vous du 10/08/2026',NULL,'2026-11-06 10:47:19'),(19,11,19,'gain',3,'Rendez-vous du 13/06/2026',NULL,'2026-11-06 10:47:19'),(20,24,20,'gain',2,'Rendez-vous du 24/06/2026',NULL,'2026-11-06 10:47:19'),(21,17,21,'gain',15,'Rendez-vous du 15/06/2026',NULL,'2026-11-06 10:47:19'),(22,9,22,'gain',3,'Rendez-vous du 02/07/2026',NULL,'2026-11-06 10:47:19'),(23,20,23,'gain',7,'Rendez-vous du 09/10/2026',NULL,'2026-11-06 10:47:19'),(24,28,24,'gain',2,'Rendez-vous du 08/07/2026',NULL,'2026-11-06 10:47:20'),(25,25,25,'gain',3,'Rendez-vous du 23/09/2026',NULL,'2026-11-06 10:47:20'),(26,14,26,'gain',2,'Rendez-vous du 26/10/2026',NULL,'2026-11-06 10:47:20'),(27,23,27,'gain',20,'Rendez-vous du 25/07/2026',NULL,'2026-11-06 10:47:20'),(28,34,28,'gain',3,'Rendez-vous du 20/06/2026',NULL,'2026-11-06 10:47:20'),(29,24,29,'gain',3,'Rendez-vous du 05/10/2026',NULL,'2026-11-06 10:47:20'),(30,14,30,'gain',15,'Rendez-vous du 13/06/2026',NULL,'2026-11-06 10:47:20'),(31,19,31,'gain',5,'Rendez-vous du 06/10/2026',NULL,'2026-11-06 10:47:20'),(32,24,32,'gain',3,'Rendez-vous du 08/06/2026',NULL,'2026-11-06 10:47:20'),(33,31,33,'gain',5,'Rendez-vous du 11/07/2026',NULL,'2026-11-06 10:47:20'),(34,17,34,'gain',30,'Rendez-vous du 17/10/2026',NULL,'2026-11-06 10:47:20'),(35,11,35,'gain',5,'Rendez-vous du 12/08/2026',NULL,'2026-11-06 10:47:20'),(36,29,36,'gain',5,'Rendez-vous du 27/07/2026',NULL,'2026-11-06 10:47:20'),(37,22,37,'gain',5,'Rendez-vous du 10/07/2026',NULL,'2026-11-06 10:47:20'),(38,17,38,'gain',30,'Rendez-vous du 22/10/2026',NULL,'2026-11-06 10:47:20'),(39,15,39,'gain',15,'Rendez-vous du 23/10/2026',NULL,'2026-11-06 10:47:20'),(40,14,40,'gain',20,'Rendez-vous du 14/10/2026',NULL,'2026-11-06 10:47:20'),(41,8,41,'gain',2,'Rendez-vous du 03/06/2026',NULL,'2026-11-06 10:47:20'),(42,1,42,'gain',5,'Rendez-vous du 25/06/2026',NULL,'2026-11-06 10:47:20'),(43,9,43,'gain',3,'Rendez-vous du 29/06/2026',NULL,'2026-11-06 10:47:20'),(44,5,44,'gain',2,'Rendez-vous du 29/06/2026',NULL,'2026-11-06 10:47:20'),(45,9,45,'gain',5,'Rendez-vous du 06/10/2026',NULL,'2026-11-06 10:47:20'),(46,5,46,'gain',20,'Rendez-vous du 19/06/2026',NULL,'2026-11-06 10:47:20'),(47,33,47,'gain',5,'Rendez-vous du 12/08/2026',NULL,'2026-11-06 10:47:20'),(48,19,48,'gain',12,'Rendez-vous du 17/06/2026',NULL,'2026-11-06 10:47:20'),(49,20,49,'gain',20,'Rendez-vous du 18/08/2026',NULL,'2026-11-06 10:47:20'),(50,17,50,'gain',2,'Rendez-vous du 28/10/2026',NULL,'2026-11-06 10:47:20'),(51,34,51,'gain',7,'Rendez-vous du 20/05/2026',NULL,'2026-11-06 10:47:20'),(52,6,52,'gain',8,'Rendez-vous du 25/09/2026',NULL,'2026-11-06 10:47:20'),(53,5,53,'gain',20,'Rendez-vous du 28/08/2026',NULL,'2026-11-06 10:47:20'),(54,14,54,'gain',5,'Rendez-vous du 05/10/2026',NULL,'2026-11-06 10:47:20'),(55,23,55,'gain',20,'Rendez-vous du 13/06/2026',NULL,'2026-11-06 10:47:20'),(56,35,56,'gain',5,'Rendez-vous du 13/10/2026',NULL,'2026-11-06 10:47:20'),(57,20,57,'gain',20,'Rendez-vous du 31/07/2026',NULL,'2026-11-06 10:47:20'),(58,35,58,'gain',5,'Rendez-vous du 13/06/2026',NULL,'2026-11-06 10:47:20'),(59,15,59,'gain',5,'Rendez-vous du 11/07/2026',NULL,'2026-11-06 10:47:21'),(60,27,60,'gain',5,'Rendez-vous du 22/07/2026',NULL,'2026-11-06 10:47:21'),(61,24,61,'gain',2,'Rendez-vous du 17/07/2026',NULL,'2026-11-06 10:47:21'),(62,27,62,'gain',5,'Rendez-vous du 25/06/2026',NULL,'2026-11-06 10:47:21'),(63,18,63,'gain',5,'Rendez-vous du 11/09/2026',NULL,'2026-11-06 10:47:21'),(64,32,64,'gain',5,'Rendez-vous du 19/10/2026',NULL,'2026-11-06 10:47:21'),(65,6,65,'gain',20,'Rendez-vous du 29/09/2026',NULL,'2026-11-06 10:47:21'),(66,35,66,'gain',5,'Rendez-vous du 28/10/2026',NULL,'2026-11-06 10:47:21'),(67,3,67,'gain',3,'Rendez-vous du 12/09/2026',NULL,'2026-11-06 10:47:21'),(68,4,68,'gain',5,'Rendez-vous du 12/10/2026',NULL,'2026-11-06 10:47:21'),(69,36,69,'gain',2,'Rendez-vous du 01/06/2026',NULL,'2026-11-06 10:47:21'),(70,16,70,'gain',3,'Rendez-vous du 27/05/2026',NULL,'2026-11-06 10:47:21'),(71,21,71,'gain',8,'Rendez-vous du 14/08/2026',NULL,'2026-11-06 10:47:21'),(72,6,72,'gain',15,'Rendez-vous du 24/10/2026',NULL,'2026-11-06 10:47:21'),(73,7,73,'gain',8,'Rendez-vous du 05/08/2026',NULL,'2026-11-06 10:47:21'),(74,15,74,'gain',3,'Rendez-vous du 25/08/2026',NULL,'2026-11-06 10:47:21'),(75,18,75,'gain',7,'Rendez-vous du 07/09/2026',NULL,'2026-11-06 10:47:21'),(76,21,76,'gain',7,'Rendez-vous du 20/08/2026',NULL,'2026-11-06 10:47:21'),(77,5,77,'gain',5,'Rendez-vous du 19/08/2026',NULL,'2026-11-06 10:47:21'),(78,34,78,'gain',3,'Rendez-vous du 10/09/2026',NULL,'2026-11-06 10:47:21'),(79,30,79,'gain',12,'Rendez-vous du 21/08/2026',NULL,'2026-11-06 10:47:21'),(80,1,80,'gain',5,'Rendez-vous du 04/08/2026',NULL,'2026-11-06 10:47:21'),(81,33,81,'gain',7,'Rendez-vous du 11/09/2026',NULL,'2026-11-06 10:47:21'),(82,18,82,'gain',5,'Rendez-vous du 22/06/2026',NULL,'2026-11-06 10:47:21'),(83,28,83,'gain',7,'Rendez-vous du 11/09/2026',NULL,'2026-11-06 10:47:21'),(84,5,84,'gain',3,'Rendez-vous du 13/08/2026',NULL,'2026-11-06 10:47:21'),(85,20,85,'gain',12,'Rendez-vous du 02/07/2026',NULL,'2026-11-06 10:47:21'),(86,29,86,'gain',5,'Rendez-vous du 06/06/2026',NULL,'2026-11-06 10:47:21'),(87,4,87,'gain',15,'Rendez-vous du 05/08/2026',NULL,'2026-11-06 10:47:21'),(88,30,88,'gain',5,'Rendez-vous du 29/10/2026',NULL,'2026-11-06 10:47:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements`
--

LOCK TABLES `paiements` WRITE;
/*!40000 ALTER TABLE `paiements` DISABLE KEYS */;
INSERT INTO `paiements` VALUES (1,31,1,4,2000,2000,0,'2026-06-06 12:20:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(2,19,2,4,5000,5000,0,'2026-10-03 17:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(3,35,4,3,5000,5000,0,'2026-10-20 16:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(4,6,5,2,5000,5000,0,'2026-10-31 13:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(5,10,6,2,2000,2000,0,'2026-10-05 12:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(6,30,7,4,3000,3000,0,'2026-06-22 17:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(7,24,9,1,7000,7000,0,'2026-10-22 10:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(8,34,10,3,5000,5000,0,'2026-10-01 12:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(9,18,11,2,5000,5000,0,'2026-09-21 14:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(10,27,13,3,5000,5000,0,'2026-06-02 11:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(11,14,17,4,8000,8000,0,'2026-06-15 14:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(12,23,18,1,3000,3000,0,'2026-11-02 16:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(13,31,19,4,5000,5000,0,'2026-09-07 18:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(14,21,20,2,2000,2000,0,'2026-10-12 10:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(15,36,23,3,8000,8000,0,'2026-05-20 11:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(16,14,25,2,5000,5000,0,'2026-06-27 09:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(17,31,26,3,12000,12000,0,'2026-09-19 13:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(18,14,27,2,12000,12000,0,'2026-08-10 10:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(19,11,28,4,3000,3000,0,'2026-06-13 13:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(20,24,29,3,2000,2000,0,'2026-06-24 15:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(21,17,30,1,15000,15000,0,'2026-06-15 13:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(22,9,31,4,3000,3000,0,'2026-07-02 15:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(23,20,34,3,7000,7000,0,'2026-10-09 14:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(24,28,36,4,2000,2000,0,'2026-07-08 13:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(25,25,37,4,3000,3000,0,'2026-09-23 18:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(26,14,39,2,2000,2000,0,'2026-10-26 11:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(27,23,40,2,20000,20000,0,'2026-07-25 18:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(28,34,42,3,3000,3000,0,'2026-06-20 11:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(29,24,43,4,3000,3000,0,'2026-10-05 13:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(30,14,44,4,15000,15000,0,'2026-06-13 16:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(31,19,46,3,5000,5000,0,'2026-10-06 13:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(32,24,47,4,3000,3000,0,'2026-06-08 16:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(33,31,48,3,5000,5000,0,'2026-07-11 16:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(34,17,50,2,30000,30000,0,'2026-10-17 16:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(35,11,52,3,5000,5000,0,'2026-08-12 18:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(36,29,53,3,5000,5000,0,'2026-07-27 10:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(37,22,54,2,5000,5000,0,'2026-07-10 18:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(38,17,55,1,30000,30000,0,'2026-10-22 14:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(39,15,56,4,15000,15000,0,'2026-10-23 13:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(40,14,57,1,20000,20000,0,'2026-10-14 12:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(41,8,58,3,2000,2000,0,'2026-06-03 17:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(42,1,59,4,5000,5000,0,'2026-06-25 13:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(43,9,60,2,3000,3000,0,'2026-06-29 18:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(44,5,61,4,2000,2000,0,'2026-06-29 15:20:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(45,9,62,1,5000,5000,0,'2026-10-06 18:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(46,5,63,1,20000,20000,0,'2026-06-19 15:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(47,33,64,1,5000,5000,0,'2026-08-12 13:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(48,19,65,1,12000,12000,0,'2026-06-17 14:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(49,20,66,1,20000,20000,0,'2026-08-18 14:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(50,17,67,4,2000,2000,0,'2026-10-28 12:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(51,34,68,4,7000,7000,0,'2026-05-20 12:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(52,6,69,1,8000,8000,0,'2026-09-25 18:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(53,5,70,3,20000,20000,0,'2026-08-28 14:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(54,14,72,4,5000,5000,0,'2026-10-05 17:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(55,23,73,3,20000,20000,0,'2026-06-13 10:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(56,35,74,4,5000,5000,0,'2026-10-13 17:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(57,20,75,4,20000,20000,0,'2026-07-31 19:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(58,35,78,3,5000,5000,0,'2026-06-13 09:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(59,15,79,1,5000,5000,0,'2026-07-11 10:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(60,27,81,1,5000,5000,0,'2026-07-22 13:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(61,24,82,2,2000,2000,0,'2026-07-17 14:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(62,27,83,2,5000,5000,0,'2026-06-25 09:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(63,18,84,2,5000,5000,0,'2026-09-11 17:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(64,32,85,4,5000,5000,0,'2026-10-19 17:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(65,6,86,4,20000,20000,0,'2026-09-29 18:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(66,35,87,1,5000,5000,0,'2026-10-28 10:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(67,3,88,3,3000,3000,0,'2026-09-12 15:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(68,4,89,3,5000,5000,0,'2026-10-12 13:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(69,36,91,2,2000,2000,0,'2026-06-01 18:50:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(70,16,92,4,3000,3000,0,'2026-05-27 09:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(71,21,93,3,8000,8000,0,'2026-08-14 17:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(72,6,94,1,15000,15000,0,'2026-10-24 11:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(73,7,95,1,8000,8000,0,'2026-08-05 17:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(74,15,96,1,3000,3000,0,'2026-08-25 18:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(75,18,97,3,7000,7000,0,'2026-09-07 18:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(76,21,98,4,7000,7000,0,'2026-08-20 13:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(77,5,99,4,5000,5000,0,'2026-08-19 13:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(78,34,101,1,3000,3000,0,'2026-09-10 11:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(79,30,102,1,12000,12000,0,'2026-08-21 11:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(80,1,103,1,5000,5000,0,'2026-08-04 11:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(81,33,104,2,7000,7000,0,'2026-09-11 19:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(82,18,105,1,5000,5000,0,'2026-06-22 13:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(83,28,106,3,7000,7000,0,'2026-09-11 10:45:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(84,5,107,4,3000,3000,0,'2026-08-13 17:00:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(85,20,108,2,12000,12000,0,'2026-07-02 10:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(86,29,109,4,5000,5000,0,'2026-06-06 10:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(87,4,110,3,15000,15000,0,'2026-08-05 13:30:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(88,30,111,3,5000,5000,0,'2026-10-29 11:15:00',NULL,1,NULL,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametres_salon`
--

LOCK TABLES `parametres_salon` WRITE;
/*!40000 ALTER TABLE `parametres_salon` DISABLE KEYS */;
INSERT INTO `parametres_salon` VALUES (1,'pause_minutes','0','2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'fidelite_fcfa_par_point','1000','2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,'inactivite_semaines','6','2026-11-06 10:47:17','2026-11-06 10:47:17'),(4,'offre_anniversaire',NULL,'2026-11-06 10:47:17','2026-11-06 10:47:17');
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
INSERT INTO `permission_role` VALUES (1,1),(1,2),(2,1),(3,1),(4,1),(5,1),(5,2),(6,1),(7,1),(7,2),(8,1),(9,1),(10,1),(10,2),(11,1),(12,1),(13,1),(14,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'clients.gerer','Gérer les clients','2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'clients.exporter','Exporter les clients','2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,'catalogue.gerer','Gérer le catalogue','2026-11-06 10:47:17','2026-11-06 10:47:17'),(4,'employes.gerer','Gérer les employés','2026-11-06 10:47:17','2026-11-06 10:47:17'),(5,'rdv.gerer','Gérer les rendez-vous','2026-11-06 10:47:17','2026-11-06 10:47:17'),(6,'rdv.passe','Créer un rendez-vous dans le passé','2026-11-06 10:47:17','2026-11-06 10:47:17'),(7,'paiements.enregistrer','Enregistrer des paiements','2026-11-06 10:47:17','2026-11-06 10:47:17'),(8,'paiements.annuler','Annuler / corriger des paiements','2026-11-06 10:47:17','2026-11-06 10:47:17'),(9,'fidelite.corriger','Corriger les points de fidélité','2026-11-06 10:47:17','2026-11-06 10:47:17'),(10,'whatsapp.envoyer','Envoyer des messages WhatsApp','2026-11-06 10:47:17','2026-11-06 10:47:17'),(11,'finances.voir','Voir les finances et rapports','2026-11-06 10:47:17','2026-11-06 10:47:17'),(12,'depenses.gerer','Gérer les dépenses','2026-11-06 10:47:17','2026-11-06 10:47:17'),(13,'stock.gerer','Gérer le stock','2026-11-06 10:47:17','2026-11-06 10:47:17'),(14,'parametres.gerer','Configurer le salon','2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produits`
--

LOCK TABLES `produits` WRITE;
/*!40000 ALTER TABLE `produits` DISABLE KEYS */;
INSERT INTO `produits` VALUES (1,'Shampooing professionnel','Capillaire',3.00,'flacon',6500,NULL,5.00,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(2,'Huile de massage','Soins',12.00,'flacon',4000,NULL,4.00,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(3,'Mèches synthétiques','Tresses',8.00,'paquet',1500,NULL,10.00,NULL,NULL,'2026-11-06 10:47:21','2026-11-06 10:47:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recompenses`
--

LOCK TABLES `recompenses` WRITE;
/*!40000 ALTER TABLE `recompenses` DISABLE KEYS */;
INSERT INTO `recompenses` VALUES (1,'Réduction de 2 000 FCFA','reduction',50,2000,1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'Manucure offerte','prestation_offerte',100,NULL,1,'2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendez_vous`
--

LOCK TABLES `rendez_vous` WRITE;
/*!40000 ALTER TABLE `rendez_vous` DISABLE KEYS */;
INSERT INTO `rendez_vous` VALUES (1,31,2,'2026-06-06 12:00:00','2026-06-06 12:20:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(2,19,3,'2026-10-03 17:00:00','2026-10-03 17:30:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(3,7,3,'2026-06-20 09:30:00','2026-06-20 10:30:00','mixte',15000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(4,35,3,'2026-10-20 15:30:00','2026-10-20 16:00:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(5,6,1,'2026-10-31 13:00:00','2026-10-31 13:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(6,10,1,'2026-10-05 12:30:00','2026-10-05 12:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(7,30,2,'2026-06-22 16:30:00','2026-06-22 17:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(8,36,2,'2026-10-29 18:00:00','2026-10-29 18:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(9,24,1,'2026-10-22 09:30:00','2026-10-22 10:15:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(10,34,3,'2026-10-01 12:00:00','2026-10-01 12:30:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(11,18,2,'2026-09-21 14:00:00','2026-09-21 14:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(12,12,3,'2026-11-12 09:30:00','2026-11-12 10:30:00','mixte',20000,'confirme',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(13,27,1,'2026-06-02 10:30:00','2026-06-02 11:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(14,9,2,'2026-07-02 11:00:00','2026-07-02 11:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(15,34,1,'2026-11-12 14:30:00','2026-11-12 14:50:00','mixte',2000,'en_attente',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(16,3,3,'2026-09-16 09:00:00','2026-09-16 10:00:00','mixte',15000,'annule',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(17,14,3,'2026-06-15 14:00:00','2026-06-15 14:45:00','mixte',8000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(18,23,2,'2026-11-02 16:00:00','2026-11-02 16:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(19,31,2,'2026-09-07 17:30:00','2026-09-07 18:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(20,21,2,'2026-10-12 10:30:00','2026-10-12 10:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(21,5,3,'2026-11-10 12:30:00','2026-11-10 13:45:00','mixte',30000,'en_attente',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(22,25,3,'2026-07-20 17:30:00','2026-07-20 18:15:00','mixte',12000,'annule',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(23,36,3,'2026-05-20 10:30:00','2026-05-20 11:15:00','mixte',8000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(24,30,1,'2026-08-01 13:30:00','2026-08-01 13:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(25,14,1,'2026-06-27 09:00:00','2026-06-27 09:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(26,31,3,'2026-09-19 12:30:00','2026-09-19 13:15:00','mixte',12000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(27,14,3,'2026-08-10 09:30:00','2026-08-10 10:15:00','mixte',12000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(28,11,2,'2026-06-13 12:30:00','2026-06-13 13:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(29,24,2,'2026-06-24 15:30:00','2026-06-24 15:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(30,17,3,'2026-06-15 12:30:00','2026-06-15 13:30:00','mixte',15000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(31,9,2,'2026-07-02 15:00:00','2026-07-02 15:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(32,6,2,'2026-06-03 15:30:00','2026-06-03 15:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(33,4,2,'2026-05-20 18:00:00','2026-05-20 18:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(34,20,1,'2026-10-09 13:30:00','2026-10-09 14:15:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:19'),(35,15,2,'2026-11-09 12:00:00','2026-11-09 12:20:00','mixte',2000,'confirme',NULL,1,'2026-11-06 10:47:19','2026-11-06 10:47:20'),(36,28,1,'2026-07-08 13:30:00','2026-07-08 13:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(37,25,2,'2026-09-23 18:00:00','2026-09-23 18:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(38,5,3,'2026-11-14 15:30:00','2026-11-14 16:00:00','mixte',5000,'confirme',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(39,14,1,'2026-10-26 11:30:00','2026-10-26 11:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(40,23,3,'2026-07-25 17:00:00','2026-07-25 18:00:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(41,15,1,'2026-06-19 11:00:00','2026-06-19 11:20:00','mixte',2000,'annule',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(42,34,2,'2026-06-20 11:00:00','2026-06-20 11:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(43,24,2,'2026-10-05 12:30:00','2026-10-05 13:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(44,14,3,'2026-06-13 15:30:00','2026-06-13 16:30:00','mixte',15000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(45,24,1,'2026-11-06 17:30:00','2026-11-06 18:15:00','mixte',5000,'en_attente',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(46,19,1,'2026-10-06 12:30:00','2026-10-06 13:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(47,24,2,'2026-06-08 15:30:00','2026-06-08 16:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(48,31,2,'2026-07-11 15:30:00','2026-07-11 16:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(49,16,2,'2026-06-22 09:00:00','2026-06-22 09:20:00','mixte',2000,'annule',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(50,17,3,'2026-10-17 15:30:00','2026-10-17 16:45:00','mixte',30000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(51,25,1,'2026-06-12 10:00:00','2026-06-12 10:45:00','mixte',5000,'annule',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(52,11,2,'2026-08-12 17:30:00','2026-08-12 18:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(53,29,1,'2026-07-27 09:30:00','2026-07-27 10:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(54,22,2,'2026-07-10 18:00:00','2026-07-10 18:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(55,17,3,'2026-10-22 13:30:00','2026-10-22 14:45:00','mixte',30000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(56,15,3,'2026-10-23 12:30:00','2026-10-23 13:30:00','mixte',15000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(57,14,3,'2026-10-14 11:00:00','2026-10-14 12:00:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(58,8,1,'2026-06-03 17:30:00','2026-06-03 17:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(59,1,1,'2026-06-25 12:30:00','2026-06-25 13:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(60,9,2,'2026-06-29 17:30:00','2026-06-29 18:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(61,5,2,'2026-06-29 15:00:00','2026-06-29 15:20:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(62,9,2,'2026-10-06 17:30:00','2026-10-06 18:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(63,5,3,'2026-06-19 14:30:00','2026-06-19 15:30:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(64,33,3,'2026-08-12 13:00:00','2026-08-12 13:30:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(65,19,3,'2026-06-17 14:00:00','2026-06-17 14:45:00','mixte',12000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(66,20,3,'2026-08-18 13:30:00','2026-08-18 14:30:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(67,17,2,'2026-10-28 12:30:00','2026-10-28 12:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(68,34,1,'2026-05-20 11:30:00','2026-05-20 12:15:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(69,6,3,'2026-09-25 18:00:00','2026-09-25 18:45:00','mixte',8000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(70,5,3,'2026-08-28 13:00:00','2026-08-28 14:00:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(71,22,2,'2026-09-03 09:30:00','2026-09-03 10:15:00','mixte',5000,'annule',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(72,14,1,'2026-10-05 16:30:00','2026-10-05 17:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(73,23,3,'2026-06-13 09:30:00','2026-06-13 10:30:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(74,35,2,'2026-10-13 17:00:00','2026-10-13 17:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(75,20,3,'2026-07-31 18:30:00','2026-07-31 19:30:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(76,22,3,'2026-11-13 16:00:00','2026-11-13 16:45:00','mixte',12000,'en_attente',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(77,12,2,'2026-11-07 11:00:00','2026-11-07 11:45:00','mixte',5000,'en_attente',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(78,35,2,'2026-06-13 09:00:00','2026-06-13 09:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(79,15,1,'2026-07-11 09:30:00','2026-07-11 10:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:20','2026-11-06 10:47:20'),(80,13,2,'2026-06-04 16:30:00','2026-06-04 17:00:00','mixte',3000,'annule',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(81,27,2,'2026-07-22 13:00:00','2026-07-22 13:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(82,24,2,'2026-07-17 14:30:00','2026-07-17 14:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(83,27,2,'2026-06-25 09:00:00','2026-06-25 09:45:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(84,18,3,'2026-09-11 16:30:00','2026-09-11 17:00:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(85,32,3,'2026-10-19 17:00:00','2026-10-19 17:30:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(86,6,3,'2026-09-29 17:30:00','2026-09-29 18:30:00','mixte',20000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(87,35,1,'2026-10-28 09:30:00','2026-10-28 10:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(88,3,2,'2026-09-12 15:00:00','2026-09-12 15:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(89,4,2,'2026-10-12 12:30:00','2026-10-12 13:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(90,29,2,'2026-09-24 18:30:00','2026-09-24 18:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(91,36,1,'2026-06-01 18:30:00','2026-06-01 18:50:00','mixte',2000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(92,16,2,'2026-05-27 09:00:00','2026-05-27 09:30:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(93,21,3,'2026-08-14 17:00:00','2026-08-14 17:45:00','mixte',8000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(94,6,3,'2026-10-24 10:30:00','2026-10-24 11:30:00','mixte',15000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(95,7,3,'2026-08-05 17:00:00','2026-08-05 17:45:00','mixte',8000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(96,15,2,'2026-08-25 17:30:00','2026-08-25 18:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(97,18,1,'2026-09-07 18:00:00','2026-09-07 18:45:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(98,21,1,'2026-08-20 12:30:00','2026-08-20 13:15:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(99,5,1,'2026-08-19 12:30:00','2026-08-19 13:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(100,35,1,'2026-10-24 12:30:00','2026-10-24 13:15:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(101,34,2,'2026-09-10 10:30:00','2026-09-10 11:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(102,30,3,'2026-08-21 11:00:00','2026-08-21 11:45:00','mixte',12000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(103,1,3,'2026-08-04 10:30:00','2026-08-04 11:00:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(104,33,1,'2026-09-11 18:30:00','2026-09-11 19:15:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(105,18,2,'2026-06-22 12:30:00','2026-06-22 13:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(106,28,1,'2026-09-11 10:00:00','2026-09-11 10:45:00','mixte',7000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(107,5,2,'2026-08-13 16:30:00','2026-08-13 17:00:00','mixte',3000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(108,20,3,'2026-07-02 09:30:00','2026-07-02 10:15:00','mixte',12000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(109,29,1,'2026-06-06 09:30:00','2026-06-06 10:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(110,4,3,'2026-08-05 12:30:00','2026-08-05 13:30:00','mixte',15000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21'),(111,30,2,'2026-10-29 10:30:00','2026-10-29 11:15:00','mixte',5000,'termine',NULL,1,'2026-11-06 10:47:21','2026-11-06 10:47:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendez_vous_service`
--

LOCK TABLES `rendez_vous_service` WRITE;
/*!40000 ALTER TABLE `rendez_vous_service` DISABLE KEYS */;
INSERT INTO `rendez_vous_service` VALUES (1,1,5,NULL,'Coupe enfant',2000,20),(2,2,15,NULL,'Manucure simple',5000,30),(3,3,18,NULL,'Soin du visage hydratant',15000,60),(4,4,15,NULL,'Manucure simple',5000,30),(5,5,3,NULL,'Brushing',5000,45),(6,6,5,NULL,'Coupe enfant',2000,20),(7,7,1,NULL,'Coupe homme',3000,30),(8,8,1,NULL,'Coupe homme',3000,30),(9,9,11,NULL,'Soin profond',7000,45),(10,10,15,NULL,'Manucure simple',5000,30),(11,11,2,NULL,'Coupe + barbe',5000,45),(12,12,13,NULL,'Massage relaxant 60 min',20000,60),(13,13,3,NULL,'Brushing',5000,45),(14,14,1,NULL,'Coupe homme',3000,30),(15,15,5,NULL,'Coupe enfant',2000,20),(16,16,18,NULL,'Soin du visage hydratant',15000,60),(17,17,17,NULL,'Pédicure complète',8000,45),(18,18,1,NULL,'Coupe homme',3000,30),(19,19,2,NULL,'Coupe + barbe',5000,45),(20,20,5,NULL,'Coupe enfant',2000,20),(21,21,14,NULL,'Massage aux pierres chaudes',30000,75),(22,22,19,NULL,'Nettoyage de peau',12000,45),(23,23,17,NULL,'Pédicure complète',8000,45),(24,24,5,NULL,'Coupe enfant',2000,20),(25,25,3,NULL,'Brushing',5000,45),(26,26,19,NULL,'Nettoyage de peau',12000,45),(27,27,19,NULL,'Nettoyage de peau',12000,45),(28,28,1,NULL,'Coupe homme',3000,30),(29,29,5,NULL,'Coupe enfant',2000,20),(30,30,18,NULL,'Soin du visage hydratant',15000,60),(31,31,1,NULL,'Coupe homme',3000,30),(32,32,5,NULL,'Coupe enfant',2000,20),(33,33,2,NULL,'Coupe + barbe',5000,45),(34,34,11,NULL,'Soin profond',7000,45),(35,35,5,NULL,'Coupe enfant',2000,20),(36,36,5,NULL,'Coupe enfant',2000,20),(37,37,1,NULL,'Coupe homme',3000,30),(38,38,15,NULL,'Manucure simple',5000,30),(39,39,5,NULL,'Coupe enfant',2000,20),(40,40,13,NULL,'Massage relaxant 60 min',20000,60),(41,41,5,NULL,'Coupe enfant',2000,20),(42,42,1,NULL,'Coupe homme',3000,30),(43,43,1,NULL,'Coupe homme',3000,30),(44,44,18,NULL,'Soin du visage hydratant',15000,60),(45,45,3,NULL,'Brushing',5000,45),(46,46,3,NULL,'Brushing',5000,45),(47,47,1,NULL,'Coupe homme',3000,30),(48,48,2,NULL,'Coupe + barbe',5000,45),(49,49,5,NULL,'Coupe enfant',2000,20),(50,50,14,NULL,'Massage aux pierres chaudes',30000,75),(51,51,3,NULL,'Brushing',5000,45),(52,52,2,NULL,'Coupe + barbe',5000,45),(53,53,3,NULL,'Brushing',5000,45),(54,54,2,NULL,'Coupe + barbe',5000,45),(55,55,14,NULL,'Massage aux pierres chaudes',30000,75),(56,56,18,NULL,'Soin du visage hydratant',15000,60),(57,57,13,NULL,'Massage relaxant 60 min',20000,60),(58,58,5,NULL,'Coupe enfant',2000,20),(59,59,3,NULL,'Brushing',5000,45),(60,60,1,NULL,'Coupe homme',3000,30),(61,61,5,NULL,'Coupe enfant',2000,20),(62,62,2,NULL,'Coupe + barbe',5000,45),(63,63,13,NULL,'Massage relaxant 60 min',20000,60),(64,64,15,NULL,'Manucure simple',5000,30),(65,65,19,NULL,'Nettoyage de peau',12000,45),(66,66,13,NULL,'Massage relaxant 60 min',20000,60),(67,67,5,NULL,'Coupe enfant',2000,20),(68,68,11,NULL,'Soin profond',7000,45),(69,69,17,NULL,'Pédicure complète',8000,45),(70,70,13,NULL,'Massage relaxant 60 min',20000,60),(71,71,2,NULL,'Coupe + barbe',5000,45),(72,72,3,NULL,'Brushing',5000,45),(73,73,13,NULL,'Massage relaxant 60 min',20000,60),(74,74,2,NULL,'Coupe + barbe',5000,45),(75,75,13,NULL,'Massage relaxant 60 min',20000,60),(76,76,19,NULL,'Nettoyage de peau',12000,45),(77,77,2,NULL,'Coupe + barbe',5000,45),(78,78,2,NULL,'Coupe + barbe',5000,45),(79,79,3,NULL,'Brushing',5000,45),(80,80,1,NULL,'Coupe homme',3000,30),(81,81,2,NULL,'Coupe + barbe',5000,45),(82,82,5,NULL,'Coupe enfant',2000,20),(83,83,2,NULL,'Coupe + barbe',5000,45),(84,84,15,NULL,'Manucure simple',5000,30),(85,85,15,NULL,'Manucure simple',5000,30),(86,86,13,NULL,'Massage relaxant 60 min',20000,60),(87,87,3,NULL,'Brushing',5000,45),(88,88,1,NULL,'Coupe homme',3000,30),(89,89,2,NULL,'Coupe + barbe',5000,45),(90,90,5,NULL,'Coupe enfant',2000,20),(91,91,5,NULL,'Coupe enfant',2000,20),(92,92,1,NULL,'Coupe homme',3000,30),(93,93,17,NULL,'Pédicure complète',8000,45),(94,94,18,NULL,'Soin du visage hydratant',15000,60),(95,95,17,NULL,'Pédicure complète',8000,45),(96,96,1,NULL,'Coupe homme',3000,30),(97,97,11,NULL,'Soin profond',7000,45),(98,98,11,NULL,'Soin profond',7000,45),(99,99,3,NULL,'Brushing',5000,45),(100,100,11,NULL,'Soin profond',7000,45),(101,101,1,NULL,'Coupe homme',3000,30),(102,102,19,NULL,'Nettoyage de peau',12000,45),(103,103,15,NULL,'Manucure simple',5000,30),(104,104,11,NULL,'Soin profond',7000,45),(105,105,2,NULL,'Coupe + barbe',5000,45),(106,106,11,NULL,'Soin profond',7000,45),(107,107,1,NULL,'Coupe homme',3000,30),(108,108,19,NULL,'Nettoyage de peau',12000,45),(109,109,3,NULL,'Brushing',5000,45),(110,110,18,NULL,'Soin du visage hydratant',15000,60),(111,111,2,NULL,'Coupe + barbe',5000,45);
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
INSERT INTO `roles` VALUES (1,'admin','Administratrice','2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,'receptionniste','Réceptionniste','2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,'professionnel','Professionnel(le)','2026-11-06 10:47:17','2026-11-06 10:47:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,1,'Coupe homme',NULL,3000,30,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(2,1,'Coupe + barbe',NULL,5000,45,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(3,2,'Brushing',NULL,5000,45,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(4,2,'Coupe femme',NULL,7000,60,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(5,3,'Coupe enfant',NULL,2000,20,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(6,4,'Tresses simples',NULL,10000,120,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(7,4,'Box braids',NULL,25000,240,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(8,4,'Nattes collées',NULL,8000,90,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(9,5,'Coloration complète',NULL,15000,90,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(10,5,'Mèches',NULL,20000,120,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(11,6,'Soin profond',NULL,7000,45,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(12,6,'Défrisage',NULL,10000,60,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(13,7,'Massage relaxant 60 min',NULL,20000,60,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(14,7,'Massage aux pierres chaudes',NULL,30000,75,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(15,8,'Manucure simple',NULL,5000,30,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(16,8,'Pose de vernis semi-permanent',NULL,10000,45,'femmes',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(17,9,'Pédicure complète',NULL,8000,45,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(18,10,'Soin du visage hydratant',NULL,15000,60,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17'),(19,10,'Nettoyage de peau',NULL,12000,45,'mixte',1,'2026-11-06 10:47:17','2026-11-06 10:47:17');
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
INSERT INTO `sessions` VALUES ('WkIokgLFdluXDqaZNPvSvwSTToOdkRf0ZSuw0Zls',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.118.1 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36','eyJfdG9rZW4iOiJjM0hlTnlibVpiTW8zd0dxZk80aVoyZnJRdFF4OWRIZ1NLQkRYZ1g0IiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiZmM5YzUxOTJiMzJmYTdjNTIxZWU4NTkxNDdhYTU3NTkzMGMxODMxYjRjZWE1MmNmMTA3NGEzMzRkNTg5NDY5MSIsIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9jbGllbnRzIiwicm91dGUiOiJjbGllbnRzLmluZGV4In19',1793962903),('ZnSzgi4RWqqA6aVBPohzcu4sJgS0er8IahQyIwHh',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Safari/605.1.15','eyJfdG9rZW4iOiJZUzJEa1RGVEhLVXc3STdxZnE1NlQwS3NmaWtnN2tUaTE0M0F6YUZLIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2NsaWVudHMifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImZjOWM1MTkyYjMyZmE3YzUyMWVlODU5MTQ3YWE1NzU5MzBjMTgzMWI0Y2VhNTJjZjEwNzRhMzM0ZDU4OTQ2OTEifQ==',1793965778);
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
INSERT INTO `users` VALUES (1,1,'Eva Diallo','admin@allinone.test',NULL,'$2y$12$ylrA8zNw10UkQVMaGmcOsOb/.nmJ29hrl25NHQ8B454YtH4jHiAO2',1,NULL,'2026-11-06 10:47:18','2026-11-06 10:47:18'),(2,2,'Awa Ndiaye','accueil@allinone.test',NULL,'$2y$12$VI16BfYQty..Kc1KwIcxf.F1z6M5KdiVgxOWKgcoAVe4C.mnIw5my',1,NULL,'2026-11-06 10:47:18','2026-11-06 10:47:18'),(3,3,'Fatou Sarr','fatou@allinone.test',NULL,'$2y$12$6GB.45T5i25w0Mm0gtXGZeuo/3zm4IE9CWdtgIdtNjn2Z5CdM5YDe',1,NULL,'2026-11-06 10:47:18','2026-11-06 10:47:18');
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

-- Dump completed on 2026-11-06 12:42:02
