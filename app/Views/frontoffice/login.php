<!DOCTYPE html>
<html lang="<?php echo et_lang(); ?>" dir="<?php echo et_dir(); ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-login">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">
        <section class="login-section container">
            <div class="login-story">
                <p class="eyebrow"><?php echo t('login.eyebrow'); ?></p>
                <h1><?php echo t('login.title'); ?></h1>
                <p><?php echo t('login.text'); ?></p>
                <img src="assets/frontoffice/img/street/street-mobile.webp" width="900" height="503" alt="">
            </div>

            <div class="login-panel">
                <form method="post" action="index.php?page=login" class="stack" novalidate data-validate-form>
<input type="hidden" name="csrf" value="<?php echo e(csrfToken()); ?>">

                    <?php if (isset($_GET['inscrit'])): ?>
                        <div class="alert alert-success">
                            <p><?php echo t('auth.success.signup'); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($erreurs['global'])): ?>
                        <div class="alert alert-error">
                            <p><?php echo $erreurs['global']; ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="field">
                        <label for="email"><?php echo t('login.email'); ?></label>
                        <input id="email" name="email" type="email" value="<?php echo e($old['email']); ?>"
                               data-validate="required|email">
                        <?php if (isset($erreurs['email'])): ?>
                            <p class="field-error server-error"><?php echo $erreurs['email']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="password"><?php echo t('login.password'); ?></label>
                        <input id="password" name="mot_de_passe" type="password" data-validate="required">
                        <?php if (isset($erreurs['mot_de_passe'])): ?>
                            <p class="field-error server-error"><?php echo $erreurs['mot_de_passe']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <div class="g-recaptcha" data-sitekey="<?php echo CitoyenController::CAPTCHA_SITE_KEY; ?>"></div>
                        <?php if (isset($erreurs['captcha'])): ?>
                            <p class="field-error"><?php echo $erreurs['captcha']; ?></p>
                        <?php endif; ?>
                    </div>

                    <button class="button button-brand button-full" type="submit"><?php echo t('login.submit'); ?></button>
                    <a class="button button-outline button-full" href="index.php?page=signup"><?php echo t('login.register'); ?></a>
                </form>

                <div class="demo-accounts">
                    <span class="badge badge-neutral"><?php echo t('login.demo'); ?></span>
                    <p><?php echo t('login.demo.text'); ?></p>
                    <div><code>admin@etounsi.tn</code> <small>admin</small></div>
                    <div><code>agent@etounsi.tn</code> <small>agent</small></div>
                    <div><code>citoyen@etounsi.tn</code> <small>citoyen</small></div>
                    <p class="demo-password"><?php echo t('login.password'); ?> : <code>password</code></p>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
    <script src="assets/frontoffice/js/validation.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
