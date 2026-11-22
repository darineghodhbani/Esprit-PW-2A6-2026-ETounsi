# Contribution CNAM et connexion Google

Cette branche ajoute les cinq entités métier de la version corrigée du diagramme : `AssureSocial`, `Beneficiaire`, `DemandeRemboursement`, `DocumentCNAM` et `DecisionCNAM`. Le titulaire `SOI` est créé avec le dossier et une demande peut concerner n’importe quel bénéficiaire validé.

## Installation locale

1. Installer PHP 8.2 ou plus récent avec PDO MySQL, Fileinfo et mbstring, MySQL 8.0.16 ou plus récent, puis les dépendances PHP avec `composer install`.
2. Copier `config.local.example.php` vers `config.local.php`, puis adapter les paramètres à votre MySQL. Le fichier réel n’est pas suivi par Git.
3. Depuis le dépôt, lancer `php scripts/migrate-cnam.php`. La migration conserve les anciennes tables. Si elle détecte des données dans le modèle CNAM historique, elle s’arrête sans les remplacer : il faut alors préparer leur conversion avant de poursuivre.
4. Pour les pièces jointes, régler PHP à `upload_max_filesize=5M`, `post_max_size=16M` et `max_file_uploads=5`. Les fichiers sont stockés hors du dossier public, avec des permissions privées. `CNAM_STORAGE_DIR` permet d’indiquer un autre chemin privé.

Les comptes de rôle `Agent` sont créés par un administrateur dans **Utilisateurs**, puis activés dans **Habilitations CNAM**. Seuls les agents habilités peuvent valider les rattachements, traiter les remboursements et enregistrer un versement manuel.

En local, lancer le serveur avec `APP_ENV=development php -S 127.0.0.1:8081 -t .`. Le CAPTCHA est volontairement absent en développement. En production, définir `recaptcha_site_key` et `recaptcha_secret_key` dans la configuration locale avec de vraies clés Google reCAPTCHA ; sans ces clés, l’authentification échoue en mode fermé.

## Connexion Google

1. Créer un client OAuth de type application Web dans Google Cloud, activer l’écran de consentement et autoriser le domaine de test.
2. Déclarer l’URI de redirection exacte de votre environnement. En local, le modèle fourni est `http://127.0.0.1:8081/index.php?page=google-callback`; en production, utiliser l’URL publique en HTTPS.
3. Copier `google.local.example.php` vers `google.local.php`, puis y renseigner le client ID, le secret et l’URI exacte. Le fichier réel est ignoré par Git : ne jamais publier le secret dans une branche ou un message.
4. Une fois configuré, **Se connecter avec Google** est disponible sur l’écran de connexion. La liaison à un compte E-Tounsi existant se fait depuis le profil après confirmation de son mot de passe et avec la même adresse vérifiée.

Le flux demande uniquement `openid`, `email` et `profile`, avec `state`, `nonce` et PKCE. Le serveur vérifie la signature et l’audience de l’identité avant de créer la liaison. Google ne donne jamais un rôle `Agent` ou `Administrateur` : ces rôles restent attribués depuis l’espace d’administration.
