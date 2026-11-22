-- Module Municipalité E-Tounsi (tables dédiées, sans toucher aux autres modules).
-- À importer après database/etounsi.sql dans la base de l'application.
ALTER TABLE citoyen MODIFY role ENUM('citoyen','agent','admin') NOT NULL DEFAULT 'citoyen';

CREATE TABLE IF NOT EXISTS municipalite_service (
  id_service INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(150) NOT NULL UNIQUE,
  type_formulaire VARCHAR(30) NOT NULL DEFAULT 'general',
  description TEXT NOT NULL,
  pieces_requises TEXT NOT NULL,
  frais DECIMAL(10,3) NOT NULL DEFAULT 0.000,
  mode_traitement ENUM('formulaire','rendez_vous') NOT NULL DEFAULT 'formulaire',
  actif TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS municipalite_demande (
  id_demande INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_service INT NOT NULL,
  id_citoyen INT NOT NULL,
  date_depot DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  statut ENUM('en_attente','en_cours','approuvee','document_pret','rejetee','annulee') NOT NULL DEFAULT 'en_attente',
  nom_concerne VARCHAR(150) DEFAULT NULL,
  telephone_contact VARCHAR(16) NOT NULL,
  date_acte DATE DEFAULT NULL,
  lieu_acte VARCHAR(150) DEFAULT NULL,
  precision_demande JSON DEFAULT NULL,
  gouvernorat VARCHAR(100) DEFAULT NULL,
  delegation VARCHAR(150) DEFAULT NULL,
  date_souhaitee DATETIME DEFAULT NULL,
  statut_rdv ENUM('en_attente','confirme','refuse','annule') DEFAULT NULL,
  CONSTRAINT fk_municipalite_demande_service FOREIGN KEY (id_service) REFERENCES municipalite_service(id_service),
  CONSTRAINT fk_municipalite_demande_citoyen FOREIGN KEY (id_citoyen) REFERENCES citoyen(id_citoyen) ON DELETE CASCADE,
  INDEX idx_municipalite_demande_citoyen (id_citoyen, date_depot),
  INDEX idx_municipalite_demande_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS municipalite_paiement (
  id_paiement INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_demande INT NOT NULL UNIQUE,
  montant DECIMAL(10,3) NOT NULL,
  devise CHAR(3) NOT NULL DEFAULT 'TND',
  statut ENUM('en_attente','payee','annulee') NOT NULL DEFAULT 'en_attente',
  mode_paiement VARCHAR(30) NOT NULL DEFAULT 'demo',
  reference VARCHAR(80) DEFAULT NULL UNIQUE,
  cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  paye_le DATETIME DEFAULT NULL,
  CONSTRAINT fk_municipalite_paiement_demande FOREIGN KEY (id_demande) REFERENCES municipalite_demande(id_demande) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS municipalite_notification (
  id_notification INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_citoyen INT NOT NULL,
  id_demande INT DEFAULT NULL,
  titre VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  lu TINYINT(1) NOT NULL DEFAULT 0,
  cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_municipalite_notification_citoyen FOREIGN KEY (id_citoyen) REFERENCES citoyen(id_citoyen) ON DELETE CASCADE,
  CONSTRAINT fk_municipalite_notification_demande FOREIGN KEY (id_demande) REFERENCES municipalite_demande(id_demande) ON DELETE CASCADE,
  INDEX idx_municipalite_notification_citoyen (id_citoyen, lu, cree_le)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Droits Municipalité attribués aux comptes Agent par un administrateur.
CREATE TABLE IF NOT EXISTS municipalite_agent_access (
  id_citoyen INT NOT NULL PRIMARY KEY,
  consulter_demandes TINYINT(1) NOT NULL DEFAULT 0,
  traiter_demandes TINYINT(1) NOT NULL DEFAULT 0,
  modifie_par INT DEFAULT NULL,
  modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_municipalite_access_agent FOREIGN KEY (id_citoyen) REFERENCES citoyen(id_citoyen) ON DELETE CASCADE,
  CONSTRAINT fk_municipalite_access_admin FOREIGN KEY (modifie_par) REFERENCES citoyen(id_citoyen) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO municipalite_service (libelle,type_formulaire,description,pieces_requises,frais,mode_traitement) VALUES
('Extrait de naissance','naissance','Demande de délivrance d’un extrait de naissance.','Pièce d’identité nationale (à confirmer auprès de la municipalité)',0.500,'formulaire'),
('Copie d’acte de naissance','naissance','Demande d’une copie d’acte de naissance.','Pièce d’identité nationale (à confirmer auprès de la municipalité)',2.000,'formulaire'),
('Extrait d’acte de mariage','mariage','Demande d’un extrait d’acte de mariage.','Pièce d’identité nationale (à confirmer auprès de la municipalité)',1.000,'formulaire'),
('Copie d’acte de mariage','mariage','Demande d’une copie d’acte de mariage.','Pièce d’identité nationale (à confirmer auprès de la municipalité)',2.000,'formulaire'),
('Acte de mariage','mariage','Demande d’un acte de mariage.','Pièce d’identité nationale (à confirmer auprès de la municipalité)',10.000,'formulaire'),
('Extrait de décès','deces','Demande de délivrance d’un extrait de décès.','Pièce d’identité nationale (à confirmer auprès de la municipalité)',0.500,'formulaire'),
('Légalisation de signature','general','Rendez-vous pour légaliser une signature en présence du demandeur.','Pièce d’identité nationale ; document à signer',0.500,'rendez_vous'),
('Copie conforme de documents','general','Rendez-vous pour faire certifier conforme une copie et présenter l’original.','Pièce d’identité nationale ; original et copie',0.300,'rendez_vous'),
('Définition de signature','general','Rendez-vous municipal pour la définition de signature.','Pièce d’identité nationale ; pièces demandées par la commune',0.500,'rendez_vous'),
('Autorisation d’occupation du domaine public','general','Demande d’autorisation municipale.','Pièce d’identité nationale ; plan de situation ; description de l’activité',15.000,'formulaire')
ON DUPLICATE KEY UPDATE type_formulaire=VALUES(type_formulaire),description=VALUES(description),pieces_requises=VALUES(pieces_requises),frais=VALUES(frais),mode_traitement=VALUES(mode_traitement),actif=1;
