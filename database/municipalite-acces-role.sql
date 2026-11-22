-- Migration idempotente : droits back-office du module Municipalité.
ALTER TABLE citoyen MODIFY role ENUM('citoyen','agent','admin') NOT NULL DEFAULT 'citoyen';

CREATE TABLE IF NOT EXISTS municipalite_agent_access (
  id_citoyen INT NOT NULL PRIMARY KEY,
  consulter_demandes TINYINT(1) NOT NULL DEFAULT 0,
  traiter_demandes TINYINT(1) NOT NULL DEFAULT 0,
  modifie_par INT DEFAULT NULL,
  modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_municipalite_access_agent FOREIGN KEY (id_citoyen) REFERENCES citoyen(id_citoyen) ON DELETE CASCADE,
  CONSTRAINT fk_municipalite_access_admin FOREIGN KEY (modifie_par) REFERENCES citoyen(id_citoyen) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
