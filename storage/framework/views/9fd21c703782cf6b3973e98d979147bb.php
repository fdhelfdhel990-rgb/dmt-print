<?php $__env->startSection('title','Cek Pesanan - DMT Print'); ?>
<?php $__env->startSection('breadcrumb'); ?><a href="<?php echo e(route('home')); ?>">Beranda</a><span>›</span><b>Cek Pesanan</b><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="track-page"><div class="track-illustration"><span class="track-doc"></span><span class="track-box"></span></div><div class="track-card"><p class="eyebrow blue">Pantau pesanan</p><h1>Cek status pesanan Anda</h1><p>Masukkan kode pesanan dan nomor WhatsApp yang digunakan saat memesan.</p><form method="POST" action="<?php echo e(route('orders.status.lookup')); ?>"><?php echo csrf_field(); ?><div class="form-group"><label for="order-number">Kode pesanan</label><input id="order-number" name="order_number" value="<?php echo e(old('order_number')); ?>" placeholder="Contoh: DMT-240901-A12B" autocomplete="off" required><?php $__errorArgs = ['order_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div><div class="form-group"><label for="tracking-phone">Nomor WhatsApp</label><input id="tracking-phone" type="tel" inputmode="tel" autocomplete="tel" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="Contoh: 081234567890" required><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div><button class="button button-primary button-block" type="submit">Cek Status Pesanan</button></form><small>Kode pesanan tersedia pada halaman berhasil setelah Anda mengirim pesanan.</small></div></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\customer\track.blade.php ENDPATH**/ ?>