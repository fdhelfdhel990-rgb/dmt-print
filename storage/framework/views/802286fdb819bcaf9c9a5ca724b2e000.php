<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'model' => null,
    'disk' => null,
    'path' => null,
    'alt' => 'Gambar produk',
    'class' => '',
    'loading' => 'lazy',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'model' => null,
    'disk' => null,
    'path' => null,
    'alt' => 'Gambar produk',
    'class' => '',
    'loading' => 'lazy',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $imageDisk = \App\Support\UploadDisk::resolve($disk ?: ($model instanceof \App\Models\OrderItem ? $model->imageDisk() : null), \App\Support\UploadDisk::public());
    $rawPath = $path ?: ($model instanceof \App\Models\OrderItem ? $model->imagePath() : ($model->image_path ?? null));
    $src = \App\Support\UploadDisk::publicUrl($rawPath, $imageDisk, asset('images/placeholders/product.svg')) ?? asset('images/placeholders/product.svg');
?>
<img src="<?php echo e($src); ?>" alt="<?php echo e($alt); ?>" <?php echo e($attributes->merge(['class' => trim('product-image '.$class), 'loading' => $loading])); ?>>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\components\product-image.blade.php ENDPATH**/ ?>