# E-Tounsi — Plateforme de services publics tunisiens

## Overview

Projet Technologies Web, classe 2A6, Esprit School of Engineering, année universitaire 2026–2027. Cette version réunit les modules Poste, Recette des Finances et CNAM dans la structure MVC et le thème commun de la branche `integration`, avec un compte citoyen partagé et une connexion Google OAuth.

## Features

- Compte commun : inscription, connexion par mot de passe ou Google, profil, CRUD utilisateurs, activation/suspension et rôles citoyen/agent/administrateur.
- CNAM : dossier assuré, bénéficiaires avec justificatifs, demandes de remboursement en brouillon ou déposées, suivi des décisions et versements. Les agents habilités valident les rattachements et traitent les demandes ; l’administrateur gère leurs habilitations.
- Poste : annuaire des bureaux, déclaration d’un envoi, référence unique, suivi et historique. Le citoyen saisit le nom et la date, choisit le bureau, puis peut modifier ces informations et supprimer ses propres déclarations en attente ; l’administrateur dispose du CRUD des bureaux et colis et gère leur traitement.
- Cycle des colis : En attente → Accepté → En transit → Livré ; annulation avant départ selon les règles du module.
- Finances : déclaration de propriétés, gestion des impôts, demande de paiement simulée, validation administrateur et reçus.
- Audit : connexions réussies/refusées, déconnexions, opérations sur les comptes, changements de rôle et accès 403. Consultation administrateur avec filtres et pagination.

## Tech Stack

PHP 8.2+, PDO, MySQL 8+, Composer, `google/auth`, HTML5, CSS3 et JavaScript. Le code utilise les classes, les sessions, les requêtes préparées et les formulaires étudiés en cours. Les vues réutilisent les ressources Front Office et Back Office présentes dans la branche d’intégration.

## Architecture

```text
E-Tounsi/
├── index.php
├── config.php
├── README.md
├── app/
│   ├── routes.php
│   ├── i18n.php / lang/
│   ├── security.php / poste.php
│   ├── Models/
│   │   ├── Citoyen.php
│   │   ├── BureauPostal.php / Colis.php
│   │   ├── Propriete.php / Impot.php
│   │   ├── AuditLog.php
│   │   └── modèles des autres membres
│   ├── Controllers/
│   │   ├── Controller.php
│   │   ├── CitoyenController.php
│   │   ├── BureauPostalController.php / ColisController.php
│   │   ├── ProprieteController.php / ImpotController.php
│   │   ├── AuditController.php / DashboardController.php
│   │   └── contrôleurs du portail et des autres membres
│   └── Views/
│       ├── frontoffice/
│       │   ├── includes/ et layout.php
│       │   ├── home.php / login.php / signup.php / profile.php
│       │   ├── poste/
│       │   ├── finances/
│       │   └── transport/ / municipalite/ / cnam/
│       └── backoffice/
│           ├── partials/ et layout.php
│           └── pages/
│               ├── dashboard.php
│               ├── utilisateurs/
│               ├── commun/  messages partagés
│               ├── poste/
│               ├── finances/
│               ├── audit/index.php
│               └── transport/ / municipalite/ / cnam/
├── assets/
│   ├── frontoffice/  css / js / img / fonts / lib
│   └── backoffice/   css / js / img / lib
├── database/etounsi.sql
├── database/cnam-google.sql
├── docs/cnam-google.md
└── uploads/cnam/  (marqueur uniquement ; les pièces sont stockées hors du site)
```

Les dix-sept modèles suivent la classe `Impot` fournie en référence : attributs privés typés, constructeur explicite, getters et setters uniquement. Ils ne contiennent ni SQL, ni connexion PDO, ni méthodes CRUD, ni constantes métier. Les listes de rôles, statuts et actions se trouvent dans les contrôleurs.

Les contrôleurs contiennent les requêtes SQL et les traitements : `$sql`, connexion avec `Database::getConnection()`, `try/catch`, `prepare()` et `execute()`. Les objets sont transmis aux méthodes d’ajout et de modification ; leurs getters fournissent les valeurs des paramètres SQL. La connexion PDO est centralisée dans `config.php`. Les méthodes de validation `clean()` et `verifier...()` préparent les valeurs `$old` et les erreurs `$erreurs`.

Comme dans Recette des Finances, les actions `listBack()`, `addBack()`, `updateBack()` et `deleteBack()` des contrôleurs Poste et utilisateurs traitent les demandes et préparent les données des vues. Les vues de liste et de formulaire affichent le HTML, les champs, les erreurs et les boucles `foreach`. Les suppressions sont traitées directement dans le contrôleur, en POST avec CSRF ; elles redirigent ensuite avec `header('Location: ...')` et `exit`. Le bureau associé à des colis reste protégé contre la suppression. La déclaration citoyenne utilise `ColisController::declarer()`.

