<?php ($editing = $category->exists); ?>
<?php $__env->startSection('title',$editing ? 'Edit Kategori' : 'Tambah Kategori'); ?>
<?php $__env->startSection('page-title',$editing ? 'Edit Kategori' : 'Tambah Kategori'); ?>
<?php $__env->startSection('page-description','Atur kategori yang tampil pada katalog pelanggan.'); ?>
<?php $__env->startSection('page-action'); ?><a href="<?php echo e(route('admin.categories')); ?>" class="button button-secondary">Kembali</a><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.admin-page-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php if(session('status')): ?><section class="admin-card"><?php echo e(session('status')); ?></section><?php endif; ?>
<form class="admin-form-layout" method="POST" action="<?php echo e($editing ? route('admin.categories.update',$category) : route('admin.categories.store')); ?>" enctype="multipart/form-data"><?php echo csrf_field(); ?> <?php if($editing): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><div><section class="admin-card form-section"><div class="form-grid"><div class="form-group"><label>Nama</label><input name="name" value="<?php echo e(old('name',$category->name)); ?>" required></div><div class="form-group"><label>Slug</label><input name="slug" value="<?php echo e(old('slug',$category->slug)); ?>"></div><div class="form-group full"><label>Deskripsi</label><textarea name="description"><?php echo e(old('description',$category->description)); ?></textarea></div><div class="form-group"><label>Urutan</label><input name="sort_order" type="number" value="<?php echo e(old('sort_order',$category->sort_order ?? 0)); ?>"></div></div></section></div><aside><section class="admin-card"><h2>Gambar Kategori</h2><?php if($category->imageUrl()): ?><img src="<?php echo e($category->imageUrl()); ?>" alt="<?php echo e($category->name); ?>" style="width:100%;border-radius:8px"><?php else: ?><div class="admin-upload compact"><b>Belum ada gambar</b></div><?php endif; ?><div class="form-group"><label>Upload gambar</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp"></div><?php if($category->image_path): ?><label><input name="remove_image" value="1" type="checkbox"> Hapus gambar</label><?php endif; ?></section><section class="admin-card"><h2>Status</h2><label><input name="is_active" value="1" type="checkbox" <?php if(old('is_active',$category->is_active ?? true)): echo 'checked'; endif; ?>> Aktif</label><label><input name="is_featured" value="1" type="checkbox" <?php if(old('is_featured',$category->is_featured)): echo 'checked'; endif; ?>> Unggulan</label></section><?php if($errors->any()): ?><section class="admin-card"><?php echo e($errors->first()); ?></section><?php endif; ?><button class="button button-primary button-block">Simpan Kategori</button></aside></form>
<?php if($editing): ?>
<section class="admin-card" style="margin-top:20px;border-top:3px solid #dc2626">
    <h2>Hapus Kategori</h2>
    <p class="muted" style="font-size:12px;margin:0 0 12px">Hapus kategori ini secara permanen beserta gambarnya jika tidak memiliki produk terkait.</p>
    <form method="POST" action="<?php echo e(route('admin.categories.destroy',$category)); ?>" onsubmit="return confirm('PERINGATAN: Hapus kategori <?php echo e($category->name); ?> secara permanen?')">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button type="submit" class="button button-block" style="background:#dc2626;color:#fff;border-color:#dc2626">Hapus Kategori Permanen</button>
    </form>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\admin\category-form.blade.php ENDPATH**/ ?>