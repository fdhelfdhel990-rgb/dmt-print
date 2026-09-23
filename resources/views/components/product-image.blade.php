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
    $imagePath = $path ?: ($model instanceof \App\Models\OrderItem ? $model->imagePath() : ($model->image_path ?? null));
    $src = asset('images/placeholders/product.svg');

    if (filled($imagePath) && \Illuminate\Support\Facades\Storage::disk($imageDisk)->exists($imagePath)) {
        $src = \Illuminate\Support\Facades\Storage::disk($imageDisk)->url($imagePath);
    }
@endphp
<img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => trim('product-image '.$class), 'loading' => $loading]) }}>
