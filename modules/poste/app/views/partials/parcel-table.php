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
                <td><?php $status = $parcel['statut']; require __DIR__ . '/status.php'; ?></td>
                <?php if ($section === 'colis'): ?>
                    <td><?= e($parcel['id_citoyen']) ?></td>
                    <td><?= e(date('d/m/Y', strtotime($parcel['created_at']))) ?></td>
                <?php endif; ?>
                <td>
                    <div class="poste-table-actions">
                        <a class="poste-btn poste-btn-outline poste-btn-sm" href="?espace=admin&amp;page=colis-form&amp;id=<?= e($parcel['id_colis']) ?>"
                           aria-label="Modifier le statut du colis <?= e($parcel['numero_suivi']) ?>">Modifier le statut</a>
                        <?php if ($section === 'colis'): ?>
                            <a class="poste-btn poste-btn-remove poste-btn-sm" href="?espace=admin&amp;page=colis-suppression&amp;id=<?= e($parcel['id_colis']) ?>"
                               aria-label="Supprimer le colis <?= e($parcel['numero_suivi']) ?>"><i class="bi bi-trash3" aria-hidden="true"></i> Supprimer</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
