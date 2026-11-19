<?php
$intro = tr('Consultez l’historique de vos envois.', 'راجع سجلّ إرسالياتك.');
require __DIR__ . '/page-heading.php';
?>
<div class="container py-5">
    <?php if ($erreur !== ''): ?>
        <p class="alert alert-danger" role="alert"><?= e($erreur) ?></p>
    <?php endif; ?>
    <?php if ($confirmation !== null): ?>
        <p class="alert alert-success" role="status">
            <?php if ($confirmation['kind'] === 'declared'): ?>
                <?= e(tr('Envoi déclaré. Numéro de suivi', 'تمّ التصريح بالإرسال. رقم التتبّع')) ?>
                <a href="<?= e(frontUrl('suivi', ['numero' => $confirmation['number']])) ?>"><bdi><?= e($confirmation['number']) ?></bdi></a>
            <?php else: ?>
                <?= e(tr('L’envoi a été annulé.', 'تمّ إلغاء الإرسالية.')) ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>
    <?php if (empty($colisDuCitoyen)): ?>
        <div class="content-panel">
            <p><?= e(tr('Vous n’avez pas encore déclaré d’envoi.', 'لم تصرّح بأيّ إرسالية بعد.')) ?></p>
            <a class="btn btn-primary" href="<?= e(frontUrl('envoi')) ?>"><?= e(tr('Déclarer un envoi', 'التصريح بإرسال طرد')) ?></a>
        </div>
    <?php else: ?>
        <ul class="parcel-list list-unstyled mb-0">
            <?php foreach ($colisDuCitoyen as $colis): ?>
                <li class="content-panel parcel-row">
                    <div>
                        <h2><a href="<?= e(frontUrl('suivi', ['numero' => $colis['numero_suivi']])) ?>"><bdi><?= e($colis['numero_suivi']) ?></bdi></a></h2>
                        <p class="mb-0"><bdi><?= e($colis['bureau']) ?> · <?= e(date('d/m/Y', strtotime($colis['created_at']))) ?></bdi></p>
                    </div>
                    <span class="status-badge"><?= e(parcelStatus($colis['statut'])) ?></span>
                    <?php if ($colis['statut'] === 'En attente'): ?>
                        <form method="post" action="<?= e(frontUrl('historique')) ?>">
                            <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
                            <input type="hidden" name="id_colis" value="<?= e($colis['id_colis']) ?>">
                            <button class="btn btn-outline-primary" type="submit" aria-label="<?= e(tr('Annuler l’envoi ', 'ألغِ الإرسالية ') . $colis['numero_suivi']) ?>"><?= e(tr('Annuler l’envoi', 'ألغِ الإرسالية')) ?></button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
