# Première validation : Poste et gestion des utilisateurs

Contrôle réalisé le **21 novembre 2026**, sur le projet `C:\xampp\htdocs\Esprit-PW-2A6-2026-ETounsi`, avec XAMPP, PHP 8.2, PDO MySQL et Chrome.

La partie Poste et la gestion des utilisateurs ont été vérifiées dans le code et par des scénarios exécutés. Ce bilan ne vaut pas validation de tous les modules du groupe : les limites constatées sur GitHub sont indiquées plus bas.

## 1. Consignes, fichiers et vérifications

| Élément demandé | Emplacement réel | Vérification effectuée |
|---|---|---|
| Créer et consulter les utilisateurs | `modules/poste/app/controllers/UtilisateurController.php`, `AuthController.php`, vues `admin/utilisateurs.php` et `admin/utilisateur-form.php` | Ajout depuis le back-office puis apparition dans la liste et recherche |
| Modifier les utilisateurs | Mêmes contrôleurs et formulaire | Nom/email, rôle, activation et mot de passe modifiables ; mot de passe vide en édition conserve le précédent |
| Supprimer les utilisateurs | `UtilisateurController::delete()`, `AuthController::supprimer()`, vue `admin/suppression.php` | Confirmation puis suppression ; refus si compte lié à des colis ou dernier administrateur actif |
| Modèles MVC | `app/models/Utilisateur.php`, `BureauPostal.php`, `Colis.php` | Attributs privés, getters/setters et conversion de tableau en objet ; aucune requête SQL |
| Contrôleurs MVC | `app/controllers/` et `app/core/App.php` | Contrôleurs pour préparation, validation, SQL et choix des vues ; routeur avec `switch` |
| Vues MVC | `app/views/` et `Views/` | Affichage et échappement ; aucune interrogation de la base ni gestion de session depuis les vues après correction |
| Rôles | `AuthController::ROLES`, `UtilisateurController`, `securite.php` | Deux rôles : citoyen et administrateur ; l'inscription publique impose citoyen, même si un rôle administrateur est ajouté au POST |
| Autorisations | `App.php`, `HomeController.php`, `securite.php` | Visiteur renvoyé vers connexion ; citoyen reçoit 403 sur le back-office ; actions personnelles contrôlées côté serveur |
| Connexion et déconnexion | `AuthController::authentifier()`, `HomeController`, `securite.php` | Bon espace après connexion ; échec avec message générique ; déconnexion POST avec CSRF et perte d'accès aux pages privées |
| Mots de passe | `AuthController.php` | `password_hash()` pour création/changement, `password_verify()` pour connexion et réauthentification ; hash absent des données affichées |
| Sessions | `app/core/securite.php` | Cookie HttpOnly/SameSite=Lax, mode strict, identifiant renouvelé, expiration après 30 minutes d'inactivité ; limite de huit heures et révocation après changement de rôle/activation/mot de passe |
| Injection SQL | Contrôleurs et `config/database.php` | `prepare()`/`execute()` et émulation désactivée ; tentative d'injection refusée |
| XSS | Validation des contrôleurs, `e()` et `etE()` dans les vues | Nom contenant des balises refusé, sorties échappées avec `htmlspecialchars()` |
| CSRF | `securite.php`, contrôleurs, champs cachés des formulaires POST | Jeton généré en session, comparé avec `hash_equals()` ; absent, falsifié ou mal typé refusé |
| Saisies | Contrôleurs utilisateurs, bureaux et colis | Noms, email et unicité, confirmation/mot de passe, rôle, activation, identifiants, bureau de départ et états contrôlés en PHP |
| Métier Poste | `PosteController.php`, `ColisController.php`, `BureauController.php` | Bureau ajouté, colis déclaré par citoyen, référence unique, acceptation puis transit/livraison, annulations et conflits de statut contrôlés |
| Jointure | `PosteController.php` | Colis lié à BureauPostal par `id_bureau_depart`, données du bureau affichées avec le colis |
| Templates et assemblage | `layouts/front.php`, `layouts/admin.php`, `includes/`, `partials/`, `shared/` | Inclusions communes, HTML dans les vues, CSS/JS/images locaux ; bases eLearning et Spark conservées avec leurs notices |
| Navigation active | `shared/header.php`, `partials/sidebar.php`, `css/etounsi.css` | Lien courant dynamique, `aria-current="page"` et style visible ; ancres non présentées comme pages courantes |
| README | README racine et README Poste | Présentation, fonctionnalités, technologies, structure, membres, contexte académique, installation, configuration et exécution présents |

## 2. Correspondance avec les supports fournis

