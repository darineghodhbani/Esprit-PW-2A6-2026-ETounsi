# E-Tounsi — Plateforme de services publics tunisiens

## Overview

Projet Technologies Web, classe 2A6, Esprit School of Engineering, année universitaire 2026–2027. Cette version réunit les fonctionnalités Poste et Recette des Finances dans la structure MVC et le thème commun de la branche `integration`. Les dossiers Transport, Municipalité et CNAM restent disponibles pour les contributions des membres concernés.

## Features

- Compte commun : inscription, connexion, profil, CRUD utilisateurs, activation/suspension et rôles citoyen/administrateur.
- Poste : annuaire des bureaux, déclaration d’un envoi, référence unique, suivi et historique. Le citoyen crée, consulte, modifie le bureau de départ et supprime ses propres déclarations en attente ; l’administrateur dispose du CRUD des bureaux et colis et gère leur traitement.
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

La connexion PDO est centralisée dans `config.php`. Les requêtes SQL sont dans les modèles. Les contrôleurs valident les données et préparent les vues. Le contrôleur de base assemble le contenu dans les layouts. Les pages Poste gardent un formulaire d’ajout/modification par entité et une confirmation de suppression partagée.

Les deux modules utilisent le même en-tête, la même navigation, les mêmes pieds de page et la même session. Les sélecteurs et dates utilisent les contrôles HTML natifs ; les listes conservent leurs filtres dans l’URL. Les changements de page après traitement utilisent `header('Location: ...')`, suivi de `exit`. Les liens HTML assurent la navigation normale ; le script de redirection JavaScript entre pages a été retiré. L’animation d’entrée dans les services est conservée.

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
2. Sur une nouvelle installation, importer `database/etounsi.sql` dans phpMyAdmin. Ce script installe le schéma commun et les données de démonstration ; il ne contient pas les comptes ni les colis privés copiés pour la prévisualisation.
3. Vérifier la connexion dans `config.php`. Un fichier local `config.local.php`, ignoré par Git, peut sélectionner une autre base : `<?php return ['host'=>'127.0.0.1','database'=>'nom_base','user'=>'root','password'=>''];`.
4. Ouvrir `http://localhost/NOM_DU_DOSSIER/index.php`.

Sur ce PC, la copie de revue est `C:\xampp\htdocs\ETounsi-Integration-Preview` et utilise la base isolée `etounsi_integration_preview`. Les comptes du module Poste y ont été copiés en conservant leurs mots de passe ; les correspondances d’identifiants ont été appliquées aux colis. Le dossier et la base d’origine restent séparés.

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

Les comptes de démonstration administrateur de la branche initiale sont `admin@etounsi.tn` et `agent@etounsi.tn`, avec le mot de passe `password`. Ils utilisent le même rôle administrateur dans la version intégrée.

Le CAPTCHA reprend les clés de test déjà présentes dans la branche. Leur usage pour les essais est décrit dans la [documentation Google reCAPTCHA](https://developers.google.com/recaptcha/docs/faq). Le SSO Google n’est pas implémenté dans cette version.

## Sécurité et journal d’audit

Les formulaires utilisent des jetons CSRF, des validations PHP et des sorties échappées. Les mots de passe sont hachés. L’identifiant de session est renouvelé après connexion ; le compte, le rôle et la version de session sont vérifiés côté serveur. La session expire après trente minutes d’inactivité, avec une limite de huit heures.

Dans « Mes colis », la modification et la suppression sont réservées au propriétaire tant que le statut est « En attente ». Le citoyen ne choisit ni le propriétaire, ni la référence, ni le statut. La suppression demande une confirmation et une requête POST avec un jeton CSRF. Les conditions de propriétaire et de statut sont également appliquées dans les requêtes SQL pour éviter une modification pendant l’acceptation par l’administrateur. Une référence supprimée n’apparaît plus dans le suivi.

`audit_logs` contient `id`, `user_id`, `action`, `target_id`, `details`, `ip_address` et `created_at`. `actor_name` conserve le nom de l’auteur lorsqu’un compte est supprimé. L’adresse provient de `REMOTE_ADDR` ; les en-têtes transmis par le client ne sont pas utilisés comme preuve d’adresse. Le journal ne contient aucun mot de passe, hash, cookie ou jeton. Les changements d’un compte et leurs traces sont enregistrés dans une même transaction. L’administration du journal est en lecture seule.

Pour la démonstration : ouvrir Utilisateurs, modifier le rôle d’un compte de test, puis ouvrir Journal d’audit et filtrer sur ce compte et « Changement de rôle ». L’ancien rôle, le nouveau, l’auteur, la date et l’IP sont visibles.

## Vérifications

Les vérifications locales couvrent les CRUD Poste et utilisateurs, le parcours Finances avec un compte commun, les accès, les sessions, les redirections, la protection CSRF, les filtres et la pagination de l’audit. Le contrôle d’une panne du journal utilise des tables temporaires pour vérifier l’annulation de la modification du compte. Les essais sur la copie sont nettoyés après exécution. Le script SQL est aussi vérifié sur une base vide.

Le CRUD citoyen des colis a été vérifié avec deux comptes distincts : modification du bureau, suppression réelle après confirmation, validation des formulaires, refus d’accès aux colis d’un autre citoyen et refus après acceptation par l’administrateur. Les écrans de liste, modification et suppression ont été contrôlés sur ordinateur et mobile, en français et en arabe. Les données de test ont été retirées sans modifier les données existantes.

## Acknowledgments

Esprit School of Engineering et l’équipe pédagogique pour les supports et ateliers. Le thème, les photographies et l’identité visuelle de la branche d’intégration sont conservés. Les notices des polices Manrope, Lora et Noto Sans Arabic restent dans `assets/frontoffice/fonts/`. Le Back Office conserve les attributions de son template et de ses ressources.

Contrôle de revue du 22 novembre 2026 : installation sur base vide vérifiée ; parcours Poste/Finances avec un compte commun et journal d’audit testés ; neuf états d’interface contrôlés sans anomalie automatisée WCAG A/AA et 2.1 AA. Ces contrôles complètent les essais fonctionnels et visuels. Les fichiers et les données du projet Poste d’origine ont été comparés à la sauvegarde et sont inchangés.
