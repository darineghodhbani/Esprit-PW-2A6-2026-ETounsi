<?php
$parcel = $data['parcel'];
$editing = $data['editing'];
$statuses = $data['statuses'];
$offices = $data['offices'];
$citizens = $data['citizens'];
$errors = $data['errors'];
?>
<a class="poste-back" href="?espace=admin&amp;page=colis"><i class="bi bi-arrow-left" aria-hidden="true"></i> Gestion des colis</a>
<div class="page-header poste-page-header">
    <h1 class="page-title"><?= e($title) ?></h1>
</div>
<section class="poste-form-card" aria-label="Informations du colis">
    <?php if ($editing && $statuses === [] && isset($errors['statut'])): ?>
        <p class="alert alert-danger" role="alert"><?= e($errors['statut']) ?></p>
    <?php else: ?>
        <?php require __DIR__ . '/../partials/form-errors.php'; ?>
    <?php endif; ?>
    <?php if ($editing): ?>
        <dl class="poste-details">
            <div><dt>Nº de suivi</dt><dd><?= e($parcel['numero_suivi']) ?></dd></div>
            <div><dt>Bureau de départ</dt><dd><?= e($parcel['bureau']) ?></dd></div>
            <div><dt>État actuel</dt><dd><?= e($parcel['statut']) ?></dd></div>
        </dl>
        <?php if ($statuses === []): ?>
            <p class="alert alert-info">Ce colis est <?= $parcel['statut'] === 'Livré' ? 'livré' : 'annulé' ?>. Son état ne peut plus être modifié.</p>
        <?php else: ?>
            <p class="poste-form-intro">Le traitement suit les étapes : En attente → Accepté → En transit → Livré.</p>
        <?php endif; ?>
    <?php elseif ($offices === []): ?>
        <p class="alert alert-warning">Ajoutez un bureau de poste avant d’enregistrer un colis.</p>
        <a class="poste-btn poste-btn-primary" href="?espace=admin&amp;page=bureau-form">Ajouter un bureau</a>
    <?php elseif ($citizens === []): ?>
        <p class="alert alert-warning">Aucun compte citoyen disponible pour enregistrer un colis.</p>
    <?php else: ?>
        <p class="poste-form-intro">Le colis recevra un numéro de suivi et le statut « En attente ».</p>
    <?php endif; ?>
    <?php if (($editing && $statuses !== []) || (!$editing && $offices !== [] && $citizens !== [])): ?>
        <form action="?espace=admin&amp;page=colis-save" method="post" novalidate data-poste-save>
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?= e($parcel['id_colis']) ?>">
                <input type="hidden" name="statut_actuel" value="<?= e($parcel['statut']) ?>">
                <div class="poste-field">
                    <label for="statut">Prochain état du colis</label>
                    <select class="form-select form-control-custom<?= isset($errors['statut']) ? ' is-invalid' : '' ?>"
                            id="statut" name="statut" required
                            <?php if (isset($errors['statut'])): ?>aria-invalid="true" aria-describedby="erreur-statut"<?php endif; ?>>
                        <option value="">Choisir la prochaine étape</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= e($status) ?>"><?= e($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['statut'])): ?><p class="invalid-feedback" id="erreur-statut"><?= e($errors['statut']) ?></p><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="poste-field">
                    <label for="id_bureau_depart">Bureau de départ</label>
                    <select class="form-select form-control-custom<?= isset($errors['id_bureau_depart']) ? ' is-invalid' : '' ?>"
                            id="id_bureau_depart" name="id_bureau_depart" required
                            <?php if (isset($errors['id_bureau_depart'])): ?>aria-invalid="true" aria-describedby="erreur-bureau"<?php endif; ?>>
                        <option value="">Choisir un bureau</option>
                        <?php foreach ($offices as $office): ?>
                            <option value="<?= e($office['id_bureau']) ?>" <?= (string) $parcel['id_bureau_depart'] === (string) $office['id_bureau'] ? 'selected' : '' ?>>
                                <?= e($office['nom']) ?> · <?= e($office['ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_bureau_depart'])): ?><p class="invalid-feedback" id="erreur-bureau"><?= e($errors['id_bureau_depart']) ?></p><?php endif; ?>
                </div>
                <div class="poste-field">
                    <label for="id_citoyen">Compte citoyen</label>
                    <select class="form-select form-control-custom<?= isset($errors['id_citoyen']) ? ' is-invalid' : '' ?>"
                            id="id_citoyen" name="id_citoyen" required
                            <?php if (isset($errors['id_citoyen'])): ?>aria-invalid="true" aria-describedby="erreur-citoyen"<?php endif; ?>>
                        <option value="">Choisir un compte</option>
                        <?php foreach ($citizens as $citizen): ?>
                            <option value="<?= e($citizen['id_citoyen']) ?>" <?= (string) $parcel['id_citoyen'] === (string) $citizen['id_citoyen'] ? 'selected' : '' ?>>
                                <?= e($citizen['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_citoyen'])): ?><p class="invalid-feedback" id="erreur-citoyen"><?= e($errors['id_citoyen']) ?></p><?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="poste-table-actions">
                <button class="poste-btn poste-btn-primary" type="submit"><?= $editing ? 'Enregistrer le statut' : 'Enregistrer le colis' ?></button>
                <a class="poste-btn poste-btn-quiet" href="?espace=admin&amp;page=colis">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</section>
