---
version: alpha
name: E-Tounsi
description: Portail tunisien aux portes en bois, avec un espace Poste PHP MVC.
colors:
  primary: "#c91f3c"
  primary-hover: "#aa1832"
  background: "#fbf9f4"
  surface: "#ffffff"
  foreground: "#292b35"
  muted: "#65656c"
  border: "#e5e1d8"
  sun: "#edc953"
  sun-soft: "#fcf3d7"
  focus: "#126b92"
  danger: "#ad1b32"
  success: "#21604d"
  wood: "#84492e"
typography:
  body:
    fontFamily: '"Plus Jakarta Sans", Cairo, "Segoe UI", sans-serif'
    fontSize: 15px
    lineHeight: 1.65
  heading:
    fontFamily: 'Fraunces, Cairo, Georgia, serif'
    fontWeight: 600
    lineHeight: 1.15
rounded:
  control: 12px
  card: 20px
  hero: 24px
spacing:
  page-max: 1280px
  section: 76px
  section-mobile: 46px
components:
  button:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
  button-hover:
    backgroundColor: "{colors.primary-hover}"
  header:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.foreground}"
  service-ribbon:
    textColor: "{colors.surface}"
  step-marker:
    backgroundColor: "{colors.sun}"
  office:
    backgroundColor: "{colors.wood}"
  form:
    backgroundColor: "{colors.background}"
  focus-indicator:
    textColor: "{colors.focus}"
  table:
    textColor: "{colors.muted}"
  divider:
    backgroundColor: "{colors.border}"
  feedback:
    textColor: "{colors.danger}"
  feedback-success:
    textColor: "{colors.success}"
  help-panel:
    backgroundColor: "{colors.sun-soft}"
  scrollbar: {}
---

# E-Tounsi Design System

## Overview

### Creative North Star

La rue tunisienne du prototype fourni : façades claires, portes brunes cintrées, palmiers et tramway. Le portail ouvre une porte sur un bureau réel dans sa représentation. La photo et la transition portent l'identité ; les formulaires restent simples.

### Product context and register

Le public est celui des services publics tunisiens décrit dans le brief E-Tounsi. Le portail présente exactement Transport, Municipalité, Assurance publique — CNAM, Poste et Recette des Finances. Seul Poste reçoit une intégration fonctionnelle dans cette livraison. Les autres vues métier de l'équipe restent conservées.

Français et arabe sont les langues du portail et du Front Office Poste ; l'arabe utilise la direction RTL et Cairo. Le Back Office existant reste en français. Les textes arabes suivent ceux du projet ; aucune relecture linguistique externe n'est revendiquée.

Registre mixte : accueil illustré et expressif, puis utilité calme dans les formulaires et le CRUD. La référence est l'archive E-Tounsi-Projet.zip et la vidéo fournies. Éviter les tableaux de bord fictifs, les cartes de statistiques décoratives et les interfaces de conversation.

**Propriété des tokens : modèle B.** La source d'exécution est `css/etounsi.css`, bloc `:root`. Ce document reflète ses valeurs. Les feuilles Poste existantes sont des adaptateurs vers ces variables, pas un deuxième thème. Les bibliothèques eLearning et Spark conservent leurs notices et leur structure utile.

## Colors

Le rouge sert aux actions principales et à l'identité. Le blanc cassé est le fond ; les cartes de saisie sont blanches. Le jaune signale les étapes et accompagne les photos. Le brun concerne uniquement les portes. Les états des colis gardent leurs badges textuels et leurs couleurs distinctes.

L'accueil commun présente les cinq services à égalité : titres du bandeau en blanc, boutons et cartes sans icônes décoratives, FAQ généraliste et appel à choisir un service. Le pied de page parle de la plateforme entière. Les crédits eLearning sont conservés sur les seules pages qui utilisent encore ce template ; le nouveau portail utilise son thème propre.

Correspondance : primary → `--et-red`, primary-hover → `--et-red-hover`, background → `--et-paper`, surface → `--et-white`, foreground → `--et-ink`, muted → `--et-muted`, border → `--et-border`, sun → `--et-yellow`, sun-soft → `--et-yellow-soft`, focus → `--et-focus`, danger → `--et-danger`, success → `--et-success`, wood → `--et-wood`.

Le focus bleu reste visible sur les actions rouges. La sélection de texte est jaune. Cette livraison propose un thème clair uniquement.

## Typography

`--et-font` correspond à body et `--et-heading` à heading. Les polices sont locales, avec leurs licences OFL. Fraunces est réservé aux titres du portail et aux grands titres de connexion. Les listes, champs et boutons utilisent Plus Jakarta Sans ; Cairo couvre les caractères arabes. Le Back Office garde des titres sans empattement pour la lecture des données.

