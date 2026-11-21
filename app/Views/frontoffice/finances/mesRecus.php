<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/../includes/head.php'; ?>
</head>
<body class="page-profile">
    <?php require_once __DIR__ . '/../includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <main id="main">
        <div class="workspace container">
            <?php require_once __DIR__ . '/menu.php'; ?>

            <div class="workspace-content">
                <h1>Mes reçus</h1>
                <p class="page-lead">Les impôts dont le paiement a été validé par l'administration.</p>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>N° reçu</th>
                                <th>Propriété</th>
                                <th>Adresse</th>
                                <th>Année</th>
                                <th>Montant payé (DT)</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recus) == 0): ?>
                                <tr><td colspan="6">Aucun impôt payé pour le moment.</td></tr>
                            <?php endif; ?>

                            <?php foreach ($recus as $r): ?>
                                <tr>
                                    <td>REC-<?php echo $r['id']; ?></td>
                                    <td><?php echo e($r['reference']); ?></td>
                                    <td><?php echo e($r['adresse']); ?></td>
                                    <td><?php echo $r['annee']; ?></td>
                                    <td><?php echo $r['montant']; ?></td>
                                    <td><span class="badge badge-success">Payé</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <?php require_once __DIR__ . '/../includes/scripts.php'; ?>
</body>
</html>
