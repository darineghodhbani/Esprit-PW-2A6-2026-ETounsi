<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>

<body class="et-page">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>

    <?php
    $porteImage = 'e-tounsi-hero.jpg';
    $porteTitre = 'Bienvenue sur E-Tounsi';
    require_once __DIR__ . '/includes/portes.php';
    ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="container-fluid bg-primary py-5 mb-5 page-header page-header-accueil">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Connexion</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php?page=home">Accueil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Connexion</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl pb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="et-card p-5">
                        <h3 class="mb-4 text-center">Accédez à votre espace</h3>

                        <?php if (isset($errors['global'])): ?>
                            <div class="alert alert-danger"><?php echo e($errors['global']); ?></div>
                        <?php endif; ?>

                        <form method="post" action="index.php?page=login" novalidate data-validate-form>
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control<?php echo invalidClass($errors, 'email'); ?>" id="email" name="email"
                                    value="<?php echo e($old['email'] ?? ''); ?>" placeholder="exemple@mail.com" data-validate="required|email">
                                <?php echo errorFor($errors, 'email'); ?>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control<?php echo invalidClass($errors, 'password'); ?>" id="password" name="password"
                                    placeholder="Votre mot de passe" data-validate="required">
                                <?php echo errorFor($errors, 'password'); ?>
                            </div>

                            <button class="btn btn-primary w-100 py-3" type="submit">Se connecter</button>
                        </form>

                        <p class="text-center mt-4 mb-0">
                            Pas encore de compte ? <a href="index.php?page=register">Créer un compte</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>

</html>
