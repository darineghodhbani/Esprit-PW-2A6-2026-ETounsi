<!doctype html>
<html lang="<?= etE($language) ?>" dir="<?= $language === 'ar' ? 'rtl' : 'ltr' ?>">
<head><?php require __DIR__ . '/../modules/poste/app/views/shared/head.php'; ?></head>
<body class="et-portal">
<?php require __DIR__ . '/../modules/poste/app/views/shared/header.php'; ?>
<main id="contenu">
    <section class="et-container et-hero-wrap" aria-labelledby="hero-title">
        <div class="et-hero">
            <img class="et-hero-image" src="<?= etE(etAsset('images/portal/e-tounsi-hero.webp')) ?>" width="1536" height="1024" alt="<?= etE(etT('Une rue tunisienne, ses portes en bois, ses palmiers et son tramway', 'نهج تونسي بأبوابه الخشبيّة ونخيله والترامواي')) ?>" fetchpriority="high">
            <div class="et-hero-copy">
                <span class="et-hero-badge"><span></span><?= etE(etT('Votre Tunisie, à portée de main', 'تونس بين يديك')) ?></span>
                <h1 id="hero-title"><?= etE(etT('Vos services publics,', 'خدماتك العموميّة،')) ?><br><em><?= etE(etT('en toute simplicité.', 'بكلّ سهولة.')) ?></em></h1>
                <p><?= etE(etT('Moins de déplacements, plus de temps pour vous. Retrouvez vos démarches du quotidien dans un seul espace.', 'تنقّلات أقلّ ووقت أكثر ليك. تلقى إجراءاتك اليوميّة في فضاء واحد.')) ?></p>
                <div class="et-hero-actions"><a class="et-button" href="#services"><?= etE(etT('Découvrir les services', 'اكتشف الخدمات')) ?><i class="bi bi-arrow-right" aria-hidden="true"></i></a><a class="et-button et-button-white" href="<?= etE(etPoste('bureaux')) ?>"><i class="bi bi-geo-alt" aria-hidden="true"></i><?= etE(etT('Trouver un bureau', 'ابحث عن مكتب')) ?></a></div>
            </div>
            <div class="et-hero-caption"><span aria-hidden="true">✦</span> <?= etE(etT('Une porte ouverte sur vos démarches', 'باب مفتوح لإجراءاتك')) ?></div>
        </div>
        <form class="et-search" method="get" action="index.php#services" role="search" data-service-search novalidate>
            <input type="hidden" name="lang" value="<?= etE($language) ?>">
            <div class="et-search-field"><i class="bi bi-search" aria-hidden="true"></i><label class="et-visually-hidden" for="service-query"><?= etE(etT('Rechercher un service', 'ابحث عن خدمة')) ?></label><input id="service-query" name="q" type="search" placeholder="<?= etE(etT('De quel service avez-vous besoin ?', 'شنوّة الخدمة اللي تحتاجها؟')) ?>" value="<?= etE(is_string($_GET['q'] ?? null) ? $_GET['q'] : '') ?>" autocomplete="off"><button type="button" class="et-search-clear" data-search-clear aria-label="<?= etE(etT('Effacer la recherche', 'امسح البحث')) ?>" hidden><i class="bi bi-x" aria-hidden="true"></i></button></div><button class="et-button" type="submit"><?= etE(etT('Rechercher', 'ابحث')) ?><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
        </form>
        <div class="et-search-hints"><span><?= etE(etT('Par exemple :', 'مثال:')) ?></span><?php foreach (['CNAM', etT('Colis', 'طرد'), etT('Transport', 'النقل')] as $etHint): ?><button type="button" data-search-hint="<?= etE($etHint) ?>"><?= etE($etHint) ?></button><?php endforeach; ?></div>
    </section>
    <section class="et-container et-section" id="services" aria-labelledby="services-title">
        <div class="et-section-heading et-heading-row"><div><span class="et-eyebrow"><?= etE(etT('À chaque démarche, sa porte', 'لكلّ إجراء بابه')) ?></span><h2 id="services-title"><?= etE(etT('Entrez, on s’occupe de vous.', 'تفضّل، خدماتنا على ذمّتك.')) ?></h2></div><p><?= etE(etT('Choisissez votre service et poussez la porte de votre administration.', 'اختر خدمتك وافتح باب إدارتك.')) ?></p></div>
        <div class="et-service-ribbon" data-service-ribbon>
            <?php foreach ($etModules as $etKey => $etModule): ?>
                <a class="et-service-photo" href="index.php?page=service&amp;service=<?= etE($etKey) ?>&amp;lang=<?= etE($language) ?>" data-open-office="<?= etE($etKey) ?>" data-name="<?= etE($etModule['nom']) ?>" data-description="<?= etE($etModule['description']) ?>" data-image="<?= etE(etAsset('images/portal/' . $etModule['image'] . '.webp')) ?>" data-search="<?= etE($etModule['nom'] . ' ' . $etModule['mots']) ?>" aria-label="<?= etE(etT('Entrer : ', 'الدخول: ') . $etModule['nom']) ?>">
                    <img src="<?= etE(etAsset('images/portal/' . $etModule['image'] . '.webp')) ?>" alt="" loading="lazy" width="400" height="600">
                    <div class="et-service-photo-shade"></div><span class="et-service-number"><?= etE(str_pad(array_search($etKey, array_keys($etModules)) + 1, 2, '0', STR_PAD_LEFT)) ?></span>
                    <div class="et-service-label"><i class="bi bi-<?= etE($etModule['icone']) ?>" aria-hidden="true"></i><h3><?= etE($etModule['nom']) ?></h3><p><?= etE($etModule['court']) ?></p><span class="et-enter"><?= etE(etT('Entrer', 'الدخول')) ?> <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span></div>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="et-search-status" data-search-status role="status" aria-live="polite" data-none="<?= etE(etT('Aucun service trouvé. Essayez « Poste », « CNAM » ou « Transport ».', 'لا توجد خدمة مطابقة. جرّب « البريد » أو « CNAM » أو « النقل ».')) ?>" data-count="<?= etE(etT('service(s) trouvé(s)', 'خدمة مطابقة')) ?>"></p>
    </section>
    <section class="et-how-section" id="comment" aria-labelledby="how-title">
        <div class="et-container et-section"><div class="et-section-heading et-centered"><span class="et-eyebrow"><?= etE(etT('Simple comme bonjour', 'سهلة كيف السلام')) ?></span><h2 id="how-title"><?= etE(etT('Vos démarches en trois étapes.', 'إجراءاتك في ثلاث مراحل.')) ?></h2></div>
            <div class="et-steps"><?php $etSteps = [[etT('Choisissez votre service', 'اختر خدمتك'), etT('Trouvez l’administration qui correspond à votre besoin.', 'اعثر على الإدارة المناسبة لحاجتك.')], [etT('Entrez dans votre espace', 'ادخل لفضائك'), etT('Ouvrez la porte du bureau et connectez-vous à votre compte.', 'افتح باب المكتب وسجّل الدخول لحسابك.')], [etT('Suivez votre démarche', 'تابع إجراءك'), etT('Retrouvez vos informations et l’avancement de vos demandes.', 'اطّلع على معلوماتك وتقدّم مطالبك.')]]; foreach ($etSteps as $etIndex => $etStep): ?><article class="et-step"><span><?= etE($etIndex + 1) ?></span><h3><?= etE($etStep[0]) ?></h3><p><?= etE($etStep[1]) ?></p></article><?php endforeach; ?></div>
        </div>
    </section>
    <section class="et-container et-section"><div class="et-poste-callout"><div><span class="et-eyebrow"><?= etE(etT('Un bureau près de chez vous', 'مكتب قريب ليك')) ?></span><h2><?= etE(etT('Gardons le lien, partout en Tunisie.', 'نبقاو قراب، في كامل تونس.')) ?></h2><p><?= etE(etT('Consultez les adresses des bureaux de poste et choisissez celui qui vous convient.', 'اطّلع على عناوين مكاتب البريد واختر المكتب المناسب ليك.')) ?></p><a class="et-button" href="<?= etE(etPoste('bureaux')) ?>"><?= etE(etT('Consulter les bureaux', 'اطّلع على المكاتب')) ?><i class="bi bi-arrow-right" aria-hidden="true"></i></a></div><img src="<?= etE(etAsset('images/portal/bureau-poste.webp')) ?>" alt="<?= etE(etT('Le hall d’un bureau de poste', 'بهو مكتب بريد')) ?>" loading="lazy" width="640" height="440"></div></section>
    <section class="et-container et-section et-faq" aria-labelledby="faq-title"><div class="et-section-heading"><span class="et-eyebrow"><?= etE(etT('On vous répond', 'نجيبوك')) ?></span><h2 id="faq-title"><?= etE(etT('Avant de commencer.', 'قبل ما تبدأ.')) ?></h2></div><div><?php $etFaqs = [[etT('Comment accéder aux services de la Poste ?', 'كيفاش ندخل لخدمات البريد؟'), etT('Cliquez sur la photo Poste, puis sur « Bureau de poste ». Connectez-vous dans la section située plus bas pour accéder à vos colis.', 'اضغط على صورة البريد ثمّ على « مكتب البريد ». سجّل الدخول في القسم الموجود أسفل الصفحة للوصول لطرودك.')], [etT('Puis-je suivre un colis sans me connecter ?', 'نجم نتتبّع طرد من غير حساب؟'), etT('Oui. Ouvrez « Suivre un colis » et saisissez votre numéro de suivi. La déclaration d’un envoi demande un compte citoyen.', 'إي. افتح « تتبّع طرد » وأدخل رقم التتبّع. التصريح بإرسال طرد يستحقّ حساب مواطن.')], [etT('Où retrouver mes déclarations ?', 'وين نلقى تصريحاتي؟'), etT('Après connexion, la page « Mes colis » présente vos déclarations et leur état actuel.', 'بعد تسجيل الدخول، صفحة « طرودي » تعرض تصريحاتك وحالتها الحاليّة.')]]; foreach ($etFaqs as $etFaq): ?><details><summary><?= etE($etFaq[0]) ?><i class="bi bi-plus" aria-hidden="true"></i></summary><p><?= etE($etFaq[1]) ?></p></details><?php endforeach; ?></div></section>
</main>
<?php require __DIR__ . '/../modules/poste/app/views/shared/footer.php'; ?>
<dialog class="et-office-dialog" id="office-dialog" aria-labelledby="office-title">
    <div class="et-office-toolbar"><button class="et-office-close" type="button" data-office-close><i class="bi bi-arrow-left" aria-hidden="true"></i><?= etE(etT('Retour aux services', 'العودة إلى الخدمات')) ?></button><span>E-Tounsi <span lang="ar">إي تونسي</span></span></div>
    <?php require __DIR__ . '/../modules/poste/app/views/shared/office.php'; ?>
</dialog>
</body></html>
