<?php
$intro = tr('Créez votre compte citoyen pour gérer vos envois.', 'أنشئ حساب المواطن لإدارة إرسالياتك.');
require __DIR__ . '/page-heading.php';
?>
<div class="container py-5">
    <div class="content-panel panel-narrow compte-panel">
        <?php require __DIR__ . '/compte-erreurs.php'; ?>
        <form method="post" action="<?= e(frontUrl('inscription')) ?>" novalidate>
            <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
            <?php
            $champsCompte = [
                'nom' => [tr('Nom complet', 'الاسم الكامل'), 'text', 'name', true],
                'email' => [tr('Adresse email', 'البريد الإلكتروني'), 'email', 'email', true],
                'password' => [tr('Mot de passe', 'كلمة المرور'), 'password', 'new-password', true],
                'confirmation' => [tr('Confirmer le mot de passe', 'تأكيد كلمة المرور'), 'password', 'new-password', true],
            ];
            require __DIR__ . '/compte-champs.php';
            ?>
            <button class="btn btn-primary w-100" type="submit"><?= e(tr('Créer mon compte', 'إنشاء حسابي')) ?></button>
        </form>
        <p class="compte-lien"><?= e(tr('Vous avez déjà un compte ?', 'لديك حساب بالفعل؟')) ?> <a href="<?= e(frontUrl('connexion')) ?>"><?= e(tr('Se connecter', 'تسجيل الدخول')) ?></a></p>
    </div>
</div>
