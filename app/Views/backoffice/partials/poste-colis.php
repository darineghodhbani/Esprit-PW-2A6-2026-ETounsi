<div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des colis">
    <table class="table-custom">
        <caption class="visually-hidden"><?= e($title) ?></caption>
        <thead><tr>
            <th scope="col">Nº de suivi</th>
            <th scope="col">Bureau de départ</th>
            <th scope="col">Statut</th>
            <?php if ($section === 'colis'): ?>
                <th scope="col">Citoyen</th>
                <th scope="col">Enregistré le</th>
            <?php endif; ?>
            <th scope="col">Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach ($parcels as $parcel): ?>
            <tr>
                <th scope="row" class="table-order-id"><?= e($parcel['numero_suivi']) ?></th>
                <td><strong><?= e($parcel['bureau']) ?></strong><br><small class="text-muted-green"><?= e($parcel['ville']) ?></small></td>
                <td><span class="poste-status"><?= e($parcel['statut']) ?></span></td>
                <?php if ($section === 'colis'): ?>
                    <td><?= e($parcel['id_citoyen']) ?></td>
                    <td><?= e(date('d/m/Y', strtotime($parcel['created_at']))) ?></td>
                <?php endif; ?>
                <td>
                    <div class="poste-table-actions">
                        <?php if ($parcel['statut'] === 'En attente'): ?>
                            <form method="post" action="?espace=admin&amp;page=colis-accepter" data-poste-save novalidate>
                                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= e($parcel['id_colis']) ?>">
                                <button class="btn-custom btn-custom-primary btn-custom-sm" type="submit"
                                        aria-label="Accepter le colis <?= e($parcel['numero_suivi']) ?>">Accepter</button>
                            </form>
                        <?php endif; ?>
                        <?php if (ColisController::statutsSuivants($parcel['statut']) !== []): ?>
                            <a class="btn-custom btn-custom-light btn-custom-sm" href="?espace=admin&amp;page=updateColis&amp;id=<?= e($parcel['id_colis']) ?>"
                               aria-label="Modifier le statut du colis <?= e($parcel['numero_suivi']) ?>">Modifier le statut</a>
                        <?php else: ?>
                            <a class="btn-custom btn-custom-light btn-custom-sm" href="?espace=admin&amp;page=updateColis&amp;id=<?= e($parcel['id_colis']) ?>"
                               aria-label="Consulter le colis <?= e($parcel['numero_suivi']) ?>">Consulter</a>
                        <?php endif; ?>
                        <?php if ($section === 'colis'): ?>
                            <a class="btn-custom btn-custom-danger btn-custom-sm" href="?espace=admin&amp;page=deleteColis&amp;id=<?= e($parcel['id_colis']) ?>"
                               aria-label="Supprimer le colis <?= e($parcel['numero_suivi']) ?>"><i class="bi bi-trash3" aria-hidden="true"></i> Supprimer</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
