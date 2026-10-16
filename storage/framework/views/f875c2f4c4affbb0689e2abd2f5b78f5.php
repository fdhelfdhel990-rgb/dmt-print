<?php $__env->startSection('title','Keranjang - DMT Print'); ?>
<?php $__env->startSection('breadcrumb'); ?><a href="<?php echo e(route('home')); ?>">Beranda</a><span>></span><b>Keranjang</b><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?> <?php echo $__env->make('partials.checkout-steps',['activeStep'=>0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<section class="page-section compact-top"><div class="site-container"><h1 class="page-title">Daftar Belanja</h1>
<?php if(session('success')): ?><p><?php echo e(session('success')); ?></p><?php endif; ?> <?php if($errors->any()): ?><p role="alert"><?php echo e($errors->first()); ?></p><?php endif; ?>
<?php if($items->isEmpty()): ?><section class="panel"><h2>Keranjang masih kosong</h2><p>Pilih produk cetak yang Anda perlukan dari katalog.</p><a href="<?php echo e(route('catalog')); ?>" class="button button-primary">Lihat Katalog</a></section>
<?php else: ?><div class="cart-table cart-table-refined"><div class="cart-head"><span>Produk</span><span>Harga</span><span>Jumlah</span><span>Total</span></div>
<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="cart-row cart-row-refined"><div class="cart-product cart-product-refined"><?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['model' => $item['product'],'alt' => $item['product']->name,'class' => 'cart-thumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['product']),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['product']->name),'class' => 'cart-thumb']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?><div class="cart-product-info"><b><?php echo e($item['product']->name); ?></b><div class="cart-options"><?php $__empty_1 = true; $__currentLoopData = $item['price']['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><span><?php echo e($value['option_name']); ?>: <?php echo e($value['option_value']); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><span>Tanpa opsi tambahan</span><?php endif; ?></div><form method="POST" action="<?php echo e(route('cart.destroy', $item['key'])); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" class="remove-item">Hapus</button></form></div></div><div class="cart-price-block"><small>Harga</small><span>Rp <?php echo e(number_format($item['price']['unit_estimate'],0,',','.')); ?></span></div><form method="POST" action="<?php echo e(route('cart.update',$item['key'])); ?>" class="cart-qty-form"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><small>Jumlah</small><div class="qty-control small"><button type="button" data-qty-minus aria-label="Kurangi jumlah">-</button><input data-qty type="number" name="quantity" min="<?php echo e($item['product']->minimum_order); ?>" value="<?php echo e($item['quantity']); ?>"><button type="button" data-qty-plus aria-label="Tambah jumlah">+</button><button type="submit" class="qty-update-button" aria-label="Perbarui jumlah">OK</button></div></form><div class="cart-total-block"><small>Total</small><b>Rp <?php echo e(number_format($item['price']['subtotal'],0,',','.')); ?></b></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
<div class="cart-summary"><div><span>Total estimasi</span><strong>Rp <?php echo e(number_format($subtotal,0,',','.')); ?></strong></div><div class="cart-actions"><a href="<?php echo e(route('catalog')); ?>" class="button button-secondary">Lanjutkan Belanja</a><a href="<?php echo e(route('checkout.recipient')); ?>" class="button button-primary">Lanjutkan</a></div></div><?php endif; ?></div></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\customer\cart-dynamic.blade.php ENDPATH**/ ?>