<?php
$stats = $data['stats'];
$parcels = $data['parcels'];
$statusCounts = $data['statusCounts'];
?>
<div class="poste-dashboard-intro">
    <section class="poste-hero" aria-labelledby="tableau-de-bord">
        <h1 id="tableau-de-bord" class="page-title">Tableau de bord</h1>
        <p>Consultez les colis déclarés et gérez les bureaux de poste.</p>
        <a class="poste-btn poste-btn-primary" href="?espace=admin&amp;page=colis-form"><i class="bi bi-plus-lg" aria-hidden="true"></i> Enregistrer un colis</a>
    </section>
    <section class="poste-distribution" aria-labelledby="repartition">
        <h2 id="repartition">Statuts des colis</h2>
        <dl>
            <?php foreach ($statusCounts as $status => $count): ?>
                <div><dt><?php require __DIR__ . '/../partials/status.php'; ?></dt><dd><?= e($count) ?></dd></div>
            <?php endforeach; ?>
        </dl>
    </section>
</div>

<div class="poste-metrics" aria-label="Chiffres du module Poste">
    <?php foreach ($stats as $stat): ?>
        <div class="poste-metric">
            <span class="poste-metric-icon"><i class="bi bi-<?= e($stat['icon']) ?>" aria-hidden="true"></i></span>
            <div><strong><?= e($stat['value']) ?></strong><span><?= e($stat['label']) ?></span></div>
        </div>
    <?php endforeach; ?>
</div>

<section class="table-card-custom" aria-labelledby="derniers-colis">
    <div class="table-header-control poste-card-header">
        <h2 id="derniers-colis">Derniers colis</h2>
        <a class="poste-btn poste-btn-outline" href="?espace=admin&amp;page=colis">Voir tous les colis <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </div>
    <?php if ($parcels === []): ?>
        <p class="poste-empty">Aucun colis enregistré.</p>
    <?php else: ?>
        <?php require __DIR__ . '/../partials/parcel-table.php'; ?>
    <?php endif; ?>
</section>
