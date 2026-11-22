<?php
$intro = $modification
    ? tr('Modifiez votre nom, la date et le bureau de départ avant l’acceptation de votre envoi.', 'عدّل اسمك والتاريخ ومكتب الإرسال قبل قبول إرساليّتك.')
    : tr('Saisissez votre nom et la date, puis choisissez le bureau de départ. Seul le code de suivi est généré automatiquement.', 'أدخل اسمك والتاريخ ثم اختر مكتب الإرسال. يُولّد رقم التتبّع وحده آليًّا.');
$erreur = implode(' ', $erreurs);
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="section-heading poste-section-heading">
        <p class="section-label"><?php echo e($modification ? tr('Modification', 'تعديل') : tr('Nouvel envoi', 'إرسالية جديدة')); ?></p>
        <h2><?php echo e($modification ? tr('Modifier mon colis', 'تعديل طردي') : tr('Déclarer un colis', 'التصريح بطرد')); ?></h2>
    </div>
    <div class="content-panel panel-narrow">
        <?php if ($erreur !== ''): ?>
            <p class="alert alert-danger" role="alert" id="erreur-envoi"><?php echo e($erreur); ?></p>
        <?php endif; ?>
        <?php if (empty($bureaux)): ?>
            <p class="mb-0"><?php echo e(tr('Aucun bureau n’est disponible pour déclarer un envoi.', 'لا يوجد مكتب متاح للتصريح بالإرسال.')); ?></p>
        <?php else: ?>
            <form method="post" action="<?php echo e($modification ? frontUrl('modifier-colis', ['id' => $old['id_colis']]) : frontUrl('envoi')); ?>" novalidate>
                <input type="hidden" name="csrf" value="<?php echo e($jeton); ?>">
                <div class="poste-form-grid">
                    <div>
                        <label class="form-label" for="nom_citoyen"><?php echo e(tr('Nom et prénom du citoyen', 'اسم المواطن ولقبه')); ?></label>
                        <input type="text" id="nom_citoyen" name="nom_citoyen" value="<?php echo e($old['nom_citoyen']); ?>" required minlength="2" maxlength="200" autocomplete="name" aria-describedby="aide-nom<?php echo isset($erreurs['nom_citoyen']) ? ' erreur-envoi' : ''; ?>"
                            <?php if (isset($erreurs['nom_citoyen'])): ?>aria-invalid="true"<?php endif; ?>>
                        <small class="form-text" id="aide-nom"><?php echo e(tr('Indiquez votre nom et votre prénom pour cet envoi.', 'أدخل اسمك ولقبك لهذه الإرسالية.')); ?></small>
                    </div>
                    <div>
                        <label class="form-label" for="id_bureau_depart"><?php echo e(tr('Bureau de départ', 'مكتب الإرسال')); ?></label>
                        <select class="form-select" id="id_bureau_depart" name="id_bureau_depart" required
                            <?php if (isset($erreurs['id_bureau_depart'])): ?>aria-invalid="true" aria-describedby="erreur-envoi"<?php endif; ?>>
                            <option value=""><?php echo e(tr('Sélectionnez un bureau', 'اختر مكتبًا')); ?></option>
                            <?php foreach ($bureaux as $bureau): ?>
                                <option value="<?php echo e($bureau['id_bureau']); ?>" <?php echo (string) $bureau['id_bureau'] === (string) $old['id_bureau_depart'] ? 'selected' : ''; ?>><?php echo e($bureau['nom']); ?> · <?php echo e($bureau['ville']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="date_declaration"><?php echo e(tr('Date de déclaration', 'تاريخ التصريح')); ?></label>
                        <input type="date" id="date_declaration" name="date_declaration" value="<?php echo e($old['date_declaration']); ?>" required min="1000-01-01" max="9999-12-31" aria-describedby="aide-date<?php echo isset($erreurs['date_declaration']) ? ' erreur-envoi' : ''; ?>"
                            <?php if (isset($erreurs['date_declaration'])): ?>aria-invalid="true"<?php endif; ?>>
                        <small class="form-text" id="aide-date"><?php echo e(tr('Choisissez la date de votre déclaration.', 'اختر تاريخ تصريحك.')); ?></small>
                    </div>
                    <div>
                        <label class="form-label" for="numero_suivi"><?php echo e(tr('Code de suivi unique', 'رقم التتبّع الفريد')); ?></label>
                        <input type="text" id="numero_suivi" dir="ltr" value="<?php echo e($modification ? $old['numero_suivi'] : ''); ?>"
                            placeholder="<?php echo e(tr('Généré à l’enregistrement', 'يُسند عند التسجيل')); ?>" readonly aria-describedby="aide-suivi">
                        <small class="form-text" id="aide-suivi"><?php echo e($modification
                            ? tr('Ce code identifie votre colis et reste inchangé.', 'يُعرّف هذا الرقم طردك ولا يتغيّر.')
                            : tr('Votre code sera affiché après l’enregistrement de l’envoi.', 'سيظهر رقمك بعد تسجيل الإرسالية.')); ?></small>
                    </div>
                </div>
                <button class="button button-dark w-100" type="submit"><?php echo e($modification ? tr('Enregistrer les modifications', 'حفظ التعديلات') : tr('Déclarer l’envoi', 'صرّح بالإرسال')); ?></button>
                <?php if ($modification): ?><a class="button button-light" href="<?php echo e(frontUrl('historique')); ?>"><?php echo e(tr('Annuler', 'رجوع')); ?></a><?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</div>
