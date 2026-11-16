E-Tounsi – Plateforme de digitalisation des services publics en Tunisie
Overview

This project was developed as part of the Projet Technologies Web – 2nd Year Engineering Program at Esprit School of Engineering (Academic Year 2026–2027).

E-Tounsi is a web platform that helps Tunisian citizens complete their administrative procedures online (municipality, STEG, SONEDE, transport, police station), without having to travel or wait in long queues.

Features
Municipalité : demandes de documents et réclamations municipales
CNAM : suivi des factures et réclamations électricité/gaz
POSTE : suivi des factures et réclamations eau
Transport : informations et services de transport
Poste de police : démarches administratives (documents, rendez-vous)
Front Office : espace citoyen
Back Office : espace administrateur (gestion des demandes)
Tech Stack
Frontend
HTML5
CSS3
JavaScript
Bootstrap / Tailwind CSS (templates)
Backend
PHP (architecture MVC)
MySQL
Architecture

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
Contributors
Samar Debbichi – Module Municipalité
Mariem Kaabi – Module CNAM
Mariem Kochbati – Module POSTE
Ranim Aissa – Module Transport
Darine Ghodhbani – Module Poste de police
Academic Context

Developed at Esprit School of Engineering – Tunisia Projet Technologies Web – 2A | Academic Year 2026–2027 Tuteur(s) : Mounira Hamdi

Getting Started
Cloner le dépôt :
bash
git clone https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi.git
Placer le dossier dans htdocs (XAMPP) ou www (WAMP).
Démarrer Apache et MySQL depuis XAMPP/WAMP.
Importer la base de données (database.sql) dans phpMyAdmin.
Ouvrir dans le navigateur :
http://localhost/Esprit-PW-2A6-2026-ETounsi/index.php
Acknowledgments

Nous remercions nos tuteurs et l'équipe pédagogique d'Esprit School of Engineering pour leur encadrement et leurs conseils tout au long de ce projet.
