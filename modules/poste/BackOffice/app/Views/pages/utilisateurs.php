<?php
$utilisateurs = $data['utilisateurs'];
$recherche = $data['recherche'];
$roles = $data['roles'];
?>
<div class="page-header poste-page-header">
    <div>
        <h1 class="page-title"><?= e($title) ?></h1>
        <p class="page-subtitle">Gérez les comptes, les rôles et l’accès à l’application.</p>
    </div>
    <a class="poste-btn poste-btn-primary" href="?page=utilisateur-form"><i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter un utilisateur</a>
</div>
<section class="table-card-custom" aria-label="Comptes utilisateurs">
    <form method="get" class="table-header-control poste-filter" action="">
        <input type="hidden" name="page" value="utilisateurs">
        <div class="poste-filter-field">
            <label for="q">Rechercher un utilisateur</label>
            <input class="form-control-custom" id="q" name="q" type="search" maxlength="120" value="<?= e($recherche) ?>" placeholder="Nom ou adresse e-mail">
        </div>
        <button class="poste-btn poste-btn-outline" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Rechercher</button>
        <?php if ($recherche !== ''): ?><a class="poste-btn poste-btn-quiet" href="?page=utilisateurs">Effacer</a><?php endif; ?>
    </form>
    <?php if ($utilisateurs === []): ?>
        <p class="poste-empty">Aucun utilisateur trouvé. Essayez un autre nom ou une autre adresse e-mail.</p>
    <?php else: ?>
        <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des utilisateurs">
            <table class="table-custom">
                <caption class="visually-hidden">Utilisateurs de l’application</caption>
                <thead><tr><th scope="col">Nom</th><th scope="col">Adresse e-mail</th><th scope="col">Rôle</th><th scope="col">État</th><th scope="col">Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <th scope="row"><?= e($utilisateur['nom']) ?></th>
                            <td><?= e($utilisateur['email']) ?></td>
                            <td><?= e($roles[$utilisateur['role']]) ?></td>
                            <td>
                                <?php if ((int) $utilisateur['actif'] === 1): ?>
                                    <span class="poste-status poste-status-delivered">Actif</span>
                                <?php else: ?>
                                    <span class="poste-status poste-status-cancelled">Désactivé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="poste-table-actions">
                                    <a class="poste-btn poste-btn-outline poste-btn-sm" href="?page=utilisateur-form&amp;id=<?= e($utilisateur['id']) ?>" aria-label="Modifier <?= e($utilisateur['nom']) ?>">Modifier</a>
                                    <a class="poste-btn poste-btn-remove poste-btn-sm" href="?page=utilisateur-suppression&amp;id=<?= e($utilisateur['id']) ?>" aria-label="Supprimer <?= e($utilisateur['nom']) ?>"><i class="bi bi-trash3" aria-hidden="true"></i> Supprimer</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