- **Workshop Session.pdf**, 35 pages : MVC p.2–8, inscription/hachage p.9–14, connexion et sessions p.14–21, rôles et interfaces p.21–24, SQL/XSS/CSRF/en-têtes p.25–28. Le document autorise le design propre du projet p.9 et p.11.
- **Atelier-Intégration Template (1).pdf** : inclusions réutilisables, navigation active, contrôleur qui choisit la vue, assemblage des layouts. L'arborescence propre au module est conservée ; les noms de fichiers de démonstration ne sont pas copiés inutilement.
- **Chapitre 4 php - partie 1.pdf** : conditions, tableaux, fonctions et formulaires GET/POST.
- **Chapitre 5 _PHP Avancé & Programmation Orientée Objet (POO) (1).pdf** : classes, attributs privés, getters/setters. `Utilisateur` utilise une création d'objet puis des setters dans `depuisTableau()` ; aucun constructeur vide inutile n'a été ajouté.
- **Chapitre 6 -PHP PDO - CRUD.pdf** : connexion PDO, requêtes préparées et opérations CRUD. Les exemples placent le SQL dans les contrôleurs, comme le workshop de sécurité : il n'est donc pas déplacé dans les modèles.
- **Chapitre 7 PHP Jointure.pdf** : jointure entre les entités reliées par clé étrangère.
- **Chapitres JS 2, 3 et 4 et ateliers fournis** : fonctions, DOM, événements, `classList`, `dataset`, `forEach`, `addEventListener`, `preventDefault` et `setTimeout`.
- **Standardisation des dépôts GitHub – Projets PI** et **Guide pour Rédiger un README [PW 2627]** : nom, description, topics, structure du README, commandes Git et étapes d'installation.

Les noms sont adaptés au projet : le modèle `Utilisateur` et la table `Citoyen` correspondent à l'utilisateur générique du workshop ; `citoyen` correspond au rôle d'exemple `user`. Il n'est pas nécessaire de créer une seconde table `users`, de renommer les champs du diagramme ou d'ajouter un rôle super administrateur.

### Compléments de présentation à savoir expliquer

Le socle PHP et les validations suivent les méthodes des cours. Certaines API du navigateur utilisées pour le thème ne sont pas retrouvées dans les supports JS fournis ; elles sont distinguées ici pour ne pas présenter tout le fichier comme une copie du cours.

| Complément dans `js/etounsi.js` | Utilité dans l'interface |
|---|---|
| `dialog.showModal()` / `close()` | Bureau modal, fond inactif et gestion native du clavier |
| `scrollIntoView()` | Bouton Bureau de poste qui descend vers la connexion |
| `matchMedia()` | Respect de la préférence de mouvement réduit |
| `URL`, `searchParams`, `history.replaceState()`, `popstate` | Recherche conservée dans l'URL et restaurée à la navigation |
| `String.normalize()` | Recherche de services sans distinction des accents |

Ces compléments ne traitent ni l'authentification ni les données métier. Les formulaires sont validés en PHP et restent utilisables sans JavaScript. Il n'y a ni React, ni compilation TypeScript, ni framework backend ajouté.

## 3. Corrections réalisées pendant ce contrôle

1. La préparation de l'utilisateur, du jeton CSRF, du service demandé et de la réponse 404 est déplacée de `Views/home.php` et `Views/service.php` vers `Controllers/HomeController.php`. Les vues n'appellent plus indirectement une lecture SQL.
2. La navigation active demandée par l'atelier est rétablie sur le portail et le Front Office. Le Back Office la possédait déjà.
3. L'ancienne page Contact eLearning, son formulaire sans traitement, sa carte New York et ses coordonnées fictives sont remplacés par des liens vers l'annuaire, le suivi et la connexion réels. Aucun nouveau module de messagerie n'est inventé.
4. Les README présentent l'arborescence réelle dans un bloc de code, la commande de clonage correctement et le module Recette des Finances de Darine. L'explication sur les constructeurs et les compléments JavaScript est précisée.

Les tables, les données, la configuration de connexion et les règles métier Poste ne sont pas modifiées par ces corrections.

## 4. Tests exécutés

| Vérification | Résultat |
|---|---|
| `php -l` sur 77 fichiers PHP | Aucune erreur |
| Tests du contrôleur utilisateurs sur tables MySQL temporaires | CRUD, hash, unicité, validation, dernier administrateur, comptes liés, rôle, révocation et CSRF : réussis |
| Navigateur : utilisateurs back-office | Création/lecture/modification/suppression, changement de rôle, activation et mot de passe : réussis ; compte temporaire supprimé |
| Navigateur : sécurité Front Office | Inscription invalide/valide, rotation de session, rôle forcé, profil protégé, déconnexion, ancien mot de passe refusé : réussis |
| Expiration sur sessions de test isolées | Inactivité supérieure à 1800 secondes et durée supérieure à 28800 secondes : accès authentifié supprimé, sans modifier les délais du projet |
| Parcours métier Poste | Bureau, déclaration, acceptation, transit/livraison, annulation, rôles, CSRF et conflits : réussis ; données de test supprimées |
| Parcours du portail | Cinq services, recherche, portes, vraie connexion citoyen/admin, 404, clavier et mouvement réduit : réussis |
| Responsive | 1440/768/390/320 px et FR/AR sur le parcours ; pas de débordement du document |
| Corrections Contact | Liens réels, aucun formulaire inactif ; FR/AR à 1440/390/320 px ; Axe ciblé A/AA sans violation relevée |
| Navigation active | Huit routes FR/AR, liens courants et menu AR mobile : réussis |
| Audit statique du thème | Mode strict : aucune erreur ni avertissement |
| Comparaison des exports de base avant/après | Données des quatre tables identiques après nettoyage des données temporaires |

