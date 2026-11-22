<?php
require_once __DIR__ . '/../includes/icons.php';
$intro = tr('Toutes les stations des lignes actives, dans l’ordre de passage, avec le tarif de chaque ligne.',
    'كلّ محطّات الخطوط النشطة حسب ترتيب المرور، مع تعريفة كلّ خطّ.');
require __DIR__ . '/../poste/page-heading.php';
$filtreActif = $filtre > 0 || $recherche !== '';
?>
<div class="transport-page">
    <?php if ($lignes !== []): ?>
        <h2 class="workspace-subtitle"><?= e(tr('Tarifs des lignes', 'تعريفات الخطوط')) ?></h2>
        <ul class="tarifs-grid">
            <?php foreach ($lignes as $ligne): ?>
                <li class="surface">
                    <a href="index.php?page=stations-transport&amp;ligne=<?= (int) $ligne['id_ligne'] ?>"<?= $filtre === (int) $ligne['id_ligne'] ? ' aria-current="true"' : '' ?>>
                        <span class="ligne-numero" dir="ltr"><?= e($ligne['numero']) ?></span>
                        <span class="tarif-nom"><bdi><?= e($ligne['nom']) ?></bdi></span>
                        <strong class="tarif-prix"><bdi><?= e(number_format((float) $ligne['tarif'], 3, '.', ' ')) ?> DT</bdi></strong>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2 class="workspace-subtitle" id="titre-stations"><?= e(tr('Stations', 'المحطّات')) ?></h2>
    <form class="transport-filter surface" method="get" action="index.php" novalidate>
        <input type="hidden" name="page" value="stations-transport">
        <div class="field">
            <label for="ligne"><?= e(tr('Ligne', 'الخطّ')) ?></label>
            <select id="ligne" name="ligne">
                <option value="0"><?= e(tr('Toutes les lignes', 'كلّ الخطوط')) ?></option>
                <?php foreach ($lignes as $ligne): ?>
                    <option value="<?= (int) $ligne['id_ligne'] ?>" <?= $filtre === (int) $ligne['id_ligne'] ? 'selected' : '' ?>><?= e($ligne['numero'] . ' · ' . $ligne['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="q"><?= e(tr('Station ou adresse', 'المحطّة أو العنوان')) ?></label>
            <input id="q" type="search" name="q" maxlength="100" value="<?= e($recherche) ?>" placeholder="<?= e(tr('Ex : Ariana', 'مثال: أريانة')) ?>">
        </div>
        <div class="transport-filter-actions">
            <button class="button button-dark button-small" type="submit"><?= e(tr('Filtrer', 'تصفية')) ?></button>
            <?php if ($filtreActif): ?>
                <a class="button button-outline button-small" href="index.php?page=stations-transport"><?= e(tr('Réinitialiser', 'إعادة التعيين')) ?></a>
            <?php endif; ?>
        </div>
    </form>

    <p class="small-note" role="status"><?= e(count($stations)) ?> <?= e(tr('station(s)', 'محطّة')) ?></p>

    <?php if ($stations === []): ?>
        <div class="surface empty-state">
            <?= et_icon('map-pin') ?>
            <h2><?= e(tr('Aucune station ne correspond à votre recherche.', 'لا توجد محطّة تطابق بحثك.')) ?></h2>
            <p><?= e(tr('Choisissez une autre ligne ou modifiez le nom recherché.', 'اختر خطًّا آخر أو غيّر الاسم المبحوث عنه.')) ?></p>
        </div>
    <?php else: ?>
        <div class="table-scroll" tabindex="0" role="region" aria-labelledby="titre-stations">
            <table>
                <thead>
                    <tr>
                        <th scope="col"><?= e(tr('Ligne', 'الخطّ')) ?></th>
                        <th scope="col"><?= e(tr('Ordre', 'الترتيب')) ?></th>
                        <th scope="col"><?= e(tr('Station', 'المحطّة')) ?></th>
                        <th scope="col"><?= e(tr('Adresse', 'العنوان')) ?></th>
                        <th scope="col"><?= e(tr('Tarif', 'التعريفة')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stations as $station): ?>
                        <tr>
                            <td><span class="ligne-numero ligne-numero-petit" dir="ltr"><?= e($station['numero']) ?></span> <bdi><?= e($station['ligne_nom']) ?></bdi></td>
                            <td><?= e($station['ordre']) ?></td>
                            <th scope="row"><bdi><?= e($station['nom']) ?></bdi></th>
                            <td><?= $station['adresse'] !== null && $station['adresse'] !== '' ? '<bdi>' . e($station['adresse']) . '</bdi>' : '—' ?></td>
                            <td><bdi><?= e(number_format((float) $station['tarif'], 3, '.', ' ')) ?> DT</bdi></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
