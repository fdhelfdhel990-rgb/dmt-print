<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DMT Print - layanan digital printing cepat dan berkualitas.">
    <title><?php echo $__env->yieldContent('title', 'DMT Print'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="customer-body">
    <?php echo $__env->make('partials.customer-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
        <div class="breadcrumb-bar"><div class="site-container"><?php echo $__env->yieldContent('breadcrumb'); ?></div></div>
    <?php endif; ?>
    <main><?php echo $__env->yieldContent('content'); ?></main>
    <?php echo $__env->make('partials.customer-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/layouts/customer.blade.php ENDPATH**/ ?>