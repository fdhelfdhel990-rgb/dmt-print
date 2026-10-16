<header class="store-header">
    <div class="site-container header-main">
        <a href="<?php echo e(route('home')); ?>" class="brand" aria-label="DMT Print beranda">
            <span class="brand-mark">d</span><span><b>DMT</b> Print<small>Darul Muttaqien Printing</small></span>
        </a>
        <form class="header-search" action="<?php echo e(route('catalog')); ?>">
            <label class="sr-only" for="global-search">Cari produk</label>
            <input id="global-search" name="q" placeholder="Cari produk cetak...">
            <button aria-label="Cari"><span class="icon icon-search"></span></button>
        </form>
        <nav class="header-actions" aria-label="Tautan cepat">
            <a href="<?php echo e(route('orders.track')); ?>" class="track-link"><span class="icon icon-receipt"></span>Cek Pesanan</a>
            <a href="<?php echo e(route('cart')); ?>" class="cart-link" aria-label="Keranjang">
                <img src="<?php echo e(asset('images/icons/cart.svg')); ?>" alt="" class="cart-icon">
                <?php if(($cartQuantity ?? 0) > 0): ?><span class="cart-count"><?php echo e($cartQuantity); ?></span><?php endif; ?>
            </a>
        </nav>
        <button class="mobile-menu-button" type="button" aria-label="Buka menu" aria-expanded="false" data-menu-toggle><span></span><span></span><span></span></button>
    </div>
    <div class="category-nav" data-mobile-menu>
        <div class="site-container category-nav-inner">
            <a href="<?php echo e(route('home')); ?>">Beranda</a>
            <a href="<?php echo e(route('catalog')); ?>">Katalog</a>
            <?php $__currentLoopData = ($categories ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('catalog', ['kategori' => $category->slug])); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => isset($activeCategory) && $activeCategory?->is($category)]); ?>"><?php echo e($category->name); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('orders.track')); ?>" class="mobile-only">Cek Pesanan</a>
            <a href="<?php echo e(route('cart')); ?>" class="mobile-only">Keranjang <?php if(($cartQuantity ?? 0) > 0): ?><b>(<?php echo e($cartQuantity); ?>)</b><?php endif; ?></a>
        </div>
    </div>
</header>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/partials/customer-header.blade.php ENDPATH**/ ?>