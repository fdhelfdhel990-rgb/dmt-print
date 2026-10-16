<?php $__env->startSection('title','Pesanan Berhasil - DMT Print'); ?>
<?php $__env->startSection('content'); ?><section class="page-section"><div class="site-container"><section class="panel"><p class="eyebrow blue">Pesanan diterima</p><h1><?php echo e($order->order_number); ?></h1><p>Admin akan memeriksa detail dan file desain Anda sebelum menetapkan harga final. Pesanan belum dianggap dibayar.</p><div class="checkout-side"><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="summary-product"><?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['model' => $item,'alt' => $item->product_name,'class' => 'summary-thumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->product_name),'class' => 'summary-thumb']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?><div><b><?php echo e($item->product_name); ?></b><small><?php $__currentLoopData = $item->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($option->option_name); ?>: <?php echo e($option->option_value); ?><?php if(!$loop->last): ?> | <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><br><?php echo e($item->quantity); ?> <?php echo e($item->unit); ?></small></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><p>Simpan kode pesanan ini untuk komunikasi dengan admin.</p><div class="cart-actions"><a href="<?php echo e(route('orders.status', ['order_number' => $order->order_number])); ?>" class="button button-primary">Cek Status Pesanan</a><a href="<?php echo e(route('home')); ?>" class="button button-secondary">Kembali ke Beranda</a></div></section></div></section><?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/customer/order-success.blade.php ENDPATH**/ ?>