# E-Tounsi – Plateforme de digitalisation des services publics en Tunisie

## Présentation

Projet réalisé dans le cadre du module **Projet Technologies Web – 2A** à **Esprit School of Engineering** (année universitaire 2026–2027).

**E-Tounsi** est une plateforme web qui permet aux citoyens tunisiens d'effectuer leurs démarches administratives en ligne (municipalité, CNAM, La Poste, transport, recette des finances), sans se déplacer ni faire la queue.

## Fonctionnalités

### Gestion des utilisateurs
- **Inscription** d'un citoyen (le rôle est toujours `user` : impossible de se créer un compte administrateur).
- **Connexion / déconnexion** avec session PHP sécurisée.
- **Deux rôles** : `user` (citoyen → Front Office) et `admin` (administrateur → Back Office).
- **Redirection selon le rôle** après la connexion.
- **Contrôle d'accès** : tout le Back Office est réservé au rôle `admin` (erreur 403 sinon) ; « Mon profil » est réservé aux personnes connectées.
- **Mon profil** : modifier ses informations, changer son mot de passe, supprimer son compte.
- **CRUD des utilisateurs (Back Office)** : ajouter, consulter (liste + détail), modifier, supprimer.
- **Recherche** (nom, prénom, email) et **filtres** (rôle, statut).
- **Bloquer / débloquer** un compte (un compte bloqué ne peut plus se connecter et sa session est fermée).
- **Statistiques** : total, citoyens, administrateurs, comptes bloqués.
- Un administrateur ne peut pas se supprimer, se bloquer ni se retirer le rôle admin.

### Sécurité
- Mots de passe hachés avec `password_hash()` / vérifiés avec `password_verify()`.
- Requêtes préparées PDO (protection contre l'**injection SQL**).
- Échappement de tous les affichages avec `htmlspecialchars()` (protection contre le **XSS**).
- Jeton **CSRF** dans tous les formulaires qui modifient des données.
- Session sécurisée : cookie `HttpOnly` + `SameSite=Lax`, `session_regenerate_id()` à la connexion, expiration après 30 minutes d'inactivité.
- Rôle et statut relus dans la base à chaque requête.
- Blocage temporaire (5 minutes) après 5 tentatives de connexion échouées.
- En-têtes HTTP de sécurité (`X-Frame-Options`, `Content-Security-Policy`, …).

### Contrôle de saisie
- **Côté serveur (PHP)** : `UserController::validate()`.
- **Côté navigateur (JavaScript)** : `js/validation.js`, réutilisable par tous les modules (voir plus bas).

### Module métier : Recette des Finances
Cette version contient uniquement le module de Darine (les autres modules seront ajoutés lors de l'intégration avec le groupe).
- Citoyen : déclarer une propriété, voir ses propriétés et ses impôts, demander le paiement d'un impôt (à régler au bureau de poste).
- Admin : CRUD des propriétés et des impôts, validation des paiements (statut non payé → en attente → payé).

## Technologies

- **Frontend** : HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend** : PHP 8 (architecture MVC, POO, PDO)
- **Base de données** : MySQL / MariaDB

## Architecture (MVC)

```
├── index.php              → point d'entrée du Front Office  (index.php?page=...)
├── admin.php              → point d'entrée du Back Office   (admin.php?page=...)
├── database.sql           → script de création de la base
├── config/
│   ├── database.php       → connexion PDO (Singleton)
│   ├── session.php        → session sécurisée + en-têtes HTTP
│   └── helpers.php        → fonctions d'affichage (e(), errorFor()...)
├── Models/
│   └── User.php           → attributs privés + getters/setters
├── Controllers/
│   ├── AuthController.php → inscription, connexion, rôles, CSRF, profil
│   ├── UserController.php → requêtes SQL + validation des utilisateurs
│   ├── HomeController.php → pages du Front Office
│   └── AdminController.php→ pages du Back Office (CRUD utilisateurs)
├── Views/
│   ├── includes/          → éléments communs du Front Office
│   └── backoffice/        → layout, partials et pages du Back Office
├── js/validation.js       → contrôle de saisie JavaScript
├── css/  img/  lib/  assets/
```

## Installation et configuration

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi.git
   ```
2. Placer le dossier dans `htdocs` (XAMPP) ou `www` (WAMP).
3. Démarrer **Apache** et **MySQL** depuis XAMPP/WAMP.
4. Ouvrir **phpMyAdmin** → onglet **Importer** → choisir `database.sql` → **Exécuter**.
   La base `etounsi` est créée avec deux comptes de test.
5. Si votre MySQL a un mot de passe, le mettre dans `config/database.php`.

## Exécution

Ouvrir dans le navigateur : `http://localhost/Esprit-PW-2A6-2026-ETounsi/index.php`

| Compte        | Email                | Mot de passe | Espace       |
|---------------|----------------------|--------------|--------------|
| Administrateur| `admin@etounsi.tn`   | `Admin@1234` | Back Office  |
| Citoyen       | `citoyen@etounsi.tn` | `User@1234`  | Front Office |

## Utiliser le contrôle de saisie JavaScript dans votre module

1. Sur la balise `<form>` : ajouter `novalidate data-validate-form`.
2. Sur chaque champ : ajouter `data-validate="..."` avec les règles séparées par `|`.

```html
<form method="post" novalidate data-validate-form>
  <input name="cin" id="cin" data-validate="required|cin">
  <input name="date" id="date" type="date" data-validate="required|futureDate">
</form>
```

Règles : `required`, `name`, `email`, `phone`, `cin`, `password`, `match:idChamp`, `min:n`, `max:n`, `number`, `date`, `futureDate`.
⚠️ Le même contrôle doit **aussi** être fait en PHP dans votre contrôleur.

## Contributeurs

- Samar Debbichi – Module Municipalité
- Mariem Kaabi – Module CNAM
- Mariem Kochbati – Module La Poste
- Ranim Aissa – Module Transport
- Darine Ghodhbani – Module Recette des Finances

## Contexte académique

Développé à **Esprit School of Engineering – Tunisie**
Projet Technologies Web – 2A | Année universitaire 2026–2027
Tuteur(s) : Mounira Hamdi

## Remerciements

Nous remercions nos tuteurs et l'équipe pédagogique d'Esprit School of Engineering pour leur encadrement et leurs conseils tout au long de ce projet.
