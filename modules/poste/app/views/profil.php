<?php
$intro = tr('Modifiez vos coordonnées et votre mot de passe.', 'عدّل بياناتك وكلمة مرورك.');
require __DIR__ . '/page-heading.php';
?>
<div class="container py-5">
    <div class="content-panel panel-narrow compte-panel">
        <?php require __DIR__ . '/compte-erreurs.php'; ?>
        <form method="post" action="<?= e(frontUrl('profil')) ?>" novalidate>
            <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
            <?php
            $champsCompte = [
                'nom' => [tr('Nom complet', 'الاسم الكامل'), 'text', 'name', true],
                'email' => [tr('Adresse email', 'البريد الإلكتروني'), 'email', 'email', true],
                'mot_de_passe_actuel' => [tr('Mot de passe actuel', 'كلمة المرور الحالية'), 'password', 'current-password', false],
                'password' => [tr('Nouveau mot de passe', 'كلمة مرور جديدة'), 'password', 'new-password', false],
                'confirmation' => [tr('Confirmer le nouveau mot de passe', 'تأكيد كلمة المرور الجديدة'), 'password', 'new-password', false],
            ];
            require __DIR__ . '/compte-champs.php';
            ?>
            <button class="btn btn-primary w-100" type="submit"><?= e(tr('Enregistrer mon profil', 'حفظ ملفّي الشخصي')) ?></button>
        </form>
    </div>
</div>
