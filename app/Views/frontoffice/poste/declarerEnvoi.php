<?php
$intro = $modification
    ? tr('Modifiez le bureau de départ avant l’acceptation de votre envoi.', 'عدّل مكتب الإرسال قبل قبول إرساليّتك.')
    : tr('Choisissez votre bureau de départ. Votre nom est repris de votre compte ; la date et le numéro de suivi sont attribués automatiquement.', 'اختر مكتب الإرسال. يُستخرج اسمك من حسابك ويُسجّل تاريخ التصريح ورقم التتبّع آليًّا.');
$nomCitoyen = trim(($utilisateur['prenom'] ?? '') . ' ' . $utilisateur['nom']);
$dateDeclaration = $modification ? substr($colis['created_at'], 0, 10) : date('Y-m-d');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="section-heading poste-section-heading">
        <p class="section-label"><?= e($modification ? tr('Modification', 'تعديل') : tr('Nouvel envoi', 'إرسالية جديدة')) ?></p>
        <h2><?= e($modification ? tr('Modifier mon colis', 'تعديل طردي') : tr('Déclarer un colis', 'التصريح بطرد')) ?></h2>
    </div>
    <div class="content-panel panel-narrow">
        <?php if ($erreur !== ''): ?>
            <p class="alert alert-danger" role="alert" id="erreur-envoi"><?= e($erreur) ?></p>
        <?php endif; ?>
        <?php if (empty($bureaux)): ?>
            <p class="mb-0"><?= e(tr('Aucun bureau n’est disponible pour déclarer un envoi.', 'لا يوجد مكتب متاح للتصريح بالإرسال.')) ?></p>
        <?php else: ?>
            <form method="post" action="<?= e($modification ? frontUrl('modifier-colis', ['id' => $colis['id_colis']]) : frontUrl('envoi')) ?>" novalidate>
                <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
                <div class="poste-form-grid">
                    <div>
                        <label class="form-label" for="nom_citoyen"><?= e(tr('Nom et prénom du citoyen', 'اسم المواطن ولقبه')) ?></label>
                        <input type="text" id="nom_citoyen" value="<?= e($nomCitoyen) ?>" readonly aria-describedby="aide-nom">
                        <small class="form-text" id="aide-nom"><?= e(tr('Informations de votre compte connecté.', 'معلومات حسابك المتّصل.')) ?></small>
                    </div>
                    <div>
                        <label class="form-label" for="id_bureau_depart"><?= e(tr('Bureau de départ', 'مكتب الإرسال')) ?></label>
                        <select class="form-select" id="id_bureau_depart" name="id_bureau_depart" required
                            <?php if ($erreur !== ''): ?>aria-invalid="true" aria-describedby="erreur-envoi" autofocus<?php endif; ?>>
                            <option value=""><?= e(tr('Sélectionnez un bureau', 'اختر مكتبًا')) ?></option>
                            <?php foreach ($bureaux as $bureau): ?>
                                <option value="<?= e($bureau['id_bureau']) ?>" <?= (string) $bureau['id_bureau'] === $bureauSelectionne ? 'selected' : '' ?>><?= e($bureau['nom']) ?> · <?= e($bureau['ville']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="date_declaration"><?= e(tr('Date de déclaration', 'تاريخ التصريح')) ?></label>
                        <input type="date" id="date_declaration" value="<?= e($dateDeclaration) ?>" readonly aria-describedby="aide-date">
                        <small class="form-text" id="aide-date"><?= e(tr('Date enregistrée automatiquement lors de la déclaration.', 'يُسجّل التاريخ آليًّا عند التصريح.')) ?></small>
                    </div>
                    <div>
                        <label class="form-label" for="numero_suivi"><?= e(tr('Code de suivi unique', 'رقم التتبّع الفريد')) ?></label>
                        <input type="text" id="numero_suivi" dir="ltr" value="<?= e($modification ? $colis['numero_suivi'] : '') ?>"
                            placeholder="<?= e(tr('Généré à l’enregistrement', 'يُسند عند التسجيل')) ?>" readonly aria-describedby="aide-suivi">
                        <small class="form-text" id="aide-suivi"><?= e($modification
                            ? tr('Ce code identifie votre colis et reste inchangé.', 'يُعرّف هذا الرقم طردك ولا يتغيّر.')
                            : tr('Votre code sera affiché après l’enregistrement de l’envoi.', 'سيظهر رقمك بعد تسجيل الإرسالية.')) ?></small>
                    </div>
                </div>
                <button class="button button-dark w-100" type="submit"><?= e($modification ? tr('Enregistrer les modifications', 'حفظ التعديلات') : tr('Déclarer l’envoi', 'صرّح بالإرسال')) ?></button>
                <?php if ($modification): ?><a class="button button-light" href="<?= e(frontUrl('historique')) ?>"><?= e(tr('Annuler', 'رجوع')) ?></a><?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</div>
