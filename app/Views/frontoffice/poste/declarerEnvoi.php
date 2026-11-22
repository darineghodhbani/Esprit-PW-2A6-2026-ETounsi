<?php
$intro = $modification
    ? tr('Modifiez le bureau de départ avant l’acceptation de votre envoi.', 'عدّل مكتب الإرسال قبل قبول إرساليّتك.')
    : tr('Choisissez le bureau de départ. Votre déclaration recevra une référence et sera vérifiée par l’administration.', 'اختر مكتب الإرسال. ستحصل على رقم تتبّع وستراجع الإدارة تصريحك.');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="section-heading poste-section-heading">
        <p class="section-label"><?= e($modification ? tr('Modification', 'تعديل') : tr('Nouvel envoi', 'إرسالية جديدة')) ?></p>
        <h2><?= e($modification ? tr('Modifier mon colis', 'تعديل طردي') : tr('Déclarer un colis', 'التصريح بطرد')) ?></h2>
    </div>
    <div class="content-panel panel-narrow">
        <?php if ($modification): ?>
            <p><?= e(tr('Référence : ', 'رقم التتبّع: ')) ?><bdi><?= e($colis['numero_suivi']) ?></bdi></p>
        <?php endif; ?>
        <?php if ($erreur !== ''): ?>
            <p class="alert alert-danger" role="alert" id="erreur-envoi"><?= e($erreur) ?></p>
        <?php endif; ?>
        <?php if (empty($bureaux)): ?>
            <p class="mb-0"><?= e(tr('Aucun bureau n’est disponible pour déclarer un envoi.', 'لا يوجد مكتب متاح للتصريح بالإرسال.')) ?></p>
        <?php else: ?>
            <form method="post" action="<?= e($modification ? frontUrl('modifier-colis', ['id' => $colis['id_colis']]) : frontUrl('envoi')) ?>" novalidate>
                <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
                <label class="form-label" for="id_bureau_depart"><?= e(tr('Bureau de départ', 'مكتب الإرسال')) ?></label>
                <select class="form-select mb-4" id="id_bureau_depart" name="id_bureau_depart" required
                    <?php if ($erreur !== ''): ?>aria-invalid="true" aria-describedby="erreur-envoi" autofocus<?php endif; ?>>
                    <option value=""><?= e(tr('Sélectionnez un bureau', 'اختر مكتبًا')) ?></option>
                    <?php foreach ($bureaux as $bureau): ?>
                        <option value="<?= e($bureau['id_bureau']) ?>" <?= (string) $bureau['id_bureau'] === $bureauSelectionne ? 'selected' : '' ?>><?= e($bureau['nom']) ?> · <?= e($bureau['ville']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="button button-dark w-100" type="submit"><?= e($modification ? tr('Enregistrer les modifications', 'حفظ التعديلات') : tr('Déclarer l’envoi', 'صرّح بالإرسال')) ?></button>
                <?php if ($modification): ?><a class="button button-light" href="<?= e(frontUrl('historique')) ?>"><?= e(tr('Annuler', 'رجوع')) ?></a><?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</div>
