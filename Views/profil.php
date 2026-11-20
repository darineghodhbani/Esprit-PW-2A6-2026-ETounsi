<?php

$infoErrors   = $form === 'infos' ? $errors : [];
$pwdErrors    = $form === 'password' ? $errors : [];
$deleteErrors = $form === 'delete' ? $errors : [];
$v = $form === 'infos' ? $old : [
    'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
    'email' => $user->getEmail(), 'telephone' => $user->getTelephone(),
];
?>
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
                    <h1 class="display-3 text-white animated slideInDown">Mon profil</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php?page=home">Accueil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Mon profil</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl pb-5">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-4">
                    <div class="et-card p-4 h-100 text-center">
                        <i class="fa fa-user-circle fa-5x text-primary mb-3"></i>
                        <h4 class="mb-1"><?php echo e($user->getNomComplet()); ?></h4>
                        <p class="text-muted mb-3"><?php echo e($user->getEmail()); ?></p>
                        <p class="mb-1"><strong>Rôle :</strong> <?php echo e(UserController::ROLES[$user->getRole()] ?? $user->getRole()); ?></p>
                        <p class="mb-1"><strong>Téléphone :</strong> <?php echo e($user->getTelephone()); ?></p>
                        <p class="mb-3"><strong>Inscrit le :</strong> <?php echo e(date('d/m/Y', strtotime($user->getCreatedAt()))); ?></p>
                        <?php if ($user->getRole() === 'admin'): ?>
                            <a href="admin.php" class="btn btn-primary w-100">Aller au Back Office</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-8">

                    <div class="et-card p-4 mb-4">
                        <h4 class="mb-3">Mes informations</h4>
                        <?php if (isset($infoErrors['global'])): ?>
                            <div class="alert alert-danger"><?php echo e($infoErrors['global']); ?></div>
                        <?php endif; ?>
                        <form method="post" action="index.php?page=profil" novalidate data-validate-form>
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                            <input type="hidden" name="form" value="infos">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control<?php echo invalidClass($infoErrors, 'nom'); ?>" id="nom" name="nom"
                                        value="<?php echo e($v['nom']); ?>" data-validate="required|name">
                                    <?php echo errorFor($infoErrors, 'nom'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control<?php echo invalidClass($infoErrors, 'prenom'); ?>" id="prenom" name="prenom"
                                        value="<?php echo e($v['prenom']); ?>" data-validate="required|name">
                                    <?php echo errorFor($infoErrors, 'prenom'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control<?php echo invalidClass($infoErrors, 'email'); ?>" id="email" name="email"
                                        value="<?php echo e($v['email']); ?>" data-validate="required|email">
                                    <?php echo errorFor($infoErrors, 'email'); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control<?php echo invalidClass($infoErrors, 'telephone'); ?>" id="telephone" name="telephone"
                                        value="<?php echo e($v['telephone']); ?>" data-validate="required|phone">
                                    <?php echo errorFor($infoErrors, 'telephone'); ?>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary py-2 px-4" type="submit">Enregistrer</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="et-card p-4 mb-4">
                        <h4 class="mb-3">Changer mon mot de passe</h4>
                        <?php if (isset($pwdErrors['global'])): ?>
                            <div class="alert alert-danger"><?php echo e($pwdErrors['global']); ?></div>
                        <?php endif; ?>
                        <form method="post" action="index.php?page=profil" novalidate data-validate-form>
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                            <input type="hidden" name="form" value="password">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="old_password" class="form-label">Ancien mot de passe</label>
                                    <input type="password" class="form-control<?php echo invalidClass($pwdErrors, 'old_password'); ?>" id="old_password" name="old_password"
                                        data-validate="required">
                                    <?php echo errorFor($pwdErrors, 'old_password'); ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control<?php echo invalidClass($pwdErrors, 'password'); ?>" id="new_password" name="password"
                                        data-validate="required|password">
                                    <?php echo errorFor($pwdErrors, 'password'); ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="new_password_confirm" class="form-label">Confirmer</label>
                                    <input type="password" class="form-control<?php echo invalidClass($pwdErrors, 'password_confirm'); ?>" id="new_password_confirm" name="password_confirm"
                                        data-validate="required|match:new_password">
                                    <?php echo errorFor($pwdErrors, 'password_confirm'); ?>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary py-2 px-4" type="submit">Modifier le mot de passe</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <?php if ($user->getRole() !== 'admin'): ?>

                    <div class="et-card p-4 border border-danger">
                        <h4 class="mb-2 text-danger">Supprimer mon compte</h4>
                        <p class="mb-3">Cette action est définitive. Confirmez avec votre mot de passe.</p>
                        <?php if (isset($deleteErrors['global'])): ?>
                            <div class="alert alert-danger"><?php echo e($deleteErrors['global']); ?></div>
                        <?php endif; ?>
                        <form method="post" action="index.php?page=profil" novalidate data-validate-form
                              data-confirm="Voulez-vous vraiment supprimer votre compte ?">
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                            <input type="hidden" name="form" value="delete">
                            <div class="row g-3 align-items-start">
                                <div class="col-md-6">
                                    <input type="password" class="form-control<?php echo invalidClass($deleteErrors, 'delete_password'); ?>" id="delete_password" name="delete_password"
                                        placeholder="Votre mot de passe" data-validate="required">
                                    <?php echo errorFor($deleteErrors, 'delete_password'); ?>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger py-2 px-4" type="submit">Supprimer mon compte</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>

</html>
