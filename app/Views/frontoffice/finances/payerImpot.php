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
                <h1>Payer mes impôts</h1>

                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?>">
                        <p><?php echo $_SESSION['flash']['message']; ?></p>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <div class="alert alert-info">
                    <p>Cliquez sur « Payer », puis réglez le montant au bureau de poste.
                       L'impôt passe à « Payé » après validation par l'administration.</p>
                </div>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Propriété</th>
                                <th>Année</th>
                                <th>Montant (DT)</th>
                                <th>Date limite</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($mesImpots) == 0): ?>
                                <tr><td colspan="6">Vous n'avez aucun impôt.</td></tr>
                            <?php endif; ?>

                            <?php foreach ($mesImpots as $i): ?>
                                <tr>
                                    <td><?php echo e($i['reference']); ?></td>
                                    <td><?php echo $i['annee']; ?></td>
                                    <td><?php echo $i['montant']; ?></td>
                                    <td><?php echo $i['date_limite']; ?></td>
                                    <td>
                                        <?php if ($i['statut'] == 'paye'): ?>
                                            <span class="badge badge-success">Payé</span>
                                        <?php elseif ($i['statut'] == 'en_attente'): ?>
                                            <span class="badge badge-warning">En attente de validation</span>
                                        <?php else: ?>
                                            <span class="badge badge-error">Non payé</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($i['statut'] == 'non_paye'): ?>
                                            <form method="post" action="index.php?page=payer-impot"
                                                  data-confirm="Vous devez régler <?php echo $i['montant']; ?> DT au bureau de poste. Confirmer ?">
                                                <input type="hidden" name="id" value="<?php echo $i['id']; ?>">
                                                <button type="submit" class="button button-small button-dark">Payer</button>
                                            </form>
                                        <?php elseif ($i['statut'] == 'en_attente'): ?>
                                            À régler à la poste
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
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
    <script src="assets/frontoffice/js/validation.js"></script>
</body>
</html>
