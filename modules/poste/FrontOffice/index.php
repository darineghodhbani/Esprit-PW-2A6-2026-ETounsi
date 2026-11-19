<?php

require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/Controllers/PosteController.php';
require_once __DIR__ . '/Controllers/CompteController.php';
require_once __DIR__ . '/includes/fonctions.php';

$language = 'fr';
$direction = 'ltr';
if (isset($_GET['lang']) && $_GET['lang'] === 'ar') {
    $language = 'ar';
    $direction = 'rtl';
}

$currentPage = 'accueil';
if (isset($_GET['page']) && is_string($_GET['page'])) {
    $currentPage = $_GET['page'];
}

$controleur = new PosteController(posteRepository());
$compteControleur = new CompteController(gestionUtilisateurs());
$utilisateur = utilisateurConnecte();
$numeroSuivi = '';
$parametresLangue = [];
$erreur = '';
$erreurs = [];
$confirmationCompte = '';
if (isset($_SESSION['poste_compte_flash'])) {
    if ($_SESSION['poste_compte_flash'] === 'inscription') {
        $confirmationCompte = tr('Votre compte a été créé. Connectez-vous pour déclarer un envoi.', 'تمّ إنشاء حسابك. سجّل الدخول للتصريح بإرسال طرد.');
    } elseif ($_SESSION['poste_compte_flash'] === 'profil') {
        $confirmationCompte = tr('Votre profil a été mis à jour.', 'تمّ تحديث ملفّك الشخصي.');
    }
}
unset($_SESSION['poste_compte_flash']);
if ($currentPage === 'connexion' && !empty($_SESSION['session_expiree'])) {
    $confirmationCompte = tr('Votre session a expiré. Connectez-vous de nouveau.', 'انتهت الجلسة. سجّل الدخول مجدّدًا.');
    unset($_SESSION['session_expiree']);
}

$valeurs = [
    'nom' => '',
    'email' => '',
    'password' => '',
    'confirmation' => '',
    'mot_de_passe_actuel' => '',
];
if ($utilisateur !== null) {
    $valeurs['nom'] = $utilisateur['nom'];
    $valeurs['email'] = $utilisateur['email'];
}

if ($currentPage === 'envoi' || $currentPage === 'historique') {
    exigerRole(['citoyen']);
}
if ($currentPage === 'profil') {
    exigerConnexion();
}

$jeton = csrfToken();
$formulaireEnvoye = $_SERVER['REQUEST_METHOD'] === 'POST';
$pagesFormulaire = ['envoi', 'historique', 'connexion', 'inscription', 'profil', 'deconnexion'];
if ($formulaireEnvoye) {
    foreach ($valeurs as $champ => $valeur) {
        if (isset($_POST[$champ]) && is_string($_POST[$champ])) {
            $valeurs[$champ] = $_POST[$champ];
        }
    }

    if (!in_array($currentPage, $pagesFormulaire, true)) {
        http_response_code(405);
        header('Allow: GET');
        $erreur = tr('Cette page accepte seulement la consultation.', 'هذه الصفحة مخصّصة للعرض فقط.');
    } elseif (!csrfValide($_POST['csrf'] ?? '')) {
        http_response_code(403);
        $erreur = tr('Le formulaire a expiré. Rechargez la page puis réessayez.', 'انتهت صلاحية الاستمارة. أعد تحميل الصفحة ثم حاول مجدّدًا.');
    }
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET, POST');
    $erreur = tr('Utilisez les liens et les formulaires du site.', 'استخدم روابط الموقع واستماراته.');
}

