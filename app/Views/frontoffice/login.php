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

                <?php if (GoogleSignIn::configured()): ?>
                <form class="stack google-signin-form" method="post" action="index.php?page=google-start">
                    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                    <button class="button button-outline button-full google-signin-button" type="submit">
                        <svg class="google-signin-mark" aria-hidden="true" focusable="false" viewBox="0 0 48 48">
                            <path fill="#4285F4" d="M43.61 24.45c0-1.42-.13-2.78-.36-4.09H24v7.74h11.03c-.48 2.6-1.95 4.8-4.18 6.28v5.14h6.77c3.97-3.66 5.99-9.05 5.99-15.07z"/>
                            <path fill="#34A853" d="M24 44c5.4 0 9.92-1.79 13.22-4.86l-6.77-5.14c-1.79 1.2-4.09 1.92-6.45 1.92-4.95 0-9.14-3.34-10.64-7.86h-7v5.3C9.66 39.59 16.28 44 24 44z"/>
                            <path fill="#FBBC05" d="M13.36 28.06c-.38-1.18-.61-2.44-.61-3.73s.23-2.55.61-3.73v-5.3h-7A19.99 19.99 0 004 24.33c0 3.22.77 6.27 2.36 9.03l7-5.3z"/>
                            <path fill="#EA4335" d="M24 12.8c2.94 0 5.57 1.02 7.64 3.03l5.73-5.73C33.92 6.86 29.4 5 24 5 16.28 5 9.66 9.41 6.36 15.3l7 5.3c1.5-4.51 5.69-7.8 10.64-7.8z"/>
                        </svg>
                        <span><?= e(tr('Se connecter avec Google', 'تسجيل الدخول بواسطة Google')) ?></span>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
    <script src="assets/frontoffice/js/validation.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
