<?php ($editing = $product->exists); ?>
<?php $__env->startSection('title',$editing ? 'Edit Produk' : 'Tambah Produk'); ?>
<?php $__env->startSection('page-title',$editing ? 'Edit Produk' : 'Tambah Produk'); ?>
<?php $__env->startSection('page-description','Isi informasi produk secara bertahap dan jelas.'); ?>
<?php $__env->startSection('page-action'); ?><a href="<?php echo e(route('admin.products')); ?>" class="button button-secondary">Batal</a><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.admin-page-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php if(session('status')): ?><section class="admin-card"><?php echo e(session('status')); ?></section><?php endif; ?>
<form class="admin-form-layout" method="POST" action="<?php echo e($editing ? route('admin.products.update',$product) : route('admin.products.store')); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if($editing): ?> <?php echo method_field('PATCH'); ?> <?php endif; ?>
    <div>
        <section class="admin-card form-section"><div class="form-section-heading"><span>1</span><div><h2>Informasi Dasar</h2><p>Nama dan penjelasan yang dilihat customer.</p></div></div><div class="form-grid"><div class="form-group"><label>Nama produk</label><input name="name" value="<?php echo e(old('name',$product->name)); ?>" required></div><div class="form-group"><label>Slug</label><input name="slug" value="<?php echo e(old('slug',$product->slug)); ?>"></div><div class="form-group"><label>Kategori</label><select name="category_id" required><option value="">Pilih kategori</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category->id); ?>" <?php if((int) old('category_id',$product->category_id) === $category->id): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div><div class="form-group"><label>SKU</label><input name="sku" value="<?php echo e(old('sku',$product->sku)); ?>"></div><div class="form-group"><label>Tag</label><input name="tag" value="<?php echo e(old('tag',$product->tag)); ?>"></div><div class="form-group full"><label>Deskripsi singkat</label><input name="short_description" value="<?php echo e(old('short_description',$product->short_description)); ?>"></div><div class="form-group full"><label>Deskripsi lengkap</label><textarea name="description" rows="5"><?php echo e(old('description',$product->description)); ?></textarea></div></div></section>
        <section class="admin-card form-section"><div class="form-section-heading"><span>2</span><div><h2>Harga, Stok, dan Produksi</h2><p>Tentukan dasar perhitungan estimasi.</p></div></div><div class="form-grid"><div class="form-group"><label>Harga dasar</label><div class="input-prefix"><span>Rp</span><input name="base_price" type="number" value="<?php echo e(old('base_price',$product->base_price)); ?>" required></div></div><div class="form-group"><label>Minimum order</label><input name="minimum_order" type="number" value="<?php echo e(old('minimum_order',$product->minimum_order ?? 1)); ?>" required></div><div class="form-group"><label>Satuan</label><input name="unit" value="<?php echo e(old('unit',$product->unit)); ?>" required></div><div class="form-group"><label>Stok saat ini</label><input name="stock_on_hand" type="number" value="<?php echo e(old('stock_on_hand',$product->stock_on_hand ?? 0)); ?>" required></div><div class="form-group"><label>Stok minimum</label><input name="stock_minimum" type="number" value="<?php echo e(old('stock_minimum',$product->stock_minimum ?? 0)); ?>" required></div><div class="form-group"><label>Urutan</label><input name="sort_order" type="number" value="<?php echo e(old('sort_order',$product->sort_order ?? 0)); ?>" required></div><div class="form-group"><label>Tone</label><select name="tone"><option value="blue" <?php if(old('tone',$product->tone)==='blue'): echo 'selected'; endif; ?>>Blue</option><option value="cyan" <?php if(old('tone',$product->tone)==='cyan'): echo 'selected'; endif; ?>>Cyan</option><option value="navy" <?php if(old('tone',$product->tone)==='navy'): echo 'selected'; endif; ?>>Navy</option><option value="yellow" <?php if(old('tone',$product->tone)==='yellow'): echo 'selected'; endif; ?>>Yellow</option><option value="green" <?php if(old('tone',$product->tone)==='green'): echo 'selected'; endif; ?>>Green</option></select></div><div class="form-group full"><label>Estimasi pengerjaan</label><input name="production_estimate" value="<?php echo e(old('production_estimate',$product->production_estimate)); ?>"></div></div></section>
    </div>
    <aside><section class="admin-card"><h2>Gambar Produk</h2><div class="product-form-preview"><?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['model' => $product,'alt' => $product->name ?: 'Gambar produk']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->name ?: 'Gambar produk')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?></div><div class="form-group"><label>Ganti gambar</label><input name="image" type="file" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, WebP maks. 4 MB</small></div><?php if($product->image_path): ?><label><input name="remove_image" value="1" type="checkbox"> Hapus gambar</label><?php endif; ?></section><section class="admin-card"><h2>Status</h2><label class="switch-row"><span><b>Produk aktif</b><small>Tampil pada katalog customer</small></span><input name="is_active" value="1" type="checkbox" <?php if(old('is_active',$product->is_active ?? true)): echo 'checked'; endif; ?>></label><label class="switch-row"><span><b>Produk unggulan</b><small>Tampil pada beranda</small></span><input name="is_featured" value="1" type="checkbox" <?php if(old('is_featured',$product->is_featured)): echo 'checked'; endif; ?>></label></section><?php if($errors->any()): ?><section class="admin-card"><h2>Periksa Input</h2><p><?php echo e($errors->first()); ?></p></section><?php endif; ?><button class="button button-primary button-block">Simpan Produk</button></aside>
</form>
<?php if($editing): ?>
<section class="admin-card" style="margin-top:20px;border-top:3px solid #dc2626">
    <h2>Hapus Produk</h2>
    <p class="muted" style="font-size:12px;margin:0 0 12px">Hapus produk ini secara permanen dari database dan bersihkan file gambar dari storage/cloud.</p>
    <form method="POST" action="<?php echo e(route('admin.products.destroy',$product)); ?>" onsubmit="return confirm('PERINGATAN: Hapus produk <?php echo e($product->name); ?> secara permanen beserta file gambarnya?')">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button type="submit" class="button button-block" style="background:#dc2626;color:#fff;border-color:#dc2626">Hapus Produk Permanen</button>
    </form>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/admin/product-form.blade.php ENDPATH**/ ?>