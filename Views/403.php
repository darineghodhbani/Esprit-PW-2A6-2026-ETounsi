<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>

<body>
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="container-xxl py-5">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <i class="bi bi-shield-lock display-1 text-primary"></i>
                    <h1 class="display-1">403</h1>
                    <h1 class="mb-4">Accès refusé</h1>
                    <p class="mb-4">Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?page=home">Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>

</html>
