<?php
$intro = tr('Choisissez le bureau de départ pour déclarer votre envoi.', 'اختر مكتب الإرسال للتصريح بإرساليتك.');
require __DIR__ . '/page-heading.php';
?>
<div class="container py-5">
    <div class="content-panel panel-narrow">
        <?php if ($erreur !== ''): ?>
            <p class="alert alert-danger" role="alert" id="erreur-envoi"><?= e($erreur) ?></p>
        <?php endif; ?>
        <?php if (empty($bureaux)): ?>
            <p class="mb-0"><?= e(tr('Aucun bureau n’est disponible pour déclarer un envoi.', 'لا يوجد مكتب متاح للتصريح بالإرسال.')) ?></p>
        <?php else: ?>
            <form method="post" action="<?= e(frontUrl('envoi')) ?>" novalidate>
                <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
                <label class="form-label" for="id_bureau_depart"><?= e(tr('Bureau de départ', 'مكتب الإرسال')) ?></label>
                <select class="form-select mb-4" id="id_bureau_depart" name="id_bureau_depart" required
                    <?php if ($erreur !== ''): ?>aria-invalid="true" aria-describedby="erreur-envoi" autofocus<?php endif; ?>>
                    <option value=""><?= e(tr('Sélectionnez un bureau', 'اختر مكتبًا')) ?></option>
                    <?php foreach ($bureaux as $bureau): ?>
                        <option value="<?= e($bureau['id_bureau']) ?>"><?= e($bureau['nom']) ?> · <?= e($bureau['ville']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary w-100" type="submit"><?= e(tr('Déclarer l’envoi', 'صرّح بالإرسال')) ?></button>
            </form>
        <?php endif; ?>
    </div>
</div>
