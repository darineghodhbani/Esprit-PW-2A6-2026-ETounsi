<?php
$intro = tr('Vérifiez le statut d’un envoi grâce à son numéro de suivi.', 'تحقّق من حالة الإرسالية باستعمال رقم التتبّع.');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="content-panel panel-narrow">
        <form action="./" method="get" class="tracking-form" novalidate>
    <input type="hidden" name="page" value="suivi">
    <input type="hidden" name="lang" value="<?= e($language) ?>">
    <label for="numero" class="form-label"><?= e(tr('Numéro de suivi', 'رقم التتبّع')) ?></label>
    <div class="tracking-controls">
        <input class="form-control" type="text" dir="ltr" name="numero" id="numero"
            maxlength="32" value="<?= e(substr($numeroSuivi, 0, 32)) ?>" placeholder="ET000001TN"
            required aria-describedby="tracking-sample" spellcheck="false" autocomplete="off" autocapitalize="characters">
        <button class="button button-dark" type="submit"><?= e(tr('Rechercher', 'ابحث')) ?></button>
    </div>
    <small class="form-text" id="tracking-sample">
        <?= e(tr('Exemple de démonstration', 'رقم للتجربة')) ?>
        <a href="<?= e(frontUrl('suivi', ['numero' => 'ET000001TN'])) ?>"><bdi>ET000001TN</bdi></a>
    </small>
</form>
        <?php if ($rechercheLancee): ?>
            <?php if (!$numeroValide): ?>
                <p class="alert alert-warning mt-4 mb-0" role="alert"><?= e(tr('Saisissez un numéro de suivi valide, par exemple ET000001TN.', 'أدخل رقم تتبّع صالحًا، مثل ET000001TN.')) ?></p>
            <?php elseif (empty($colis)): ?>
                <p class="alert alert-info mt-4 mb-0"><?= e(tr('Aucun colis trouvé pour ce numéro. Vérifiez le numéro puis réessayez.', 'لم نعثر على طرد بهذا الرقم. تحقّق من الرقم ثم أعد المحاولة.')) ?></p>
            <?php else: ?>
                <article class="tracking-result" aria-labelledby="resultat">
                    <div class="result-heading">
                        <h2 id="resultat"><bdi><?= e($colis['numero_suivi']) ?></bdi></h2>
                        <span class="status-badge" data-statut="<?= e($colis['statut']) ?>"><?= e(parcelStatus($colis['statut'])) ?></span>
                    </div>
                    <dl>
                        <dt><?= e(tr('Bureau de départ', 'مكتب الإرسال')) ?></dt>
                        <dd><bdi><?= e($colis['bureau']) ?> · <?= e($colis['ville']) ?></bdi></dd>
                        <dt><?= e(tr('Date de déclaration', 'تاريخ التصريح')) ?></dt>
                        <dd><bdi><?= e(date('d/m/Y', strtotime($colis['created_at']))) ?></bdi></dd>
                    </dl>
                </article>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
