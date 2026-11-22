<?php
require_once __DIR__ . '/../includes/icons.php';
$intro = tr('Lignes actives, tarifs, stations et horaires. Ouvrez une ligne pour voir ses arrêts et ses départs.',
    'الخطوط النشطة وتعريفاتها ومحطّاتها ومواقيتها. افتح خطًّا لعرض محطّاته ورحلاته.');
require __DIR__ . '/../poste/page-heading.php';
?>
<div class="transport-page">
    <form class="search-form transport-search" method="get" action="index.php" role="search" novalidate>
        <input type="hidden" name="page" value="lignes-transport">
        <?= et_icon('search') ?>
        <label class="sr-only" for="q"><?= e(tr('Numéro ou nom de la ligne', 'رقم الخطّ أو اسمه')) ?></label>
        <input id="q" type="search" name="q" maxlength="100" value="<?= e($recherche) ?>"
               placeholder="<?= e(tr('Numéro ou nom (ex : T1, Ariana)', 'الرقم أو الاسم (مثال: T1، أريانة)')) ?>">
        <button class="button button-dark button-small" type="submit"><?= e(tr('Rechercher', 'ابحث')) ?></button>
    </form>

    <div class="results-bar">
        <p class="small-note" role="status">
            <?= e(count($lignes)) ?> <?= e(tr('ligne(s) trouvée(s)', 'خطّ/خطوط')) ?><?php if ($recherche !== ''): ?> · « <bdi><?= e($recherche) ?></bdi> »<?php endif; ?>
        </p>
        <?php if ($recherche !== ''): ?>
            <a class="text-link" href="index.php?page=lignes-transport"><?= e(tr('Voir toutes les lignes', 'عرض كلّ الخطوط')) ?></a>
        <?php endif; ?>
    </div>

    <?php if ($lignes === []): ?>
        <div class="surface empty-state">
            <?= et_icon('bus-front') ?>
            <h2><?= e($recherche !== '' ? tr('Aucune ligne ne correspond à votre recherche.', 'لا يوجد خطّ يطابق بحثك.') : tr('Aucune ligne n’est disponible pour le moment.', 'لا توجد خطوط متاحة حاليًّا.')) ?></h2>
            <p><?= e(tr('Vérifiez le numéro ou le nom de la ligne.', 'تحقّق من رقم الخطّ أو اسمه.')) ?></p>
        </div>
    <?php endif; ?>

    <div class="stack">
        <?php foreach ($lignes as $index => $element): ?>
            <?php
            $ligne = $element['ligne'];
            $prochain = $element['prochain'];
            $idTitre = 'ligne-' . (int) $ligne['id_ligne'];
            $nbStations = count($element['stations']);
            ?>
            <details class="ligne-card surface"<?= $index === 0 ? ' open' : '' ?>>
                <summary>
                    <span class="ligne-numero" dir="ltr"><?= e($ligne['numero']) ?></span>
                    <span class="ligne-titre">
                        <strong id="<?= e($idTitre) ?>"><bdi><?= e($ligne['nom']) ?></bdi></strong>
                        <span class="ligne-meta">
                            <span><?= et_icon('ticket') ?><bdi><?= e(number_format((float) $ligne['tarif'], 3, '.', ' ')) ?> DT</bdi></span>
                            <span><?= et_icon('map-pin') ?><?= e($nbStations) ?> <?= e(tr('station(s)', 'محطّة')) ?></span>
                            <span><?= et_icon('clock') ?><?= e(count($element['horaires'])) ?> <?= e(tr('horaire(s)', 'موقيت')) ?></span>
                        </span>
                    </span>
                    <?php if ($prochain !== null): ?>
                        <span class="badge badge-success prochain-badge"><?= e(tr('Prochain départ', 'الانطلاق القادم')) ?> <bdi><?= e(HoraireController::heure($prochain['heure_depart'])) ?></bdi></span>
                    <?php else: ?>
                        <span class="badge badge-neutral prochain-badge"><?= e(tr('Plus de départ aujourd’hui', 'لا رحلات أخرى اليوم')) ?></span>
                    <?php endif; ?>
                    <span class="ligne-chevron"><?= et_icon('chevron-down') ?></span>
                </summary>

                <div class="ligne-body">
                    <section aria-labelledby="<?= e($idTitre) ?>-stations">
                        <h2 class="ligne-section-title" id="<?= e($idTitre) ?>-stations"><?= e(tr('Stations', 'المحطّات')) ?></h2>
                        <?php if ($element['stations'] === []): ?>
                            <p class="small-note"><?= e(tr('Aucune station enregistrée pour cette ligne.', 'لا توجد محطّات مسجّلة لهذا الخطّ.')) ?></p>
                        <?php else: ?>
                            <ol class="timeline">
                                <?php foreach ($element['stations'] as $numero => $station): ?>
                                    <li>
                                        <span class="timeline-node"><?= ($numero === 0 || $numero === $nbStations - 1) ? et_icon('map-pin') : et_icon('check') ?></span>
                                        <div>
                                            <strong><bdi><?= e($station['nom']) ?></bdi></strong>
                                            <small><?php if (!empty($station['adresse'])): ?><bdi><?= e($station['adresse']) ?></bdi> · <?php endif; ?><?= e(tr('arrêt', 'محطّة')) ?> <?= e($station['ordre']) ?></small>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    </section>

                    <section aria-labelledby="<?= e($idTitre) ?>-horaires">
                        <h2 class="ligne-section-title" id="<?= e($idTitre) ?>-horaires"><?= e(tr('Horaires', 'المواقيت')) ?></h2>
                        <?php if ($prochain !== null): ?>
                            <p class="alert alert-info prochain-depart"><?= et_icon('clock') ?>
                                <span><?= e(tr('Prochain départ aujourd’hui :', 'الانطلاق القادم اليوم:')) ?>
                                    <strong><bdi><?= e(HoraireController::heure($prochain['heure_depart'])) ?></bdi></strong>
                                    (<?= e(tr($prochain['sens'], $prochain['sens'] === 'Aller' ? 'ذهاب' : 'إياب')) ?>),
                                    <?= e(tr('arrivée', 'الوصول')) ?> <bdi><?= e(HoraireController::heure($prochain['heure_arrivee'])) ?></bdi>.</span>
                            </p>
                        <?php elseif ($element['horaires'] !== []): ?>
                            <p class="alert alert-warning prochain-depart"><?= et_icon('info') ?><span><?= e(tr('Plus de départ prévu aujourd’hui sur cette ligne.', 'لا توجد رحلات أخرى اليوم على هذا الخطّ.')) ?></span></p>
                        <?php endif; ?>

                        <?php if ($element['horaires'] === []): ?>
                            <p class="small-note"><?= e(tr('Aucun horaire enregistré pour cette ligne.', 'لا توجد مواقيت مسجّلة لهذا الخطّ.')) ?></p>
                        <?php else: ?>
                            <div class="table-scroll" tabindex="0" role="region" aria-label="<?= e(tr('Horaires de la ligne', 'مواقيت الخطّ')) ?> <?= e($ligne['numero']) ?>">
                                <table class="horaires-table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?= e(tr('Sens', 'الاتّجاه')) ?></th>
                                            <th scope="col"><?= e(tr('Départ', 'الانطلاق')) ?></th>
                                            <th scope="col"><?= e(tr('Arrivée', 'الوصول')) ?></th>
                                            <th scope="col"><?= e(tr('Jours', 'الأيّام')) ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($element['horaires'] as $horaire): ?>
                                            <?php $estProchain = $prochain !== null && (int) $prochain['id_horaire'] === (int) $horaire['id_horaire']; ?>
                                            <tr<?= $estProchain ? ' class="prochain"' : '' ?>>
                                                <td><span class="badge <?= $horaire['sens'] === 'Aller' ? 'badge-info' : 'badge-warning' ?>"><?= e(tr($horaire['sens'], $horaire['sens'] === 'Aller' ? 'ذهاب' : 'إياب')) ?></span></td>
                                                <td><strong><bdi><?= e(HoraireController::heure($horaire['heure_depart'])) ?></bdi></strong><?php if ($estProchain): ?><span class="sr-only"> <?= e(tr('(prochain départ)', '(الانطلاق القادم)')) ?></span><?php endif; ?></td>
                                                <td><bdi><?= e(HoraireController::heure($horaire['heure_arrivee'])) ?></bdi></td>
                                                <td><?= e(tr($horaire['jours'], ['Tous les jours' => 'كلّ يوم', 'Lundi - Vendredi' => 'من الاثنين إلى الجمعة', 'Week-end' => 'نهاية الأسبوع'][$horaire['jours']] ?? $horaire['jours'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</div>
