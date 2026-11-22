<!DOCTYPE html>
<html lang="<?php echo et_lang(); ?>" dir="<?php echo et_dir(); ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-signup">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">
        <section class="login-section container">
            <div class="login-story">
                <p class="eyebrow"><?php echo t('signup.eyebrow'); ?></p>
                <h1><?php echo t('signup.title'); ?></h1>
                <p><?php echo t('signup.text'); ?></p>
                <img src="assets/frontoffice/img/street/street-desktop.webp" width="1678" height="937" alt="">
            </div>

            <div class="login-panel">
                <form method="post" action="index.php?page=signup" class="stack" novalidate data-validate-form>
<input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

                    <div class="field-row">
                        <div class="field">
                            <label for="nom"><?php echo t('signup.nom'); ?> *</label>
                            <input id="nom" name="nom" type="text" value="<?php echo e($old['nom']); ?>"
                                   data-validate="required|min:2|max:50">
                            <?php if (isset($erreurs['nom'])): ?>
                                <p class="field-error server-error"><?php echo $erreurs['nom']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="field">
                            <label for="prenom"><?php echo t('signup.prenom'); ?> *</label>
                            <input id="prenom" name="prenom" type="text" value="<?php echo e($old['prenom']); ?>"
                                   data-validate="required|min:2|max:50">
                            <?php if (isset($erreurs['prenom'])): ?>
                                <p class="field-error server-error"><?php echo $erreurs['prenom']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="field">
                        <label for="email"><?php echo t('login.email'); ?> *</label>
                        <input id="email" name="email" type="email" value="<?php echo e($old['email']); ?>"
                               data-validate="required|email">
                        <?php if (isset($erreurs['email'])): ?>
                            <p class="field-error server-error"><?php echo $erreurs['email']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="telephone"><?php echo t('signup.telephone'); ?></label>
                        <input id="telephone" name="telephone" type="tel" value="<?php echo e($old['telephone']); ?>"
                               placeholder="22123456" data-validate="phone">
                        <?php if (isset($erreurs['telephone'])): ?>
                            <p class="field-error server-error"><?php echo $erreurs['telephone']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="mot_de_passe"><?php echo t('login.password'); ?> *</label>
                        <input id="mot_de_passe" name="mot_de_passe" type="password" data-validate="required|min:6">
                        <p class="field-help"><?php echo t('signup.help'); ?></p>
                        <?php if (isset($erreurs['mot_de_passe'])): ?>
                            <p class="field-error server-error"><?php echo $erreurs['mot_de_passe']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="mot_de_passe2"><?php echo t('signup.password2'); ?> *</label>
                        <input id="mot_de_passe2" name="mot_de_passe2" type="password" data-validate="required|match:mot_de_passe">
                        <?php if (isset($erreurs['mot_de_passe2'])): ?>
                            <p class="field-error server-error"><?php echo $erreurs['mot_de_passe2']; ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (CitoyenController::captchaEnabled() || isset($erreurs['captcha'])): ?>
                    <div class="field">
                        <?php if (CitoyenController::captchaEnabled()): ?><div class="g-recaptcha" data-sitekey="<?= e(CitoyenController::captchaSiteKey()) ?>"></div><?php endif; ?>
                        <?php if (isset($erreurs['captcha'])): ?><p class="field-error"><?= e($erreurs['captcha']) ?></p><?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <button class="button button-brand button-full" type="submit"><?php echo t('signup.submit'); ?></button>
                    <a class="button button-outline button-full" href="index.php?page=login"><?php echo t('signup.have'); ?></a>
                </form>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
    <script src="assets/frontoffice/js/validation.js"></script>
    <?php if (CitoyenController::captchaEnabled()): ?><script src="https://www.google.com/recaptcha/api.js" async defer></script><?php endif; ?>
</body>
</html>
