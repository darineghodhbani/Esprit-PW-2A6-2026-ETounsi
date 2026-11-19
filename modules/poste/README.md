# E-Tounsi — Module Poste et gestion des utilisateurs

## Overview

Ce module de Maryem Kochbati, équipe Power Girls, permet de consulter les bureaux de poste et de déclarer, suivre et administrer les colis. Il intègre le Front Office eLearning et le Back Office Spark Admin dans une seule structure MVC PHP.

## Features

- Annuaire des bureaux avec filtre par ville et suivi public des colis.
- Inscription, connexion, profil et déconnexion.
- Déclaration d’envoi, historique personnel et annulation des envois en attente.
- CRUD des bureaux, gestion des colis et modification des statuts.
- CRUD des utilisateurs, gestion des rôles et activation des comptes.
- Contrôle des accès, sessions et validation PHP des formulaires.
- Front Office en français et en arabe, logo E-Tounsi de la présentation.

## Tech Stack

HTML5, CSS3, Flexbox, Grid, JavaScript, Bootstrap, PHP 8.2, PDO MySQL et MariaDB avec XAMPP. Les extensions PHP `pdo_mysql` et `mbstring` sont nécessaires.

## Architecture

```text
poste/
├── index.php                   Point d’entrée du Front et du Back Office
├── .htaccess                   Accès local et anciennes adresses
├── config/
│   ├── database.php            Connexion PDO
│   └── local.example.php       Exemple de configuration locale
├── app/
│   ├── core/
│   │   ├── App.php             Choix du contrôleur avec switch
│   │   ├── Controller.php      Affichage commun du Back Office
│   │   ├── fonctions.php       Liens, traduction et échappement HTML
│   │   └── securite.php        Sessions, rôles et jetons CSRF
│   ├── controllers/            Requêtes, validation et traitements
│   ├── models/                 Utilisateur, BureauPostal et Colis
│   └── views/
│       ├── layouts/            Assemblage du Front et du Back Office
│       ├── includes/           Blocs communs eLearning
│       ├── partials/           Blocs communs Spark Admin
│       ├── admin/              Pages d’administration
│       └── *.php               Pages citoyennes avec leur HTML
├── public/assets/
│   ├── front/                  CSS et JavaScript du Front Office
│   ├── back/                   CSS et JavaScript du Back Office
│   ├── images/                 Logo et image du template
│   ├── fonts/                  Polices locales
│   ├── js/                     Bootstrap partagé
│   └── vendor/                 Icônes partagées
└── sql/
    ├── database.sql            Tables et relations
    └── installer.php           Installation depuis le terminal
```

Les modèles représentent les entités avec des attributs privés, un constructeur et des getters/setters. Les contrôleurs contiennent les requêtes PDO et les traitements, selon le workshop de sécurité. `HomeController` prépare les pages citoyennes. `AdminController`, `BureauController`, `ColisController` et `UtilisateurController` préparent les vues administratives. `AuthController` gère les comptes et `PosteController` les données postales.

Les vues contiennent le HTML. Les fichiers `public/assets/front/css/style.css` et `public/assets/back/css/main.css` proviennent des templates fournis. Leurs adaptations se trouvent dans les deux fichiers `poste.css`. Le Front Office assemble `head`, `spinner`, `navbar`, la vue, `footer`, puis `scripts`, dans l’ordre de l’atelier.

### Notions des cours

Le PHP utilise les fonctions, tableaux, conditions, boucles, formulaires GET/POST, classes, sessions et requêtes PDO préparées. Le JavaScript utilise les fonctions, les sélecteurs DOM, `classList`, `dataset` et `addEventListener`, étudiés dans les chapitres 2, 3 et 4 et leurs ateliers. Les formulaires sont contrôlés côté PHP, y compris lorsque JavaScript est désactivé. Aucune compilation n’est nécessaire.

## Contributors

Maryem Kochbati — module Poste et gestion des utilisateurs, équipe Power Girls.

## Academic Context

Developed at Esprit School of Engineering – Tunisia. Projet Technologies Web, classe 2A6, année universitaire 2026–2027.

## Getting Started