Les références de suivi restent complètes, en LTR, et les erreurs ne sont pas tronquées. Le titre de l'accueil varie de 34 à 65 px selon la largeur ; les formulaires conservent une taille lisible et un interligne aéré.

## Layout

La largeur maximale est `--et-page-max` : 1280 px. Les marges passent de 48 px à 28 puis 18 px. Les ruptures principales sont 1100, 900 et 650 px. L'en-tête occupe 86 px sur ordinateur et 72 px sur téléphone. Sous 900 px, la navigation devient un menu dépliable dans le document.

La bande comporte cinq photos dans une seule ligne sur ordinateur. Sur téléphone elle défile horizontalement avec ancrage ; tous les liens restent accessibles au clavier. Les listes métier gardent leur défilement horizontal local et leurs actions. Les formulaires défilent avec la page, sans hauteur fixe.

## Elevation & Depth

Les cartes utilisent une bordure fine. Une ombre légère détache la recherche et le menu du compte. Les images possèdent un voile sombre pour la lisibilité. Les portes utilisent la photographie d'origine comme texture, deux battants et une perspective. Aucun flou décoratif n'est appliqué aux données.

## Shapes

Les rayons control, card et hero correspondent à `--et-radius-control`, `--et-radius-card` et `--et-radius-hero`. Les boutons de connexion et les badges sont arrondis en pilule. Le Back Office réutilise les rayons Spark 10/14/18 px ; cette variante garde la compatibilité avec sa mise en page.

## Components

### Foundational visual states

Les boutons changent de fond au survol, montrent un focus et un léger déplacement à l'activation. Les formulaires POST désactivent le bouton pendant l'envoi avec un indicateur discret ; leur géométrie reste stable. Les erreurs serveur sont persistantes et les champs concernés sont associés à leur texte d'erreur. Les retours navigateur réactivent les boutons.

### Buttons and actions

`.et-button` est l'action principale ; ses variantes white/light accompagnent les photos. Les actions destructives existantes gardent leur libellé explicite et la page de confirmation. Aucune action ne simule un résultat métier.

### Navigation and data display

L'en-tête et le pied de page sont partagés dans `modules/poste/app/views/shared`. Le compte se déplie avec `details`, contient profil, espace autorisé et déconnexion POST. Les cinq services utilisent les données de `portail.php`. Les tables continuent d'afficher le résultat serveur complet : choix conservé pour le petit jeu de validation, sans pagination ni tri inventés. Les filtres GET restent partageables dans l'URL.

### Forms and overlays

`shared/login.php` est l'unique formulaire de connexion utilisé par la page dédiée et le bureau. Son action est le vrai contrôleur Poste, avec la session et le jeton CSRF existants. Les autres formulaires et leurs validations restent ceux du module. Les sélecteurs sont natifs : leur fenêtre est volontairement gérée par le navigateur, notamment pour villes, bureaux, rôles et statuts.

Le bureau utilise `dialog.showModal()` ; le navigateur gère l'inactivité du fond et le clavier. Échap ou Retour ferment, restaurent le focus et effacent le mot de passe. Le bureau possède son propre défilement. Le bouton « Bureau de poste » atteint et focalise sa section d'accès.

### Iconography

L'accueil commun et sa navigation utilisent des libellés de texte sans icônes décoratives ; le logo PNG de la présentation est conservé. Les écrans métier conservent Bootstrap Icons local, avec un texte ou un nom accessible.

### Motion

Survol des photos : 500 ms. L'entrée suit la vidéo fournie : la porte photographiée remplit l'écran, ses deux battants ouvrent de 83° avec un léger décalage, puis la caméra avance dans le bureau et rejoint son intérieur par un fondu. L'ensemble dure 9600 ms et démarre automatiquement au choix du service, sans commande Pause, Rejouer, progression ou Passer. Le même composant est partagé par les cinq services et garde leur propre photo intérieure. Le retour aux services et Échap permettent de quitter. Les changements courants utilisent 150–200 ms. La préférence `prefers-reduced-motion` rend l'entrée immédiate. Les images sont locales et la fermeture annule le mouvement en cours. Les mots de passe et l'état d'ouverture sont réinitialisés à la fermeture.

### Content and data visualization

Les libellés nomment une action concrète. Les états de colis viennent des contrôleurs et de la base. Pas de faux chiffres ni de fausse connexion. Les autres bureaux affichent clairement leur périmètre de présentation. Le chatbot, ses textes et ses ressources ne sont pas intégrés.

## Do's and Don'ts

- **Do:** utiliser les images fournies, les tokens communs et les véritables routes PHP.
- **Do:** conserver les noms des champs, les contrôles serveur, les licences et les droits d'accès.
- **Don't:** installer React, TypeScript ou un second système d'authentification dans le projet.
- **Don't:** remplacer une erreur par une alerte navigateur ou une réussite simulée.
