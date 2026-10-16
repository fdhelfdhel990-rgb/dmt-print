<?php $__env->startSection('title', 'DMT Print - Digital Printing'); ?>
<?php $__env->startSection('content'); ?>
<section class="hero-slider" data-slider>
    <?php $__empty_1 = true; $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <article class="hero-slide <?php echo e($loop->first ? 'active' : ''); ?>">
        <div class="site-container hero-slide-inner">
            <?php if($banner->imageUrl()): ?>
                <img src="<?php echo e($banner->imageUrl()); ?>" alt="<?php echo e($banner->title ?: 'Banner DMT Print'); ?>" class="hero-artwork-img" loading="<?php echo e($loop->first ? 'eager' : 'lazy'); ?>" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='grid';">
                <div class="hero-art-fallback" style="display:none">
                    <div class="hero-fallback-brand">
                        <span class="hero-fallback-mark">d</span>
                        <b>DMT PRINT</b>
                        <small><?php echo e($banner->title ?: 'Digital Printing & Custom Merchandise'); ?></small>
                    </div>
                </div>
            <?php else: ?>
                <div class="hero-art-fallback">
                    <div class="hero-fallback-brand">
                        <span class="hero-fallback-mark">d</span>
                        <b>DMT PRINT</b>
                        <small><?php echo e($banner->title ?: 'Digital Printing & Custom Merchandise'); ?></small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <article class="hero-slide active">
        <div class="site-container hero-slide-inner">
            <div class="hero-art-fallback">
                <div class="hero-fallback-brand">
                    <span class="hero-fallback-mark">d</span>
                    <b>DMT PRINT</b>
                    <small>Semua kebutuhan cetak dalam satu tempat</small>
                </div>
            </div>
        </div>
    </article>
    <?php endif; ?>
    <?php if($banners->count() > 1): ?>
        <button class="slider-arrow slider-prev" type="button" data-slide-prev aria-label="Banner sebelumnya">‹</button>
        <button class="slider-arrow slider-next" type="button" data-slide-next aria-label="Banner berikutnya">›</button>
        <div class="slider-dots"><?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><button class="<?php echo e($loop->first ? 'active' : ''); ?>" data-slide="<?php echo e($i); ?>" aria-label="Banner <?php echo e($loop->iteration); ?>"></button><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
    <?php endif; ?>
</section>
<section class="category-strip"><div class="site-container category-list">
    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a href="<?php echo e(route('catalog', ['kategori' => $category->slug])); ?>" class="category-item"><?php if (isset($component)) { $__componentOriginal99e3095fe204bf0a105d1301124943c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal99e3095fe204bf0a105d1301124943c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.category-icon','data' => ['slug' => $category->slug]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('category-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['slug' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($category->slug)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal99e3095fe204bf0a105d1301124943c8)): ?>
<?php $attributes = $__attributesOriginal99e3095fe204bf0a105d1301124943c8; ?>
<?php unset($__attributesOriginal99e3095fe204bf0a105d1301124943c8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal99e3095fe204bf0a105d1301124943c8)): ?>
<?php $component = $__componentOriginal99e3095fe204bf0a105d1301124943c8; ?>
<?php unset($__componentOriginal99e3095fe204bf0a105d1301124943c8); ?>
<?php endif; ?><span><?php echo e($category->name); ?></span></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div></section>
<section class="page-section"><div class="site-container"><div class="section-heading"><div><p class="eyebrow blue">Pilihan populer</p><h2>Produk unggulan</h2></div><a href="<?php echo e(route('catalog')); ?>">Lihat semua <span>→</span></a></div><div class="product-grid"><?php $__currentLoopData = $products->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo $__env->make('partials.product-card', ['product'=>$product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></section>
<section class="page-section"><div class="site-container"><div class="section-heading"><div><p class="eyebrow blue">Butuh bantuan memilih?</p><h2>Temukan produk cetak yang sesuai</h2><p>Jelajahi produk dan pilih spesifikasi yang Anda perlukan.</p></div><a href="<?php echo e(route('catalog')); ?>" class="button button-primary">Lihat Katalog</a></div></div></section>
<section class="page-section"><div class="site-container"><div class="section-heading"><div><p class="eyebrow blue">Lengkapi kebutuhan Anda</p><h2>Produk lainnya</h2></div></div><div class="product-grid"><?php $__currentLoopData = $products->skip(4)->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo $__env->make('partials.product-card', ['product'=>$product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div></section>
<section class="how-section"><div class="site-container"><div class="section-heading centered"><div><p class="eyebrow blue">Mudah dan jelas</p><h2>Cara memesan</h2></div></div><div class="steps-grid"><article><span>1</span><h3>Pilih produk</h3><p>Tentukan ukuran, bahan, finishing, dan jumlah.</p></article><article><span>2</span><h3>Kirim detail</h3><p>Isi data serta unggah desain atau tautan file.</p></article><article><span>3</span><h3>Konfirmasi harga</h3><p>Admin memeriksa dan menetapkan harga final.</p></article><article><span>4</span><h3>Produksi</h3><p>Pantau status sampai siap diambil atau dikirim.</p></article></div></div></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\customer\home.blade.php ENDPATH**/ ?>