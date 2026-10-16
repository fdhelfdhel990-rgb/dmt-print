<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($invoiceNumber); ?> - DMT Print</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="invoice-body">
    <main class="invoice-page">
        <div class="invoice-actions">
            <button class="button button-primary" type="button" onclick="window.print()">Cetak</button>
            <a href="<?php echo e(route('admin.orders.show',$order)); ?>" class="button button-secondary">Kembali</a>
        </div>
        <header class="invoice-header">
            <div class="invoice-brand">
                <img src="<?php echo e(asset('images/branding/dmt-print-logo.svg')); ?>" alt="DMT Print" onerror="this.style.display='none'">
                <div>
                    <h1><?php echo e($business['business_name'] ?? 'Darul Muttaqien Printing'); ?></h1>
                    <p><?php echo e($business['address'] ?? '-'); ?></p>
                    <p>WhatsApp: <?php echo e($business['phone'] ?? '-'); ?></p>
                </div>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p><?php echo e($invoiceNumber); ?></p>
            </div>
        </header>

        <section class="invoice-meta">
            <dl>
                <div><dt>Kode pesanan</dt><dd><?php echo e($order->order_number); ?></dd></div>
                <div><dt>Tanggal pesanan</dt><dd><?php echo e($order->created_at->format('d M Y, H.i')); ?></dd></div>
                <div><dt>Tanggal invoice</dt><dd><?php echo e(now()->format('d M Y, H.i')); ?></dd></div>
                <div><dt>Status pesanan</dt><dd><?php echo e(str($order->status)->replace('_',' ')->title()); ?></dd></div>
                <div><dt>Status pembayaran</dt><dd><?php echo e($remaining <= 0 ? 'Lunas' : 'Belum lunas'); ?></dd></div>
                <div><dt>Skema pembayaran</dt><dd><?php echo e($order->payment_scheme ? str($order->payment_scheme)->replace('_',' ')->title() : '-'); ?></dd></div>
            </dl>
            <dl>
                <div><dt>Pelanggan</dt><dd><?php echo e($order->customer->name); ?></dd></div>
                <div><dt>WhatsApp</dt><dd><?php echo e($order->customer->phone); ?></dd></div>
                <div><dt>Email</dt><dd><?php echo e($order->customer->email ?: '-'); ?></dd></div>
                <div><dt>Penerimaan</dt><dd><?php echo e($order->fulfillment_method === 'shipping' ? 'Pengiriman' : 'Ambil sendiri'); ?></dd></div>
                <div><dt>Alamat</dt><dd><?php echo e($order->shipping_address ?: '-'); ?> <?php echo e($order->shipping_region); ?></dd></div>
            </dl>
        </section>

        <section>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Opsi</th>
                        <th>Jumlah</th>
                        <th>Harga/unit</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($unitPrice = $item->unit_final_price ?? $item->unit_estimate); ?>
                        <?php ($subtotal = $item->final_subtotal ?? $item->subtotal); ?>
                        <tr>
                            <td><div class="invoice-product"><?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['model' => $item,'alt' => $item->product_name,'class' => 'invoice-thumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->product_name),'class' => 'invoice-thumb']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?> <span><?php echo e($item->product_name); ?></span></div></td>
                            <td><?php $__empty_1 = true; $__currentLoopData = $item->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php echo e($option->option_name); ?>: <?php echo e($option->option_value); ?><?php if(!$loop->last): ?><br><?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?> - <?php endif; ?></td>
                            <td><?php echo e($item->quantity); ?> <?php echo e($item->unit); ?></td>
                            <td>Rp <?php echo e(number_format($unitPrice,0,',','.')); ?></td>
                            <td>Rp <?php echo e(number_format($subtotal,0,',','.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </section>

        <section class="invoice-summary">
            <div>
                <h3>Catatan</h3>
                <p><?php echo e($order->customer_note ?: '-'); ?></p>
            </div>
            <dl>
                <div><dt>Subtotal</dt><dd>Rp <?php echo e(number_format($order->estimated_subtotal,0,',','.')); ?></dd></div>
                <div><dt>Ongkir</dt><dd>Rp <?php echo e(number_format($order->shipping_cost,0,',','.')); ?></dd></div>
                <div><dt>Total</dt><dd>Rp <?php echo e(number_format($total,0,',','.')); ?></dd></div>
                <div><dt>Pembayaran terverifikasi</dt><dd>Rp <?php echo e(number_format($verifiedTotal,0,',','.')); ?></dd></div>
                <div class="invoice-total"><dt>Sisa tagihan</dt><dd>Rp <?php echo e(number_format($remaining,0,',','.')); ?></dd></div>
            </dl>
        </section>
    </main>
</body>
</html>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/admin/order-invoice.blade.php ENDPATH**/ ?>