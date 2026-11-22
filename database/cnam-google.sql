-- Mariem Kaabi — cinq entités métier conformes au diagramme corrigé.
-- Appliquer avec scripts/migrate-cnam.php : aucun effacement de données existantes.
ALTER TABLE citoyen MODIFY role ENUM('citoyen','agent','admin') NOT NULL DEFAULT 'citoyen';

CREATE TABLE IF NOT EXISTS assure_social (
 id_assure INT AUTO_INCREMENT PRIMARY KEY,
 id_citoyen INT NOT NULL UNIQUE,
 matricule_cnam VARCHAR(32) NOT NULL UNIQUE,
 filiere_soins VARCHAR(80) NOT NULL,
 telephone VARCHAR(25) NOT NULL,
 adresse VARCHAR(255) NOT NULL,
 CONSTRAINT cnam_assure_citoyen FOREIGN KEY (id_citoyen) REFERENCES citoyen(id_citoyen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS beneficiaire (
 id_beneficiaire INT AUTO_INCREMENT PRIMARY KEY,
 id_assure INT NOT NULL,
 nom VARCHAR(100) NOT NULL,
 prenom VARCHAR(100) NOT NULL,
 date_naissance DATE NOT NULL,
 lien_parente ENUM('SOI','CONJOINT','ENFANT','PARENT','AUTRE') NOT NULL,
 statut_validation ENUM('EN_ATTENTE','VALIDE','REFUSE') NOT NULL DEFAULT 'EN_ATTENTE',
 soi_unique INT GENERATED ALWAYS AS (CASE WHEN lien_parente='SOI' THEN id_assure ELSE NULL END) STORED UNIQUE,
 CONSTRAINT cnam_beneficiaire_assure FOREIGN KEY (id_assure) REFERENCES assure_social(id_assure)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS demande_remboursement (
 id_demande INT AUTO_INCREMENT PRIMARY KEY,
 id_beneficiaire INT NOT NULL,
 reference VARCHAR(40) NOT NULL UNIQUE,
 date_soins DATE NOT NULL,
 nature_soins VARCHAR(150) NOT NULL,
 date_depot DATETIME NULL,
 montant_demande DECIMAL(12,3) NOT NULL,
 statut ENUM('BROUILLON','DEPOSEE','EN_ETUDE','A_COMPLETER','ACCEPTEE','REFUSEE','PAYEE') NOT NULL DEFAULT 'BROUILLON',
 date_versement DATE NULL,
 reference_versement VARCHAR(100) NULL,
 version INT UNSIGNED NOT NULL DEFAULT 1,
 creation_token CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL UNIQUE,
 UNIQUE KEY cnam_demande_beneficiaire (id_demande,id_beneficiaire),
 CONSTRAINT cnam_demande_beneficiaire_fk FOREIGN KEY (id_beneficiaire) REFERENCES beneficiaire(id_beneficiaire),
 CONSTRAINT cnam_montant_positif CHECK (montant_demande > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS document_cnam (
 id_document INT AUTO_INCREMENT PRIMARY KEY,
 id_beneficiaire INT NOT NULL,
 id_demande INT NULL,
 type_document VARCHAR(40) NOT NULL,
 fichier VARCHAR(80) CHARACTER SET ascii COLLATE ascii_bin NOT NULL UNIQUE,
 date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 nom_original VARCHAR(180) NOT NULL,
 mime VARCHAR(40) NOT NULL,
 taille INT UNSIGNED NOT NULL,
 CONSTRAINT cnam_document_beneficiaire FOREIGN KEY (id_beneficiaire) REFERENCES beneficiaire(id_beneficiaire),
 CONSTRAINT cnam_document_coherent FOREIGN KEY (id_demande,id_beneficiaire)
  REFERENCES demande_remboursement(id_demande,id_beneficiaire)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS decision_cnam (
 id_decision INT AUTO_INCREMENT PRIMARY KEY,
 id_demande INT NOT NULL,
 id_agent INT NOT NULL,
 resultat ENUM('A_COMPLETER','ACCEPTEE','REFUSEE') NOT NULL,
 motif TEXT NOT NULL,
 montant_accorde DECIMAL(12,3) NULL,
 date_decision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT cnam_decision_demande FOREIGN KEY (id_demande) REFERENCES demande_remboursement(id_demande),
 CONSTRAINT cnam_decision_agent FOREIGN KEY (id_agent) REFERENCES citoyen(id_citoyen),
 CONSTRAINT cnam_accord CHECK (montant_accorde IS NULL OR montant_accorde >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tables techniques : aucun nouveau compte métier distinct de Citoyen.
CREATE TABLE IF NOT EXISTS cnam_habilitations (
 id_citoyen INT PRIMARY KEY,
 CONSTRAINT cnam_habilitation_citoyen FOREIGN KEY (id_citoyen) REFERENCES citoyen(id_citoyen) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS google_identities (
 google_sub VARCHAR(255) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
 id_citoyen INT NOT NULL UNIQUE,
 date_liaison DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT google_identity_citoyen FOREIGN KEY (id_citoyen) REFERENCES citoyen(id_citoyen) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
