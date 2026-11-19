CREATE TABLE IF NOT EXISTS Citoyen (
    id_citoyen INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(80) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) DEFAULT NULL,
    role ENUM('citoyen', 'admin') NOT NULL DEFAULT 'citoyen',
    actif TINYINT(1) NOT NULL DEFAULT 1,
    version_session INT UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS BureauPostal (
    id_bureau INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(120) NOT NULL,
    adresse VARCHAR(250) NOT NULL,
    ville VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Colis (
    id_colis INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_bureau_depart INT UNSIGNED NOT NULL,
    id_citoyen INT UNSIGNED NOT NULL,
    numero_suivi VARCHAR(32) NOT NULL UNIQUE,
    statut ENUM('En attente', 'En transit', 'Livré', 'Annulé') NOT NULL DEFAULT 'En attente',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_colis_bureau FOREIGN KEY (id_bureau_depart) REFERENCES BureauPostal(id_bureau),
    CONSTRAINT fk_colis_citoyen FOREIGN KEY (id_citoyen) REFERENCES Citoyen(id_citoyen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Migration (
    nom VARCHAR(80) PRIMARY KEY,
    appliquee_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
