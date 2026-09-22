<?php $__env->startSection('title', $product->name.' - DMT Print'); ?>
<?php $__env->startSection('breadcrumb'); ?><a href="<?php echo e(route('home')); ?>">Beranda</a><span>></span><a href="<?php echo e(route('catalog')); ?>">Katalog</a><span>></span><b><?php echo e($product->name); ?></b><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="page-section product-detail-page"><div class="site-container product-detail-grid">
<div class="product-gallery"><div class="product-main-art tone-<?php echo e($product->tone); ?>"><span class="print-sheet large"><i></i><b>DMT<br>PRINT</b><small><?php echo e($product->category->name); ?></small></span><span class="price-sticker">Mulai dari<br><b>Rp <?php echo e(number_format($product->base_price, 0, ',', '.')); ?></b></span></div></div>
<div class="product-config"><p class="product-code"><?php echo e($product->category->name); ?> | SKU <?php echo e($product->sku); ?></p><h1><?php echo e($product->name); ?></h1><p class="lead"><?php echo e($product->short_description); ?></p>
<form method="POST" action="<?php echo e(route('cart.store', $product)); ?>"><?php echo csrf_field(); ?>
<?php $__currentLoopData = $product->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="form-group"><label for="option-<?php echo e($option->id); ?>"><?php echo e($option->name); ?> <?php if($option->is_required): ?><small>(wajib)</small><?php endif; ?></label><select id="option-<?php echo e($option->id); ?>" name="options[<?php echo e($option->id); ?>]" <?php if($option->is_required): echo 'required'; endif; ?>><option value="">Pilih <?php echo e(strtolower($option->name)); ?></option><?php $__currentLoopData = $option->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($value->id); ?>"><?php echo e($value->name); ?><?php if($value->price_adjustment): ?> (+Rp <?php echo e(number_format($value->price_adjustment, 0, ',', '.')); ?>)<?php endif; ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['options.'.$option->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="qty-line"><label>Jumlah <small>(minimum <?php echo e($product->minimum_order); ?> <?php echo e($product->unit); ?>)</small></label><div class="qty-control"><button type="button" data-qty-minus>-</button><input type="number" name="quantity" value="<?php echo e(old('quantity', $product->minimum_order)); ?>" min="<?php echo e($product->minimum_order); ?>" data-qty><button type="button" data-qty-plus>+</button></div></div>
<div class="estimate-box"><div><span>Harga dasar</span><b>Rp <?php echo e(number_format($product->base_price, 0, ',', '.')); ?></b></div><div><span>Estimasi pengerjaan</span><b><?php echo e($product->production_estimate); ?></b></div><small>Estimasi dihitung ulang oleh sistem saat masuk keranjang.</small></div>
<button type="submit" class="button button-primary button-block"><span class="icon icon-cart"></span> Tambahkan ke Keranjang</button></form></div></div></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/customer/product-order.blade.php ENDPATH**/ ?>