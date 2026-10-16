<?php $__env->startSection('title', 'Detail Penerima - DMT Print'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('home')); ?>">Beranda</a><span>›</span><b>Detail Penerima</b>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.checkout-steps', ['activeStep' => 2], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="page-section compact-top">
        <form class="site-container checkout-grid" method="POST" action="<?php echo e(route('checkout.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="checkout-main">
                <section class="panel accent-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Detail Penerima</h2>
                            <p>Admin akan menghubungi nomor ini untuk konfirmasi.</p>
                        </div>
                    </div>
                    <?php if($errors->any()): ?>
                        <p role="alert">Periksa kembali data yang ditandai sebelum melanjutkan.</p>
                    <?php endif; ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="recipient-name">Nama lengkap</label>
                            <input id="recipient-name" name="name" value="<?php echo e(old('name')); ?>" autocomplete="name" required <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group">
                            <label for="recipient-phone">Nomor WhatsApp</label>
                            <input id="recipient-phone" type="tel" inputmode="tel" name="phone" value="<?php echo e(old('phone')); ?>" autocomplete="tel" required <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group full">
                            <label for="recipient-email">Email <small>(opsional)</small></label>
                            <input id="recipient-email" type="email" name="email" value="<?php echo e(old('email')); ?>" autocomplete="email">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group full">
                            <label for="customer-note">Catatan <small>(opsional)</small></label>
                            <textarea id="customer-note" name="customer_note"><?php echo e(old('customer_note')); ?></textarea>
                            <?php $__errorArgs = ['customer_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </section>

                <section class="panel accent-panel">
                    <h2>Cara menerima pesanan</h2>
                    <div class="choice-cards">
                        <label class="choice-card"><input type="radio" name="fulfillment_method" value="shipping" data-fulfillment-option <?php if(old('fulfillment_method', 'shipping') === 'shipping'): echo 'checked'; endif; ?> required> Pengiriman</label>
                        <label class="choice-card"><input type="radio" name="fulfillment_method" value="pickup" data-fulfillment-option <?php if(old('fulfillment_method') === 'pickup'): echo 'checked'; endif; ?>> Ambil sendiri</label>
                    </div>
                    <div class="form-grid shipping-fields" data-shipping-fields>
                        <div class="form-group full">
                            <label for="shipping-address">Alamat lengkap</label>
                            <textarea id="shipping-address" name="shipping_address" autocomplete="street-address"><?php echo e(old('shipping_address')); ?></textarea>
                            <?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group">
                            <label for="shipping-region">Kota/kabupaten</label>
                            <input id="shipping-region" name="shipping_region" value="<?php echo e(old('shipping_region')); ?>" autocomplete="address-level2">
                            <?php $__errorArgs = ['shipping_region'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group">
                            <label for="postal-code">Kode pos <small>(opsional)</small></label>
                            <input id="postal-code" name="postal_code" value="<?php echo e(old('postal_code')); ?>" autocomplete="postal-code" inputmode="numeric">
                            <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group full">
                            <label for="address-note">Catatan alamat <small>(opsional)</small></label>
                            <input id="address-note" name="address_note" value="<?php echo e(old('address_note')); ?>" placeholder="Patokan rumah atau instruksi kurir">
                            <?php $__errorArgs = ['address_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <h2>File desain <small>(opsional)</small></h2>
                    <p>Unggah desain jika sudah tersedia. Anda juga dapat mengirimkannya kemudian.</p>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group">
                            <label for="design-file-<?php echo e($item['key']); ?>"><?php echo e($item['product']->name); ?></label>
                            <input id="design-file-<?php echo e($item['key']); ?>" type="file" name="design_files[<?php echo e($item['key']); ?>]" accept=".jpg,.jpeg,.png,.pdf,.zip">
                            <small>JPG, PNG, PDF, atau ZIP; ukuran maksimal 10 MB.</small>
                            <?php $__errorArgs = ['design_files.'.$item['key']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </section>

                <div>
                    <label class="check-row"><input type="checkbox" name="approved" value="1" <?php if(old('approved')): echo 'checked'; endif; ?> required> Saya memahami harga ini masih estimasi dan harga final ditentukan admin setelah pemeriksaan.</label>
                    <?php $__errorArgs = ['approved'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small role="alert"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="button button-primary button-block">Buat Pesanan</button>
            </div>

            <aside class="checkout-side">
                <section class="panel sticky-panel">
                    <h2>Ringkasan</h2>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="summary-product">
                            <?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['model' => $item['product'],'alt' => $item['product']->name,'class' => 'summary-thumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['product']),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['product']->name),'class' => 'summary-thumb']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
                            <div>
                                <b><?php echo e($item['product']->name); ?></b>
                                <small><?php $__currentLoopData = $item['price']['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($value['option_name']); ?>: <?php echo e($value['option_value']); ?><?php if(!$loop->last): ?> | <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><br><?php echo e($item['quantity']); ?> <?php echo e($item['product']->unit); ?> | Rp <?php echo e(number_format($item['price']['subtotal'], 0, ',', '.')); ?></small>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="summary-line total"><span>Total estimasi</span><b>Rp <?php echo e(number_format($subtotal, 0, ',', '.')); ?></b></div>
                    <p>Pembayaran dilakukan setelah admin menetapkan harga final.</p>
                </section>
            </aside>
        </form>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\customer\checkout-order.blade.php ENDPATH**/ ?>