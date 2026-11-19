<?php
$intro = tr('Connectez-vous pour accéder à votre espace.', 'سجّل الدخول للوصول إلى فضائك.');
require __DIR__ . '/page-heading.php';
?>
<div class="container py-5">
    <div class="content-panel panel-narrow compte-panel">
        <?php require __DIR__ . '/compte-erreurs.php'; ?>
        <form method="post" action="<?= e(frontUrl('connexion')) ?>" novalidate>
            <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
            <?php
            $champsCompte = [
                'email' => [tr('Adresse email', 'البريد الإلكتروني'), 'email', 'email', true],
                'password' => [tr('Mot de passe', 'كلمة المرور'), 'password', 'current-password', true],
            ];
            require __DIR__ . '/compte-champs.php';
            ?>
            <button class="btn btn-primary w-100" type="submit"><?= e(tr('Se connecter', 'تسجيل الدخول')) ?></button>
        </form>
        <p class="compte-lien"><?= e(tr('Vous n’avez pas encore de compte ?', 'ليس لديك حساب بعد؟')) ?> <a href="<?= e(frontUrl('inscription')) ?>"><?= e(tr('Créer un compte', 'إنشاء حساب')) ?></a></p>
    </div>
</div>
