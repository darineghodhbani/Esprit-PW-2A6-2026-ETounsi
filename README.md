# E-Tounsi — Plateforme de services publics tunisiens

## Overview

Projet Technologies Web, classe 2A6, Esprit School of Engineering, année universitaire 2026–2027. Cette version réunit les fonctionnalités Poste et Recette des Finances dans la structure MVC et le thème commun de la branche `integration`. Les dossiers Transport, Municipalité et CNAM restent disponibles pour les contributions des membres concernés.

## Features

- Compte commun : inscription, connexion, profil, CRUD utilisateurs, activation/suspension et rôles citoyen/administrateur.
- Poste : annuaire des bureaux, déclaration d’un envoi, référence unique, suivi et historique. Le citoyen saisit le nom et la date, choisit le bureau, puis peut modifier ces informations et supprimer ses propres déclarations en attente ; l’administrateur dispose du CRUD des bureaux et colis et gère leur traitement.
- Cycle des colis : En attente → Accepté → En transit → Livré ; annulation avant départ selon les règles du module.
- Finances : déclaration de propriétés, gestion des impôts, demande de paiement simulée, validation administrateur et reçus.
- Audit : connexions réussies/refusées, déconnexions, opérations sur les comptes, changements de rôle et accès 403. Consultation administrateur avec filtres et pagination.

## Tech Stack

PHP 8, PDO, MySQL/MariaDB, HTML5, CSS3 et JavaScript. Le code utilise les classes, les sessions, les requêtes préparées et les formulaires étudiés en cours. Les vues réutilisent les ressources Front Office et Back Office présentes dans la branche d’intégration.

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
└── uploads/cnam/
```

Les contrôleurs Poste et utilisateurs suivent la présentation de Recette des Finances : `clean()`, méthodes de validation, variables `$old` et `$erreurs`, actions `listBack()`, `addBack()`, `updateBack()` et `deleteBack()`. La déclaration du colis utilise `declarer()`. Les modèles portent les propriétés, constructeurs et getters/setters. Les données validées servent à construire les objets transmis aux méthodes CRUD. Les vues administrateur d’ajout et de modification sont séparées, comme dans Finances : `addBureau.php` / `updateBureau.php`, `addColis.php` / `updateColis.php` et `addUser.php` / `updateUser.php`.

La gestion des utilisateurs est commune à E-Tounsi : ses vues administrateur sont dans `pages/utilisateurs/`. Les messages partagés sont dans `pages/commun/`. Le dossier `pages/poste/` contient uniquement les vues des bureaux et des colis. Les modèles `Citoyen.php` et `AuditLog.php`, les contrôleurs et les formulaires de connexion restent communs aux modules.

Les pages `addBureau.php`, `addColis.php` et `addUser.php` suivent le formulaire fourni en référence : contrôleur inclus en haut du fichier, contrôle du rôle, lecture de `POST`, validation, création de l’objet puis appel de la méthode CRUD et redirection `header()`. Le formulaire HTML se trouve juste après ce traitement. `invalidClass()` et `errorFor()` affichent les erreurs des champs. Le layout commun assemble les en-têtes, menus et pieds de page.

Les listes `bureauList.php` et `colisList.php` chargent leurs données et compteurs en haut du fichier, puis affichent les cartes et le tableau avec `foreach`, comme la liste Impôts fournie en référence. Chaque ligne contient un lien Modifier et un formulaire Supprimer en `POST`, protégé par CSRF et une confirmation. Les requêtes de comptage sont dans les contrôleurs.

Les pages `updateBureau.php` et `updateColis.php` chargent la ligne par son identifiant, traitent `POST` et affichent le formulaire prérempli avec `$old` et `$erreurs`. Les scripts `deleteBureau.php` et `deleteColis.php` contrôlent le rôle, la méthode `POST` et le jeton CSRF avant de supprimer, puis redirigent vers la liste avec un message. Le routeur leur confie ce contrôle CSRF afin qu’ils puissent afficher le message de refus après redirection. La suppression d’un bureau associé à des colis reste interdite.

La gestion utilisateurs suit les mêmes pages simples : `userList.php` charge les comptes et la recherche en haut du fichier ; `addUser.php` et `updateUser.php` traitent `POST` puis affichent leurs formulaires ; `deleteUser.php` vérifie le rôle, `POST` et CSRF puis redirige avec un message. Les champs nom, prénom, e-mail, téléphone, rôle et état sont présents à l’ajout et à la modification. Le mot de passe est obligatoire à l’ajout et facultatif en modification. `CitoyenController.php` assure les requêtes préparées, le hashage du mot de passe, la protection du dernier administrateur actif et les transactions avec le journal d’audit. `Citoyen.php` contient les attributs, le constructeur et les getters/setters, selon l’exemple CRUD du cours. La langue choisie est conservée lors de la déconnexion pour permettre le retour au formulaire de connexion.

La connexion PDO est centralisée dans `config.php`. Toutes les requêtes SQL sont dans les contrôleurs, comme dans la référence CRUD du cours : `CitoyenController.php`, `BureauPostalController.php`, `ColisController.php`, `ProprieteController.php`, `ImpotController.php` et `AuditController.php`. Les modèles conservent les attributs, constructeurs, getters/setters et constantes nécessaires. Ils ne contiennent aucune requête SQL ni connexion PDO. Les contrôleurs valident les données et préparent les vues. Le contrôleur de base assemble le contenu dans les layouts. Les formulaires Poste reprennent le HTML de Finances, les valeurs `$old`, les erreurs par champ `$erreurs` et les attributs `data-validate`. Les règles de validation PHP restent dans les contrôleurs ; les suppressions Poste utilisent la confirmation des formulaires de liste.

Le journal utilise `AuditController::log()` pour les écritures et les méthodes `compter()` / `lister()` pour sa consultation. Les opérations utilisateurs transmettent leur connexion PDO au contrôleur d’audit afin d’enregistrer la modification du compte et sa trace dans la même transaction. Les classes des modules encore à intégrer restent dans leurs dossiers sans ouvrir de connexion à la base.

Les deux modules utilisent le même en-tête, la même navigation, les mêmes pieds de page et la même session. Les sélecteurs et dates utilisent les contrôles HTML natifs ; les listes des bureaux et colis affichent les données dans le tableau. Les changements de page après traitement utilisent `header('Location: ...')`, suivi de `exit`. Les liens HTML assurent la navigation normale ; le script de redirection JavaScript entre pages a été retiré. L’animation d’entrée dans les services est conservée.

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

1. Placer le projet sous `C:\xampp\htdocs` et démarrer Apache et MySQL dans XAMPP.
2. Importer `database/etounsi.sql` dans phpMyAdmin, onglet **Importer**. Le script crée la base commune `etounsi` et les tables manquantes. Il conserve les tables et les données lors d’une réimportation. Les exemples sont ajoutés uniquement lorsque toutes les tables sont vides ; le fichier partagé contient uniquement des données de démonstration.
3. Vérifier la connexion dans `config.php`. Un fichier local `config.local.php`, ignoré par Git, peut sélectionner une autre base : `<?php return ['host'=>'127.0.0.1','database'=>'nom_base','user'=>'root','password'=>''];`.
4. Ouvrir `http://localhost/NOM_DU_DOSSIER/index.php`.

