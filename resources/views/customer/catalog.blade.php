@extends('layouts.customer')
@section('title','Katalog Produk - DMT Print')
@section('breadcrumb')<a href="{{ route('home') }}">Beranda</a><span>›</span><b>Katalog</b>@endsection
@section('content')
<section class="page-section catalog-page"><div class="site-container"><div class="catalog-title"><div><p class="eyebrow blue">Katalog DMT Print</p><h1>Temukan produk cetak yang Anda butuhkan</h1><p>Pilih kategori atau cari langsung dari daftar produk.</p></div></div><div class="catalog-toolbar"><div class="chip-list"><button class="active">Semua</button>@foreach($categories->take(6) as $category)<button>{{ $category->name }}</button>@endforeach</div><select aria-label="Urutkan produk"><option>Produk terbaru</option><option>Harga terendah</option><option>Harga tertinggi</option><option>Nama A-Z</option></select></div><div class="catalog-result"><p>Menampilkan <b>{{ $products->count() }} produk</b></p><div class="product-grid">@foreach($products as $product) @include('partials.product-card',['product'=>$product]) @endforeach</div><nav class="pagination"><a class="disabled">‹</a><a class="active">1</a><a>2</a><a>3</a><a>›</a></nav></div></div></section>
@endsection
