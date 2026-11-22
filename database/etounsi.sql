-- E-Tounsi : schema commun Poste et Recette des Finances
-- Donnees de demonstration uniquement.
CREATE DATABASE IF NOT EXISTS `etounsi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `etounsi`;
SET FOREIGN_KEY_CHECKS=0;


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
DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `actor_name` varchar(161) NOT NULL,
  `action` varchar(40) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` varchar(500) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`,`created_at`),
  KEY `idx_audit_target` (`target_id`,`created_at`),
  KEY `idx_audit_action_date` (`action`,`created_at`),
  KEY `idx_audit_created` (`created_at`,`id`),
  CONSTRAINT `fk_audit_actor` FOREIGN KEY (`user_id`) REFERENCES `citoyen` (`id_citoyen`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `bureau_postal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bureau_postal` (
  `id_bureau` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL,
  `adresse` varchar(250) NOT NULL,
  `gouvernorat` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `ville` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_bureau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `citoyen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citoyen` (
  `id_citoyen` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(80) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  `email` varchar(190) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('citoyen','admin') NOT NULL DEFAULT 'citoyen',
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `version_session` int(10) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_citoyen`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colis` (
  `id_colis` int(11) NOT NULL AUTO_INCREMENT,
  `id_bureau_depart` int(11) NOT NULL,
  `id_citoyen` int(11) NOT NULL,
  `numero_suivi` varchar(32) NOT NULL,
  `statut` enum('En attente','Accepté','En transit','Livré','Annulé') NOT NULL DEFAULT 'En attente',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_colis`),
  UNIQUE KEY `numero_suivi` (`numero_suivi`),
  KEY `fk_poste_bureau` (`id_bureau_depart`),
  KEY `fk_poste_citoyen` (`id_citoyen`),
  CONSTRAINT `fk_poste_bureau` FOREIGN KEY (`id_bureau_depart`) REFERENCES `bureau_postal` (`id_bureau`),
  CONSTRAINT `fk_poste_citoyen` FOREIGN KEY (`id_citoyen`) REFERENCES `citoyen` (`id_citoyen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `service_municipal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_municipal` (
  `id_service` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `delai_traitement` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


SET FOREIGN_KEY_CHECKS=0;

UPDATE bureau_postal SET ville=gouvernorat WHERE ville='';
SET FOREIGN_KEY_CHECKS=1;