La gestion des utilisateurs est commune : ses vues sont dans `pages/utilisateurs/`, tandis que `pages/poste/` contient les listes et formulaires des bureaux et colis. Les contrôleurs vérifient les rôles et les sessions. Les opérations sur les comptes et leurs traces utilisent une même transaction PDO ; le journal passe par l’objet `AuditLog` et les requêtes de `AuditController`. La langue choisie est conservée lors de la déconnexion.

Les classes Transport et Municipalité reprennent les attributs des tables déjà présentes dans la base commune. Le module CNAM et la connexion Google sont intégrés ; leurs requêtes et traitements se trouvent également dans les contrôleurs. Les classes de `app/Models/` contiennent uniquement les attributs, constructeurs, getters et setters. Les contrôleurs CNAM partagent les requêtes préparées et transactions de `CnamController`, et `InstallationCNAMController` est appelé uniquement par le script de migration. Les instructions de configuration et de migration sont détaillées dans [docs/cnam-google.md](docs/cnam-google.md). Tous les modules partagent les en-têtes, menus, pieds de page et sessions ; les formulaires gardent les contrôles PHP et JavaScript, et les changements de page après traitement utilisent une redirection PHP.

## Contributors

| Membre | Module |
|---|---|
| Ranim Aissa | Transport |
| Samar Debbichi | Municipalité |
| Mariem Kaabi | CNAM |
| Maryem Kochbati | Poste |
| Darine Ghodbani | Recette des Finances |
| Équipe | Compte citoyen et intégration |

## Academic Context

Développé à Esprit School of Engineering — Tunisia, dans le cadre du Projet Technologies Web de deuxième année. Les emplacements des autres modules ne signifient pas que leurs fonctionnalités sont intégrées ou validées dans cette version.

## Getting Started

1. Démarrer Apache et MySQL, puis importer `database/etounsi.sql` si la base commune `etounsi` n’existe pas encore.
2. Installer les dépendances avec `composer install`.
3. Copier `config.local.example.php` vers `config.local.php` et renseigner MySQL. Le fichier local est ignoré par Git ; `app_env` doit rester `development` en local.
4. Pour activer la connexion Google, copier `google.local.example.php` vers `google.local.php`. Renseigner l’identifiant et le secret du client Google, avec une URI de redirection correspondant exactement à l’adresse du projet. Ce fichier est aussi ignoré par Git.
5. Lancer une fois `php scripts/migrate-cnam.php`, puis ouvrir `http://localhost/NOM_DU_DOSSIER/index.php` avec Apache. Avec le serveur PHP intégré, lancer `php -S 127.0.0.1:8081 -t .` avant d’ouvrir `http://127.0.0.1:8081/index.php`.

Pour une base déjà installée avant l’ajout des champs de saisie des colis, sélectionner cette base dans phpMyAdmin et importer une seule fois `database/mise-a-jour-colis.sql`. Cette mise à jour ajoute le nom saisi et la date choisie sans remplacer les colis existants. Sur une installation neuve, `database/etounsi.sql` contient déjà ces colonnes.

Sur ce PC, le projet est dans `C:\xampp\htdocs\ETounsi-Integration-Preview` et s’ouvre à `http://localhost/ETounsi-Integration-Preview/`. Il utilise la base commune `etounsi`, définie dans `config.php`. Les données de `etounsi_integration_preview` y ont été conservées, avec les mêmes identifiants, mots de passe hachés et relations. L’ancienne base de prévisualisation et le projet Poste d’origine sont conservés séparément. Le fichier privé `config.local.php` active les clés publiques de démonstration reCAPTCHA sur cette machine ; il est ignoré par Git.

Une réimportation crée les tables absentes ; elle ne modifie pas les colonnes d’une table déjà installée. Utiliser la mise à jour ci-dessus uniquement pour une ancienne base qui ne contient pas encore les deux champs de déclaration des colis.

La migration crée les tables CNAM et Google et ajoute le rôle Agent. Elle conserve les anciennes tables CNAM en les renommant si elles sont vides ; si des données du schéma historique sont détectées, elle s’arrête pour éviter de les remplacer. Les détails du parcours, des pièces jointes et de l’OAuth sont dans [docs/cnam-google.md](docs/cnam-google.md).

Les agents sont créés avec le rôle Agent dans **Utilisateurs**, puis activés par l’administrateur dans **Habilitations CNAM**. En développement sans clés configurées, le CAPTCHA est désactivé ; en production, configurez les vraies clés reCAPTCHA dans `config.local.php`.

Routes principales :

| Espace | Adresse |
|---|---|
| Accueil | `index.php?page=home` |
| Connexion | `index.php?page=login` |
| Poste | `index.php?page=module&id=poste` |
| Bureaux / suivi | `index.php?page=bureaux-poste` / `index.php?page=suivi-colis` |
| Déclaration / mes colis | `index.php?page=declarer-envoi` / `index.php?page=mes-colis` |
| Modifier / supprimer mon colis | `index.php?page=modifier-colis&id=ID` / `index.php?page=supprimer-colis&id=ID` |
| Finances | `index.php?page=module&id=finances` |
| CNAM citoyen | `index.php?page=module&id=cnam` |
| Dossier / bénéficiaires / remboursements | `index.php?page=mon-dossier` / `index.php?page=mes-beneficiaires` / `index.php?page=mes-remboursements` |
| CNAM back office | `index.php?page=admin-demandes-cnam` |
| Administration | `index.php?page=dashboard` |
| Utilisateurs | `index.php?page=admin-utilisateurs` |
| Journal d’audit | `index.php?page=admin-audit` |

