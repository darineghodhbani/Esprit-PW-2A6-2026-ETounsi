<?php
$parametres = ['page' => 'admin-audit'];
foreach ($filtres as $nom => $valeur) if ($valeur !== '' && $valeur !== null) $parametres[$nom] = $valeur;
?>
<main id="contenu" class="poste-admin">
    <div class="page-header"><div><h1 class="page-title">Journal d’audit</h1><p class="page-subtitle">Connexions, gestion des comptes et accès refusés.</p></div></div>
    <?php if ($erreurs !== []): ?><div class="alert alert-danger" role="alert"><?php foreach ($erreurs as $message): ?><p><?= e($message) ?></p><?php endforeach; ?></div><?php endif; ?>
    <section class="table-card-custom" aria-label="Historique des actions">
        <form method="get" action="index.php" class="poste-filter" novalidate>
            <input type="hidden" name="page" value="admin-audit">
            <div class="poste-filter-field"><label for="audit-utilisateur">Utilisateur ou compte concerné</label><select class="form-control-custom" id="audit-utilisateur" name="utilisateur"><option value="">Tous les utilisateurs</option>
                <?php foreach ($utilisateurs as $personne): ?><option value="<?= e($personne['id_citoyen']) ?>" <?= (int) $personne['id_citoyen'] === $filtres['utilisateur'] ? 'selected' : '' ?>><?= e(trim($personne['prenom'] . ' ' . $personne['nom'])) ?></option><?php endforeach; ?>
            </select></div>
            <div class="poste-filter-field"><label for="audit-action">Action</label><select class="form-control-custom" id="audit-action" name="action"><option value="">Toutes les actions</option><?php foreach ($actions as $cle => $libelle): ?><option value="<?= e($cle) ?>" <?= $filtres['action'] === $cle ? 'selected' : '' ?>><?= e($libelle) ?></option><?php endforeach; ?></select></div>
            <div class="poste-filter-field"><label for="audit-debut">Du</label><input class="form-control-custom" type="date" id="audit-debut" name="debut" value="<?= e($filtres['debut']) ?>"></div>
            <div class="poste-filter-field"><label for="audit-fin">Au</label><input class="form-control-custom" type="date" id="audit-fin" name="fin" value="<?= e($filtres['fin']) ?>"></div>
            <button class="btn-custom btn-custom-primary" type="submit">Filtrer</button><a class="btn-custom btn-custom-light" href="index.php?page=admin-audit">Réinitialiser</a>
        </form>
        <?php if ($lignes === []): ?><p class="poste-empty">Aucune action ne correspond à ces filtres.</p><?php else: ?>
        <div class="table-responsive" role="region" tabindex="0" aria-label="Actions enregistrées"><table class="table-custom audit-table"><caption class="visually-hidden">Historique des actions sur les comptes</caption>
            <thead><tr><th scope="col">Date et heure</th><th scope="col">Auteur</th><th scope="col">Action</th><th scope="col">Compte concerné</th><th scope="col">Détails</th><th scope="col">Adresse IP</th></tr></thead>
            <tbody><?php foreach ($lignes as $ligne): ?><tr>
                <td><time datetime="<?= e(str_replace(' ', 'T', $ligne['created_at'])) ?>"><?= e(date('d/m/Y H:i:s', strtotime($ligne['created_at']))) ?></time></td>
                <td><?= e($ligne['actor_name']) ?><?php if ($ligne['user_id'] !== null): ?><small class="d-block text-muted-green">#<?= e($ligne['user_id']) ?></small><?php endif; ?></td>
                <td><?= e($actions[$ligne['action']] ?? $ligne['action']) ?></td>
                <td><?php if ($ligne['target_id'] !== null): ?><a href="index.php?<?= e(http_build_query(['page' => 'admin-audit', 'utilisateur' => $ligne['target_id']])) ?>">Compte #<?= e($ligne['target_id']) ?></a><?php else: ?>—<?php endif; ?></td>
                <td><?= e($ligne['details']) ?></td><td><bdi><?= e($ligne['ip_address'] ?? 'Non disponible') ?></bdi></td>
            </tr><?php endforeach; ?></tbody>
        </table></div><?php endif; ?>
        <nav class="poste-filter" aria-label="Pagination du journal"><span><?= e($total) ?> action(s) · Page <?= e($numero) ?> sur <?= e($nombrePages) ?></span>
            <?php if ($numero > 1): ?><a class="btn-custom btn-custom-light" href="index.php?<?= e(http_build_query(array_merge($parametres, ['numero' => $numero - 1]))) ?>">Précédente</a><?php endif; ?>
            <?php if ($numero < $nombrePages): ?><a class="btn-custom btn-custom-light" href="index.php?<?= e(http_build_query(array_merge($parametres, ['numero' => $numero + 1]))) ?>">Suivante</a><?php endif; ?>
        </nav>
    </section>
</main>
