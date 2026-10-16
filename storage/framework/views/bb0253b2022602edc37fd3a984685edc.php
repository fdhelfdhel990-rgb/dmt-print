<?php $__env->startSection('title','Tampilan Web'); ?>
<?php $__env->startSection('page-title','Tampilan Web'); ?>
<?php $__env->startSection('page-description','Atur konten yang dilihat customer tanpa mengubah kode.'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.admin-page-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php if(session('status')): ?><section class="admin-card"><?php echo e(session('status')); ?></section><?php endif; ?>
<?php if($errors->any()): ?><section class="admin-card"><?php echo e($errors->first()); ?></section><?php endif; ?>
<div class="admin-tabs" data-tabs><button class="active" data-tab="banner">Banner</button><button data-tab="featured">Produk Unggulan</button><button data-tab="profile">Profil Usaha</button><button data-tab="payment">Pembayaran</button><button data-tab="social">Media Sosial</button></div>
<section class="admin-card tab-panel active" data-panel="banner">
    <div class="admin-card-heading">
        <div>
            <h2>Banner Beranda (Artwork Visual)</h2>
            <p>Admin cukup mengupload artwork gambar banner. Gambar akan tampil penuh responsif tanpa overlay teks.</p>
        </div>
    </div>
    <form method="POST" action="<?php echo e(route('admin.banners.store')); ?>" enctype="multipart/form-data" class="banner-add-form">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            <div class="form-group full">
                <label>Gambar Banner Artwork (JPG, PNG, WebP maks. 4 MB)</label>
                <input name="image" type="file" accept="image/jpeg,image/png,image/webp" required>
            </div>
            <div class="form-group">
                <label>Urutan Tampil (0 = paling awal)</label>
                <input name="sort_order" type="number" value="<?php echo e(old('sort_order', 0)); ?>" min="0" required>
            </div>
            <div class="form-group">
                <label>Nama / Label Internal (Opsional)</label>
                <input name="title" value="<?php echo e(old('title')); ?>" placeholder="Contoh: Promo Ramadhan / Banner Utama">
            </div>
            <div class="form-group full">
                <label class="check-inline">
                    <input name="is_active" value="1" type="checkbox" checked> Aktifkan Banner
                </label>
            </div>
        </div>
        <button class="button button-primary">Tambah Banner</button>
    </form>

    <div class="banner-admin-grid">
        <?php $__empty_1 = true; $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="banner-card-admin">
                <div class="banner-card-preview">
                    <?php if($banner->imageUrl()): ?>
                        <img src="<?php echo e($banner->imageUrl()); ?>" alt="<?php echo e($banner->title); ?>" class="banner-preview-img">
                    <?php else: ?>
                        <div class="banner-preview"><b>Belum ada gambar</b></div>
                    <?php endif; ?>
                </div>
                <div class="banner-card-body">
                    <div class="banner-card-header">
                        <b><?php echo e($banner->title ?: 'Banner #'.$banner->id); ?></b>
                        <span class="status-pill <?php echo e($banner->is_active ? 'success' : 'neutral'); ?>"><?php echo e($banner->is_active ? 'Aktif' : 'Nonaktif'); ?> (Urutan: <?php echo e($banner->sort_order); ?>)</span>
                    </div>
                    <form method="POST" action="<?php echo e(route('admin.banners.update', $banner)); ?>" enctype="multipart/form-data" class="banner-edit-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="banner-form-row">
                            <div class="form-group">
                                <label>Ganti Gambar</label>
                                <input name="image" type="file" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="form-group">
                                <label>Urutan</label>
                                <input name="sort_order" type="number" value="<?php echo e($banner->sort_order); ?>" min="0" style="width: 80px;">
                            </div>
                            <div class="form-group">
                                <label>Nama/Label</label>
                                <input name="title" value="<?php echo e($banner->title); ?>">
                            </div>
                        </div>
                        <div class="banner-action-bar">
                            <label class="check-inline">
                                <input name="is_active" value="1" type="checkbox" <?php if($banner->is_active): echo 'checked'; endif; ?>> Aktif
                            </label>
                            <div class="admin-row-actions">
                                <button type="submit" class="button button-primary" style="min-height:34px;padding:0 14px;font-size:12px">Simpan</button>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.banners.destroy', $banner)); ?>" class="banner-delete-form" onsubmit="return confirm('Hapus banner ini secara permanen beserta file gambarnya?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="table-link danger-link">Hapus Banner</button>
                    </form>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="muted">Belum ada banner yang dibuat.</p>
        <?php endif; ?>
    </div>
</section>
<section class="admin-card tab-panel" data-panel="featured"><h2>Produk Unggulan</h2><form method="POST" action="<?php echo e(route('admin.appearance.featured')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><div class="featured-select"><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><label><input name="products[]" value="<?php echo e($product->id); ?>" type="checkbox" <?php if(in_array($product->id, $featuredProductIds, true)): echo 'checked'; endif; ?>><span class="table-thumb tone-<?php echo e($product->tone); ?>"></span><?php echo e($product->name); ?></label><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><button class="button button-primary">Simpan Produk Unggulan</button></form></section>
<section class="admin-card tab-panel" data-panel="profile"><h2>Profil Usaha</h2><form method="POST" action="<?php echo e(route('admin.appearance.business')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><div class="form-grid"><div class="form-group"><label>Nama usaha</label><input name="business_name" value="<?php echo e(old('business_name',$business['business_name'] ?? '')); ?>" required></div><div class="form-group"><label>WhatsApp</label><input name="phone" value="<?php echo e(old('phone',$business['phone'] ?? '')); ?>" required></div><div class="form-group full"><label>Alamat</label><textarea name="address" rows="3" required><?php echo e(old('address',$business['address'] ?? '')); ?></textarea></div><div class="form-group"><label>Jam operasional</label><input name="opening_hours" value="<?php echo e(old('opening_hours',$business['opening_hours'] ?? '')); ?>" required></div><div class="form-group"><label>Google Maps</label><input name="maps_url" value="<?php echo e(old('maps_url',$business['maps_url'] ?? '')); ?>"></div></div><button class="button button-primary">Simpan Profil</button></form></section>
<section class="admin-card tab-panel" data-panel="payment"><h2>Metode Pembayaran</h2><?php $__currentLoopData = ['qris'=>'QRIS','bank_transfer'=>'Transfer Bank','cash'=>'Bayar Langsung']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php ($method = $paymentMethods->firstWhere('type',$type) ?? new \App\Models\PaymentMethod(['type'=>$type,'name'=>$label,'is_active'=>true,'sort_order'=>0])); ?><form method="POST" action="<?php echo e(route('admin.appearance.payment')); ?>" enctype="multipart/form-data"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><input type="hidden" name="type" value="<?php echo e($type); ?>"><div class="form-grid"><div class="form-group"><label>Nama metode</label><input name="name" value="<?php echo e($method->name); ?>" required></div><div class="form-group"><label>Urutan</label><input name="sort_order" type="number" value="<?php echo e($method->sort_order); ?>"></div><div class="form-group"><label>Bank</label><input name="bank_name" value="<?php echo e($method->bank_name); ?>"></div><div class="form-group"><label>No rekening</label><input name="account_number" value="<?php echo e($method->account_number); ?>"></div><div class="form-group"><label>Nama pemilik</label><input name="account_name" value="<?php echo e($method->account_name); ?>"></div><div class="form-group full"><label>Instruksi</label><textarea name="instructions"><?php echo e($method->instructions); ?></textarea></div><?php if($type === 'qris'): ?><div class="form-group"><?php if($method->imageUrl()): ?><img src="<?php echo e($method->imageUrl()); ?>" alt="QRIS" style="width:160px;border-radius:8px"><?php else: ?><div class="admin-upload compact"><b>QRIS belum diunggah</b></div><?php endif; ?><label>Gambar QRIS</label><input name="image" type="file" accept="image/jpeg,image/png,image/webp"><label><input name="remove_image" value="1" type="checkbox"> Hapus QRIS</label></div><?php endif; ?><label><input name="is_active" value="1" type="checkbox" <?php if($method->is_active): echo 'checked'; endif; ?>> Aktif</label></div><button class="button button-primary">Simpan <?php echo e($label); ?></button></form><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></section>
<section class="admin-card tab-panel" data-panel="social"><h2>Media Sosial</h2><form method="POST" action="<?php echo e(route('admin.appearance.social')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><div class="form-grid"><div class="form-group"><label>Instagram</label><input name="instagram" value="<?php echo e(old('instagram',$social['instagram'] ?? '')); ?>" placeholder="https://instagram.com/..."><label><input name="show_instagram" value="1" type="checkbox" <?php if($social['show_instagram'] ?? false): echo 'checked'; endif; ?>> Tampilkan Instagram</label></div><div class="form-group"><label>TikTok</label><input name="tiktok" value="<?php echo e(old('tiktok',$social['tiktok'] ?? '')); ?>" placeholder="https://tiktok.com/@..."><label><input name="show_tiktok" value="1" type="checkbox" <?php if($social['show_tiktok'] ?? false): echo 'checked'; endif; ?>> Tampilkan TikTok</label></div></div><button class="button button-primary">Simpan Media Sosial</button></form></section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/admin/appearance.blade.php ENDPATH**/ ?>