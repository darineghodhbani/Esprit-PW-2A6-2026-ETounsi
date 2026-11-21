# E-Tounsi – Plateforme de digitalisation des services publics en Tunisie
## Overview

This project was developed as part of the Projet Technologies Web – 2nd Year Engineering Program at Esprit School of Engineering (Academic Year 2026–2027).

E-Tounsi est une plateforme de services publics tunisiens. Le portail présente cinq espaces : Transport, Municipalité, Assurance publique — CNAM, Poste et Recette des Finances. Le module Poste est relié à la base MySQL et à la gestion des utilisateurs ; l'intégration métier des autres espaces relève des membres concernés.

## Features
Municipalité : demandes de documents et réclamations municipales
CNAM : espace d'assurance publique
POSTE : annuaire des bureaux, déclaration, acceptation et suivi des colis
Transport : informations et services de transport
Recette des Finances : espace des démarches fiscales
Front Office : espace citoyen
Back Office : espace administrateur (gestion des demandes)
## Tech Stack
### Frontend
HTML5
CSS3
JavaScript
Bootstrap (templates), CSS et JavaScript natif ; aucune compilation React/TypeScript
### Backend
PHP (architecture MVC)
MySQL
## Architecture

Le projet suit l'architecture MVC (Model – View – Controller) :

```text
├── index.php                  Routage du portail
├── Controllers/               Préparation des pages du portail
├── Views/                     HTML et inclusions du portail
├── css/, js/, img/, lib/       Ressources de présentation
└── modules/poste/
    ├── index.php              Point d'entrée du module
    ├── config/                Connexion PDO commune
    ├── app/core/              Routage, sessions et fonctions communes
    ├── app/controllers/       Validation, requêtes PDO et traitements
    ├── app/models/            Entités privées et getters/setters
    ├── app/views/             Vues et layouts du Front et Back Office
    ├── public/assets/         CSS, JavaScript, images et polices
    └── sql/                   Schéma et installation
```
## Contributors
Samar Debbichi – Module Municipalité
Mariem Kaabi – Module CNAM
Maryem Kochbati – Module POSTE
Ranim Aissa – Module Transport
Darine Ghodhbani – Module Recette des Finances
## Academic Context

Developed at Esprit School of Engineering – Tunisia Projet Technologies Web – 2A | Academic Year 2026–2027 Tuteur(s) : Mounira Hamdi

## Getting Started
Cloner le dépôt :
```bat
git clone https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi.git
```
Placer le dossier dans htdocs (XAMPP) ou www (WAMP).
Démarrer Apache et MySQL depuis XAMPP/WAMP.
Pour le module Poste et les utilisateurs, ouvrir CMD dans modules\poste et lancer C:\xampp\php\php.exe sql\installer.php. Ce script initialise MySQL et les comptes locaux de validation.
Ouvrir dans le navigateur :
http://localhost/Esprit-PW-2A6-2026-ETounsi/index.php
## Acknowledgments

Nous remercions nos tuteurs et l'équipe pédagogique d'Esprit School of Engineering pour leur encadrement et leurs conseils tout au long de ce projet.


## Module Poste et gestion des utilisateurs

Le module de Maryem Kochbati est intégré dans [modules/poste](modules/poste/README.md). Ses structures eLearning et Spark Admin sont adaptées au nouveau design E-Tounsi fourni : rouge, blanc cassé, images tunisiennes et portes en bois. Il utilise MySQL dans XAMPP et une gestion des comptes commune. Ce module possède un point d’entrée `index.php`, les dossiers `app/core`, `app/controllers`, `app/models`, `app/views`, `config`, `public/assets` et `sql`.

Le parcours du portail est **Accueil → photo Poste → ouverture des portes → Bureau de poste → connexion → services réels**. Un citoyen retrouve ses colis ; l'administrateur accède à la gestion. Le bouton du bureau fait défiler vers le formulaire. Les autres photos ouvrent leur présentation, sans simuler une fonctionnalité métier.

Le HTML de l'accueil est dans `Views/home.php`, les blocs partagés dans `modules/poste/app/views/shared`, le thème dans `css/etounsi.css` et les interactions dans `js/etounsi.js`. Les six images et les polices sont locales dans `modules/poste/public/assets`. Le [rapport de validation](docs/validation-interface.md) décrit les contrôles. Aucun chatbot n'est intégré.

L'accueil, ses questions et son footer concernent toute la plateforme. Les deux portes rouges et leur ouverture rapide reprennent le premier prototype fourni, en CSS et JavaScript dans `css/portes.css` et `js/portes.js`. Chaque service conserve sa propre photo intérieure. Aucun bouton de lecture n'est ajouté. Le retour aux services et Échap permettent de quitter ; la préférence de mouvement réduit rend l'entrée immédiate. Les images sont locales.

Les deux rôles sont citoyen et administrateur. L’inscription publique crée un citoyen. L’administrateur gère les utilisateurs, leurs rôles, les bureaux et les colis. Les sessions, les autorisations et les formulaires suivent le workshop de sécurité.

La [vérification de première validation](docs/verification-premiere-validation.md) relie les consignes aux fichiers, aux cours et aux tests. Elle distingue la partie Poste vérifiée des points d'intégration et d'administration GitHub qui concernent le groupe.

Après installation, accéder au module depuis le lien **Poste** du menu ou depuis
http://localhost/Esprit-PW-2A6-2026-ETounsi/modules/poste/index.php.

Les accès générés se trouvent dans le fichier local modules/poste/config/acces-local.txt. Ce fichier et les paramètres privés de chaque machine sont exclus de Git.

Les ressources eLearning sont attribuées à HTML Codex et celles de Spark Admin à Spark Admin Dev / ThemeWagon. Leurs notices sont conservées dans le module. Les images de la nouvelle interface proviennent de l'archive E-Tounsi-Projet.zip fournie ; les polices Plus Jakarta Sans, Fraunces et Cairo conservent leurs licences OFL.

## Suivi des tâches Poste et utilisateurs

Le [tableau GitHub Poste et utilisateurs](https://github.com/users/kochbatimaryem574-maker/projects/1) suit les contributions de Maryem Kochbati. Les issues #2, #3 et #4 concernent les fonctionnalités et la sécurité. L’issue #7 concerne le rangement MVC pour la validation et l’issue #9 le rôle administrateur unique et les footers. L’issue #11 suit l’acceptation et le traitement des colis. Elles sont attribuées à kochbatimaryem574-maker et leur avancement est indiqué dans le tableau.

La vue **Avancement Poste** présente ces tâches sous forme de tableau. Le projet est public, sous le compte de Maryem, et accessible depuis ce lien pour la validation.

L'[issue #14](https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi/issues/14) et la [pull request #15](https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi/pull/15) suivent l'intégration du nouveau portail, des portes en bois et du parcours visuel Poste.
