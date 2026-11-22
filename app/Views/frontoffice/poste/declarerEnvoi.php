<?php
$intro = $modification
    ? tr('Modifiez votre nom, la date et le bureau de départ avant l’acceptation de votre envoi.', 'عدّل اسمك والتاريخ ومكتب الإرسال قبل قبول إرساليّتك.')
    : tr('Saisissez votre nom et la date, puis choisissez le bureau de départ. Seul le code de suivi est généré automatiquement.', 'أدخل اسمك والتاريخ ثم اختر مكتب الإرسال. يُولّد رقم التتبّع وحده آليًّا.');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
  <div class="profile-card surface">
    <?php if (count($bureaux) == 0): ?>
      <p><?php echo e(tr('Aucun bureau n’est disponible pour déclarer un envoi.', 'لا يوجد مكتب متاح للتصريح بالإرسال.')); ?></p>
    <?php else: ?>
      <form method="post" action="<?php echo e($modification ? frontUrl('modifier-colis', ['id' => $old['id_colis']]) : frontUrl('envoi')); ?>" class="stack" novalidate data-validate-form>
        <input type="hidden" name="csrf" value="<?php echo e(csrfToken()); ?>">
        <div class="field-row">
          <div class="field">
            <label for="nom_citoyen"><?php echo e(tr('Nom et prénom du citoyen', 'اسم المواطن ولقبه')); ?></label>
            <input type="text" id="nom_citoyen" name="nom_citoyen" class="<?php if (isset($erreurs['nom_citoyen'])) echo 'is-invalid'; ?>" value="<?php echo e($old['nom_citoyen']); ?>" data-validate="required|min:2|max:200" required minlength="2" maxlength="200" autocomplete="name"
                   <?php if (isset($erreurs['nom_citoyen'])): ?>aria-invalid="true" aria-describedby="erreur-nom-citoyen"<?php endif; ?>>
            <?php if (isset($erreurs['nom_citoyen'])): ?>
              <p class="field-error server-error" id="erreur-nom-citoyen" role="alert"><?php echo e($erreurs['nom_citoyen']); ?></p>
            <?php endif; ?>
          </div>
          <div class="field">
            <label for="id_bureau_depart"><?php echo e(tr('Bureau de départ', 'مكتب الإرسال')); ?></label>
            <select id="id_bureau_depart" name="id_bureau_depart" class="<?php if (isset($erreurs['id_bureau_depart'])) echo 'is-invalid'; ?>" data-validate="required" required
                    <?php if (isset($erreurs['id_bureau_depart'])): ?>aria-invalid="true" aria-describedby="erreur-bureau"<?php endif; ?>>
              <option value=""><?php echo e(tr('Sélectionnez un bureau', 'اختر مكتبًا')); ?></option>
              <?php foreach ($bureaux as $b): ?>
                <option value="<?php echo e($b['id_bureau']); ?>" <?php if ((string) $old['id_bureau_depart'] === (string) $b['id_bureau']) echo 'selected'; ?>><?php echo e($b['nom'] . ' — ' . $b['ville']); ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($erreurs['id_bureau_depart'])): ?>
              <p class="field-error server-error" id="erreur-bureau" role="alert"><?php echo e($erreurs['id_bureau_depart']); ?></p>
            <?php endif; ?>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label for="date_declaration"><?php echo e(tr('Date de déclaration', 'تاريخ التصريح')); ?></label>
            <input type="date" id="date_declaration" name="date_declaration" class="<?php if (isset($erreurs['date_declaration'])) echo 'is-invalid'; ?>" value="<?php echo e($old['date_declaration']); ?>" data-validate="required|date" required min="1000-01-01" max="9999-12-31"
                   <?php if (isset($erreurs['date_declaration'])): ?>aria-invalid="true" aria-describedby="erreur-date"<?php endif; ?>>
            <?php if (isset($erreurs['date_declaration'])): ?>
              <p class="field-error server-error" id="erreur-date" role="alert"><?php echo e($erreurs['date_declaration']); ?></p>
            <?php endif; ?>
          </div>
          <div class="field">
            <label for="numero_suivi"><?php echo e(tr('Code de suivi unique', 'رقم التتبّع الفريد')); ?></label>
            <input type="text" id="numero_suivi" dir="ltr" value="<?php echo e($modification ? $old['numero_suivi'] : ''); ?>" placeholder="<?php echo e(tr('Généré à l’enregistrement', 'يُسند عند التسجيل')); ?>" readonly aria-describedby="aide-suivi">
            <p class="field-help" id="aide-suivi"><?php echo e(tr('Le code est attribué automatiquement et reste inchangé.', 'يُسند الرقم آليًّا ولا يتغيّر.')); ?></p>
          </div>
        </div>
        <div class="poste-actions">
          <button class="button button-brand" type="submit"><?php echo e($modification ? tr('Enregistrer les modifications', 'حفظ التعديلات') : tr('Déclarer l’envoi', 'صرّح بالإرسال')); ?></button>
          <?php if ($modification): ?><a class="button button-light" href="<?php echo e(frontUrl('mes-colis')); ?>"><?php echo e(tr('Annuler', 'رجوع')); ?></a><?php endif; ?>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>
