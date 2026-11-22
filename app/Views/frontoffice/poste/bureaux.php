<?php
$intro = t('poste.bureaux_introduction');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="section-heading poste-section-heading">
        <p class="section-label"><?= e(t('poste.annuaire')) ?></p>
        <h2><?= e(t('poste.trouver_bureau')) ?></h2>
    </div>
    <form method="get" action="./" class="filter-bar mb-4" novalidate>
        <input type="hidden" name="page" value="bureaux">
        <input type="hidden" name="lang" value="<?= e($language) ?>">
        <div>
            <label for="ville" class="form-label"><?= e(t('poste.ville')) ?></label>
            <select class="form-select" id="ville" name="ville">
                <option value=""><?= e(t('poste.toutes_les_villes')) ?></option>
                <?php foreach ($annuaire['villes'] as $ville): ?>
                    <option value="<?= e($ville) ?>" <?php if ($ville === $annuaire['ville']) echo 'selected'; ?>>
                        <?= e($ville) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button button-dark" type="submit"><?= e(t('poste.filtrer')) ?></button>
        <?php if ($annuaire['ville'] !== ''): ?>
            <a class="button button-light" href="<?= e(frontUrl('bureaux')) ?>"><?= e(t('poste.toutes_les_villes')) ?></a>
        <?php endif; ?>
    </form>
    <?php if (empty($annuaire['bureaux'])): ?>
        <p class="content-panel"><?= e(t('poste.aucun_bureau')) ?></p>
    <?php endif; ?>
    <div class="grille-bureaux">
        <?php foreach ($annuaire['bureaux'] as $bureau): ?>
            <article class="service-item office-card">
                <i class="bi bi-geo-alt-fill service-icon" aria-hidden="true"></i>
                <h2><bdi><?= e($bureau['nom']) ?></bdi></h2>
                <p><bdi><?= e($bureau['adresse']) ?></bdi></p>
                <strong><bdi><?= e($bureau['ville']) ?></bdi></strong>
            </article>
        <?php endforeach; ?>
    </div>
</div>
