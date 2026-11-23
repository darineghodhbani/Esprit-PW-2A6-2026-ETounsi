<!DOCTYPE html>
<html lang="<?php echo et_lang(); ?>" dir="<?php echo et_dir(); ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-profile">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">
        <div class="workspace container">
            <aside class="workspace-sidebar">
                <p class="eyebrow"><?php echo t('account.title'); ?></p>
                <div class="sidebar-person">
                    <?php echo et_icon('user'); ?>
                    <div>
                        <strong><?php echo e($citoyen['prenom'] . ' ' . $citoyen['nom']); ?></strong>
                        <small><?php echo e($citoyen['email']); ?></small>
                    </div>
                </div>
                <nav>
                    <a href="index.php?page=profile" aria-current="page">
                        <?php echo et_icon('user'); ?> <?php echo t('account.profile'); ?>
                    </a>
                    <?php if ($_SESSION['role'] == 'agent' || $_SESSION['role'] == 'admin'): ?>
                        <a href="index.php?page=<?= $_SESSION['role'] === 'agent' ? 'admin-demandes-cnam' : 'dashboard' ?>">
                            <?php echo et_icon('landmark'); ?> <?php echo t('nav.backoffice'); ?>
                        </a>
                    <?php endif; ?>
                </nav>
                <form method="post" action="index.php?page=logout" class="logout-inline"><input type="hidden" name="csrf" value="<?php echo e(csrfToken()); ?>"><button type="submit" class="sidebar-logout">
                    <?php echo et_icon('log-out'); ?> <?php echo t('nav.logout'); ?>
                </button></form>
            </aside>

            <div class="workspace-content">
                <h1><?php echo t('account.welcome'); ?> <?php echo e($citoyen['prenom']); ?></h1>

                <?php if ($citoyen['role'] === 'citoyen'): ?><p><a class="button button-outline" href="index.php?page=mon-dossier"><?= e(tr('Mon dossier CNAM', 'ملفي لدى الصندوق')) ?></a></p><?php endif; ?>

                <div class="profile-card surface">
                    <h2><?php echo t('account.infos'); ?></h2>
                    <dl>
                        <dt><?php echo t('signup.nom'); ?></dt>
                        <dd><?php echo e($citoyen['nom']); ?></dd>
                        <dt><?php echo t('signup.prenom'); ?></dt>
                        <dd><?php echo e($citoyen['prenom']); ?></dd>
                        <dt><?php echo t('account.email'); ?></dt>
                        <dd><?php echo e($citoyen['email']); ?></dd>
                        <dt><?php echo t('account.phone'); ?></dt>
                        <dd>
                            <?php if ($citoyen['telephone'] == ''): ?>
                                —
                            <?php else: ?>
                                <?php echo e($citoyen['telephone']); ?>
                            <?php endif; ?>
                        </dd>
                        <dt><?php echo t('account.role'); ?></dt>
                        <dd><span class="badge badge-info"><?php echo $citoyen['role']; ?></span></dd>
                        <dt><?php echo t('account.since'); ?></dt>
                        <dd><?php echo $citoyen['date_inscription']; ?></dd>
                    </dl>
                </div>

                <?php if ($googleReady): ?>
                <section class="profile-card surface"><h2><?= e(tr('Compte Google', 'حساب Google')) ?></h2>
                    <?php if ($googleLinked): ?><p><?= e(tr('Votre compte Google est lié.', 'حسابك مرتبط بحساب Google.')) ?></p>
                    <?php else: ?><form class="stack" method="post" action="index.php?page=google-start">
                        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="mode" value="link">
                        <label for="google-password"><?= e(tr('Confirmez votre mot de passe actuel', 'تأكيد كلمة المرور الحالية')) ?></label>
                        <input id="google-password" name="password" type="password" autocomplete="current-password" required>
                        <button class="button button-outline" type="submit"><?= e(tr('Lier mon compte Google', 'ربط حساب Google')) ?></button>
                    </form><?php endif; ?>
                </section><?php endif; ?>

                <?php if (isset($_SESSION['flash'])): ?><p class="alert alert-success" role="status"><?php echo e($_SESSION['flash']['message']); ?></p><?php unset($_SESSION['flash']); endif; ?>
                <?php if ($erreurs !== []): ?><div class="alert alert-error" role="alert"><?php foreach ($erreurs as $message): ?><p><?php echo e($message); ?></p><?php endforeach; ?></div><?php endif; ?>
                <form class="stack profile-edit" method="post" action="index.php?page=profile" novalidate data-validate-form>
                    <input type="hidden" name="csrf" value="<?php echo e(csrfToken()); ?>">

                    <div class="field">
                        <label for="nom"><?php echo e(t('profil.nom_complet')); ?></label>
                        <input id="nom" name="nom" type="text" value="<?php echo e($old['nom']); ?>" data-validate="required|min:2|max:50">
                        <?php if (isset($erreurs['nom'])): ?><small class="field-error server-error"><?php echo e($erreurs['nom']); ?></small><?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="email"><?php echo e(t('profil.adresse_email')); ?></label>
                        <input id="email" name="email" type="email" value="<?php echo e($old['email']); ?>" data-validate="required|email">
                        <?php if (isset($erreurs['email'])): ?><small class="field-error server-error"><?php echo e($erreurs['email']); ?></small><?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="mot_de_passe_actuel"><?php echo e(t('profil.mot_de_passe_actuel')); ?></label>
                        <input id="mot_de_passe_actuel" name="mot_de_passe_actuel" type="password" autocomplete="current-password">
                        <?php if (isset($erreurs['mot_de_passe_actuel'])): ?><small class="field-error server-error"><?php echo e($erreurs['mot_de_passe_actuel']); ?></small><?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="password"><?php echo e(t('profil.nouveau_mot_de_passe')); ?></label>
                        <input id="password" name="password" type="password" autocomplete="new-password" data-validate="min:8">
                        <?php if (isset($erreurs['password'])): ?><small class="field-error server-error"><?php echo e($erreurs['password']); ?></small><?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="confirmation"><?php echo e(t('profil.confirmer_mot_de_passe')); ?></label>
                        <input id="confirmation" name="confirmation" type="password" autocomplete="new-password" data-validate="match:password">
                        <?php if (isset($erreurs['confirmation'])): ?><small class="field-error server-error"><?php echo e($erreurs['confirmation']); ?></small><?php endif; ?>
                    </div>
                    <p class="page-lead"><?php echo e(t('profil.aide_mot_de_passe')); ?></p>
                    <button class="button button-dark" type="submit"><?php echo e(t('profil.enregistrer')); ?></button>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
    <script src="assets/frontoffice/js/validation.js"></script>
</body>
</html>
