-- Bases existantes : executer une seule fois apres avoir selectionne la base E-Tounsi.
-- Les anciennes references, statuts, comptes et dates d'enregistrement sont conserves.
ALTER TABLE colis
    ADD COLUMN nom_citoyen VARCHAR(200) NOT NULL DEFAULT '',
    ADD COLUMN date_declaration DATE NULL;

UPDATE colis c
INNER JOIN citoyen u ON u.id_citoyen = c.id_citoyen
SET c.nom_citoyen = TRIM(CONCAT(COALESCE(u.prenom, ''), ' ', u.nom)),
    c.date_declaration = DATE(c.created_at);

ALTER TABLE colis MODIFY COLUMN date_declaration DATE NOT NULL;
