<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>

<body>
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>

    <?php
    $porteImage = 'finances-office.jpg';
    $porteTitre = 'Bienvenue — Recette des Finances';
    require_once __DIR__ . '/includes/portes.php';
    ?>

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="container-fluid bg-primary py-5 mb-5 page-header page-header-finances">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Recette des Finances</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php?page=home">Accueil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Recette des Finances</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Nouvelle propriété</h6>
                <h1 class="mb-5">Déclarer une propriété</h1>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.3s">
                    <form method="post" action="index.php?page=administratif" novalidate data-validate-form>
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                        <input type="hidden" name="form" value="propriete">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select<?php echo invalidClass($errors, 'type'); ?>" id="type" name="type" data-validate="required">
                                        <option value="">-- Choisir --</option>
                                        <?php foreach (ProprieteController::TYPES as $key => $label): ?>
                                            <option value="<?php echo e($key); ?>" <?php echo $old['type'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="type">Type de propriété</label>
                                </div>
                                <?php echo errorFor($errors, 'type'); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control<?php echo invalidClass($errors, 'reference'); ?>" id="reference" name="reference"
                                        placeholder="Référence" value="<?php echo e($old['reference']); ?>" data-validate="required|min:3|max:50">
                                    <label for="reference">Référence (titre foncier ou immatriculation)</label>
                                </div>
                                <?php echo errorFor($errors, 'reference'); ?>
                            </div>
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control<?php echo invalidClass($errors, 'adresse'); ?>" id="adresse" name="adresse"
                                        placeholder="Adresse" value="<?php echo e($old['adresse']); ?>" data-validate="required|min:5|max:150">
                                    <label for="adresse">Adresse</label>
                                </div>
                                <?php echo errorFor($errors, 'adresse'); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control<?php echo invalidClass($errors, 'valeur'); ?>" id="valeur" name="valeur"
                                        placeholder="Valeur" value="<?php echo e($old['valeur']); ?>" data-validate="required|number">
                                    <label for="valeur">Valeur (DT)</label>
                                </div>
                                <?php echo errorFor($errors, 'valeur'); ?>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Ajouter la propriété</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Patrimoine</h6>
                <h1 class="mb-5">Mes propriétés</h1>
            </div>
            <div class="table-responsive wow fadeInUp" data-wow-delay="0.3s">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Adresse</th>
                            <th>Valeur (DT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$mesProprietes): ?>
                            <tr><td colspan="4">Vous n'avez encore aucune propriété.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($mesProprietes as $p): ?>
                            <tr>
                                <td><?php echo e($p->getReference()); ?></td>
                                <td><?php echo e(ProprieteController::TYPES[$p->getType()] ?? $p->getType()); ?></td>
                                <td><?php echo e($p->getAdresse()); ?></td>
                                <td><?php echo $p->getValeur(); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Paiement</h6>
                <h1 class="mb-5">Mes impôts</h1>
            </div>
            <div class="table-responsive wow fadeInUp" data-wow-delay="0.3s">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Propriété</th>
                            <th>Année</th>
                            <th>Montant (DT)</th>
                            <th>Date limite</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$mesImpots): ?>
                            <tr><td colspan="6">Vous n'avez aucun impôt.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($mesImpots as $i): ?>
                            <tr>
                                <td><?php echo e($i->getReference()); ?></td>
                                <td><?php echo $i->getAnnee(); ?></td>
                                <td><?php echo $i->getMontant(); ?></td>
                                <td><?php echo e($i->getDateLimite()); ?></td>
                                <td>
                                    <?php if ($i->getStatut() === 'paye'): ?>
                                        <span class="badge bg-success">Payé</span>
                                    <?php elseif ($i->getStatut() === 'en_attente'): ?>
                                        <span class="badge bg-warning text-dark">En attente de validation</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Non payé</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($i->getStatut() === 'non_paye'): ?>
                                        <form method="post" action="index.php?page=administratif" data-confirm="Vous devez régler <?php echo $i->getMontant(); ?> DT au bureau de poste. Confirmer ?">
                                            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                                            <input type="hidden" name="form" value="payer">
                                            <input type="hidden" name="id" value="<?php echo $i->getId(); ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">Payer</button>
                                        </form>
                                    <?php elseif ($i->getStatut() === 'en_attente'): ?>
                                        À régler à la poste
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>

</html>