1. Cloner le dépôt d’équipe dans `C:\xampp\htdocs\Esprit-PW-2A6-2026-ETounsi`.
2. Démarrer Apache et MySQL dans XAMPP.
3. Ouvrir CMD et exécuter les commandes suivantes.

```bat
cd /d C:\xampp\htdocs\Esprit-PW-2A6-2026-ETounsi\modules\poste
C:\xampp\php\php.exe sql\installer.php
```

4. Ouvrir le [Front Office](http://localhost/Esprit-PW-2A6-2026-ETounsi/modules/poste/index.php).
5. Consulter les comptes générés dans `config/acces-local.txt`, fichier privé exclu de Git. Se connecter avec un administrateur pour accéder au [Back Office](http://localhost/Esprit-PW-2A6-2026-ETounsi/modules/poste/index.php?espace=admin).

La configuration XAMPP locale utilise `127.0.0.1`, le port `3306`, la base `etounsi_poste` et l’utilisateur MySQL `root` avec le mot de passe local vide. Pour adapter ces valeurs, copier `config/local.example.php` vers `config/local.php`. Ce fichier reste privé et exclu de Git.

Le schéma se trouve dans `sql/database.sql` et la base est visible dans [phpMyAdmin](http://localhost/phpmyadmin/). L’installation ajoute trois comptes de démonstration, quatre bureaux et trois colis sur une base neuve. Le numéro `ET000001TN` permet de tester le suivi. Relancer l’installateur sur une base déjà installée conserve ses données. Apache réserve l’application à la machine locale pour la validation.

### Rôles

| Rôle | Accès |
| --- | --- |
| Visiteur | Consulter les bureaux, suivre un colis, s’inscrire ou se connecter |
| Citoyen | Déclarer, consulter et annuler ses propres envois en attente, modifier son profil |
| Administrateur | Gérer les utilisateurs, leurs rôles, les bureaux et les colis, modifier son profil |

L’inscription publique crée un citoyen. La suppression d’un utilisateur possédant des colis est bloquée pour conserver leur historique. Son compte peut être désactivé. Au moins un administrateur actif doit rester disponible.

Le compte administrateur de démonstration est `admin.poste@example.test`. Il accède à toutes les pages du Back Office. Sur une ancienne installation, relancer `C:\xampp\php\php.exe sql\installer.php` met à jour les rôles et conserve les données métier. L’ancien compte de démonstration supplémentaire est retiré seulement si un autre administrateur actif existe et qu’aucun colis ne lui est associé.

### Sessions et validation

Les requêtes utilisent des paramètres PDO. Les sorties HTML sont échappées. Les actions de modification utilisent POST et un jeton CSRF. Les mots de passe sont hachés et comportent au moins huit caractères, dans la limite de 72 octets. Le rôle, l’état du compte et la version de session sont vérifiés côté serveur.

La connexion renouvelle l’identifiant de session. Les cookies sont HttpOnly et SameSite=Lax, avec Secure en HTTPS. Une session expire après 30 minutes d’inactivité ou huit heures de connexion. Modifier le mot de passe, le rôle ou l’activation révoque les sessions antérieures. La limitation des tentatives de connexion s’applique à la session du navigateur après cinq échecs en cinq minutes.

## Contribution

Créer une branche par tâche, tester sur XAMPP, puis ouvrir une pull request vers le dépôt d’équipe. Les contributions de Maryem sont suivies dans le [GitHub Project Board Poste et utilisateurs](https://github.com/users/kochbatimaryem574-maker/projects/1).

## Licence

Les notices tierces sont conservées dans `public/assets/front/LICENSE.txt`, `public/assets/back/SPARK-README.md`, `public/assets/fonts/Cairo-OFL.txt` et `public/assets/vendor/bootstrap-icons/LICENSE.txt`.

## Acknowledgments

eLearning par [HTML Codex](https://htmlcodex.com/elearning-html-template/). Spark Admin par Spark Admin Dev, distribué par [ThemeWagon](https://themewagon.com/themes/spark-admin/). Organisation MVC et sessions adaptées des workshops fournis par l’enseignante.