Pour une base déjà installée avant l’ajout des champs de saisie des colis, sélectionner cette base dans phpMyAdmin et importer une seule fois `database/mise-a-jour-colis.sql`. Cette mise à jour ajoute le nom saisi et la date choisie sans remplacer les colis existants. Sur une installation neuve, `database/etounsi.sql` contient déjà ces colonnes.

Sur ce PC, le projet est dans `C:\xampp\htdocs\ETounsi-Integration-Preview` et utilise la base commune `etounsi`, définie dans `config.php`. Les données de `etounsi_integration_preview` y ont été conservées, avec les mêmes identifiants, mots de passe hachés et relations. L’ancienne base de prévisualisation et le projet Poste d’origine sont conservés séparément. Aucun fichier `config.local.php` n’est nécessaire sur ce PC.

Une réimportation crée les tables absentes ; elle ne modifie pas les colonnes d’une table déjà installée. Utiliser la mise à jour ci-dessus uniquement pour une ancienne base qui ne contient pas encore les deux champs de déclaration des colis.

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
| Administration | `index.php?page=dashboard` |
| Utilisateurs | `index.php?page=admin-utilisateurs` |
| Journal d’audit | `index.php?page=admin-audit` |

Sur une installation vide, le script crée `admin@etounsi.tn` (administrateur) et `citoyen@etounsi.tn` (citoyen), avec le mot de passe de démonstration `password`. Lors d’une réimportation sur une base remplie, les comptes et mots de passe existants sont conservés.

Le CAPTCHA reprend les clés de test déjà présentes dans la branche. Leur usage pour les essais est décrit dans la [documentation Google reCAPTCHA](https://developers.google.com/recaptcha/docs/faq). Le SSO Google n’est pas implémenté dans cette version.

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
