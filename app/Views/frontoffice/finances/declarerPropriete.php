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
                <h1>Déclarer une propriété</h1>

                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?>">
                        <p><?php echo $_SESSION['flash']['message']; ?></p>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <div class="profile-card surface">
                    <form method="post" action="index.php?page=declarer-propriete" class="stack" novalidate data-validate-form>
<input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                        <div class="field-row">
                            <div class="field">
                                <label for="type">Type de propriété</label>
                                <select id="type" name="type" data-validate="required">
                                    <option value="">-- Choisir --</option>
                                    <?php foreach (ProprieteController::TYPES as $key => $label): ?>
                                        <option value="<?php echo $key; ?>" <?php if ($old['type'] == $key) echo 'selected'; ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($erreurs['type'])): ?>
                                    <p class="field-error server-error"><?php echo $erreurs['type']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="field">
                                <label for="reference">Référence (titre foncier ou immatriculation)</label>
                                <input type="text" id="reference" name="reference" value="<?php echo e($old['reference']); ?>"
                                       placeholder="TF-123456" data-validate="required|min:3|max:50">
                                <?php if (isset($erreurs['reference'])): ?>
                                    <p class="field-error server-error"><?php echo $erreurs['reference']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label for="adresse">Adresse</label>
                                <input type="text" id="adresse" name="adresse" value="<?php echo e($old['adresse']); ?>"
                                       placeholder="12 rue de Marseille, Tunis" data-validate="required|min:5|max:150">
                                <?php if (isset($erreurs['adresse'])): ?>
                                    <p class="field-error server-error"><?php echo $erreurs['adresse']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="field">
                                <label for="valeur">Valeur (DT)</label>
                                <input type="text" id="valeur" name="valeur" value="<?php echo e($old['valeur']); ?>"
                                       placeholder="250000" data-validate="required|number">
                                <?php if (isset($erreurs['valeur'])): ?>
                                    <p class="field-error server-error"><?php echo $erreurs['valeur']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button class="button button-brand" type="submit">Ajouter la propriété</button>
                    </form>
                </div>

                <h2>Mes propriétés</h2>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Type</th>
                                <th>Adresse</th>
                                <th>Valeur (DT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($mesProprietes) == 0): ?>
                                <tr><td colspan="4">Vous n'avez encore aucune propriété.</td></tr>
                            <?php endif; ?>

                            <?php foreach ($mesProprietes as $p): ?>
                                <tr>
                                    <td><?php echo e($p['reference']); ?></td>
                                    <td><?php echo ProprieteController::TYPES[$p['type']]; ?></td>
                                    <td><?php echo e($p['adresse']); ?></td>
                                    <td><?php echo $p['valeur']; ?></td>
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
