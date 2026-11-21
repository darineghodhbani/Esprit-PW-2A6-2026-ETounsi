# E-Tounsi

> Plateforme de digitalisation des services publics en Tunisie — développée dans le cadre du module Technologies Web (2A), Esprit School of Engineering.

## Overview

E-Tounsi est une application web qui centralise cinq services publics tunisiens (Transport, Municipalité, CNAM, Poste, Recette des Finances) derrière un compte citoyen unique. Le projet est construit en **PHP avec une architecture MVC maison** : routeur frontal (`index.php?page=xxx`), templates front office et back office découpés en includes/partials réutilisables, et contrôle d'accès par rôles (citoyen / agent / admin).

## Features

- **Partie commune** : inscription, connexion, déconnexion, sessions, contrôle des rôles (RBAC), CAPTCHA et SSO Google sur le login (Phase 4).
- **Transport** : consultation des lignes, stations et tarifs (front) ; CRUD lignes et stations (back).
- **Municipalité** : dépôt et suivi de demandes municipales (front) ; gestion des services et traitement des demandes (back).
- **CNAM** : dossier d'assuré social, bénéficiaires, demandes de remboursement avec **upload de justificatifs** (front) ; validation et décision d'un agent (back).
- **Poste** : déclaration d'envoi et suivi de colis par référence (front) ; gestion des bureaux postaux et des statuts de colis (back).
- **Recette des Finances** : déclaration de propriété, paiement simulé d'impôts et reçus (front) ; validation des propriétés et gestion des impôts (back).

## Tech Stack

- PHP 8 (PDO) — sans framework
- MySQL / MariaDB
- HTML5, CSS3, JavaScript
- Apache (XAMPP) — modèle MVC maison
- Google reCAPTCHA v2 & Google Identity (SSO) — Phase 4

## Architecture

```
E-Tounsi/
├── index.php                # routeur frontal (index.php?page=xxx)
├── config.php               # connexion PDO unique (Database::getConnection())
├── app/
│   ├── routes.php           # tableau des routes (page => [Controller, méthode])
│   ├── Models/              # 1 classe par table — SQL UNIQUEMENT ici
│   ├── Controllers/         # Controller.php de base + 1 controller par module
│   └── Views/
│       ├── frontoffice/     # includes/ (head, spinner, navbar, footer, scripts)
│       └── backoffice/      # partials/ + layout.php ($title, $currentPage, $content)
├── assets/                  # css / js / img / lib (front + back)
├── uploads/cnam/            # justificatifs uploadés (DocumentCNAM)
└── database/etounsi.sql     # script de création de la base partagée
```

Flux MVC (ex. consultation des lignes) :
`index.php?page=lignes` → `app/routes.php` → `LigneTransportController::listFront()` → `Models/LigneTransport.php` (SQL via `config.php`) → capture de la vue dans `$content` → assemblage head + navbar + footer + scripts.

## Contributors

| Membre | Module |
|---|---|
| Ranim Aissa | Transport (`LigneTransport`, `Station`) |
| Samar Debbichi | Municipalité (`ServiceMunicipal`, `DemandeMunicipale`) |
| Mariem Kaabi | CNAM (`AssureSocial`, `Beneficiaire`, `DemandeRemboursement`, `DocumentCNAM`, `DecisionCNAM`) |
| Mariem Kochbati | Poste (`BureauPostal`, `Colis`) |
| Darine Ghodbani | Recette des Finances (`Propriete`, `Impot`) |
| Toutes | Partie commune : `Citoyen` (auth, rôles, CAPTCHA, SSO) |

## Academic Context

Projet réalisé à **Esprit School of Engineering – Tunisia**, module **Technologies Web (PW)**, 2ᵉ année — année universitaire **2026/2027**.

## Getting Started

1. Installer [XAMPP](https://www.apachefriends.org/) et démarrer Apache + MySQL.
2. Cloner le dépôt dans le dossier web :
   ```bash
   cd /Applications/XAMPP/htdocs   # macOS
   git clone https://github.com/<org>/Esprit-PW-2A-2027-E-Tounsi.git E-Tounsi
   ```
3. Créer la base : ouvrir phpMyAdmin → Importer → `database/etounsi.sql`.
4. Vérifier les identifiants DB dans `config.php` (par défaut `root` / mot de passe vide).
5. Ouvrir http://localhost/E-Tounsi/index.php?page=home
   - Back office : http://localhost/E-Tounsi/index.php?page=dashboard
   - Compte admin de test : `admin@etounsi.tn` / `password`
   - Compte agent de test : `agent@etounsi.tn` / `password`

## Acknowledgments

- Esprit School of Engineering — encadrement et ateliers Technologies Web.
- Front office : design « la rue E-Tounsi » (prototype de l'équipe, dossier `E_Tounsi_Dossier/`) — polices Manrope, Lora et Noto Sans Arabic, photographies et identité visuelle intégrées dans `assets/frontoffice/`.
- Back office : squelette inspiré de TailAdmin / PlainAdmin (atelier partie 2).
