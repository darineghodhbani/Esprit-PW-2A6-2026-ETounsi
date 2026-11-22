<div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des colis">
    <table class="table-custom">
        <caption class="visually-hidden"><?php echo e($title); ?></caption>
        <thead><tr>
            <th scope="col">Nº de suivi</th>
            <th scope="col">Bureau de départ</th>
            <th scope="col">Statut</th>
            <?php if ($section === 'colis'): ?>
                <th scope="col">Nom déclaré</th>
                <th scope="col">Date de déclaration</th>
            <?php endif; ?>
            <th scope="col">Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach ($liste as $colis): ?>
            <tr>
                <th scope="row" class="table-order-id"><?php echo e($colis['numero_suivi']); ?></th>
                <td><strong><?php echo e($colis['bureau']); ?></strong><br><small class="text-muted-green"><?php echo e($colis['ville']); ?></small></td>
                <td><span class="poste-status"><?php echo e($colis['statut']); ?></span></td>
                <?php if ($section === 'colis'): ?>
                    <td><?php echo e($colis['nom_citoyen']); ?><br><small>Compte nº <?php echo e($colis['id_citoyen']); ?></small></td>
                    <td><?php echo e(date('d/m/Y', strtotime($colis['date_declaration']))); ?></td>
                <?php endif; ?>
                <td>
                    <div class="poste-table-actions">
                        <?php if ($colis['statut'] === 'En attente'): ?>
                            <form method="post" action="?espace=admin&amp;page=colis-accepter" data-poste-save novalidate>
                                <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
                                <input type="hidden" name="id" value="<?php echo e($colis['id_colis']); ?>">
                                <button class="btn-custom btn-custom-primary btn-custom-sm" type="submit"
                                        aria-label="Accepter le colis <?php echo e($colis['numero_suivi']); ?>">Accepter</button>
                            </form>
                        <?php endif; ?>
                        <?php if (ColisController::statutsSuivants($colis['statut']) !== []): ?>
                            <a class="btn-custom btn-custom-light btn-custom-sm" href="?espace=admin&amp;page=updateColis&amp;id=<?php echo e($colis['id_colis']); ?>"
                               aria-label="Modifier le statut du colis <?php echo e($colis['numero_suivi']); ?>">Modifier le statut</a>
                        <?php else: ?>
                            <a class="btn-custom btn-custom-light btn-custom-sm" href="?espace=admin&amp;page=updateColis&amp;id=<?php echo e($colis['id_colis']); ?>"
                               aria-label="Consulter le colis <?php echo e($colis['numero_suivi']); ?>">Consulter</a>
                        <?php endif; ?>
                        <?php if ($section === 'colis'): ?>
                            <a class="btn-custom btn-custom-danger btn-custom-sm" href="?espace=admin&amp;page=deleteColis&amp;id=<?php echo e($colis['id_colis']); ?>"
                               aria-label="Supprimer le colis <?php echo e($colis['numero_suivi']); ?>"><i class="bi bi-trash3" aria-hidden="true"></i> Supprimer</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
