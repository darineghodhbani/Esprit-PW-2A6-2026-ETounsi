-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: etounsi
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
-- Current Database: `etounsi`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `etounsi` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `etounsi`;

--
-- Table structure for table `assure_social`
--

DROP TABLE IF EXISTS `assure_social`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assure_social` (
  `id_assure` int(11) NOT NULL AUTO_INCREMENT,
  `numero_assure` varchar(20) NOT NULL,
  `cin` varchar(8) NOT NULL,
  `regime` varchar(50) NOT NULL DEFAULT 'CNSS',
  `date_affiliation` datetime NOT NULL DEFAULT current_timestamp(),
  `id_citoyen` int(11) NOT NULL,
  PRIMARY KEY (`id_assure`),
  UNIQUE KEY `numero_assure` (`numero_assure`),
  UNIQUE KEY `cin` (`cin`),
  KEY `id_citoyen` (`id_citoyen`),
  CONSTRAINT `assure_social_ibfk_1` FOREIGN KEY (`id_citoyen`) REFERENCES `citoyen` (`id_citoyen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assure_social`
--

LOCK TABLES `assure_social` WRITE;
/*!40000 ALTER TABLE `assure_social` DISABLE KEYS */;
/*!40000 ALTER TABLE `assure_social` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiaire`
--

