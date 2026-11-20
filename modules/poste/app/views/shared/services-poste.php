<div class="et-postal-actions">
    <?php
    $etActions = [
        ['suivi', 'search', etT('Suivre un colis', 'تتبّع طردًا'), etT('Consultez son état avec votre référence.', 'اطّلع على حالته باستعمال رقم التتبّع.')],
        ['bureaux', 'geo-alt', etT('Bureaux de poste', 'مكاتب البريد'), etT('Trouvez un bureau et son adresse.', 'اعثر على مكتب وعنوانه.')],
    ];
    if ($utilisateur !== null && $utilisateur['role'] === 'admin') {
        $etActions[] = ['dashboard', 'grid', etT('Administration', 'الإدارة'), etT('Gérer les colis, les bureaux et les utilisateurs.', 'إدارة الطرود والمكاتب والمستخدمين.')];
    } else {
        $etActions[] = ['envoi', 'box-seam', etT('Déclarer un envoi', 'التصريح بإرسال طرد'), etT('Choisissez le bureau de départ de votre colis.', 'اختر مكتب انطلاق طردك.')];
        $etActions[] = ['historique', 'clock', etT('Mes colis', 'طرودي'), etT('Retrouvez vos déclarations et leur avancement.', 'اطّلع على تصريحاتك ومراحل تقدّمها.')];
    }
    foreach ($etActions as $etAction): ?>
        <a class="et-action-card" href="<?= etE(etPoste($etAction[0], $etAction[0] === 'dashboard')) ?>">
            <i class="bi bi-<?= etE($etAction[1]) ?>" aria-hidden="true"></i>
            <h3><?= etE($etAction[2]) ?></h3><p><?= etE($etAction[3]) ?></p>
            <span><?= etE(etT('Accéder', 'الدخول')) ?> <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
        </a>
    <?php endforeach; ?>
</div>
