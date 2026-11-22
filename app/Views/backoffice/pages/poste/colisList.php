<div class="page-header poste-page-header">
    <div>
        <h1 class="page-title"><?php echo e($title); ?></h1>
        <p class="page-subtitle"><?php echo ($recherche !== '' || $filtreStatut !== '') ? 'Résultats selon la référence, le bureau, la ville et le statut choisis.' : 'Acceptez les déclarations en attente, puis suivez leur acheminement jusqu’à la livraison.'; ?></p>
    </div>
    <a class="btn-custom btn-custom-primary" href="?espace=admin&amp;page=addColis"><i class="bi bi-plus-lg" aria-hidden="true"></i> Enregistrer un colis</a>
</div>

<section class="table-card-custom" aria-label="Liste des colis">
    <form method="get" class="table-header-control poste-filter" action="" novalidate>
        <input type="hidden" name="espace" value="admin">
        <input type="hidden" name="page" value="searchColis">
        <div class="poste-filter-field">
            <label for="q">Rechercher un colis</label>
            <input type="search" class="form-control-custom" id="q" name="q" value="<?php echo e($recherche); ?>" placeholder="Nº de suivi, nom, bureau ou ville">
        </div>
        <div class="poste-filter-field">
            <label for="filtre-statut">Statut</label>
            <select class="form-select form-control-custom" id="filtre-statut" name="statut">
                <option value="">Tous les statuts</option>
                <?php foreach ($statuts as $statut): ?>
                    <option value="<?php echo e($statut); ?>" <?php echo $filtreStatut === $statut ? 'selected' : ''; ?>><?php echo e($statut); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn-custom btn-custom-light" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Rechercher</button>
        <?php if ($recherche !== '' || $filtreStatut !== ''): ?><a href="?espace=admin&amp;page=colis" class="btn-custom btn-custom-light">Effacer</a><?php endif; ?>
    </form>
    <?php if ($liste === []): ?>
        <p class="poste-empty"><?php echo $recherche === '' && $filtreStatut === '' ? 'Aucun colis enregistré.' : 'Aucun colis trouvé. Modifiez votre recherche ou votre filtre.'; ?></p>
    <?php else: ?>
        <?php require __DIR__ . '/../../partials/poste-colis.php'; ?>
    <?php endif; ?>
</section>