// Chaque cas prépare les données de la vue correspondante.
$vue = $currentPage;
switch ($currentPage) {
    case 'accueil':
        $pageTitle = tr('Services postaux', 'الخدمات البريدية');
        break;

    case 'suivi':
        $pageTitle = tr('Suivre un colis', 'تتبّع طرد');
        $rechercheLancee = isset($_GET['numero']);
        if ($rechercheLancee && is_string($_GET['numero'])) {
            $numeroSuivi = strtoupper(trim($_GET['numero']));
        }
        $numeroValide = preg_match('/^ET[0-9A-Z]{6,20}TN$/', $numeroSuivi) === 1;
        $colis = null;
        if ($numeroValide) {
            $colis = $controleur->chercherColis($numeroSuivi);
        }
        if ($numeroSuivi !== '') {
            $parametresLangue['numero'] = $numeroSuivi;
        }
        break;

    case 'bureaux':
        $pageTitle = tr('Bureaux de poste', 'مكاتب البريد');
        $annuaire = $controleur->filtrerBureaux($_GET['ville'] ?? '');
        if ($annuaire['ville'] !== '') {
            $parametresLangue['ville'] = $annuaire['ville'];
        }
        break;

    case 'envoi':
        $pageTitle = tr('Déclarer un envoi', 'التصريح بإرسال طرد');
        if ($formulaireEnvoye && $erreur === '') {
            $erreur = $controleur->declarerEnvoi($_POST['id_bureau_depart'] ?? '');
        }
        $bureaux = $controleur->listerBureaux();
        break;

    case 'historique':
        $pageTitle = tr('Mes colis', 'طرودي');
        if ($formulaireEnvoye && $erreur === '') {
            $erreur = $controleur->annulerEnvoi($_POST['id_colis'] ?? '');
        }
        $colisDuCitoyen = $controleur->listerHistorique();
        $confirmation = $_SESSION['poste_front_flash'] ?? null;
        unset($_SESSION['poste_front_flash']);
        break;

    case 'connexion':
        $pageTitle = tr('Se connecter', 'تسجيل الدخول');
        if ($formulaireEnvoye && $erreur === '') {
            $resultat = $compteControleur->connexion($valeurs);
            $erreurs = $resultat['erreurs'];
            if (empty($erreurs) && $resultat['utilisateur'] !== null) {
                connecterUtilisateur($resultat['utilisateur']);
                if ($resultat['utilisateur']['role'] !== 'citoyen') {
                    header('Location: ../BackOffice/', true, 303);
                } else {
                    header('Location: ' . frontUrl('historique'), true, 303);
                }
                exit;
            }
            http_response_code(422);
        }
        break;

    case 'inscription':
        $pageTitle = tr('Créer un compte', 'إنشاء حساب');
        if ($formulaireEnvoye && $erreur === '') {
            $resultat = $compteControleur->inscription($valeurs);
            $erreurs = $resultat['erreurs'];
            if (empty($erreurs)) {
                $_SESSION['poste_compte_flash'] = 'inscription';
                header('Location: ' . frontUrl('connexion'), true, 303);
                exit;
            }
            http_response_code(422);
        }
        break;

    case 'profil':
        $pageTitle = tr('Mon profil', 'ملفّي الشخصي');
        if ($formulaireEnvoye && $erreur === '') {
            $resultat = $compteControleur->modifierProfil($utilisateur, $valeurs);
            $erreurs = $resultat['erreurs'];
            if (empty($erreurs)) {
                connecterUtilisateur($resultat['utilisateur']);
                $_SESSION['poste_compte_flash'] = 'profil';
                header('Location: ' . frontUrl('profil'), true, 303);
                exit;
            }
            http_response_code(422);
        }
        break;

    case 'deconnexion':
        if (!$formulaireEnvoye) {
            http_response_code(405);
            header('Allow: POST');
            $erreur = tr('Utilisez le bouton de déconnexion.', 'استخدم زرّ تسجيل الخروج.');
        }
        if ($formulaireEnvoye && $erreur === '') {
            deconnecterUtilisateur();
            header('Location: ' . frontUrl('connexion'), true, 303);
            exit;
        }
        $pageTitle = tr('Déconnexion', 'تسجيل الخروج');
        $vue = 'connexion';
        break;

    default:
        http_response_code(404);
        $pageTitle = tr('Page introuvable', 'الصفحة غير موجودة');
        $vue = 'introuvable';
        break;
}
?>
<!doctype html>
<html lang="<?= e($language) ?>" dir="<?= e($direction) ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
    <main id="contenu">
        <?php require_once __DIR__ . '/Views/' . $vue . '.php'; ?>
    </main>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
