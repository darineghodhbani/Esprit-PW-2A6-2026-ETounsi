<?php
$intro = tr('Consultez vos envois. Vous pouvez modifier ou supprimer un colis tant qu’il est en attente d’acceptation.', 'راجع إرسالياتك. يمكنك تعديل الطرد أو حذفه ما دام في انتظار القبول.');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="section-heading poste-section-heading">
        <p class="section-label"><?php echo e(tr('Historique', 'السجلّ')); ?></p>
        <h2 id="mes-colis"><?php echo e(tr('Mes colis', 'طرودي')); ?></h2>
    </div>
    <?php if ($erreur !== ''): ?>
        <p class="alert alert-danger" role="alert"><?php echo e($erreur); ?></p>
    <?php endif; ?>
    <?php if ($confirmation !== null): ?>
        <p class="alert alert-success" role="status">
            <?php if ($confirmation['kind'] === 'declared'): ?>
                <?php echo e(tr('Envoi déclaré, en attente d’acceptation. Numéro de suivi', 'تمّ التصريح بالإرسال في انتظار القبول. رقم التتبّع')); ?>
                <a href="<?php echo e(frontUrl('suivi', ['numero' => $confirmation['number']])); ?>"><bdi><?php echo e($confirmation['number']); ?></bdi></a>
            <?php elseif ($confirmation['kind'] === 'updated'): ?>
                <?php echo e(tr('Les informations de votre envoi ont été mises à jour.', 'تمّ تعديل معلومات إرساليّتك.')); ?>
            <?php elseif ($confirmation['kind'] === 'deleted'): ?>
                <?php echo e(tr('Votre envoi a été supprimé.', 'تمّ حذف إرساليّتك.')); ?>
            <?php else: ?>
                <?php echo e(tr('L’envoi a été annulé.', 'تمّ إلغاء الإرسالية.')); ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>
    <?php if ($colisASupprimer !== null): ?>
        <section class="content-panel poste-delete-confirmation" aria-labelledby="confirmer-suppression">
            <h2 id="confirmer-suppression"><?php echo e(tr('Supprimer cet envoi ?', 'حذف هذه الإرسالية؟')); ?></h2>
            <p><bdi><?php echo e($colisASupprimer['numero_suivi']); ?></bdi> — <bdi><?php echo e($colisASupprimer['bureau']); ?></bdi></p>
            <p><?php echo e(tr('La déclaration sera retirée de votre historique et du suivi. Cette suppression est définitive.', 'سيُحذف التصريح من سجلّك ومن التتبّع. هذا الحذف نهائيّ.')); ?></p>
            <form method="post" action="<?php echo e(frontUrl('supprimer-colis', ['id' => $colisASupprimer['id_colis']])); ?>">
                <input type="hidden" name="csrf" value="<?php echo e($jeton); ?>">
                <div class="poste-actions">
                    <button class="button button-dark" type="submit"><?php echo e(tr('Confirmer la suppression', 'تأكيد الحذف')); ?></button>
                    <a class="button button-light" href="<?php echo e(frontUrl('historique')); ?>"><?php echo e(tr('Conserver mon envoi', 'الاحتفاظ بإرساليّتي')); ?></a>
                </div>
            </form>
        </section>
    <?php endif; ?>
    <?php if (empty($liste)): ?>
        <div class="content-panel">
            <p><?php echo e(tr('Vous n’avez pas encore déclaré d’envoi.', 'لم تصرّح بأيّ إرسالية بعد.')); ?></p>
            <a class="button button-dark" href="<?php echo e(frontUrl('envoi')); ?>"><?php echo e(tr('Déclarer un envoi', 'التصريح بإرسال طرد')); ?></a>
        </div>
    <?php else: ?>
        <div class="table-scroll" role="region" aria-labelledby="mes-colis" tabindex="0">
        <table class="table poste-history-table">
            <thead><tr>
                <th scope="col"><?php echo e(tr('Référence', 'رقم التتبّع')); ?></th>
                <th scope="col"><?php echo e(tr('Nom et prénom', 'الاسم واللقب')); ?></th>
                <th scope="col"><?php echo e(tr('Bureau de départ', 'مكتب الإرسال')); ?></th>
                <th scope="col"><?php echo e(tr('Date', 'التاريخ')); ?></th>
                <th scope="col"><?php echo e(tr('Statut', 'الحالة')); ?></th>
                <th scope="col"><?php echo e(tr('Action', 'الإجراء')); ?></th>
            </tr></thead>
            <tbody>
            <?php foreach ($liste as $colis): ?>
                <tr class="parcel-row">
                    <th scope="row"><a href="<?php echo e(frontUrl('suivi', ['numero' => $colis['numero_suivi']])); ?>"><bdi><?php echo e($colis['numero_suivi']); ?></bdi></a></th>
                    <td><bdi><?php echo e($colis['nom_citoyen']); ?></bdi></td>
                    <td><bdi><?php echo e($colis['bureau']); ?></bdi></td>
                    <td><bdi><?php echo e(date('d/m/Y', strtotime($colis['date_declaration']))); ?></bdi></td>
                    <td><span class="status-badge" data-statut="<?php echo e($colis['statut']); ?>"><?php echo e(parcelStatus($colis['statut'])); ?></span></td>
                    <td>
                    <?php if ($colis['statut'] === 'En attente'): ?>
                        <div class="poste-actions">
                        <a class="button button-light" href="<?php echo e(frontUrl('modifier-colis', ['id' => $colis['id_colis']])); ?>" aria-label="<?php echo e(tr('Modifier l’envoi ', 'تعديل الإرسالية ') . $colis['numero_suivi']); ?>"><?php echo e(tr('Modifier', 'تعديل')); ?></a>
                        <a class="button button-light" href="<?php echo e(frontUrl('supprimer-colis', ['id' => $colis['id_colis']])); ?>" aria-label="<?php echo e(tr('Supprimer l’envoi ', 'حذف الإرسالية ') . $colis['numero_suivi']); ?>"><?php echo e(tr('Supprimer', 'حذف')); ?></a>
                        <form method="post" action="<?php echo e(frontUrl('historique')); ?>" novalidate>
                            <input type="hidden" name="csrf" value="<?php echo e($jeton); ?>">
                            <input type="hidden" name="id_colis" value="<?php echo e($colis['id_colis']); ?>">
                            <button class="button button-light" type="submit" aria-label="<?php echo e(tr('Annuler l’envoi ', 'ألغِ الإرسالية ') . $colis['numero_suivi']); ?>"><?php echo e(tr('Annuler l’envoi', 'ألغِ الإرسالية')); ?></button>
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
