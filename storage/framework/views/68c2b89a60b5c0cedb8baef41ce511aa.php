<?php $__env->startSection('title','Status Pesanan - DMT Print'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<a href="<?php echo e(route('home')); ?>">Beranda</a><span>›</span><a href="<?php echo e(route('orders.track')); ?>">Cek Pesanan</a><span>›</span><b><?php echo e($order->order_number); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $remaining = max(0, (int) $order->final_total - (int) $order->amount_paid);
    $minimumDownPayment = (int) ceil(((int) $order->final_total) / 2);
    $payableAmount = $order->amount_paid > 0 ? $remaining : min($remaining, $minimumDownPayment);
?>
<section class="page-section">
    <div class="site-container order-status-grid">
        <div>
            <div class="status-header">
                <div>
                    <p class="eyebrow blue"><?php echo e($order->order_number); ?></p>
                    <h1><?php echo e(str($order->status)->replace('_',' ')->title()); ?></h1>
                    <p>Diperbarui <?php echo e($order->updated_at->format('d M Y, H.i')); ?></p>
                </div>
                <span class="status-badge production"><?php echo e(str($order->status)->replace('_',' ')->title()); ?></span>
            </div>

            <?php if(session('status')): ?>
                <section class="panel" aria-live="polite"><?php echo e(session('status')); ?></section>
            <?php endif; ?>

            <section class="panel">
                <h2>Perjalanan Pesanan</h2>
                <div class="order-timeline">
                    <?php $__currentLoopData = $order->statusHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="<?php echo e($loop->last ? 'current' : 'done'); ?>">
                            <span><?php echo e($loop->last ? $loop->iteration : '✓'); ?></span>
                            <div>
                                <b><?php echo e(str($history->status)->replace('_',' ')->title()); ?></b>
                                <small><?php echo e($history->created_at->format('d M Y, H.i')); ?></small>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <?php if($order->status === 'waiting_customer_approval'): ?>
                <section class="panel">
                    <h2>Konfirmasi Harga</h2>
                    <form method="POST" action="<?php echo e(route('orders.approve',$order)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label>Skema pembayaran</label>
                            <select name="payment_scheme">
                                <option value="down_payment">DP 50%</option>
                                <option value="full">Pembayaran penuh</option>
                            </select>
                        </div>
                        <button class="button button-primary">Setujui Harga</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('orders.revise',$order)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label>Catatan revisi</label>
                            <textarea name="revision_note"></textarea>
                        </div>
                        <button class="button button-secondary">Minta Revisi</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if($remaining > 0 && in_array($order->status, ['waiting_payment', 'paid'], true)): ?>
                <section class="panel">
                    <h2>Pembayaran</h2>
                    <div class="summary-line total"><span>Nominal yang harus dibayar</span><b>Rp <?php echo e(number_format($payableAmount,0,',','.')); ?></b></div>
                    <?php $__empty_1 = true; $__currentLoopData = ($paymentMethods ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <form method="POST" action="<?php echo e(route('orders.payment',$order)); ?>" enctype="multipart/form-data" class="payment-method-card">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="method" value="<?php echo e($method->type); ?>">
                            <div class="payment-method-heading">
                                <div><b><?php echo e($method->name); ?></b><small><?php echo e(str($method->type)->replace('_',' ')->title()); ?></small></div>
                                <span>Rp <?php echo e(number_format($payableAmount,0,',','.')); ?></span>
                            </div>
                            <?php if($method->type === 'qris'): ?>
                                <img src="<?php echo e($method->imageUrl()); ?>" alt="QRIS <?php echo e($method->name); ?>" class="qris-image">
                            <?php elseif($method->type === 'bank_transfer'): ?>
                                <dl class="payment-detail-list">
                                    <div><dt>Bank</dt><dd><?php echo e($method->bank_name); ?></dd></div>
                                    <div><dt>Nomor rekening</dt><dd><span data-copy-source><?php echo e($method->account_number); ?></span> <button type="button" class="text-button" data-copy-button>Salin</button></dd></div>
                                    <div><dt>Atas nama</dt><dd><?php echo e($method->account_name); ?></dd></div>
                                </dl>
                            <?php else: ?>
                                <p>Pembayaran dilakukan langsung di lokasi DMT Print.</p>
                            <?php endif; ?>
                            <?php if($method->instructions): ?><p><?php echo e($method->instructions); ?></p><?php endif; ?>
                            <div class="form-group">
                                <label>Jenis pembayaran</label>
                                <select name="payment_type">
                                    <?php if($order->amount_paid > 0): ?>
                                        <option value="settlement">Pelunasan</option>
                                    <?php else: ?>
                                        <option value="down_payment">DP 50%</option>
                                        <option value="full">Pembayaran penuh</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php if($method->type !== 'cash'): ?>
                                <div class="form-group">
                                    <label>Bukti pembayaran</label>
                                    <input type="file" name="proof">
                                </div>
                            <?php endif; ?>
                            <button class="button button-primary"><?php echo e($method->type === 'cash' ? 'Pilih Bayar di Lokasi' : 'Upload Bukti Pembayaran'); ?></button>
                        </form>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <section class="empty-state">
                            <p>Belum ada metode pembayaran aktif. Silakan hubungi admin untuk instruksi pembayaran.</p>
                        </section>
                    <?php endif; ?>
                    <p class="copy-feedback" data-copy-feedback aria-live="polite"></p>
                </section>
            <?php endif; ?>
        </div>
        <aside>
            <section class="panel">
                <h2>Ringkasan Pesanan</h2>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="summary-product"><?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
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
<?php endif; ?><div><b><?php echo e($item->product_name); ?></b><small><?php $__currentLoopData = $item->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($option->option_name); ?>: <?php echo e($option->option_value); ?><?php if(!$loop->last): ?> | <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><br><?php echo e($item->quantity); ?> <?php echo e($item->unit); ?></small></div></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="summary-line"><span>Total pesanan</span><b><?php echo e($order->final_total === null ? 'Menunggu admin' : 'Rp '.number_format($order->final_total,0,',','.')); ?></b></div>
                <div class="summary-line"><span>Sudah dibayar</span><b>Rp <?php echo e(number_format($order->amount_paid,0,',','.')); ?></b></div>
                <div class="summary-line total"><span>Sisa pembayaran</span><b>Rp <?php echo e(number_format($remaining,0,',','.')); ?></b></div>
            </section>
            <section class="panel help-panel"><h3>Butuh bantuan?</h3><p>Hubungi admin dan sertakan kode pesanan.</p></section>
        </aside>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/customer/order-status.blade.php ENDPATH**/ ?>