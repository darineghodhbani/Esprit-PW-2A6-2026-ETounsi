<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>

<body class="et-page">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="container-fluid bg-primary py-5 mb-5 page-header page-header-accueil">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Inscription</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php?page=home">Accueil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Inscription</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl pb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="et-card p-5">
                        <h3 class="mb-4 text-center">Créer votre compte citoyen</h3>

                        <?php if (isset($errors['global'])): ?>
                            <div class="alert alert-danger"><?php echo e($errors['global']); ?></div>
                        <?php endif; ?>

                        <form method="post" action="index.php?page=register" novalidate data-validate-form>
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control<?php echo invalidClass($errors, 'nom'); ?>" id="nom" name="nom"
                                        value="<?php echo e($old['nom'] ?? ''); ?>" data-validate="required|name">
                                    <?php echo errorFor($errors, 'nom'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control<?php echo invalidClass($errors, 'prenom'); ?>" id="prenom" name="prenom"
                                        value="<?php echo e($old['prenom'] ?? ''); ?>" data-validate="required|name">
                                    <?php echo errorFor($errors, 'prenom'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control<?php echo invalidClass($errors, 'email'); ?>" id="email" name="email"
                                        value="<?php echo e($old['email'] ?? ''); ?>" data-validate="required|email">
                                    <?php echo errorFor($errors, 'email'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control<?php echo invalidClass($errors, 'telephone'); ?>" id="telephone" name="telephone"
                                        value="<?php echo e($old['telephone'] ?? ''); ?>" placeholder="22123456" data-validate="required|phone">
                                    <?php echo errorFor($errors, 'telephone'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control<?php echo invalidClass($errors, 'password'); ?>" id="password" name="password"
                                        data-validate="required|password">
                                    <?php echo errorFor($errors, 'password'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control<?php echo invalidClass($errors, 'password_confirm'); ?>" id="password_confirm" name="password_confirm"
                                        data-validate="required|match:password">
                                    <?php echo errorFor($errors, 'password_confirm'); ?>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule et un chiffre.</small>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Créer mon compte</button>
                                </div>
                            </div>
                        </form>

                        <p class="text-center mt-4 mb-0">
                            Déjà inscrit ? <a href="index.php?page=login">Se connecter</a>
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
