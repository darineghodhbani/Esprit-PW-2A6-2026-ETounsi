<?php if (!empty($flash)): ?>

    <div class="container">
        <div class="alert alert-<?php echo e($flash['type']); ?> alert-dismissible fade show" role="alert">
            <?php echo e($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    </div>
<?php endif; ?>
