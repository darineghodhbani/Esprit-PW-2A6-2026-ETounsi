<?php if ($erreur !== ''): ?>
    <p class="alert alert-danger" role="alert"><?= e($erreur) ?></p>
<?php endif; ?>
<?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger" role="alert">
        <p class="mb-0"><?= e(tr('Vérifiez les champs indiqués ci-dessous.', 'تحقّق من الخانات المشار إليها أدناه.')) ?></p>
        <?php if (isset($erreurs['general'])): ?>
            <p class="mb-0"><?= e($erreurs['general']) ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if ($confirmationCompte !== ''): ?>
    <p class="alert alert-success" role="status"><?= e($confirmationCompte) ?></p>
<?php endif; ?>
