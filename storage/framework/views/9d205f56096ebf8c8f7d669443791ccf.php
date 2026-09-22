<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> - DMT Print</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar" data-admin-sidebar>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="admin-brand"><span class="brand-mark">d</span><span><b>DMT</b> Print<small>Panel Admin</small></span></a>
            <nav class="admin-nav">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"><span>⌂</span>Beranda</a>
                <a href="<?php echo e(route('admin.orders')); ?>" class="<?php echo e(request()->routeIs('admin.orders*') ? 'active' : ''); ?>"><span>▤</span>Pesanan</a>
                <a href="<?php echo e(route('admin.products')); ?>" class="<?php echo e(request()->routeIs('admin.products*','admin.stock') ? 'active' : ''); ?>"><span>□</span>Produk & Stok</a>
                <a href="<?php echo e(route('admin.cashbook')); ?>" class="<?php echo e(request()->routeIs('admin.cashbook') ? 'active' : ''); ?>"><span>Rp</span>Buku Kas</a>
                <a href="<?php echo e(route('admin.appearance')); ?>" class="<?php echo e(request()->routeIs('admin.appearance') ? 'active' : ''); ?>"><span>◫</span>Tampilan Web</a>
                <a href="<?php echo e(route('admin.settings')); ?>" class="<?php echo e(request()->routeIs('admin.settings') ? 'active' : ''); ?>"><span>⚙</span>Pengaturan</a>
            </nav>
            <form method="POST" action="<?php echo e(route('admin.logout')); ?>"><?php echo csrf_field(); ?><button class="admin-logout" type="submit">Keluar</button></form>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar"><button class="admin-menu-button" type="button" data-admin-menu aria-label="Buka sidebar">☰</button><div><span class="muted">Darul Muttaqien Printing</span></div><div class="admin-user"><span class="admin-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></span><div><b><?php echo e(auth()->user()->name); ?></b><small>Admin</small></div></div></header>
            <main class="admin-content"><?php echo $__env->yieldContent('content'); ?></main>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/layouts/admin.blade.php ENDPATH**/ ?>