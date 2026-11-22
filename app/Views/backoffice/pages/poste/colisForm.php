<?php $modification = !empty($old['id_colis']); ?>
<a class="poste-back" href="?espace=admin&amp;page=colisList">Gestion des colis</a>
<div class="page-header poste-page-header"><h1 class="page-title"><?php echo e($title); ?></h1></div>
<section class="poste-form-card" aria-label="<?php echo $modification ? 'Informations du colis' : 'Nouveau colis'; ?>">
    <?php if ($modification): ?>

<?php if ($statuts === [] && isset($erreurs['statut'])): ?><p class="alert alert-danger" role="alert"><?php echo e($erreurs['statut']); ?></p><?php else: ?><?php require __DIR__ . '/../../partials/poste-errors.php'; ?><?php endif; ?>
    <dl class="poste-details">
        <div><dt>Nº de suivi</dt><dd><?php echo e($old['numero_suivi']); ?></dd></div>
        <div><dt>Bureau de départ</dt><dd><?php echo e($old['bureau']); ?></dd></div>
        <div><dt>Nom déclaré</dt><dd><?php echo e($old['nom_citoyen']); ?></dd></div>
        <div><dt>Date de déclaration</dt><dd><?php echo e(date('d/m/Y', strtotime($old['date_declaration']))); ?></dd></div>
        <div><dt>État actuel</dt><dd><?php echo e($old['statut']); ?></dd></div>
    </dl>
    <?php if ($statuts === []): ?>
        <p class="alert alert-info">Ce colis est <?php echo $old['statut'] === 'Livré' ? 'livré' : 'annulé'; ?>. Son état ne peut plus être modifié.</p>
    <?php else: ?>
        <p class="poste-form-intro">Le traitement suit les étapes : En attente → Accepté → En transit → Livré.</p>
        <form action="?espace=admin&amp;page=updateColis" method="post" novalidate data-poste-save>
            <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>"><input type="hidden" name="id" value="<?php echo e($old['id_colis']); ?>"><input type="hidden" name="statut_actuel" value="<?php echo e($old['statut']); ?>">
            <div class="poste-field">
                <label for="statut">Prochain état du colis</label>
                <select class="form-select form-control-custom<?php echo isset($erreurs['statut']) ? ' is-invalid' : ''; ?>" id="statut" name="statut" required
                    <?php if (isset($erreurs['statut'])): ?>aria-invalid="true" aria-describedby="erreur-statut"<?php endif; ?>>
                    <option value="">Choisir la prochaine étape</option>
                    <?php foreach ($statuts as $statut): ?><option value="<?php echo e($statut); ?>"><?php echo e($statut); ?></option><?php endforeach; ?>
                </select>
                <?php if (isset($erreurs['statut'])): ?><p class="invalid-feedback" id="erreur-statut"><?php echo e($erreurs['statut']); ?></p><?php endif; ?>
            </div>
            <div class="poste-table-actions"><button class="btn-custom btn-custom-primary" type="submit">Enregistrer le statut</button><a class="btn-custom btn-custom-light" href="?espace=admin&amp;page=colisList">Annuler</a></div>
        </form>
    <?php endif; ?>
    <?php else: ?>

<?php require __DIR__ . '/../../partials/poste-errors.php'; ?>
    <?php if ($bureaux === []): ?>
        <p class="alert alert-warning">Ajoutez un bureau de poste avant d’enregistrer un colis.</p>
        <a class="btn-custom btn-custom-primary" href="?espace=admin&amp;page=addBureau">Ajouter un bureau</a>
    <?php elseif ($citoyens === []): ?>
        <p class="alert alert-warning">Aucun compte citoyen disponible pour enregistrer un colis.</p>
    <?php else: ?>
        <p class="poste-form-intro">Le colis recevra un numéro de suivi et le statut « En attente ».</p>
        <form action="?espace=admin&amp;page=addColis" method="post" novalidate data-poste-save>
            <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
            <div class="poste-field">
                <label for="id_bureau_depart">Bureau de départ</label>
                <select class="form-select form-control-custom<?php echo isset($erreurs['id_bureau_depart']) ? ' is-invalid' : ''; ?>" id="id_bureau_depart" name="id_bureau_depart" required
                    <?php if (isset($erreurs['id_bureau_depart'])): ?>aria-invalid="true" aria-describedby="erreur-bureau"<?php endif; ?>>
                    <option value="">Choisir un bureau</option>
                    <?php foreach ($bureaux as $bureau): ?><option value="<?php echo e($bureau['id_bureau']); ?>" <?php echo (string) $old['id_bureau_depart'] === (string) $bureau['id_bureau'] ? 'selected' : ''; ?>><?php echo e($bureau['nom']); ?> · <?php echo e($bureau['ville']); ?></option><?php endforeach; ?>
                </select>
                <?php if (isset($erreurs['id_bureau_depart'])): ?><p class="invalid-feedback" id="erreur-bureau"><?php echo e($erreurs['id_bureau_depart']); ?></p><?php endif; ?>
            </div>
            <div class="poste-field">
                <label for="id_citoyen">Compte citoyen</label>
                <select class="form-select form-control-custom<?php echo isset($erreurs['id_citoyen']) ? ' is-invalid' : ''; ?>" id="id_citoyen" name="id_citoyen" required
                    <?php if (isset($erreurs['id_citoyen'])): ?>aria-invalid="true" aria-describedby="erreur-citoyen"<?php endif; ?>>
                    <option value="">Choisir un compte</option>
                    <?php foreach ($citoyens as $citoyen): ?><option value="<?php echo e($citoyen['id_citoyen']); ?>" <?php echo (string) $old['id_citoyen'] === (string) $citoyen['id_citoyen'] ? 'selected' : ''; ?>><?php echo e($citoyen['nom']); ?></option><?php endforeach; ?>
                </select>
                <?php if (isset($erreurs['id_citoyen'])): ?><p class="invalid-feedback" id="erreur-citoyen"><?php echo e($erreurs['id_citoyen']); ?></p><?php endif; ?>
            </div>
            <div class="poste-table-actions"><button class="btn-custom btn-custom-primary" type="submit">Enregistrer le colis</button><a class="btn-custom btn-custom-light" href="?espace=admin&amp;page=colisList">Annuler</a></div>
        </form>
    <?php endif; ?>
    <?php endif; ?>
</section>
