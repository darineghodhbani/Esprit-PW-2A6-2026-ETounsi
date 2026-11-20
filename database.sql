CREATE DATABASE IF NOT EXISTS etounsi CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE etounsi;

CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(50)  NOT NULL,
    prenom      VARCHAR(50)  NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    telephone   VARCHAR(8)   NOT NULL,
    password    VARCHAR(255) NOT NULL,                       -- haché avec password_hash()
    role        ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    statut      ENUM('actif', 'bloque') NOT NULL DEFAULT 'actif',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO users (nom, prenom, email, telephone, password, role) VALUES
('Admin',   'E-Tounsi', 'admin@etounsi.tn',   '71000000', '$2y$10$NuaWBTMqzCXYy2rstjLYf.TrVxoJCx8.rmEcX9WEPe.CQRgVphwKC', 'admin'),
('Ben Ali', 'Sami',     'citoyen@etounsi.tn', '22123456', '$2y$10$bpAUB.bDrS99OGbRCO23TOGvYo6MLi5nAlQfAuOI7ms/7yHDJHKBq', 'user');

CREATE TABLE IF NOT EXISTS propriete (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_user     INT          NOT NULL,
    type        ENUM('maison', 'appartement', 'terrain', 'voiture', 'local_commercial') NOT NULL,
    reference   VARCHAR(50)  NOT NULL,
    adresse     VARCHAR(150) NOT NULL,
    valeur      INT          NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS impot (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    id_propriete  INT  NOT NULL,
    annee         INT  NOT NULL,
    montant       INT  NOT NULL,
    date_limite   DATE NOT NULL,
    statut        ENUM('non_paye', 'en_attente', 'paye') NOT NULL DEFAULT 'non_paye',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_propriete) REFERENCES propriete(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO propriete (id, id_user, type, reference, adresse, valeur) VALUES
(1, 2, 'maison',  'TF-123456',   '12 rue de Marseille, Tunis', 250000),
(2, 2, 'voiture', '215 TU 4587', 'Ariana',                     45000);

INSERT IGNORE INTO impot (id, id_propriete, annee, montant, date_limite, statut) VALUES
(1, 1, 2025, 320, '2025-12-31', 'paye'),
(2, 1, 2026, 340, '2026-12-31', 'non_paye'),
(3, 2, 2026, 180, '2026-12-31', 'non_paye');
