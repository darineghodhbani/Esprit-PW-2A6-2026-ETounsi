<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4"><div><p class="text-muted mb-1">CNAM</p><h1 class="h3 mb-0">Dossiers des assurés</h1></div><span class="badge bg-light text-dark"><?= count($assures) ?> dossier(s)</span></div>
    <?php if (!$assures): ?><div class="card"><div class="card-body">Aucun dossier CNAM n’est enregistré.</div></div><?php else: ?>
    <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Assuré</th><th>Matricule</th><th>Filière</th><th>Téléphone</th><th>Adresse</th><th>Email</th></tr></thead><tbody>
    <?php foreach ($assures as $a): ?><tr><th><?= e(trim($a['prenom'].' '.$a['nom'])) ?></th><td><bdi><?= e($a['matricule_cnam']) ?></bdi></td><td><?= e($a['filiere_soins']) ?></td><td><bdi><?= e($a['telephone']) ?></bdi></td><td><?= e($a['adresse']) ?></td><td><bdi><?= e($a['email']) ?></bdi></td></tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?>
</div>
