# Validation de l'interface E-Tounsi

Validation effectuée le 20 novembre 2026 sur la machine XAMPP du projet, avec PHP 8.2, MariaDB et Google Chrome piloté par Playwright. Intégration du prototype fourni dans PHP MVC : accueil commun, cinq bureaux illustrés, portes en bois, connexion Poste et adaptation des vues existantes.

Les modèles, contrôleurs, paramètres de connexion, règles de sécurité et scripts SQL sont hors du périmètre de modification. Les autres modules conservent leurs fonctionnalités existantes.

## Résultats

| Contrôle | Résultat |
|---|---|
| Syntaxe PHP des vues, du contrôleur de présentation et de l'entrée | 59 fichiers vérifiés, aucune erreur |
| Syntaxe de `js/etounsi.js` | `node --check`, aucune erreur |
| Accueil | Exactement cinq services, images et polices locales, aucun chatbot |
| Recherche | Synonymes dont « colis », filtre des cartes, URL, effacement, résultat vide et retour arrière |
| Entrée | Porte fermée, ouverture brune texturée, intérieur du bureau correspondant, retour et Échap |
| Clavier | Dialogue modal natif, fond inactif, focus contenu puis restauré, navigation mobile, sélecteur de ville |
| Connexion depuis le bureau | POST au vrai contrôleur, jeton CSRF de la même session, erreur 422 réelle, email conservé, mot de passe vidé et masqué |
| Compte | Connexions citoyen et administrateur, bonne destination, actions selon le rôle, profil et déconnexion POST |
| Poste métier | Bureau ajouté, compte créé, colis déclaré, référence unique, acceptation, transit, livraison, annulations et suppression des données temporaires |
| Refus serveur | Rôle non autorisé, CSRF incorrect, saut d'étape, formulaire périmé et traitement d'un colis terminé refusés |
| Responsive | 1440, 768, 390 et 320 px sur neuf routes ; aucun débordement horizontal du document |
| Langues | Portail et Front Office FR/AR, direction RTL, références LTR |
| Mouvement réduit | Animation et défilement immédiats avec la préférence activée |
| Sans JavaScript | La photo ouvre une vraie page bureau ; intérieur, lien et formulaire Poste restent accessibles |
| Accessibilité automatisée | Axe WCAG A/AA et 2.1 AA : aucune violation relevée sur dix écrans/états vérifiés |
| Ressources et erreurs | Aucune ressource en erreur sur le parcours, aucune exception JavaScript ni avertissement PHP observé |
| Audit statique du thème | Mode strict : 0 erreur, 0 avertissement, aucune propriété d'interface non résolue |
| DESIGN.md | Lint officiel : 0 erreur et 0 avertissement |

Les dix états Axe sont : accueil FR, dialogue bureau, connexion bureau, erreur de connexion, inscription, accueil AR, tableau de bord administrateur, formulaire bureau, accueil AR mobile et liste des colis administrateur mobile. Les contrastes du fil d'Ariane et le lien de connexion de l'inscription ont été corrigés après ce contrôle.

Les 21 fichiers des contrôleurs, modèles, noyau, configuration et SQL Poste ont été comparés en SHA-256 avant/après : aucune modification. Les données exportées des quatre tables sont identiques à celles de la sauvegarde initiale après nettoyage des seuls comptes, bureaux et colis créés par les tests. Les compteurs automatiques d'identifiants peuvent avancer pendant ces essais, sans modifier les lignes existantes.

## Refaire le parcours sur XAMPP

1. Démarrer Apache et MySQL ; conserver la base et la configuration existantes. Cette modification d'interface ne demande aucune migration.
2. Ouvrir `http://localhost/Esprit-PW-2A6-2026-ETounsi/` et recharger avec Ctrl+F5.
3. Rechercher « colis », effacer la recherche, puis cliquer sur la photo Poste. Attendre l'ouverture de la porte.
4. Cliquer sur « Bureau de poste » : la section de connexion apparaît plus bas. Essayer un identifiant incorrect, puis un compte de validation du fichier privé `modules/poste/config/acces-local.txt`.
5. Avec un citoyen, déclarer un colis dans un bureau existant puis consulter sa référence et son historique. Avec l'administrateur, accepter la déclaration, passer en transit puis livrer. Utiliser uniquement des données de test pour les suppressions.
6. Vérifier l'arabe, le menu sur téléphone, Échap, les formulaires et le suivi public.

