<?php $__env->startSection('title','Katalog Produk - DMT Print'); ?>
<?php $__env->startSection('breadcrumb'); ?><a href="<?php echo e(route('home')); ?>">Beranda</a><span>›</span><b>Katalog</b><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="page-section catalog-page">
    <div class="site-container">
        <div class="catalog-title">
            <div>
                <p class="eyebrow blue">Katalog DMT Print</p>
                <h1>Temukan produk cetak yang Anda butuhkan</h1>
                <p>Pilih kategori atau cari langsung dari daftar produk.</p>
            </div>
        </div>
        <div class="catalog-toolbar">
            <div class="chip-list">
                <a href="<?php echo e(route('catalog', filled($search) ? ['q' => $search] : [])); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => $activeCategory === null]); ?>">Semua Produk</a>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('catalog', array_filter(['kategori' => $category->slug, 'q' => $search ?: null]))); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => $activeCategory?->is($category)]); ?>"><?php echo e($category->name); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <form action="<?php echo e(route('catalog')); ?>" class="catalog-search">
                <label class="sr-only" for="catalog-search">Cari produk</label>
                <?php if($activeCategory): ?><input type="hidden" name="kategori" value="<?php echo e($activeCategory->slug); ?>"><?php endif; ?>
                <input id="catalog-search" name="q" value="<?php echo e($search); ?>" placeholder="Cari produk...">
                <button class="button button-primary">Cari</button>
            </form>
        </div>
        <div class="catalog-result">
            <p>Menampilkan <b><?php echo e($products->total()); ?> produk</b><?php if($activeCategory): ?> dalam kategori <b><?php echo e($activeCategory->name); ?></b><?php endif; ?></p>
            <?php if($products->isEmpty()): ?>
                <section class="panel empty-state">
                    <h2>Produk belum tersedia</h2>
                    <p>Kategori ini belum memiliki produk aktif. Silakan pilih kategori lain atau kembali ke semua produk.</p>
                    <a href="<?php echo e(route('catalog')); ?>" class="button button-primary">Semua Produk</a>
                </section>
            <?php else: ?>
                <div class="product-grid">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.product-card',['product'=>$product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php echo e($products->links()); ?>

            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\customer\catalog.blade.php ENDPATH**/ ?>