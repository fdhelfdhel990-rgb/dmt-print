@extends('layouts.customer')
@section('title', $product->name.' - DMT Print')
@section('breadcrumb')<a href="{{ route('home') }}">Beranda</a><span>></span><a href="{{ route('catalog') }}">Katalog</a><span>></span><b>{{ $product->name }}</b>@endsection
@section('content')
<section class="page-section product-detail-page"><div class="site-container product-detail-grid">
<div class="product-gallery"><div class="product-main-art tone-{{ $product->tone }}"><x-product-image :model="$product" :alt="$product->name" class="product-image-main" loading="eager" /></div></div>
<div class="product-config"><p class="product-code">{{ $product->category->name }} | SKU {{ $product->sku }}</p><h1>{{ $product->name }}</h1><p class="lead">{{ $product->short_description }}</p>
<form method="POST" action="{{ route('cart.store', $product) }}">@csrf
@foreach($product->options as $option)<div class="form-group"><label for="option-{{ $option->id }}">{{ $option->name }} @if($option->is_required)<small>(wajib)</small>@endif</label><select id="option-{{ $option->id }}" name="options[{{ $option->id }}]" @required($option->is_required)><option value="">Pilih {{ strtolower($option->name) }}</option>@foreach($option->values as $value)<option value="{{ $value->id }}">{{ $value->name }}@if($value->price_adjustment) (+Rp {{ number_format($value->price_adjustment, 0, ',', '.') }})@endif</option>@endforeach</select>@error('options.'.$option->id)<small>{{ $message }}</small>@enderror</div>@endforeach
<div class="qty-line"><label>Jumlah <small>(minimum {{ $product->minimum_order }} {{ $product->unit }})</small></label><div class="qty-control"><button type="button" data-qty-minus>-</button><input type="number" name="quantity" value="{{ old('quantity', $product->minimum_order) }}" min="{{ $product->minimum_order }}" data-qty><button type="button" data-qty-plus>+</button></div></div>
<div class="estimate-box"><div><span>Harga dasar</span><b>Rp {{ number_format($product->base_price, 0, ',', '.') }}</b></div><div><span>Estimasi pengerjaan</span><b>{{ $product->production_estimate }}</b></div><small>Estimasi dihitung ulang oleh sistem saat masuk keranjang.</small></div>
<button type="submit" class="button button-primary button-block button-add-cart">
    <svg class="btn-cart-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="9" cy="20" r="1.6"/>
        <circle cx="18" cy="20" r="1.6"/>
        <path d="M3 4h2.4l2.2 11.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6l1.1-6.2H7"/>
    </svg>
    <span>Tambahkan ke Keranjang</span>
</button></form></div></div></section>
@endsection
