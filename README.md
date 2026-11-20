# E-Tounsi – Plateforme de digitalisation des services publics en Tunisie
## Overview

This project was developed as part of the Projet Technologies Web – 2nd Year Engineering Program at Esprit School of Engineering (Academic Year 2026–2027).

E-Tounsi is a web platform that helps Tunisian citizens complete their administrative procedures online (municipality, STEG, SONEDE, transport, police station), without having to travel or wait in long queues.

## Features
Municipalité : demandes de documents et réclamations municipales
CNAM : suivi des factures et réclamations électricité/gaz
POSTE : annuaire des bureaux, déclaration, acceptation et suivi des colis
Transport : informations et services de transport
Poste de police : démarches administratives (documents, rendez-vous)
Front Office : espace citoyen
Back Office : espace administrateur (gestion des demandes)
## Tech Stack
### Frontend
HTML5
CSS3
JavaScript
Bootstrap / Tailwind CSS (templates)
### Backend
PHP (architecture MVC)
MySQL
## Architecture

Le projet suit l'architecture MVC (Model – View – Controller) :

├── Controllers/   → reçoivent les requêtes et choisissent la vue
├── Models/        → classes et accès à la base de données
├── Views/
│   ├── includes/  → éléments communs du Front Office (head, navbar, footer…)
│   └── partials/  → éléments communs du Back Office (head, sidebar, header…)
├── css/
├── img/
├── js/
└── lib/
## Contributors
Samar Debbichi – Module Municipalité
Mariem Kaabi – Module CNAM
Maryem Kochbati – Module POSTE
Ranim Aissa – Module Transport
Darine Ghodhbani – Module Poste de police
## Academic Context

Developed at Esprit School of Engineering – Tunisia Projet Technologies Web – 2A | Academic Year 2026–2027 Tuteur(s) : Mounira Hamdi

## Getting Started
Cloner le dépôt :
bash
git clone https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi.git
Placer le dossier dans htdocs (XAMPP) ou www (WAMP).
Démarrer Apache et MySQL depuis XAMPP/WAMP.
Pour le module Poste et les utilisateurs, ouvrir CMD dans modules\poste et lancer C:\xampp\php\php.exe sql\installer.php. Ce script initialise MySQL et les comptes locaux de validation.
Ouvrir dans le navigateur :
http://localhost/Esprit-PW-2A6-2026-ETounsi/index.php
## Acknowledgments

Nous remercions nos tuteurs et l'équipe pédagogique d'Esprit School of Engineering pour leur encadrement et leurs conseils tout au long de ce projet.


## Module Poste et gestion des utilisateurs

Le module de Maryem Kochbati est intégré dans [modules/poste](modules/poste/README.md). Son Front Office eLearning et son Back Office Spark Admin utilisent MySQL dans XAMPP et une gestion des comptes commune. Ce module possède un point d’entrée `index.php`, les dossiers `app/core`, `app/controllers`, `app/models`, `app/views`, `config`, `public/assets` et `sql`.

Les deux rôles sont citoyen et administrateur. L’inscription publique crée un citoyen. L’administrateur gère les utilisateurs, leurs rôles, les bureaux et les colis. Les sessions, les autorisations et les formulaires suivent le workshop de sécurité.

Après installation, accéder au module depuis le lien **Poste** du menu ou depuis
http://localhost/Esprit-PW-2A6-2026-ETounsi/modules/poste/index.php.

Les accès générés se trouvent dans le fichier local modules/poste/config/acces-local.txt. Ce fichier et les paramètres privés de chaque machine sont exclus de Git.

Les ressources eLearning sont attribuées à HTML Codex et celles de Spark Admin à Spark Admin Dev / ThemeWagon. Leurs notices sont conservées dans le module.

## Suivi des tâches Poste et utilisateurs

Le [tableau GitHub Poste et utilisateurs](https://github.com/users/kochbatimaryem574-maker/projects/1) suit les contributions de Maryem Kochbati. Les issues #2, #3 et #4 concernent les fonctionnalités et la sécurité. L’issue #7 concerne le rangement MVC pour la validation et l’issue #9 le rôle administrateur unique et les footers. L’issue #11 suit l’acceptation et le traitement des colis. Elles sont attribuées à kochbatimaryem574-maker et leur avancement est indiqué dans le tableau.

La vue **Avancement Poste** présente ces tâches sous forme de tableau. Le projet est public, sous le compte de Maryem, et accessible depuis ce lien pour la validation.
