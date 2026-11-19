<?php
$parcels = $data['parcels'];
$query = $data['query'];
$statusFilter = $data['statusFilter'];
$statuses = $data['statuses'];
?>
<div class="page-header poste-page-header">
    <div>
        <h1 class="page-title"><?= e($title) ?></h1>
        <p class="page-subtitle">Retrouvez les envois, consultez leur statut et mettez-le à jour.</p>
    </div>
    <a class="poste-btn poste-btn-primary" href="?espace=admin&amp;page=colis-form"><i class="bi bi-plus-lg" aria-hidden="true"></i> Enregistrer un colis</a>
</div>

<section class="table-card-custom" aria-label="Liste des colis">
    <form method="get" class="table-header-control poste-filter" action="">
        <input type="hidden" name="espace" value="admin">
        <input type="hidden" name="page" value="colis">
        <div class="poste-filter-field">
            <label for="q">Rechercher un colis</label>
            <input type="search" class="form-control-custom" id="q" name="q" value="<?= e($query) ?>" placeholder="Nº de suivi, bureau ou ville">
        </div>
        <div class="poste-filter-field">
            <label for="filtre-statut">Statut</label>
            <select class="form-select form-control-custom" id="filtre-statut" name="statut">
                <option value="">Tous les statuts</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="poste-btn poste-btn-outline" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Rechercher</button>
        <?php if ($query !== '' || $statusFilter !== ''): ?><a href="?espace=admin&amp;page=colis" class="poste-btn poste-btn-quiet">Effacer</a><?php endif; ?>
    </form>
    <?php if ($parcels === []): ?>
        <p class="poste-empty"><?= $query === '' && $statusFilter === '' ? 'Aucun colis enregistré.' : 'Aucun colis trouvé. Modifiez votre recherche ou votre filtre.' ?></p>
    <?php else: ?>
        <?php require __DIR__ . '/../partials/parcel-table.php'; ?>
    <?php endif; ?>
</section>
