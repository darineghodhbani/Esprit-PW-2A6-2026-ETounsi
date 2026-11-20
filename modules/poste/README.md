# E-Tounsi — Module Poste et gestion des utilisateurs

## Overview

Ce module de Maryem Kochbati, équipe Power Girls, permet de consulter les bureaux de poste et de déclarer, suivre et administrer les colis. Il conserve la structure MVC PHP et les bases eLearning/Spark Admin avec la nouvelle présentation E-Tounsi fournie dans le prototype.

## Features

- Annuaire des bureaux avec filtre par ville et suivi public des colis.
- Inscription, connexion, profil et déconnexion.
- Déclaration d’envoi, historique personnel et annulation des envois en attente.
- CRUD des bureaux, acceptation des déclarations et suivi des étapes de traitement des colis.
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
│       ├── shared/             En-tête, pied de page, bureau et connexion partagés avec le portail
│       ├── admin/              Pages d’administration
│       └── *.php               Pages citoyennes avec leur HTML
├── public/assets/
│   ├── front/                  CSS du Front Office initial
│   ├── back/                   CSS et JavaScript du Back Office
│   ├── images/                 Logo, image initiale et six photos locales dans portal/
│   ├── fonts/                  Polices locales
│   ├── js/                     Bootstrap partagé
│   └── vendor/                 Icônes partagées
└── sql/
    ├── database.sql            Tables et relations
    └── installer.php           Installation depuis le terminal
```

Les modèles représentent les entités avec des attributs privés, un constructeur et des getters/setters. Les contrôleurs contiennent les requêtes PDO et les traitements, selon le workshop de sécurité. `HomeController` prépare les pages citoyennes. `AdminController`, `BureauController`, `ColisController` et `UtilisateurController` préparent les vues administratives. `AuthController` gère les comptes et `PosteController` les données postales.

Les vues contiennent le HTML. Les fichiers `public/assets/front/css/style.css` et `public/assets/back/css/main.css` proviennent des templates fournis. Leurs adaptations se trouvent dans les deux fichiers `poste.css` et dans le thème commun `../../css/etounsi.css`. Les interactions communes sont dans `../../js/etounsi.js` ; le menu administrateur reste dans `public/assets/back/js/poste.js`. Le Front Office assemble `head`, `spinner`, `navbar`, la vue, `footer`, puis `scripts`, dans l’ordre de l’atelier.

Depuis [l'accueil E-Tounsi](http://localhost/Esprit-PW-2A6-2026-ETounsi/), cliquer sur la photo **Poste**, attendre l'ouverture des portes en bois et choisir **Bureau de poste**. Le bouton descend vers la vraie connexion. La page dédiée `index.php?page=connexion` utilise le même formulaire. Après connexion, les services sont proposés selon le rôle réel. Les autres bureaux du portail sont des présentations ; aucune fausse connexion n'est utilisée.

Cette mise à jour visuelle ne nécessite ni migration de base ni nouvelle installation. Pour une installation déjà fonctionnelle, démarrer Apache/MySQL et recharger la page avec Ctrl+F5 suffit. Les vérifications sont décrites dans [le rapport d'interface](../../docs/validation-interface.md).

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

Le compte administrateur de démonstration est `admin.poste@example.test`. Il accède à toutes les pages du Back Office. Sur une ancienne installation, relancer `C:\xampp\php\php.exe sql\installer.php` met à jour les rôles et les statuts des colis, en conservant les données métier. L’ancien compte de démonstration supplémentaire est retiré seulement si un autre administrateur actif existe et qu’aucun colis ne lui est associé.

### Parcours des colis

1. L’administrateur ajoute un bureau avec le formulaire du Back Office.
2. Le citoyen connecté choisit un bureau et déclare son envoi. Le système attribue une référence unique et le statut **En attente**.
3. La déclaration apparaît dans **Gestion des colis**. L’administrateur utilise **Accepter** pour la valider.
4. L’administrateur fait ensuite évoluer le colis vers **En transit**, puis **Livré**. Le citoyen voit l’état dans son historique et avec sa référence de suivi.

Le parcours est **En attente → Accepté → En transit → Livré**. Le citoyen peut annuler ses propres envois en attente. L’administrateur peut annuler un colis avant son départ. Les états **Livré** et **Annulé** sont définitifs. Les contrôles PHP et la mise à jour PDO empêchent les sauts d’étape et l’écrasement d’un état modifié depuis l’ouverture du formulaire.

La table `Colis` conserve les champs du diagramme fourni, dont `id_bureau_depart`, `id_citoyen`, `numero_suivi` unique et `statut`. Le statut **Accepté** est ajouté par l’installateur dans la base existante. Après récupération d’une mise à jour du projet, relancer `C:\xampp\php\php.exe sql\installer.php` depuis le dossier du module.

### Sessions et validation

Les requêtes utilisent des paramètres PDO. Les sorties HTML sont échappées. Les actions de modification utilisent POST et un jeton CSRF. Les mots de passe sont hachés et comportent au moins huit caractères, dans la limite de 72 octets. Le rôle, l’état du compte et la version de session sont vérifiés côté serveur.

La connexion renouvelle l’identifiant de session. Les cookies sont HttpOnly et SameSite=Lax, avec Secure en HTTPS. Une session expire après 30 minutes d’inactivité ou huit heures de connexion. Modifier le mot de passe, le rôle ou l’activation révoque les sessions antérieures. La limitation des tentatives de connexion s’applique à la session du navigateur après cinq échecs en cinq minutes.

## Contribution

Créer une branche par tâche, tester sur XAMPP, puis ouvrir une pull request vers le dépôt d’équipe. Les contributions de Maryem sont suivies dans le [GitHub Project Board Poste et utilisateurs](https://github.com/users/kochbatimaryem574-maker/projects/1).

## Licence

Les notices tierces sont conservées dans `public/assets/front/LICENSE.txt`, `public/assets/back/SPARK-README.md`, `public/assets/fonts/Cairo-OFL.txt`, `Jakarta-OFL.txt`, `Fraunces-OFL.txt` et `public/assets/vendor/bootstrap-icons/LICENSE.txt`. Les photos de la nouvelle interface proviennent de l'archive E-Tounsi-Projet.zip fournie par l'équipe.

## Acknowledgments

eLearning par [HTML Codex](https://htmlcodex.com/elearning-html-template/). Spark Admin par Spark Admin Dev, distribué par [ThemeWagon](https://themewagon.com/themes/spark-admin/). Organisation MVC et sessions adaptées des workshops fournis par l’enseignante.
