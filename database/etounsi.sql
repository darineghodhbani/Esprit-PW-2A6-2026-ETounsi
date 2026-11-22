-- E-Tounsi : base commune du projet.
-- Importer ce fichier dans phpMyAdmin, onglet Importer.
-- Les tables et les donnees existantes sont conservees lors d'une reimportation.
-- Sur une base vide, des comptes et des donnees de demonstration sont ajoutes.

CREATE DATABASE IF NOT EXISTS `etounsi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `etounsi`;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `citoyen` (
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

CREATE TABLE IF NOT EXISTS `bureau_postal` (
  `id_bureau` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL,
  `adresse` varchar(250) NOT NULL,
  `gouvernorat` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `ville` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_bureau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `colis` (
  `id_colis` int(11) NOT NULL AUTO_INCREMENT,
  `id_bureau_depart` int(11) NOT NULL,
  `id_citoyen` int(11) NOT NULL,
  `numero_suivi` varchar(32) NOT NULL,
  `statut` enum('En attente','Accepté','En transit','Livré','Annulé') NOT NULL DEFAULT 'En attente',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `nom_citoyen` varchar(200) NOT NULL DEFAULT '',
  `date_declaration` date NOT NULL,
  PRIMARY KEY (`id_colis`),
  UNIQUE KEY `numero_suivi` (`numero_suivi`),
  KEY `fk_poste_bureau` (`id_bureau_depart`),
  KEY `fk_poste_citoyen` (`id_citoyen`),
  CONSTRAINT `fk_poste_bureau` FOREIGN KEY (`id_bureau_depart`) REFERENCES `bureau_postal` (`id_bureau`),
  CONSTRAINT `fk_poste_citoyen` FOREIGN KEY (`id_citoyen`) REFERENCES `citoyen` (`id_citoyen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `propriete` (
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

CREATE TABLE IF NOT EXISTS `impot` (
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

CREATE TABLE IF NOT EXISTS `ligne_transport` (
  `id_ligne` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `tarif` decimal(5,2) NOT NULL DEFAULT 0.00,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_ligne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `station` (
  `id_station` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 1,
  `id_ligne` int(11) NOT NULL,
  PRIMARY KEY (`id_station`),
  KEY `id_ligne` (`id_ligne`),
  CONSTRAINT `station_ibfk_1` FOREIGN KEY (`id_ligne`) REFERENCES `ligne_transport` (`id_ligne`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `horaire` (
  `id_horaire` int(11) NOT NULL AUTO_INCREMENT,
  `id_ligne` int(11) NOT NULL,
  `sens` varchar(10) NOT NULL DEFAULT 'Aller',
  `heure_depart` time NOT NULL,
  `heure_arrivee` time NOT NULL,
  `jours` varchar(30) NOT NULL DEFAULT 'Tous les jours',
  PRIMARY KEY (`id_horaire`),
  KEY `id_ligne` (`id_ligne`),
  CONSTRAINT `horaire_ibfk_1` FOREIGN KEY (`id_ligne`) REFERENCES `ligne_transport` (`id_ligne`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `service_municipal` (
  `id_service` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `delai_traitement` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `demande_municipale` (
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

CREATE TABLE IF NOT EXISTS `assure_social` (
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

CREATE TABLE IF NOT EXISTS `beneficiaire` (
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

CREATE TABLE IF NOT EXISTS `demande_remboursement` (
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

CREATE TABLE IF NOT EXISTS `document_cnam` (
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

CREATE TABLE IF NOT EXISTS `decision_cnam` (
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

CREATE TABLE IF NOT EXISTS `audit_logs` (
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

-- Initialiser les exemples uniquement si toutes les tables sont vides.
SET @initialiser_demo = ((SELECT COUNT(*) FROM `citoyen`) = 0 AND (SELECT COUNT(*) FROM `bureau_postal`) = 0 AND (SELECT COUNT(*) FROM `colis`) = 0 AND (SELECT COUNT(*) FROM `propriete`) = 0 AND (SELECT COUNT(*) FROM `impot`) = 0 AND (SELECT COUNT(*) FROM `ligne_transport`) = 0 AND (SELECT COUNT(*) FROM `station`) = 0 AND (SELECT COUNT(*) FROM `horaire`) = 0 AND (SELECT COUNT(*) FROM `service_municipal`) = 0 AND (SELECT COUNT(*) FROM `demande_municipale`) = 0 AND (SELECT COUNT(*) FROM `assure_social`) = 0 AND (SELECT COUNT(*) FROM `beneficiaire`) = 0 AND (SELECT COUNT(*) FROM `demande_remboursement`) = 0 AND (SELECT COUNT(*) FROM `document_cnam`) = 0 AND (SELECT COUNT(*) FROM `decision_cnam`) = 0 AND (SELECT COUNT(*) FROM `audit_logs`) = 0);

-- Mot de passe des deux comptes de demonstration : password.
INSERT INTO citoyen (id_citoyen, nom, prenom, email, telephone, mot_de_passe, role, actif)
SELECT 1, 'Demonstration', 'Admin', 'admin@etounsi.tn', '22123456', '$2y$10$Yz1t/EbsJ1FGFj7sbkeavOOfFXow/AeLvKLVOTDiJ5YNcm4zQQqF.', 'admin', 1
WHERE @initialiser_demo = 1;

INSERT INTO citoyen (id_citoyen, nom, prenom, email, telephone, mot_de_passe, role, actif)
SELECT 2, 'Demonstration', 'Citoyen', 'citoyen@etounsi.tn', '22123457', '$2y$10$8tfsYiJQAyEezu2FtR3OUul8SJO5SEITC6QayiJ1S3ChlIZVxOl.m', 'citoyen', 1
WHERE @initialiser_demo = 1;

INSERT INTO bureau_postal (id_bureau, nom, adresse, ville)
SELECT 1, 'Bureau Central Tunis', 'Rue Charles de Gaulle, Tunis', 'Tunis' WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 'Bureau Ariana', 'Avenue de l’Independance, Ariana', 'Ariana' WHERE @initialiser_demo = 1
UNION ALL SELECT 3, 'Bureau Tunis Centre', 'Avenue Habib Bourguiba', 'Tunis' WHERE @initialiser_demo = 1
UNION ALL SELECT 4, 'Bureau Sfax Ville', 'Avenue Hedi Chaker', 'Sfax' WHERE @initialiser_demo = 1
UNION ALL SELECT 5, 'Bureau Sousse Centre', 'Avenue de la Republique', 'Sousse' WHERE @initialiser_demo = 1
UNION ALL SELECT 6, 'Bureau Nabeul Ville', 'Avenue Habib Thameur', 'Nabeul' WHERE @initialiser_demo = 1;

INSERT INTO colis (id_colis, id_bureau_depart, id_citoyen, numero_suivi, statut, nom_citoyen, date_declaration)
SELECT 1, 1, 2, 'ET000001TN', 'En attente', 'Citoyen Demonstration', '2026-11-20' WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 2, 2, 'ET000002TN', 'En transit', 'Citoyen Demonstration', '2026-11-20' WHERE @initialiser_demo = 1
UNION ALL SELECT 3, 3, 2, 'ET000003TN', 'Livré', 'Citoyen Demonstration', '2026-11-20' WHERE @initialiser_demo = 1;

INSERT INTO propriete (id, id_citoyen, type, reference, adresse, valeur)
SELECT 1, 2, 'maison', 'DEMO-MAISON-001', 'Tunis - adresse de demonstration', 120000 WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 2, 'voiture', 'DEMO-VOITURE-001', 'Tunis - adresse de demonstration', 35000 WHERE @initialiser_demo = 1;

INSERT INTO impot (id, id_propriete, annee, montant, date_limite, statut)
SELECT 1, 1, 2026, 120, '2026-12-31', 'non_paye' WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 2, 2026, 90, '2026-12-31', 'en_attente' WHERE @initialiser_demo = 1
UNION ALL SELECT 3, 1, 2025, 100, '2025-12-31', 'paye' WHERE @initialiser_demo = 1;

INSERT INTO ligne_transport (id_ligne, numero, nom, tarif, actif)
SELECT 1, 'T1', 'Tunis - Ariana', 0.800, 1 WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 'T2', 'Tunis - Sousse', 12.000, 1 WHERE @initialiser_demo = 1;

INSERT INTO station (id_station, nom, adresse, ordre, id_ligne)
SELECT 1, 'Tunis Centre', 'Tunis', 1, 1 WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 'Ariana Centre', 'Ariana', 2, 1 WHERE @initialiser_demo = 1
UNION ALL SELECT 3, 'Sousse Centre', 'Sousse', 2, 2 WHERE @initialiser_demo = 1;

INSERT INTO horaire (id_horaire, id_ligne, sens, heure_depart, heure_arrivee, jours)
SELECT 1, 1, 'Aller', '06:00:00', '06:25:00', 'Tous les jours' WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 1, 'Aller', '08:00:00', '08:25:00', 'Lundi - Vendredi' WHERE @initialiser_demo = 1
UNION ALL SELECT 3, 1, 'Retour', '17:30:00', '17:55:00', 'Lundi - Vendredi' WHERE @initialiser_demo = 1
UNION ALL SELECT 4, 1, 'Retour', '21:00:00', '21:25:00', 'Tous les jours' WHERE @initialiser_demo = 1
UNION ALL SELECT 5, 2, 'Aller', '07:00:00', '09:30:00', 'Tous les jours' WHERE @initialiser_demo = 1
UNION ALL SELECT 6, 2, 'Aller', '10:00:00', '12:30:00', 'Week-end' WHERE @initialiser_demo = 1
UNION ALL SELECT 7, 2, 'Retour', '16:00:00', '18:30:00', 'Tous les jours' WHERE @initialiser_demo = 1;

INSERT INTO service_municipal (id_service, nom, description, delai_traitement)
SELECT 1, 'Acte de naissance', 'Exemple de service municipal.', 2 WHERE @initialiser_demo = 1
UNION ALL SELECT 2, 'Legalisation de signature', 'Exemple de service municipal.', 1 WHERE @initialiser_demo = 1
UNION ALL SELECT 3, 'Certificat de residence', 'Exemple de service municipal.', 3 WHERE @initialiser_demo = 1;
