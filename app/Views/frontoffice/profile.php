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
                <a class="sidebar-logout" href="index.php?page=logout">
                    <?php echo et_icon('log-out'); ?> <?php echo t('nav.logout'); ?>
                </a>
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
                    <p class="small-note"><?php echo t('account.edit.note'); ?></p>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