Les scripts techniques et captures de la vérification locale sont gardés hors du dépôt applicatif, dans `C:\Users\HP2026\Documents\Codex\2026-11-16\bonjour-toutes-et-tous-durant-la\work\design-lovable`. Ils n'ajoutent aucune dépendance Node au site. Les commandes exécutées sont `php -l` sur les fichiers PHP, `node --check js/etounsi.js`, puis `node verifier-parcours.cjs`, `node verifier-interface.cjs` et `node verifier-accessibilite.cjs` depuis cet espace de vérification. Les deux audits de thème ont utilisé `audit_project.py --mode strict` et `npx -p @google/design.md designmd lint DESIGN.md`.

## Organisation des fichiers

| Fichiers | Rôle de la modification |
|---|---|
| `index.php`, `Controllers/HomeController.php` | Route de présentation du bureau et session Poste réutilisée sur le nouvel accueil |
| `Views/home.php`, `Views/service.php` | Accueil à cinq services et page de repli des bureaux |
| `Views/includes/head.php`, `navbar.php`, `footer.php` | Thème et éléments communs sur les anciennes pages conservées |
| `modules/poste/app/views/shared/portail.php` | Traductions, liens, données des cinq services et échappement des sorties |
| `shared/head.php`, `header.php`, `footer.php` | Présentation commune, logo existant, compte et navigation |
| `shared/office.php`, `login.php`, `services-poste.php` | Bureau, connexion réelle et raccourcis vers les fonctionnalités autorisées |
| `modules/poste/app/views/accueil.php`, `connexion.php` | Nouvelle présentation Poste et formulaire commun |
| `modules/poste/app/views/includes/*` | Assemblage du thème commun, suppression du chargement de l'ancien JavaScript du Front Office |
| `modules/poste/app/views/compte-erreurs.php` | Identifiant du message général pour son association aux champs de connexion |
| Vues de listes, confirmation et fragments de formulaires Poste | Attribut `novalidate` : les messages PHP restent la validation visible |
| `css/etounsi.css`, `js/etounsi.js` | Tokens, responsive, portes, recherche, menus, mots de passe et état d'envoi |
| `modules/poste/app/views/partials/head.php`, `public/assets/back/css/poste.css` | Thème du back-office et navigation latérale rouge, structure Spark conservée |
| `modules/poste/public/assets/images/portal/*.webp` | Six images du prototype, optimisées pour le chargement local |
| `modules/poste/public/assets/fonts/*` | Plus Jakarta Sans et Fraunces locales avec leurs licences ; Cairo existante conservée |
| `README.md`, `modules/poste/README.md`, `DESIGN.md`, `UX-CONTRACT.md`, `premium-ui.json` | Installation, périmètre, propriété du thème et trace des contrôles |

L'ancien `modules/poste/public/assets/front/js/poste.js` a été retiré : ses interactions sont regroupées dans `js/etounsi.js`, ce qui évite une double activation des boutons de mot de passe. Aucune archive React, dépendance de compilation ou ressource de chatbot n'est copiée dans le site.

## Limites

Les quatre autres bureaux présentent le service ; leurs traitements métier ne sont pas ajoutés dans cette tâche Poste. Les anciennes pages d'équipe restent disponibles. Le Back Office conserve sa langue française et le traitement existant des erreurs serveur.

Les tests ont été réalisés dans Chrome sur ce PC, avec dimensions mobiles simulées. Ils ne remplacent pas une vérification sur un téléphone physique, un lecteur d'écran ou une certification d'accessibilité. Aucun test Safari/Firefox ni contrôle exhaustif du zoom navigateur à 200 % n'est revendiqué ; le reflow à 720 px a été contrôlé.

## Mise à jour : accueil commun et porte photographique

L'accueil présente maintenant les cinq services sans icônes décoratives, avec le texte du bandeau en blanc, une FAQ générale et un footer commun. Le bloc réservé aux bureaux de poste devient un appel à choisir son service. Les crédits du template initial restent sur les pages qui emploient encore ce template ; ils ne sont pas affichés sur le nouveau portail indépendant.

La porte de la démonstration locale fournie est enregistrée en WebP dans les assets. `css/portes.css` et `js/portes.js` portent l'animation partagée : deux pivots décalés, éclairage, caméra et fondu sur 7,2 secondes. Chaque service garde son intérieur et son libellé. Le bouton texte « Passer l'animation », le mouvement réduit, la fermeture pendant le chargement et la réouverture ont été vérifiés.

Contrôles de cette mise à jour : accueil FR/AR à 1440/768/390/320 px, cinq services et intérieurs, recherche/effacement, footer, texte blanc, absence de débordement, accès citoyen réel et historique, aucune exception JavaScript ni ressource en erreur. Les captures de la porte fermée, à mi-ouverture et des bureaux ont été inspectées. L'accessibilité automatisée ciblée de l'accueil et les audits de thème restent sans anomalie relevée. Le CRUD utilisateurs et la sécurité Front Office ont également été réexécutés pour la validation, avec nettoyage des seules données de test.
