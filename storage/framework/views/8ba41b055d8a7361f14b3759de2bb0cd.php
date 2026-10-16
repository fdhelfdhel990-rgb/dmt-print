<?php $__env->startSection('title', $product->name.' - DMT Print'); ?>
<?php $__env->startSection('breadcrumb'); ?><a href="<?php echo e(route('home')); ?>">Beranda</a><span>></span><a href="<?php echo e(route('catalog')); ?>">Katalog</a><span>></span><b><?php echo e($product->name); ?></b><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="page-section product-detail-page"><div class="site-container product-detail-grid">
<div class="product-gallery"><div class="product-main-art tone-<?php echo e($product->tone); ?>"><?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['model' => $product,'alt' => $product->name,'class' => 'product-image-main','loading' => 'eager']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->name),'class' => 'product-image-main','loading' => 'eager']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?></div></div>
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
<button type="submit" class="button button-primary button-block button-add-cart">
    <svg class="btn-cart-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="9" cy="20" r="1.6"/>
        <circle cx="18" cy="20" r="1.6"/>
        <path d="M3 4h2.4l2.2 11.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6l1.1-6.2H7"/>
    </svg>
    <span>Tambahkan ke Keranjang</span>
</button></form></div></div></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\customer\product-order.blade.php ENDPATH**/ ?>