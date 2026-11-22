<?php
$intro = t('poste.suivi_introduction');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="content-panel panel-narrow">
        <form action="./" method="get" class="tracking-form" novalidate>
    <input type="hidden" name="page" value="suivi">
    <input type="hidden" name="lang" value="<?= e($language) ?>">
    <label for="numero" class="form-label"><?= e(t('poste.numero_suivi')) ?></label>
    <div class="tracking-controls">
        <input class="form-control" type="text" dir="ltr" name="numero" id="numero"
            maxlength="32" value="<?= e(substr($numeroSuivi, 0, 32)) ?>" placeholder="ET000001TN"
            required aria-describedby="tracking-sample" spellcheck="false" autocomplete="off" autocapitalize="characters">
        <button class="button button-dark" type="submit"><?= e(t('poste.rechercher')) ?></button>
    </div>
    <small class="form-text" id="tracking-sample">
        <?= e(t('poste.exemple_demonstration')) ?>
        <a href="<?= e(frontUrl('suivi', ['numero' => 'ET000001TN'])) ?>"><bdi>ET000001TN</bdi></a>
    </small>
</form>
        <?php if ($rechercheLancee): ?>
            <?php if (!$numeroValide): ?>
                <p class="alert alert-warning mt-4 mb-0" role="alert"><?= e(t('poste.erreur.numero_suivi')) ?></p>
            <?php elseif (empty($colis)): ?>
                <p class="alert alert-info mt-4 mb-0"><?= e(t('poste.erreur.suivi_introuvable')) ?></p>
            <?php else: ?>
                <article class="tracking-result" aria-labelledby="resultat">
                    <div class="result-heading">
                        <h2 id="resultat"><bdi><?= e($colis['numero_suivi']) ?></bdi></h2>
                        <span class="status-badge" data-statut="<?= e($colis['statut']) ?>"><?= e(parcelStatus($colis['statut'])) ?></span>
                    </div>
                    <dl>
                        <dt><?= e(t('poste.bureau_depart')) ?></dt>
                        <dd><bdi><?= e($colis['bureau']) ?> · <?= e($colis['ville']) ?></bdi></dd>
                        <dt><?= e(t('poste.date_declaration')) ?></dt>
                        <dd><bdi><?= e(date('d/m/Y', strtotime($colis['date_declaration']))) ?></bdi></dd>
                    </dl>
                </article>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
