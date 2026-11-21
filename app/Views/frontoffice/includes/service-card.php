<a class="service-card" href="index.php?page=module&id=<?= e($item['id']) ?>"
   style="--module-accent: <?= e($item['accent']) ?>">
    <div class="service-card-top">
        <span class="module-icon"><?= et_icon($item['icon']) ?></span>
        <span class="card-arrow"><?= et_icon('arrow-up-right') ?></span>
    </div>
    <div>
        <p class="service-category"><?= e(m($item, 'category')) ?></p>
        <h3><?= e(m($item, 'label')) ?></h3>
        <p class="service-description"><?= e(m($item, 'description')) ?></p>
    </div>
    <div class="service-card-foot">
        <span><?= e(t('card.discover')) ?></span>
        <span class="status-dot" aria-hidden="true"></span>
    </div>
</a>
