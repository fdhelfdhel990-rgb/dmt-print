@props([
    'model' => null,
    'disk' => null,
    'path' => null,
    'alt' => 'Gambar produk',
    'class' => '',
    'loading' => 'lazy',
])
@php
    $imageDisk = \App\Support\UploadDisk::resolve($disk ?: ($model instanceof \App\Models\OrderItem ? $model->imageDisk() : null), \App\Support\UploadDisk::public());
    $rawPath = $path ?: ($model instanceof \App\Models\OrderItem ? $model->imagePath() : ($model->image_path ?? null));
    $src = \App\Support\UploadDisk::publicUrl($rawPath, $imageDisk, asset('images/placeholders/product.svg')) ?? asset('images/placeholders/product.svg');
@endphp
<img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => trim('product-image '.$class), 'loading' => $loading]) }}>
