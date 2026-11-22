<?php require __DIR__ . '/../poste/page-heading.php'; ?>
<div class="poste-section">
    <?php if (!empty($erreurs)): ?><div class="alert alert-error" role="alert"><?php foreach ($erreurs as $erreur): ?><p><?= e($erreur) ?></p><?php endforeach; ?></div><?php endif; ?>
    <?php if (isset($_SESSION['flash'])): ?><p class="alert alert-success" role="status"><?= e($_SESSION['flash']['message']) ?></p><?php unset($_SESSION['flash']); endif; ?>
    <section class="content-panel panel-narrow">
        <h2><?= e(tr($dossier?'Informations de l’assuré':'Créer mon dossier', $dossier?'بيانات المؤمن':'إنشاء ملفّي')) ?></h2>
        <p><?= e(tr('Votre dossier rassemble votre affiliation, vos bénéficiaires et les demandes de remboursement.','يجمع ملفّك الانخراط والمستفيدين وطلبات استرجاع المصاريف.')) ?></p>
        <form method="post" action="index.php?page=mon-dossier" novalidate>
            <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
            <?php if (!$dossier): ?>
                <div class="poste-form-grid">
                    <div><label for="nom"><?= e(tr('Nom','اللقب')) ?></label><input id="nom" name="nom" maxlength="100" required value="<?= e($old['nom']??'') ?>"></div>
                    <div><label for="prenom"><?= e(tr('Prénom','الاسم')) ?></label><input id="prenom" name="prenom" maxlength="100" required value="<?= e($old['prenom']??'') ?>"></div>
                    <div><label for="date_naissance"><?= e(tr('Date de naissance','تاريخ الميلاد')) ?></label><input id="date_naissance" name="date_naissance" type="date" max="<?= e(date('Y-m-d')) ?>" required value="<?= e($old['date_naissance']??'') ?>"></div>
                    <div><label for="matricule_cnam"><?= e(tr('Matricule CNAM','رقم الانخراط')) ?></label><input id="matricule_cnam" name="matricule_cnam" maxlength="32" required autocomplete="off" value="<?= e($old['matricule_cnam']??'') ?>"></div>
                </div>
            <?php else: ?>
                <p><strong><?= e(tr('Matricule CNAM','رقم الانخراط')) ?> :</strong> <bdi><?= e($dossier['matricule_cnam']) ?></bdi></p>
            <?php endif; ?>
            <div class="poste-form-grid">
                <div><label for="filiere_soins"><?= e(tr('Filière de soins','نظام العلاج')) ?></label><input id="filiere_soins" name="filiere_soins" maxlength="80" required value="<?= e($old['filiere_soins']??'') ?>"></div>
                <div><label for="telephone"><?= e(tr('Téléphone','الهاتف')) ?></label><input id="telephone" name="telephone" type="tel" maxlength="25" autocomplete="tel" required value="<?= e($old['telephone']??'') ?>"></div>
                <div class="poste-form-wide"><label for="adresse"><?= e(tr('Adresse','العنوان')) ?></label><input id="adresse" name="adresse" maxlength="255" autocomplete="street-address" required value="<?= e($old['adresse']??'') ?>"></div>
            </div>
            <button class="button button-dark" type="submit"><?= e(tr($dossier?'Mettre à jour mon dossier':'Créer mon dossier CNAM',$dossier?'تحديث ملفّي':'إنشاء ملفّي')) ?></button>
        </form>
        <?php if ($dossier): ?><p class="form-text"><?= e(tr('Votre bénéficiaire titulaire est actif. Les bénéficiaires rattachés doivent être validés avant toute demande.','المؤمّن الرئيسي مفعّل. يجب التثبت من المستفيدين الملحقين قبل تقديم أي طلب.')) ?></p><?php endif; ?>
    </section>
</div>
