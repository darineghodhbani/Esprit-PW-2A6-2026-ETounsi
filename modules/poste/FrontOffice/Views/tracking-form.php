<form action="./" method="get" class="tracking-form" novalidate>
    <input type="hidden" name="page" value="suivi">
    <input type="hidden" name="lang" value="<?= e($language) ?>">
    <label for="numero" class="form-label"><?= e(tr('Numéro de suivi', 'رقم التتبّع')) ?></label>
    <div class="tracking-controls">
        <input class="form-control" type="text" dir="ltr" name="numero" id="numero"
            maxlength="32" value="<?= e(substr($numeroSuivi, 0, 32)) ?>" placeholder="ET000001TN"
            required aria-describedby="tracking-sample" spellcheck="false" autocomplete="off" autocapitalize="characters">
        <button class="btn btn-primary" type="submit"><?= e(tr('Rechercher', 'ابحث')) ?></button>
    </div>
    <small class="form-text" id="tracking-sample">
        <?= e(tr('Exemple de démonstration', 'رقم للتجربة')) ?>
        <a href="<?= e(frontUrl('suivi', ['numero' => 'ET000001TN'])) ?>"><bdi>ET000001TN</bdi></a>
    </small>
</form>
