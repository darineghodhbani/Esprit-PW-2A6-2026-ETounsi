<?php
$etPrefix = $etPrefix ?? '';
$etLoginInvalid = isset($erreurs['general']);
$etEmailInvalid = isset($erreurs['email']) || $etLoginInvalid;
$etPasswordInvalid = isset($erreurs['password']) || $etLoginInvalid;
$etEmailError = $etLoginInvalid ? 'erreur-compte' : $etPrefix . 'erreur-email';
$etPasswordError = $etLoginInvalid ? 'erreur-compte' : $etPrefix . 'erreur-password';
?>
<form class="et-login-form" method="post" action="<?= etE(etPoste('connexion')) ?>" novalidate>
    <input type="hidden" name="csrf" value="<?= etE($jeton) ?>">
    <div class="compte-champ">
        <label class="form-label" for="<?= etE($etPrefix) ?>email"><?= etE(etT('Adresse email', 'البريد الإلكتروني')) ?></label>
        <input class="form-control<?= $etEmailInvalid ? ' is-invalid' : '' ?>"
            id="<?= etE($etPrefix) ?>email" name="email" type="email"
            autocomplete="email" maxlength="150" required dir="ltr"
            placeholder="nom@example.com" value="<?= etE($valeurs['email'] ?? '') ?>"
            <?php if ($etEmailInvalid): ?>
                aria-invalid="true" aria-describedby="<?= etE($etEmailError) ?>"
            <?php endif; ?>>
        <?php if (isset($erreurs['email'])): ?><p class="invalid-feedback d-block" id="<?= etE($etPrefix) ?>erreur-email"><?= etE($erreurs['email']) ?></p><?php endif; ?>
    </div>
    <div class="compte-champ">
        <label class="form-label" for="<?= etE($etPrefix) ?>password"><?= etE(etT('Mot de passe', 'كلمة المرور')) ?></label>
        <div class="password-field">
            <input class="form-control<?= $etPasswordInvalid ? ' is-invalid' : '' ?>"
                id="<?= etE($etPrefix) ?>password" name="password" type="password"
                autocomplete="current-password" maxlength="72" required
                <?php if ($etPasswordInvalid): ?>
                    aria-invalid="true" aria-describedby="<?= etE($etPasswordError) ?>"
                <?php endif; ?>>
            <button class="password-toggle" type="button"
                data-password="<?= etE($etPrefix) ?>password"
                aria-controls="<?= etE($etPrefix) ?>password" aria-pressed="false"
                data-show="<?= etE(etT('Afficher', 'إظهار')) ?>"
                data-hide="<?= etE(etT('Masquer', 'إخفاء')) ?>"
                aria-label="<?= etE(etT('Afficher le mot de passe', 'إظهار كلمة المرور')) ?>">
                <?= etE(etT('Afficher', 'إظهار')) ?>
            </button>
        </div>
        <?php if (isset($erreurs['password'])): ?><p class="invalid-feedback d-block" id="<?= etE($etPrefix) ?>erreur-password"><?= etE($erreurs['password']) ?></p><?php endif; ?>
    </div>
    <button class="et-button et-button-wide" type="submit"><?= etE(etT('Se connecter', 'تسجيل الدخول')) ?><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
</form>
<p class="et-account-link"><?= etE(etT('Pas encore de compte ?', 'ليس لديك حساب؟')) ?> <a href="<?= etE(etPoste('inscription')) ?>"><?= etE(etT('Créer un compte', 'إنشاء حساب')) ?></a></p>
