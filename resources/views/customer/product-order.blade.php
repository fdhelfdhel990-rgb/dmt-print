@extends('layouts.customer')
@section('title', $product->name.' - DMT Print')
@section('breadcrumb')<a href="{{ route('home') }}">Beranda</a><span>></span><a href="{{ route('catalog') }}">Katalog</a><span>></span><b>{{ $product->name }}</b>@endsection
@section('content')
<section class="page-section product-detail-page"><div class="site-container product-detail-grid">
<div class="product-gallery"><div class="product-main-art tone-{{ $product->tone }}"><span class="print-sheet large"><i></i><b>DMT<br>PRINT</b><small>{{ $product->category->name }}</small></span><span class="price-sticker">Mulai dari<br><b>Rp {{ number_format($product->base_price, 0, ',', '.') }}</b></span></div></div>
<div class="product-config"><p class="product-code">{{ $product->category->name }} | SKU {{ $product->sku }}</p><h1>{{ $product->name }}</h1><p class="lead">{{ $product->short_description }}</p>
<form method="POST" action="{{ route('cart.store', $product) }}">@csrf
@foreach($product->options as $option)<div class="form-group"><label for="option-{{ $option->id }}">{{ $option->name }} @if($option->is_required)<small>(wajib)</small>@endif</label><select id="option-{{ $option->id }}" name="options[{{ $option->id }}]" @required($option->is_required)><option value="">Pilih {{ strtolower($option->name) }}</option>@foreach($option->values as $value)<option value="{{ $value->id }}">{{ $value->name }}@if($value->price_adjustment) (+Rp {{ number_format($value->price_adjustment, 0, ',', '.') }})@endif</option>@endforeach</select>@error('options.'.$option->id)<small>{{ $message }}</small>@enderror</div>@endforeach
<div class="qty-line"><label>Jumlah <small>(minimum {{ $product->minimum_order }} {{ $product->unit }})</small></label><div class="qty-control"><button type="button" data-qty-minus>-</button><input type="number" name="quantity" value="{{ old('quantity', $product->minimum_order) }}" min="{{ $product->minimum_order }}" data-qty><button type="button" data-qty-plus>+</button></div></div>
<div class="estimate-box"><div><span>Harga dasar</span><b>Rp {{ number_format($product->base_price, 0, ',', '.') }}</b></div><div><span>Estimasi pengerjaan</span><b>{{ $product->production_estimate }}</b></div><small>Estimasi dihitung ulang oleh sistem saat masuk keranjang.</small></div>
<button class="button button-primary button-block"><span class="icon icon-cart"></span> Tambahkan ke Keranjang</button></form></div></div></section>
@endsection
