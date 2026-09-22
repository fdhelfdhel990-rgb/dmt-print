@extends('layouts.customer')
@section('title','Keranjang - DMT Print')
@section('breadcrumb')<a href="{{ route('home') }}">Beranda</a><span>›</span><b>Keranjang</b>@endsection
@section('content')
@include('partials.checkout-steps',['activeStep'=>0])
<section class="page-section compact-top"><div class="site-container"><h1 class="page-title">Daftar Belanja</h1><div class="cart-table"><div class="cart-head"><span>Produk</span><span>Harga</span><span>Jumlah</span><span>Total</span></div>@foreach([['Stempel Flash K2','1 warna tinta','Rp 75.000'],['Stempel Flash K3','Warna hitam','Rp 95.000']] as $item)<div class="cart-row"><div class="cart-product"><button class="remove-item" aria-label="Hapus">×</button><span class="cart-thumb"></span><div><b>{{ $item[0] }}</b><small>{{ $item[1] }}</small></div></div><span>{{ $item[2] }}</span><div class="qty-control small"><button>−</button><input value="1"><button>+</button></div><b>{{ $item[2] }}</b></div>@endforeach</div><div class="cart-summary"><div><span>Total harga barang</span><strong>Rp 170.000</strong></div><div class="cart-actions"><a href="{{ route('catalog') }}" class="button button-secondary">Lanjutkan Belanja</a><a href="{{ route('checkout.recipient') }}" class="button button-primary">Lanjutkan</a></div></div></div></section>
@endsection