DROP TABLE IF EXISTS `beneficiaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beneficiaire` (
  `id_beneficiaire` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lien` enum('conjoint','enfant','parent') NOT NULL DEFAULT 'enfant',
  `statut` enum('en_attente','valide','refuse') NOT NULL DEFAULT 'en_attente',
  `id_assure` int(11) NOT NULL,
  PRIMARY KEY (`id_beneficiaire`),
  KEY `id_assure` (`id_assure`),
  CONSTRAINT `beneficiaire_ibfk_1` FOREIGN KEY (`id_assure`) REFERENCES `assure_social` (`id_assure`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiaire`
--

LOCK TABLES `beneficiaire` WRITE;
/*!40000 ALTER TABLE `beneficiaire` DISABLE KEYS */;
/*!40000 ALTER TABLE `beneficiaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bureau_postal`
--

DROP TABLE IF EXISTS `bureau_postal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bureau_postal` (
  `id_bureau` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `gouvernorat` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_bureau`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bureau_postal`
--

LOCK TABLES `bureau_postal` WRITE;
/*!40000 ALTER TABLE `bureau_postal` DISABLE KEYS */;
INSERT INTO `bureau_postal` VALUES (1,'Bureau Central Tunis','Rue Charles de Gaulle, Tunis','Tunis','71 000 000'),(2,'Bureau Ariana','Avenue de l\'Ind├®pendance, Ariana','Ariana','70 000 000');
/*!40000 ALTER TABLE `bureau_postal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citoyen`
--

DROP TABLE IF EXISTS `citoyen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citoyen` (
  `id_citoyen` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('citoyen','agent','admin') NOT NULL DEFAULT 'citoyen',
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_citoyen`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citoyen`
--

LOCK TABLES `citoyen` WRITE;
/*!40000 ALTER TABLE `citoyen` DISABLE KEYS */;
INSERT INTO `citoyen` VALUES (1,'Admin','E-Tounsi','admin@etounsi.tn',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2026-11-21 20:41:13'),(2,'Agent','E-Tounsi','agent@etounsi.tn',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','agent','2026-11-21 20:41:13'),(3,'Test','Citoyen','citoyen@etounsi.tn','22123456','$2y$10$iZ3lrYd6YWhj4IopGAysl.Iy65yr0R5D2bxyYEIyYk7U/NXkgha/m','citoyen','2026-11-21 20:41:34');
/*!40000 ALTER TABLE `citoyen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colis`
--

DROP TABLE IF EXISTS `colis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colis` (
  `id_colis` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(20) NOT NULL,
  `destinataire_nom` varchar(150) NOT NULL,
  `destinataire_adresse` varchar(255) NOT NULL,
  `poids` decimal(6,2) DEFAULT NULL,
  `statut` enum('declare','en_transit','en_distribution','livre','retourne') NOT NULL DEFAULT 'declare',
  `date_declaration` datetime NOT NULL DEFAULT current_timestamp(),
  `date_livraison` datetime DEFAULT NULL,
  `id_citoyen` int(11) NOT NULL,
  `id_bureau` int(11) NOT NULL,
  PRIMARY KEY (`id_colis`),
  UNIQUE KEY `reference` (`reference`),
  KEY `id_citoyen` (`id_citoyen`),
  KEY `id_bureau` (`id_bureau`),
  CONSTRAINT `colis_ibfk_1` FOREIGN KEY (`id_citoyen`) REFERENCES `citoyen` (`id_citoyen`),
  CONSTRAINT `colis_ibfk_2` FOREIGN KEY (`id_bureau`) REFERENCES `bureau_postal` (`id_bureau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colis`
--

LOCK TABLES `colis` WRITE;
/*!40000 ALTER TABLE `colis` DISABLE KEYS */;
/*!40000 ALTER TABLE `colis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `decision_cnam`
--

DROP TABLE IF EXISTS `decision_cnam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `decision_cnam` (
  `id_decision` int(11) NOT NULL AUTO_INCREMENT,
  `decision` enum('acceptee','refusee') NOT NULL,
  `motif` text DEFAULT NULL,
  `montant_accorde` decimal(8,2) DEFAULT NULL,
  `date_decision` datetime NOT NULL DEFAULT current_timestamp(),
  `id_remboursement` int(11) NOT NULL,
  `id_agent` int(11) NOT NULL,
  PRIMARY KEY (`id_decision`),
  KEY `id_remboursement` (`id_remboursement`),
  KEY `id_agent` (`id_agent`),
  CONSTRAINT `decision_cnam_ibfk_1` FOREIGN KEY (`id_remboursement`) REFERENCES `demande_remboursement` (`id_remboursement`),
  CONSTRAINT `decision_cnam_ibfk_2` FOREIGN KEY (`id_agent`) REFERENCES `citoyen` (`id_citoyen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `decision_cnam`
--

LOCK TABLES `decision_cnam` WRITE;
/*!40000 ALTER TABLE `decision_cnam` DISABLE KEYS */;
/*!40000 ALTER TABLE `decision_cnam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demande_municipale`
--

DROP TABLE IF EXISTS `demande_municipale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demande_municipale` (
  `id_demande` int(11) NOT NULL AUTO_INCREMENT,
  `objet` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `statut` enum('en_attente','en_cours','acceptee','refusee') NOT NULL DEFAULT 'en_attente',
  `date_demande` datetime NOT NULL DEFAULT current_timestamp(),
  `id_citoyen` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  PRIMARY KEY (`id_demande`),
  KEY `id_citoyen` (`id_citoyen`),
  KEY `id_service` (`id_service`),
  CONSTRAINT `demande_municipale_ibfk_1` FOREIGN KEY (`id_citoyen`) REFERENCES `citoyen` (`id_citoyen`),
  CONSTRAINT `demande_municipale_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `service_municipal` (`id_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demande_municipale`
--

LOCK TABLES `demande_municipale` WRITE;
/*!40000 ALTER TABLE `demande_municipale` DISABLE KEYS */;
/*!40000 ALTER TABLE `demande_municipale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demande_remboursement`
--

DROP TABLE IF EXISTS `demande_remboursement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demande_remboursement` (
  `id_remboursement` int(11) NOT NULL AUTO_INCREMENT,
  `type_soin` varchar(100) NOT NULL,
  `montant` decimal(8,2) NOT NULL,
  `statut` enum('en_attente','en_cours','acceptee','refusee','remboursee') NOT NULL DEFAULT 'en_attente',
  `date_demande` datetime NOT NULL DEFAULT current_timestamp(),
  `id_assure` int(11) NOT NULL,
  PRIMARY KEY (`id_remboursement`),
  KEY `id_assure` (`id_assure`),
  CONSTRAINT `demande_remboursement_ibfk_1` FOREIGN KEY (`id_assure`) REFERENCES `assure_social` (`id_assure`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demande_remboursement`
--

LOCK TABLES `demande_remboursement` WRITE;
/*!40000 ALTER TABLE `demande_remboursement` DISABLE KEYS */;
/*!40000 ALTER TABLE `demande_remboursement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_cnam`
--

DROP TABLE IF EXISTS `document_cnam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_cnam` (
  `id_document` int(11) NOT NULL AUTO_INCREMENT,
  `nom_fichier` varchar(255) NOT NULL,
  `chemin` varchar(255) NOT NULL,
  `type_document` varchar(50) DEFAULT NULL,
  `date_upload` datetime NOT NULL DEFAULT current_timestamp(),
  `id_remboursement` int(11) DEFAULT NULL,
  `id_beneficiaire` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_document`),
  KEY `id_remboursement` (`id_remboursement`),
  KEY `id_beneficiaire` (`id_beneficiaire`),
  CONSTRAINT `document_cnam_ibfk_1` FOREIGN KEY (`id_remboursement`) REFERENCES `demande_remboursement` (`id_remboursement`) ON DELETE CASCADE,
  CONSTRAINT `document_cnam_ibfk_2` FOREIGN KEY (`id_beneficiaire`) REFERENCES `beneficiaire` (`id_beneficiaire`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_cnam`
--

LOCK TABLES `document_cnam` WRITE;
/*!40000 ALTER TABLE `document_cnam` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_cnam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impot`
--

DROP TABLE IF EXISTS `impot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_propriete` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `montant` int(11) NOT NULL,
  `date_limite` date NOT NULL,
  `statut` enum('non_paye','en_attente','paye') NOT NULL DEFAULT 'non_paye',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_propriete` (`id_propriete`),
  CONSTRAINT `impot_ibfk_1` FOREIGN KEY (`id_propriete`) REFERENCES `propriete` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impot`
--

LOCK TABLES `impot` WRITE;
/*!40000 ALTER TABLE `impot` DISABLE KEYS */;
INSERT INTO `impot` VALUES (1,1,2025,320,'2025-12-31','paye','2026-11-20 15:05:30'),(2,1,2026,340,'2026-12-31','non_paye','2026-11-20 15:05:30'),(3,2,2026,180,'2026-12-31','en_attente','2026-11-20 15:05:30');
/*!40000 ALTER TABLE `impot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ligne_transport`
--

DROP TABLE IF EXISTS `ligne_transport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_transport` (
  `id_ligne` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `tarif` decimal(5,2) NOT NULL DEFAULT 0.00,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_ligne`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ligne_transport`
--

LOCK TABLES `ligne_transport` WRITE;
/*!40000 ALTER TABLE `ligne_transport` DISABLE KEYS */;
INSERT INTO `ligne_transport` VALUES (1,'M1','M├®tro Tunis Marine ÔÇö Ben Arous',0.80,1),(2,'B42','Bus Ariana ÔÇö Centre Urbain Nord',0.60,1);
/*!40000 ALTER TABLE `ligne_transport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `propriete`
--

DROP TABLE IF EXISTS `propriete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `propriete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_citoyen` int(11) NOT NULL,
  `type` enum('maison','appartement','terrain','voiture','local_commercial') NOT NULL,
  `reference` varchar(50) NOT NULL,
  `adresse` varchar(150) NOT NULL,
  `valeur` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_citoyen` (`id_citoyen`),
  CONSTRAINT `propriete_ibfk_1` FOREIGN KEY (`id_citoyen`) REFERENCES `citoyen` (`id_citoyen`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propriete`
--

LOCK TABLES `propriete` WRITE;
/*!40000 ALTER TABLE `propriete` DISABLE KEYS */;
INSERT INTO `propriete` VALUES (1,3,'maison','TF-123456','12 rue de Marseille, Tunis',250000,'2026-11-20 15:05:30'),(2,3,'voiture','215 TU 4587','Ariana',45000,'2026-11-20 15:05:30');
/*!40000 ALTER TABLE `propriete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_municipal`
--

DROP TABLE IF EXISTS `service_municipal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_municipal` (
  `id_service` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `delai_traitement` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_service`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_municipal`
--

LOCK TABLES `service_municipal` WRITE;
/*!40000 ALTER TABLE `service_municipal` DISABLE KEYS */;
INSERT INTO `service_municipal` VALUES (1,'├ëtat civil','Actes de naissance, mariage, d├®c├¿s',3),(2,'Urbanisme','Permis de construire et certificats',15),(3,'Propret├®','Signalement et collecte des d├®chets',7);
/*!40000 ALTER TABLE `service_municipal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `station`
--

DROP TABLE IF EXISTS `station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station` (
  `id_station` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 1,
  `id_ligne` int(11) NOT NULL,
  PRIMARY KEY (`id_station`),
  KEY `id_ligne` (`id_ligne`),
  CONSTRAINT `station_ibfk_1` FOREIGN KEY (`id_ligne`) REFERENCES `ligne_transport` (`id_ligne`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `station`
--

LOCK TABLES `station` WRITE;
/*!40000 ALTER TABLE `station` DISABLE KEYS */;
INSERT INTO `station` VALUES (1,'Tunis Marine','Place de Barcelone, Tunis',1,1),(2,'Barcelone','Avenue Habib Bourguiba, Tunis',2,1),(3,'Ben Arous','Centre ville, Ben Arous',3,1);
/*!40000 ALTER TABLE `station` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-21 21:02:33
