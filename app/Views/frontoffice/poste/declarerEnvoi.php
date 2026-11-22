<?php
$intro = $modification
    ? t('poste.modifier_envoi_introduction')
    : t('poste.declarer_envoi_introduction');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
  <div class="profile-card surface">
    <?php if (count($bureaux) == 0): ?>
      <p><?php echo e(t('poste.aucun_bureau_pour_envoi')); ?></p>
    <?php else: ?>
      <form method="post" action="<?php echo e($modification ? frontUrl('modifier-colis', ['id' => $old['id_colis']]) : frontUrl('envoi')); ?>" class="stack" novalidate data-validate-form>
        <input type="hidden" name="csrf" value="<?php echo e(csrfToken()); ?>">
        <div class="field-row">
          <div class="field">
            <label for="nom_citoyen"><?php echo e(t('poste.nom_citoyen')); ?></label>
            <input type="text" id="nom_citoyen" name="nom_citoyen" class="<?php if (isset($erreurs['nom_citoyen'])) echo 'is-invalid'; ?>" value="<?php echo e($old['nom_citoyen']); ?>" data-validate="required|min:2|max:200" required minlength="2" maxlength="200" autocomplete="name"
                   <?php if (isset($erreurs['nom_citoyen'])): ?>aria-invalid="true" aria-describedby="erreur-nom-citoyen"<?php endif; ?>>
            <?php if (isset($erreurs['nom_citoyen'])): ?>
              <p class="field-error server-error" id="erreur-nom-citoyen" role="alert"><?php echo e($erreurs['nom_citoyen']); ?></p>
            <?php endif; ?>
          </div>
          <div class="field">
            <label for="id_bureau_depart"><?php echo e(t('poste.bureau_depart')); ?></label>
            <select id="id_bureau_depart" name="id_bureau_depart" class="<?php if (isset($erreurs['id_bureau_depart'])) echo 'is-invalid'; ?>" data-validate="required" required
                    <?php if (isset($erreurs['id_bureau_depart'])): ?>aria-invalid="true" aria-describedby="erreur-bureau"<?php endif; ?>>
              <option value=""><?php echo e(t('poste.selectionner_bureau')); ?></option>
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
            <label for="date_declaration"><?php echo e(t('poste.date_declaration')); ?></label>
            <input type="date" id="date_declaration" name="date_declaration" class="<?php if (isset($erreurs['date_declaration'])) echo 'is-invalid'; ?>" value="<?php echo e($old['date_declaration']); ?>" data-validate="required|date" required min="1000-01-01" max="9999-12-31"
                   <?php if (isset($erreurs['date_declaration'])): ?>aria-invalid="true" aria-describedby="erreur-date"<?php endif; ?>>
            <?php if (isset($erreurs['date_declaration'])): ?>
              <p class="field-error server-error" id="erreur-date" role="alert"><?php echo e($erreurs['date_declaration']); ?></p>
            <?php endif; ?>
          </div>
          <div class="field">
            <label for="numero_suivi"><?php echo e(t('poste.code_suivi_unique')); ?></label>
            <input type="text" id="numero_suivi" dir="ltr" value="<?php echo e($modification ? $old['numero_suivi'] : ''); ?>" placeholder="<?php echo e(t('poste.code_genere_enregistrement')); ?>" readonly aria-describedby="aide-suivi">
            <p class="field-help" id="aide-suivi"><?php echo e(t('poste.aide_code_suivi')); ?></p>
          </div>
        </div>
        <div class="poste-actions">
          <button class="button button-brand" type="submit"><?php echo e($modification ? t('poste.enregistrer_modifications') : t('poste.declarer_envoi_bouton')); ?></button>
          <?php if ($modification): ?><a class="button button-light" href="<?php echo e(frontUrl('mes-colis')); ?>"><?php echo e(t('poste.annuler')); ?></a><?php endif; ?>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>
