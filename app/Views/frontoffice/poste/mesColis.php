<?php
$intro = t('poste.mes_colis_introduction');
require __DIR__ . '/page-heading.php';
?>
<div class="poste-section">
    <div class="section-heading poste-section-heading">
        <p class="section-label"><?php echo e(t('poste.historique')); ?></p>
        <h2 id="mes-colis"><?php echo e(t('poste.mes_colis')); ?></h2>
    </div>
    <?php if ($erreur !== ''): ?>
        <p class="alert alert-danger" role="alert"><?php echo e($erreur); ?></p>
    <?php endif; ?>
    <?php if ($confirmation !== null): ?>
        <p class="alert alert-success" role="status">
            <?php if ($confirmation['kind'] === 'declared'): ?>
                <?php echo e(t('poste.envoi_declare')); ?>
                <a href="<?php echo e(frontUrl('suivi', ['numero' => $confirmation['number']])); ?>"><bdi><?php echo e($confirmation['number']); ?></bdi></a>
            <?php elseif ($confirmation['kind'] === 'updated'): ?>
                <?php echo e(t('poste.envoi_modifie')); ?>
            <?php elseif ($confirmation['kind'] === 'deleted'): ?>
                <?php echo e(t('poste.envoi_supprime')); ?>
            <?php else: ?>
                <?php echo e(t('poste.envoi_annule')); ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>
    <?php if ($colisASupprimer !== null): ?>
        <section class="content-panel poste-delete-confirmation" aria-labelledby="confirmer-suppression">
            <h2 id="confirmer-suppression"><?php echo e(t('poste.supprimer_envoi_question')); ?></h2>
            <p><bdi><?php echo e($colisASupprimer['numero_suivi']); ?></bdi> — <bdi><?php echo e($colisASupprimer['bureau']); ?></bdi></p>
            <p><?php echo e(t('poste.supprimer_envoi_confirmation')); ?></p>
            <form method="post" action="<?php echo e(frontUrl('supprimer-colis', ['id' => $colisASupprimer['id_colis']])); ?>">
                <input type="hidden" name="csrf" value="<?php echo e($jeton); ?>">
                <div class="poste-actions">
                    <button class="button button-dark" type="submit"><?php echo e(t('poste.confirmer_suppression')); ?></button>
                    <a class="button button-light" href="<?php echo e(frontUrl('historique')); ?>"><?php echo e(t('poste.conserver_envoi')); ?></a>
                </div>
            </form>
        </section>
    <?php endif; ?>
    <?php if (empty($liste)): ?>
        <div class="content-panel">
            <p><?php echo e(t('poste.aucun_envoi')); ?></p>
            <a class="button button-dark" href="<?php echo e(frontUrl('envoi')); ?>"><?php echo e(t('poste.declarer_envoi')); ?></a>
        </div>
    <?php else: ?>
        <div class="table-scroll" role="region" aria-labelledby="mes-colis" tabindex="0">
        <table class="table poste-history-table">
            <thead><tr>
                <th scope="col"><?php echo e(t('poste.reference')); ?></th>
                <th scope="col"><?php echo e(t('poste.nom_prenom')); ?></th>
                <th scope="col"><?php echo e(t('poste.bureau_depart')); ?></th>
                <th scope="col"><?php echo e(t('poste.date')); ?></th>
                <th scope="col"><?php echo e(t('poste.statut')); ?></th>
                <th scope="col"><?php echo e(t('poste.action')); ?></th>
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
                        <a class="button button-light" href="<?php echo e(frontUrl('modifier-colis', ['id' => $colis['id_colis']])); ?>" aria-label="<?php echo e(t('poste.modifier_envoi_libelle') . $colis['numero_suivi']); ?>"><?php echo e(t('poste.modifier')); ?></a>
                        <a class="button button-light" href="<?php echo e(frontUrl('supprimer-colis', ['id' => $colis['id_colis']])); ?>" aria-label="<?php echo e(t('poste.supprimer_envoi_libelle') . $colis['numero_suivi']); ?>"><?php echo e(t('poste.supprimer')); ?></a>
                        <form method="post" action="<?php echo e(frontUrl('historique')); ?>" novalidate>
                            <input type="hidden" name="csrf" value="<?php echo e($jeton); ?>">
                            <input type="hidden" name="id_colis" value="<?php echo e($colis['id_colis']); ?>">
                            <button class="button button-light" type="submit" aria-label="<?php echo e(t('poste.annuler_envoi_libelle') . $colis['numero_suivi']); ?>"><?php echo e(t('poste.annuler_envoi')); ?></button>
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
