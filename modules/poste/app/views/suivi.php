<?php
$intro = tr('Vérifiez le statut d’un envoi grâce à son numéro de suivi.', 'تحقّق من حالة الإرسالية باستعمال رقم التتبّع.');
require __DIR__ . '/page-heading.php';
?>
<div class="container py-5">
    <div class="content-panel panel-narrow">
        <?php require __DIR__ . '/tracking-form.php'; ?>
        <?php if ($rechercheLancee): ?>
            <?php if (!$numeroValide): ?>
                <p class="alert alert-warning mt-4 mb-0" role="alert"><?= e(tr('Saisissez un numéro de suivi valide, par exemple ET000001TN.', 'أدخل رقم تتبّع صالحًا، مثل ET000001TN.')) ?></p>
            <?php elseif (empty($colis)): ?>
                <p class="alert alert-info mt-4 mb-0"><?= e(tr('Aucun colis trouvé pour ce numéro. Vérifiez le numéro puis réessayez.', 'لم نعثر على طرد بهذا الرقم. تحقّق من الرقم ثم أعد المحاولة.')) ?></p>
            <?php else: ?>
                <article class="tracking-result" aria-labelledby="resultat">
                    <div class="result-heading">
                        <h2 id="resultat"><bdi><?= e($colis['numero_suivi']) ?></bdi></h2>
                        <span class="status-badge"><?= e(parcelStatus($colis['statut'])) ?></span>
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