Les scripts et captures de ce contrôle restent dans l'espace de vérification local `work/premiere-validation`, hors du dossier public et du dépôt applicatif. Ils utilisent les outils de vérification du PC, sans ajouter de dépendance au site. Les compteurs automatiques peuvent avancer lors des tests, sans changer les lignes existantes.

## 5. GitHub et travail du groupe : état vérifié

Le dépôt est public, porte le nom `Esprit-PW-2A6-2026-ETounsi` et sa description mentionne l'établissement, l'année et les technologies. Les tâches de Maryem sont suivies dans le [Project Poste et utilisateurs](https://github.com/users/kochbatimaryem574-maker/projects/1), avec issues et pull requests traçables. Ce contrôle est suivi par l'[issue #16](https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi/issues/16).

Les points suivants restent à traiter ou confirmer par le groupe/propriétaire :

| Point | Constat réel et prochaine action |
|---|---|
| Topics obligatoires | Liste vide lors du contrôle. Tentative de mise à jour refusée par l'API avec HTTP 404 ; le compte Maryem a accès push mais pas administration. La propriétaire doit ajouter au minimum `esprit-school-of-engineering`, `academic-project`, `esprit-pw`, `2026-2027`, `php` ; `mysql` est aussi pertinent. |
| Tableau lié au dépôt | Les issues et le README lient les travaux au dépôt. Le rattachement technique a été tenté puis refusé : GitHub impose le même propriétaire pour le Project et le dépôt. Le tableau est chez Maryem et le dépôt chez Darine. Pour un tableau central directement rattaché, le groupe doit utiliser un Project chez Darine ou une organisation commune. |
| Tâches de tous les membres | Le tableau contrôlé couvre Maryem ; il ne prouve pas le suivi des quatre autres membres. Chaque membre doit ajouter et tenir à jour ses véritables tâches. Ne pas inventer de contributions ni marquer leurs travaux terminés à leur place. |
| Intégration des autres modules | La PR [#13 Recette des Finances](https://github.com/darineghodhbani/Esprit-PW-2A6-2026-ETounsi/pull/13) était ouverte et non fusionnée au moment du contrôle. Elle doit être examinée, intégrée et testée sur la machine du groupe. Aucune fusion non vérifiée de cette autre partie n'est faite dans la tâche Poste. |
| Tuteurs | Les cinq étudiants sont visibles comme collaborateurs ; aucun compte tuteur n'est identifié dans cette liste. La lecture des invitations est refusée (403), donc une invitation en attente n'est ni confirmée ni exclue. La propriétaire doit vérifier les invitations avec les vrais identifiants des tuteurs. |
| Validation sur une machine | Poste et utilisateurs fonctionnent sur ce XAMPP. Une démonstration complète du groupe demande encore le test commun de tous les modules intégrés, de la configuration et des données sur cette même machine. |

## 6. Démonstration à préparer pour la professeure

1. Montrer `app/models/Utilisateur.php`, puis les méthodes PDO de `AuthController.php` et les vues utilisateurs : expliquer les trois responsabilités MVC.
2. Ouvrir l'inscription, envoyer des champs invalides, puis créer un compte de test. Montrer que le rôle public est citoyen et que le mot de passe en base est haché.
3. Se connecter comme citoyen, déclarer un colis, consulter son historique et essayer l'URL du back-office : accès refusé.
4. Se connecter comme administrateur, montrer le CRUD utilisateurs puis l'acceptation et la progression d'un colis.
5. Montrer le jeton caché d'un formulaire, les requêtes préparées, `htmlspecialchars()`, la configuration du cookie et le renouvellement de session ; ne jamais publier les identifiants ou le cookie.
6. Se déconnecter et réessayer une page privée. Ouvrir ensuite le README, le Project et les commits/pull requests de chaque membre.

Les tests automatiques sont des preuves de vérification sur Chrome/XAMPP, pas une appréciation à la place du tuteur. Les points de groupe ci-dessus doivent être confirmés avant de déclarer toute la plateforme prête.
