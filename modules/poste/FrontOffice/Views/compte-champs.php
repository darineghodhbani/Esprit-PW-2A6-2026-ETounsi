<?php foreach ($champsCompte as $champ => $info): ?>
    <div class="compte-champ">
        <label class="form-label" for="<?= e($champ) ?>"><?= e($info[0]) ?></label>
        <?php if ($info[1] === 'password'): ?>
            <div class="password-field">
        <?php endif; ?>
        <input class="form-control<?= isset($erreurs[$champ]) ? ' is-invalid' : '' ?>"
            id="<?= e($champ) ?>" name="<?= e($champ) ?>" type="<?= e($info[1]) ?>"
            autocomplete="<?= e($info[2]) ?>"
            <?php if ($info[3]): ?>required<?php endif; ?>
            <?php if ($info[1] !== 'password'): ?>value="<?= e($valeurs[$champ]) ?>"<?php endif; ?>
            <?php if ($champ === 'nom'): ?>minlength="2" maxlength="80"<?php endif; ?>
            <?php if ($champ === 'email'): ?>maxlength="150" dir="ltr"<?php endif; ?>
            <?php if ($info[1] === 'password'): ?>maxlength="72"<?php endif; ?>
            aria-describedby="aide-<?= e($champ) ?><?= isset($erreurs[$champ]) ? ' erreur-' . e($champ) : '' ?>"
            <?php if (isset($erreurs[$champ])): ?>aria-invalid="true"<?php endif; ?>>
        <?php if ($info[1] === 'password'): ?>
                <button type="button" class="password-toggle" data-password="<?= e($champ) ?>"
                    aria-controls="<?= e($champ) ?>" aria-pressed="false"
                    data-show="<?= e(tr('Afficher', 'إظهار')) ?>" data-hide="<?= e(tr('Masquer', 'إخفاء')) ?>"
                    aria-label="<?= e(tr('Afficher ', 'إظهار ') . $info[0]) ?>"><?= e(tr('Afficher', 'إظهار')) ?></button>
            </div>
        <?php endif; ?>
        <small class="form-text" id="aide-<?= e($champ) ?>">
            <?php if ($champ === 'password' && $currentPage !== 'connexion'): ?>
                <?= e(tr('Utilisez au moins 8 caractères, dans la limite de 72 octets.', 'استخدم 8 أحرف على الأقلّ، في حدود 72 بايتًا.')) ?>
                <?php if ($currentPage === 'profil'): ?>
                    <?= e(tr('Laissez ce champ vide pour conserver votre mot de passe.', 'اترك هذه الخانة فارغة للاحتفاظ بكلمة مرورك.')) ?>
                <?php endif; ?>
            <?php elseif ($champ === 'mot_de_passe_actuel'): ?>
                <?= e(tr('Nécessaire pour changer votre email ou votre mot de passe.', 'مطلوبة لتغيير بريدك الإلكتروني أو كلمة مرورك.')) ?>
            <?php elseif ($champ === 'nom'): ?>
                <?= e(tr('De 2 à 80 caractères.', 'من 2 إلى 80 حرفًا.')) ?>
            <?php elseif ($champ === 'email'): ?>
                <?= e(tr('Exemple : nom@example.com', 'مثال: nom@example.com')) ?>
            <?php endif; ?>
        </small>
        <?php if (isset($erreurs[$champ])): ?>
            <p class="invalid-feedback d-block" id="erreur-<?= e($champ) ?>"><?= e($erreurs[$champ]) ?></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
