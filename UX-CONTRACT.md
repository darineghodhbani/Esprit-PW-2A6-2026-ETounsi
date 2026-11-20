# Contrat des interfaces E-Tounsi

## Sources et périmètre

Le brief de conception fourni le 20 novembre 2026, E-Tounsi-Projet.zip et la vidéo définissent l'apparence. Le code Poste déjà validé reste l'autorité pour les données et les règles métier. Cette adaptation concerne la présentation du portail, l'entrée des cinq bureaux et le module Poste. Les fonctionnalités des autres membres ne sont ni recréées ni déclarées terminées.

Le contrôle du 21 novembre avec le workshop Sessions précise que les données d'utilisateur/session et la validation du service du portail sont préparées dans `Controllers/HomeController.php`, avant les vues. La page Contact présente uniquement des liens vers les services réels. La navigation marque la page courante, sans confondre ses ancres avec des pages distinctes. Le bilan et les limites de groupe sont dans `docs/verification-premiere-validation.md`.

| Domaine | Source de vérité | Conséquence dans l'interface |
|---|---|---|
| Comptes et rôles | `modules/poste/app/core/securite.php`, `app/controllers/AuthController.php` | Session commune ; citoyen et administrateur ; contrôles toujours sur le serveur |
| Connexion | `modules/poste/app/controllers/HomeController.php`, cas connexion | POST réel, erreurs 422, redirection vers Mes colis ou Administration |
| Cycle du colis | `modules/poste/app/controllers/PosteController.php` | Boutons et statuts existants conservés ; aucune transition créée par le design |
| Suppression | `modules/poste/app/controllers/ColisController.php`, `BureauController.php`, `UtilisateurController.php` | Confirmation dédiée, refus existants pour liens et dernier administrateur |
| Vie privée et session | `modules/poste/app/core/securite.php` | CSRF, cookies et restrictions CSP inchangés ; aucun stockage local de secret |
| Données | `modules/poste/config/database.php`, `sql/database.sql` | Même base, tables et champs ; aucune migration requise |
| Facturation | Aucun paiement dans le module | Aucun formulaire de paiement ajouté |
| Attribution | Notices eLearning/Spark et licences OFL locales | Notices conservées, crédit du template initial dans le footer |

## Project UI Resolution

| Capability | Canonical owner | Source of truth | Allowed variants | Verification |
|---|---|---|---|---|
| Select/Listbox | Sélecteurs HTML natifs des vues Poste | DESIGN.md, formulaires existants | native ; fenêtre et clavier du navigateur acceptés | Clavier villes/statuts et parcours CRUD |
| Form | Vues Poste et `shared/login.php`, comportement `js/etounsi.js` | Contrôleurs existants, ce contrat | connexion, création, édition, confirmation | Validation vide/invalide, erreurs associées, double envoi, succès réel |
| Scrollbar | `css/etounsi.css` | Tokens de DESIGN.md | document, dialogue bureau, table horizontale | Styles calculés et petits écrans |
| Toast | Messages serveur `compte-erreurs.php`, `layouts/admin.php` | Contrôleurs et vues de feedback existants | alertes persistantes, status pour succès ; aucun toast éphémère | Erreur de connexion et succès CRUD |
| CRUD | Routeur `app/core/App.php` et contrôleurs métier Poste | Code serveur existant | Routes, confirmations et retours existants | Bureau → compte → colis → acceptation → livraison → nettoyage |

Table Selection et Date ne s'appliquent pas : aucune sélection multiple ni saisie de date ajoutée. Les dates affichées restent celles des requêtes et des vues existantes.

## Parcours et interactions

| Parcours | Comportement et retour | Origine |
|---|---|---|
| Recherche portail | Filtre local des cinq services, synonymes, champ q dans l'URL, effacement explicite, annonce sans résultat | Brief et `shared/portail.php` |
| Entrée d'un service | Lien réel de repli ; si JS actif, dialogue natif plein écran, portes brunes puis intérieur | Brief et `js/etounsi.js` |
| Poste → Bureau de poste | Défilement vers la section d'accès et focus de cette section | Brief ; `shared/office.php` |
| Connexion | Formulaire commun, CSRF existant ; aucun résultat de démonstration | HomeController connexion |
| Visiteur déjà connecté | Affichage des actions de son rôle ; profil et déconnexion accessibles dans le menu du compte | `utilisateurConnecte()` et contrôleurs existants |
| Citoyen | Déclaration, historique, annulation autorisée et suivi réels | HomeController et PosteController |
| Administrateur | Gestion colis, bureaux, utilisateurs et autorisations existantes | App.php, AdminController et contrôleurs métier |
| Autres bureaux | Présentation du service, retour au portail ; pas de formulaire métier fictif | Périmètre du brief |
| Suppression | Page de confirmation existante ; envoi POST, pas de confirm() navigateur | Contrôleurs métier |

L'affichage de tous les résultats serveur est conservé pour les listes de validation. Les filtres GET persistent dans l'URL. Les tableaux utilisent un défilement horizontal local, les longs formulaires celui du document. Une pagination serveur pourra être étudiée avec un jeu de données plus important ; cette modification visuelle ne change pas les requêtes.

## Clavier, mobile, langue et erreurs

Le dialogue natif enferme le focus et rend le fond inactif. Retour et Échap ferment ; le lien photo retrouve le focus. Le mot de passe est vidé et masqué. La navigation mobile du portail est un dépliage non modal : elle ne prétend pas bloquer le fond. Le menu mobile administrateur conserve son comportement existant.

La bande photo défile au toucher et au clavier ; aucun service n'exige le survol. Les formulaires gardent leurs labels, leurs valeurs non sensibles après erreur et leurs messages PHP. Les champs invalides reçoivent le focus. Les mots de passe sont masqués et peuvent être affichés. Les POST désactivent le bouton jusqu'à la navigation ; aucun retry automatique d'une mutation n'est ajouté. Le contrôle des formulaires périmés et des transitions de colis reste serveur.

FR et AR/RTL couvrent le portail et le Front Office. Le Back Office reste FR comme avant. La préférence de mouvement réduit est respectée. Les ressources nouvelles sont locales. Le rapport `docs/validation-interface.md` précise les vérifications et leurs limites ; l'audit statique ne prouve pas le comportement PHP ou navigateur.
