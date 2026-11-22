<?php
$intro = tr('Consultez vos envois. Vous pouvez modifier ou supprimer un colis tant qu’il est en attente d’acceptation.', 'راجع إرسالياتك. يمكنك تعديل الطرد أو حذفه ما دام في انتظار القبول.');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="section-heading poste-section-heading">
        <p class="section-label"><?= e(tr('Historique', 'السجلّ')) ?></p>
        <h2 id="mes-colis"><?= e(tr('Mes colis', 'طرودي')) ?></h2>
    </div>
    <?php if ($erreur !== ''): ?>
        <p class="alert alert-danger" role="alert"><?= e($erreur) ?></p>
    <?php endif; ?>
    <?php if ($confirmation !== null): ?>
        <p class="alert alert-success" role="status">
            <?php if ($confirmation['kind'] === 'declared'): ?>
                <?= e(tr('Envoi déclaré, en attente d’acceptation. Numéro de suivi', 'تمّ التصريح بالإرسال في انتظار القبول. رقم التتبّع')) ?>
                <a href="<?= e(frontUrl('suivi', ['numero' => $confirmation['number']])) ?>"><bdi><?= e($confirmation['number']) ?></bdi></a>
            <?php elseif ($confirmation['kind'] === 'updated'): ?>
                <?= e(tr('Les informations de votre envoi ont été mises à jour.', 'تمّ تعديل معلومات إرساليّتك.')) ?>
            <?php elseif ($confirmation['kind'] === 'deleted'): ?>
                <?= e(tr('Votre envoi a été supprimé.', 'تمّ حذف إرساليّتك.')) ?>
            <?php else: ?>
                <?= e(tr('L’envoi a été annulé.', 'تمّ إلغاء الإرسالية.')) ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>
    <?php if ($colisASupprimer !== null): ?>
        <section class="content-panel poste-delete-confirmation" aria-labelledby="confirmer-suppression">
            <h2 id="confirmer-suppression"><?= e(tr('Supprimer cet envoi ?', 'حذف هذه الإرسالية؟')) ?></h2>
            <p><bdi><?= e($colisASupprimer['numero_suivi']) ?></bdi> — <bdi><?= e($colisASupprimer['bureau']) ?></bdi></p>
            <p><?= e(tr('La déclaration sera retirée de votre historique et du suivi. Cette suppression est définitive.', 'سيُحذف التصريح من سجلّك ومن التتبّع. هذا الحذف نهائيّ.')) ?></p>
            <form method="post" action="<?= e(frontUrl('supprimer-colis', ['id' => $colisASupprimer['id_colis']])) ?>">
                <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
                <div class="poste-actions">
                    <button class="button button-dark" type="submit"><?= e(tr('Confirmer la suppression', 'تأكيد الحذف')) ?></button>
                    <a class="button button-light" href="<?= e(frontUrl('historique')) ?>"><?= e(tr('Conserver mon envoi', 'الاحتفاظ بإرساليّتي')) ?></a>
                </div>
            </form>
        </section>
    <?php endif; ?>
    <?php if (empty($colisDuCitoyen)): ?>
        <div class="content-panel">
            <p><?= e(tr('Vous n’avez pas encore déclaré d’envoi.', 'لم تصرّح بأيّ إرسالية بعد.')) ?></p>
            <a class="button button-dark" href="<?= e(frontUrl('envoi')) ?>"><?= e(tr('Déclarer un envoi', 'التصريح بإرسال طرد')) ?></a>
        </div>
    <?php else: ?>
        <div class="table-scroll" role="region" aria-labelledby="mes-colis" tabindex="0">
        <table class="table poste-history-table">
            <thead><tr>
                <th scope="col"><?= e(tr('Référence', 'رقم التتبّع')) ?></th>
                <th scope="col"><?= e(tr('Nom et prénom', 'الاسم واللقب')) ?></th>
                <th scope="col"><?= e(tr('Bureau de départ', 'مكتب الإرسال')) ?></th>
                <th scope="col"><?= e(tr('Date', 'التاريخ')) ?></th>
                <th scope="col"><?= e(tr('Statut', 'الحالة')) ?></th>
                <th scope="col"><?= e(tr('Action', 'الإجراء')) ?></th>
            </tr></thead>
            <tbody>
            <?php foreach ($colisDuCitoyen as $colis): ?>
                <tr class="parcel-row">
                    <th scope="row"><a href="<?= e(frontUrl('suivi', ['numero' => $colis['numero_suivi']])) ?>"><bdi><?= e($colis['numero_suivi']) ?></bdi></a></th>
                    <td><bdi><?= e($colis['nom_citoyen']) ?></bdi></td>
                    <td><bdi><?= e($colis['bureau']) ?></bdi></td>
                    <td><bdi><?= e(date('d/m/Y', strtotime($colis['date_declaration']))) ?></bdi></td>
                    <td><span class="status-badge" data-statut="<?= e($colis['statut']) ?>"><?= e(parcelStatus($colis['statut'])) ?></span></td>
                    <td>
                    <?php if ($colis['statut'] === 'En attente'): ?>
                        <div class="poste-actions">
                        <a class="button button-light" href="<?= e(frontUrl('modifier-colis', ['id' => $colis['id_colis']])) ?>" aria-label="<?= e(tr('Modifier l’envoi ', 'تعديل الإرسالية ') . $colis['numero_suivi']) ?>"><?= e(tr('Modifier', 'تعديل')) ?></a>
                        <a class="button button-light" href="<?= e(frontUrl('supprimer-colis', ['id' => $colis['id_colis']])) ?>" aria-label="<?= e(tr('Supprimer l’envoi ', 'حذف الإرسالية ') . $colis['numero_suivi']) ?>"><?= e(tr('Supprimer', 'حذف')) ?></a>
                        <form method="post" action="<?= e(frontUrl('historique')) ?>" novalidate>
                            <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
                            <input type="hidden" name="id_colis" value="<?= e($colis['id_colis']) ?>">
                            <button class="button button-light" type="submit" aria-label="<?= e(tr('Annuler l’envoi ', 'ألغِ الإرسالية ') . $colis['numero_suivi']) ?>"><?= e(tr('Annuler l’envoi', 'ألغِ الإرسالية')) ?></button>
                        </form>
                        </div>
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
