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
                        <a href="index.php?page=dashboard">
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

                <?php if (isset($_SESSION['flash'])): ?><p class="alert alert-success" role="status"><?php echo e($_SESSION['flash']['message']); ?></p><?php unset($_SESSION['flash']); endif; ?>
                <?php if ($erreurs !== []): ?><div class="alert alert-error" role="alert"><?php foreach ($erreurs as $message): ?><p><?php echo e($message); ?></p><?php endforeach; ?></div><?php endif; ?>
                <form class="stack profile-edit" method="post" action="index.php?page=profile" novalidate>
                    <input type="hidden" name="csrf" value="<?php echo e(csrfToken()); ?>">
                    <?php foreach (['nom' => [tr('Nom complet', 'الاسم الكامل'), 'text'], 'email' => [tr('Adresse email', 'البريد الإلكتروني'), 'email'],
                        'mot_de_passe_actuel' => [tr('Mot de passe actuel', 'كلمة المرور الحالية'), 'password'],
                        'password' => [tr('Nouveau mot de passe', 'كلمة مرور جديدة'), 'password'], 'confirmation' => [tr('Confirmer le mot de passe', 'تأكيد كلمة المرور'), 'password']] as $champ => $info): ?>
                    <div class="field"><label for="<?php echo e($champ); ?>"><?php echo e($info[0]); ?></label>
                        <input id="<?php echo e($champ); ?>" name="<?php echo e($champ); ?>" type="<?php echo e($info[1]); ?>" <?php echo $info[1] !== 'password' ? 'required' : ''; ?>
                            value="<?php echo e($info[1] === 'password' ? '' : $old[$champ]); ?>"
                            autocomplete="<?php echo $champ === 'mot_de_passe_actuel' ? 'current-password' : ($info[1] === 'password' ? 'new-password' : ($champ === 'email' ? 'email' : 'name')); ?>"
                            <?php echo isset($erreurs[$champ]) ? 'aria-invalid="true" aria-describedby="erreur-' . e($champ) . '"' : ''; ?>>
                        <?php if (isset($erreurs[$champ])): ?><small class="field-error" id="erreur-<?php echo e($champ); ?>"><?php echo e($erreurs[$champ]); ?></small><?php endif; ?>
                    </div><?php endforeach; ?>
                    <p class="page-lead"><?php echo e(tr('Laissez les nouveaux mots de passe vides pour conserver le vôtre. Le mot de passe actuel est nécessaire pour modifier vos identifiants.', 'اترك كلمة المرور الجديدة فارغة للاحتفاظ بكلمتك الحالية.')); ?></p>
                    <button class="button button-dark" type="submit"><?php echo e(tr('Enregistrer mon profil', 'حفظ ملفّي الشخصي')); ?></button>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
