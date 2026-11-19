<?php
$offices = $data['offices'];
$query = $data['query'];
?>
<div class="page-header poste-page-header">
    <div>
        <h1 class="page-title"><?= e($title) ?></h1>
        <p class="page-subtitle">Gérez les coordonnées affichées dans l’annuaire citoyen.</p>
    </div>
    <a class="poste-btn poste-btn-primary" href="?page=bureau-form"><i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter un bureau</a>
</div>

<section class="table-card-custom" aria-label="Annuaire des bureaux">
    <form method="get" class="table-header-control poste-filter" action="">
        <input type="hidden" name="page" value="bureaux">
        <div class="poste-filter-field">
            <label for="q">Rechercher un bureau</label>
            <input type="search" class="form-control-custom" id="q" name="q" value="<?= e($query) ?>" placeholder="Nom, adresse ou ville">
        </div>
        <button class="poste-btn poste-btn-outline" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Rechercher</button>
        <?php if ($query !== ''): ?><a href="?page=bureaux" class="poste-btn poste-btn-quiet">Effacer</a><?php endif; ?>
    </form>
    <?php if ($offices === []): ?>
        <p class="poste-empty"><?= $query === '' ? 'Aucun bureau enregistré. Ajoutez le premier bureau à l’annuaire.' : 'Aucun bureau trouvé. Essayez un autre nom ou une autre ville.' ?></p>
    <?php else: ?>
        <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des bureaux">
            <table class="table-custom">
                <caption class="visually-hidden">Bureaux de poste</caption>
                <thead><tr><th scope="col">Bureau</th><th scope="col">Adresse</th><th scope="col">Ville</th><th scope="col">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($offices as $office): ?>
                    <tr>
                        <th scope="row" class="table-order-id"><?= e($office['nom']) ?></th>
                        <td><?= e($office['adresse']) ?></td>
                        <td><?= e($office['ville']) ?></td>
                        <td>
                            <div class="poste-table-actions">
                                <a class="poste-btn poste-btn-outline poste-btn-sm" href="?page=bureau-form&amp;id=<?= e($office['id_bureau']) ?>"
                                   aria-label="Modifier <?= e($office['nom']) ?>">Modifier</a>
                                <a class="poste-btn poste-btn-remove poste-btn-sm" href="?page=bureau-suppression&amp;id=<?= e($office['id_bureau']) ?>"
                                   aria-label="Supprimer <?= e($office['nom']) ?>"><i class="bi bi-trash3" aria-hidden="true"></i> Supprimer</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
