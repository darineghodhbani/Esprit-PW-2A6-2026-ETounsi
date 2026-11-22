-- Bases existantes : executer une seule fois apres avoir selectionne la base E-Tounsi.
-- Ajoute la table des horaires du module Transport. Les lignes et les stations existantes sont conservees.
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

-- Horaires de demonstration pour les lignes T1 et T2, seulement si la table est vide et si ces lignes existent.
SET @ajouter_horaires = ((SELECT COUNT(*) FROM `horaire`) = 0
    AND (SELECT COUNT(*) FROM `ligne_transport` WHERE id_ligne IN (1, 2)) = 2);

INSERT INTO horaire (id_ligne, sens, heure_depart, heure_arrivee, jours)
SELECT 1, 'Aller', '06:00:00', '06:25:00', 'Tous les jours' WHERE @ajouter_horaires = 1
UNION ALL SELECT 1, 'Aller', '08:00:00', '08:25:00', 'Lundi - Vendredi' WHERE @ajouter_horaires = 1
UNION ALL SELECT 1, 'Retour', '17:30:00', '17:55:00', 'Lundi - Vendredi' WHERE @ajouter_horaires = 1
UNION ALL SELECT 1, 'Retour', '21:00:00', '21:25:00', 'Tous les jours' WHERE @ajouter_horaires = 1
UNION ALL SELECT 2, 'Aller', '07:00:00', '09:30:00', 'Tous les jours' WHERE @ajouter_horaires = 1
UNION ALL SELECT 2, 'Aller', '10:00:00', '12:30:00', 'Week-end' WHERE @ajouter_horaires = 1
UNION ALL SELECT 2, 'Retour', '16:00:00', '18:30:00', 'Tous les jours' WHERE @ajouter_horaires = 1;
