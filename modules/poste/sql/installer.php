<?php

// Exécution dans le terminal uniquement, depuis le dossier du projet.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/config/database.php';

$parametres = Config::parametres();
if (!preg_match('/^[a-zA-Z0-9_]+$/', $parametres['base'])) {
    exit("Nom de base invalide.\n");
}

try {
    $dsn = 'mysql:host=' . $parametres['hote'] . ';port=' . $parametres['port'] . ';charset=utf8mb4';
    $serveur = new PDO($dsn, $parametres['utilisateur'], $parametres['mot_de_passe'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $serveur->exec('CREATE DATABASE IF NOT EXISTS ' . $parametres['base']
        . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $base = Config::getConnexion();
    $base->exec(file_get_contents(__DIR__ . '/database.sql'));

    // Mise à jour des bases installées avec les deux anciens rôles administrateurs.
    $migration = $base->prepare('SELECT nom FROM Migration WHERE nom = :nom');
    $migration->execute(['nom' => 'administrateur-unique-20261120']);
    if (!$migration->fetch()) {
        $base->exec("ALTER TABLE Citoyen MODIFY role
            ENUM('citoyen', 'admin_poste', 'super_admin', 'admin') NOT NULL DEFAULT 'citoyen'");
        $base->beginTransaction();
        $base->exec("UPDATE Citoyen SET role = 'admin', version_session = version_session + 1
            WHERE role IN ('admin_poste', 'super_admin')");

        // Retirer uniquement l'ancien compte de démonstration, sans supprimer ses colis.
        $autreAdmin = $base->prepare("SELECT COUNT(*) FROM Citoyen
            WHERE role = 'admin' AND actif = 1 AND email <> :email");
        $autreAdmin->execute(['email' => 'super.admin@example.test']);
        if ((int) $autreAdmin->fetchColumn() > 0) {
            $suppression = $base->prepare("DELETE FROM Citoyen WHERE email = :email AND role = 'admin'
                AND id_citoyen NOT IN (SELECT id_citoyen FROM Colis)");
            $suppression->execute(['email' => 'super.admin@example.test']);
        }
        $base->commit();
        $base->exec("ALTER TABLE Citoyen MODIFY role ENUM('citoyen', 'admin') NOT NULL DEFAULT 'citoyen'");
        $base->prepare('INSERT INTO Migration (nom) VALUES (:nom)')
            ->execute(['nom' => 'administrateur-unique-20261120']);
        echo "Le rôle Administrateur regroupe tous les accès du Back Office.\n";
    }

    $cheminAcces = dirname(__DIR__) . '/config/acces-local.txt';
    $ancienAdmin = $base->prepare('SELECT id_citoyen FROM Citoyen WHERE email = :email');
    $ancienAdmin->execute(['email' => 'super.admin@example.test']);
    if (!$ancienAdmin->fetch() && file_exists($cheminAcces)) {
        $contenu = str_replace("\r\n", "\n", file_get_contents($cheminAcces));
        $blocsConserves = [];
        foreach (explode("\n\n", $contenu) as $bloc) {
            if (strpos($bloc, 'Email super.admin@example.test') === false) {
                $blocsConserves[] = $bloc;
            }
        }
        if (file_put_contents($cheminAcces, implode("\n\n", $blocsConserves), LOCK_EX) === false) {
            throw new RuntimeException('Impossible de mettre à jour les accès locaux.');
        }
    }

    $requete = $base->prepare('SELECT nom FROM Migration WHERE nom = :nom');
    $requete->execute(['nom' => 'utilisateurs-et-poste-20261119']);
    if ($requete->fetch()) {
        echo "La base est déjà installée.\n";
        exit;
    }

    if ((int) $base->query('SELECT COUNT(*) FROM Citoyen')->fetchColumn() !== 0
        || (int) $base->query('SELECT COUNT(*) FROM BureauPostal')->fetchColumn() !== 0) {
        exit("Une base contenant des données existe déjà. Vérifiez-la avant de relancer l’installation.\n");
    }

    $base->beginTransaction();
    $base->exec("INSERT INTO Citoyen (nom, email) VALUES
        ('Citoyen démonstration', 'citoyen@example.test'),
        ('Second compte de démonstration', 'citoyen2@example.test')");
    $base->exec("INSERT INTO BureauPostal (nom, adresse, ville) VALUES
        ('Bureau Tunis Centre', 'Avenue Habib Bourguiba', 'Tunis'),
        ('Bureau Sfax Ville', 'Avenue Hédi Chaker', 'Sfax'),
        ('Bureau Sousse Centre', 'Avenue de la République', 'Sousse'),
        ('Bureau Nabeul Ville', 'Avenue Habib Thameur', 'Nabeul')");
    $base->exec("INSERT INTO Colis (id_bureau_depart, id_citoyen, numero_suivi, statut) VALUES
        (1, 1, 'ET000001TN', 'En transit'), (2, 1, 'ET000002TN', 'En attente'),
        (3, 2, 'ET000003TN', 'Livré')");

    $comptes = $base->query("SELECT id_citoyen, nom, email FROM Citoyen
        WHERE email LIKE '%@example.test' AND mot_de_passe IS NULL")->fetchAll();
    $ajoutAdmin = $base->prepare("INSERT INTO Citoyen (nom, email, role) VALUES (:nom, :email, :role)");
    $ajoutAdmin->execute([
        'nom' => 'Administration Poste', 'email' => 'admin.poste@example.test', 'role' => 'admin',
    ]);
    $comptes[] = [
        'id_citoyen' => $base->lastInsertId(),
        'nom' => 'Administration Poste',
        'email' => 'admin.poste@example.test',
    ];

    $acces = "E-Tounsi — comptes de validation locale\n\n";
    $changerPassword = $base->prepare('UPDATE Citoyen SET mot_de_passe = :mot_de_passe WHERE id_citoyen = :id');
    foreach ($comptes as $compte) {
        $password = 'Et!' . bin2hex(random_bytes(8));
        $changerPassword->execute([
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $compte['id_citoyen'],
        ]);
        $acces .= $compte['nom'] . "\nEmail " . $compte['email'] . "\nMot de passe " . $password . "\n\n";
    }
    if (file_put_contents(dirname(__DIR__) . '/config/acces-local.txt', $acces, LOCK_EX) === false) {
        throw new RuntimeException('Impossible de sauvegarder les accès de validation.');
    }

    $base->prepare('INSERT INTO Migration (nom) VALUES (:nom)')
        ->execute(['nom' => 'utilisateurs-et-poste-20261119']);
    $base->commit();

    echo "MySQL installé. Les accès locaux se trouvent dans config/acces-local.txt.\n";
    foreach (['Citoyen', 'BureauPostal', 'Colis'] as $table) {
        echo $table . ' : ' . $base->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn() . "\n";
    }
} catch (Exception $erreur) {
    if (isset($base) && $base->inTransaction()) {
        $base->rollBack();
    }
    fwrite(STDERR, "Installation interrompue. " . $erreur->getMessage() . "\n");
    exit(1);
}