Sur une installation vide, le script crée `admin@etounsi.tn` (administrateur) et `citoyen@etounsi.tn` (citoyen), avec le mot de passe de démonstration `password`. Lors d’une réimportation sur une base remplie, les comptes et mots de passe existants sont conservés.

Le flux OAuth utilise `state`, `nonce`, PKCE et la vérification serveur de l’identité Google. Un compte déjà existant se lie depuis son profil après confirmation du mot de passe ; Google ne peut pas attribuer les rôles Agent ou Administrateur.

## Sécurité et journal d’audit

Les formulaires utilisent des jetons CSRF, des validations PHP et des sorties échappées. Les mots de passe sont hachés. L’identifiant de session est renouvelé après connexion ; le compte, le rôle et la version de session sont vérifiés côté serveur. La session expire après trente minutes d’inactivité, avec une limite de huit heures.

Dans « Mes colis », la modification et la suppression sont réservées au propriétaire tant que le statut est « En attente ». Le citoyen ne choisit ni le propriétaire, ni la référence, ni le statut. La suppression demande une confirmation et une requête POST avec un jeton CSRF. Les conditions de propriétaire et de statut sont également appliquées dans les requêtes SQL pour éviter une modification pendant l’acceptation par l’administrateur. Une référence supprimée n’apparaît plus dans le suivi.

Le citoyen saisit le nom et la date de déclaration et choisit un bureau existant. Ces informations sont enregistrées dans `colis.nom_citoyen`, `colis.date_declaration` et `colis.id_bureau_depart`. Le compte propriétaire reste déterminé par la session (`id_citoyen`) ; saisir un nom ne modifie pas le compte utilisateur. `created_at` conserve la date et l’heure réelles de l’enregistrement. Seul le code de suivi est automatique et en lecture seule : il est généré à l’enregistrement, protégé par une contrainte UNIQUE, puis affiché dans la confirmation et dans « Mes colis ». La modification permet de corriger le nom, la date et le bureau tant que le colis est en attente.

`audit_logs` contient `id`, `user_id`, `action`, `target_id`, `details`, `ip_address` et `created_at`. `actor_name` conserve le nom de l’auteur lorsqu’un compte est supprimé. L’adresse provient de `REMOTE_ADDR` ; les en-têtes transmis par le client ne sont pas utilisés comme preuve d’adresse. Le journal ne contient aucun mot de passe, hash, cookie ou jeton. Les changements d’un compte et leurs traces sont enregistrés dans une même transaction. L’administration du journal est en lecture seule.

Pour la démonstration : ouvrir Utilisateurs, modifier le rôle d’un compte de test, puis ouvrir Journal d’audit et filtrer sur ce compte et « Changement de rôle ». L’ancien rôle, le nouveau, l’auteur, la date et l’IP sont visibles.

## Vérifications

Les vérifications locales couvrent les CRUD Poste et utilisateurs, le parcours Finances avec un compte commun, les accès, les sessions, les redirections, la protection CSRF, les filtres et la pagination de l’audit. Le contrôle d’une panne du journal utilise des tables temporaires pour vérifier l’annulation de la modification du compte. Les essais sur la copie sont nettoyés après exécution. Le script SQL est aussi vérifié sur une base vide.

Le CRUD citoyen des colis a été vérifié avec deux comptes distincts : saisie et modification du nom, de la date et du bureau, suppression réelle après confirmation, validation des formulaires, refus d’accès aux colis d’un autre citoyen et refus après acceptation par l’administrateur. Les dates impossibles et les noms invalides sont refusés ; le code, le compte propriétaire et l’horodatage réel ne peuvent pas être remplacés par un formulaire falsifié. Les écrans de déclaration, liste, modification et suppression ont été contrôlés sur ordinateur et mobile, en français et en arabe. Les données de test ont été retirées sans modifier les données existantes.

## Acknowledgments

Esprit School of Engineering et l’équipe pédagogique pour les supports et ateliers. Le thème, les photographies et l’identité visuelle de la branche d’intégration sont conservés. Les notices des polices Manrope, Lora et Noto Sans Arabic restent dans `assets/frontoffice/fonts/`. Le Back Office conserve les attributions de son template et de ses ressources.

Contrôle de revue du 22 novembre 2026 : installation sur base vide vérifiée ; parcours Poste/Finances avec un compte commun et journal d’audit testés ; neuf états d’interface contrôlés sans anomalie automatisée WCAG A/AA et 2.1 AA. Ces contrôles complètent les essais fonctionnels et visuels. Les fichiers et les données du projet Poste d’origine ont été comparés à la sauvegarde et sont inchangés.
