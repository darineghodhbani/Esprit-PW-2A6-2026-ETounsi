# E-Tounsi — Module Poste et gestion des utilisateurs

## Overview

Ce module de l’équipe Power Girls regroupe les services postaux et la gestion des utilisateurs pour E-Tounsi. Le Front Office reprend eLearning et le Back Office Spark Admin. Le code utilise PHP MVC et les notions des cours HTML, CSS, PHP, POO et PDO.

## Features

- Recherche de bureaux par ville et suivi public des colis.
- Déclaration d’envoi, historique personnel et annulation d’un envoi en attente.
- Inscription citoyenne, connexion, profil et déconnexion.
- CRUD utilisateurs, attribution des rôles et activation ou désactivation des comptes.
- Gestion administrative des bureaux, des colis et de leurs statuts.
- Validation PHP des formulaires, requêtes PDO préparées, mots de passe hachés et jetons CSRF.
- Interface Front Office en français et en arabe.

## Tech Stack

### Frontend

HTML5, CSS3 avec Flexbox et Grid, JavaScript et Bootstrap. Templates eLearning et Spark Admin.

### Backend

PHP 8.2, PDO MySQL, MariaDB fourni par XAMPP et sessions PHP. Extensions requises `pdo_mysql` et `mbstring`. L’import des anciennes données demande aussi `pdo_sqlite`.

## Architecture

- `FrontOffice/index.php` prépare les pages citoyennes avec un `switch` et assemble les blocs de l’atelier.
- `FrontOffice/Controllers/` prépare les parcours de compte et de colis.
- `BackOffice/app/` contient routes, contrôleurs, vues et layout Spark Admin.
- `Models/Utilisateur.php` contient l’entité utilisateur et ses getters/setters.
- `Controllers/UtilisateurController.php` contient les requêtes et la validation des comptes.
- `Models/PosteRepository.php` contient les requêtes du module Poste.
- `securite.php` centralise les sessions, les rôles et les jetons CSRF.
- `config/database.php` ouvre la connexion PDO et `data/installation.sql` décrit la base.

## Contributors

Mariem Kochbati — module Poste, équipe Power Girls.

## Academic Context

Developed at Esprit School of Engineering – Tunisia. Projet Web de la classe 2A6, année universitaire 2026–2027.

## Getting Started

1. Cloner le dépôt d’équipe dans `C:\xampp\htdocs\Esprit-PW-2A6-2026-ETounsi`.
2. Démarrer **Apache** et **MySQL** dans XAMPP.
3. Dans CMD, ouvrir le dossier `modules\poste` du dépôt, puis lancer `C:\xampp\php\php.exe scripts\installer.php`.
4. Ouvrir `http://localhost/Esprit-PW-2A6-2026-ETounsi/modules/poste/FrontOffice/`.
5. Les comptes générés pour la validation locale se trouvent dans `data/acces-local.txt`. Ce fichier est privé et exclu de Git.

Les valeurs XAMPP locales par défaut sont le serveur `127.0.0.1`, le port `3306`, la base `etounsi_poste` et l’utilisateur MySQL `root` avec son mot de passe local vide. Si nécessaire, copier `config/local.example.php` vers `config/local.php` et adapter ces valeurs. Le fichier local reste exclu de Git.

Le script d’installation initialise MySQL une seule fois. Si une ancienne base `data/poste.sqlite` existe lors de l’installation, ses citoyens, bureaux et colis sont importés avec leurs identifiants. Les six colis existant sur la machine de développement ont ainsi été conservés. Une nouvelle installation utilise des données de démonstration et le numéro `ET000001TN`.

Le schéma est consultable dans phpMyAdmin à `http://localhost/phpmyadmin/`, base `etounsi_poste`. Apache limite l’application à un accès local, adapté à la validation sur une machine.

### Rôles

| Rôle | Accès |
| --- | --- |
| Visiteur | Consulter les bureaux, suivre un colis, créer un compte ou se connecter |
| Citoyen | Déclarer un envoi, consulter et annuler ses propres envois en attente, modifier son profil |
| Administrateur Poste | Gérer les bureaux et les colis, modifier son profil |
| Super administrateur | Accès administrateur et gestion complète des utilisateurs et de leurs rôles |

La suppression d’un utilisateur possédant des colis est bloquée pour conserver leur historique. Le super administrateur peut le désactiver. Au moins un super administrateur actif doit rester disponible.

### Sessions et validation

Les mots de passe comportent au moins huit caractères et au plus 72 octets, conformément à la limite de bcrypt. La connexion renouvelle l’identifiant de session. Les cookies sont HttpOnly et SameSite=Lax, avec Secure si HTTPS est actif. Une session expire après 30 minutes d’inactivité ou huit heures de connexion. Le rôle, l’état du compte et sa version de session sont relus côté serveur. Une modification du mot de passe, du rôle ou de l’activation révoque les sessions antérieures.

Les formulaires sont validés côté PHP même si JavaScript est désactivé. La limitation des tentatives de connexion est appliquée à la session du navigateur après cinq échecs en cinq minutes.

## Contribution

Créer une branche pour chaque tâche, vérifier le parcours sur XAMPP et proposer une pull request au dépôt d’équipe. Suivre les tâches et leur état dans le GitHub Project Board.

## Licence

Les ressources tierces conservent leurs licences. Consulter `FrontOffice/LICENSE.txt`, les notices des polices et `BackOffice/assets/SPARK-README.md`. Les comptes et les paramètres privés de chaque machine restent dans les fichiers locaux exclus de Git.

## Acknowledgments

eLearning par [HTML Codex](https://htmlcodex.com/elearning-html-template/). Spark Admin par Spark Admin Dev, distribué par [ThemeWagon](https://themewagon.com/themes/spark-admin/). Gestion des sessions adaptée du workshop fourni par l’enseignante.
