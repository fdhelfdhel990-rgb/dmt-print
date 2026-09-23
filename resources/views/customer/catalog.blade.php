@extends('layouts.customer')
@section('title','Katalog Produk - DMT Print')
@section('breadcrumb')<a href="{{ route('home') }}">Beranda</a><span>›</span><b>Katalog</b>@endsection
@section('content')
<section class="page-section catalog-page">
    <div class="site-container">
        <div class="catalog-title">
            <div>
                <p class="eyebrow blue">Katalog DMT Print</p>
                <h1>Temukan produk cetak yang Anda butuhkan</h1>
                <p>Pilih kategori atau cari langsung dari daftar produk.</p>
            </div>
        </div>
        <div class="catalog-toolbar">
            <div class="chip-list">
                <a href="{{ route('catalog', filled($search) ? ['q' => $search] : []) }}" @class(['active' => $activeCategory === null])>Semua Produk</a>
                @foreach($categories as $category)
                    <a href="{{ route('catalog', array_filter(['kategori' => $category->slug, 'q' => $search ?: null])) }}" @class(['active' => $activeCategory?->is($category)])>{{ $category->name }}</a>
                @endforeach
            </div>
            <form action="{{ route('catalog') }}" class="catalog-search">
                <label class="sr-only" for="catalog-search">Cari produk</label>
                @if($activeCategory)<input type="hidden" name="kategori" value="{{ $activeCategory->slug }}">@endif
                <input id="catalog-search" name="q" value="{{ $search }}" placeholder="Cari produk...">
                <button class="button button-primary">Cari</button>
            </form>
        </div>
        <div class="catalog-result">
            <p>Menampilkan <b>{{ $products->total() }} produk</b>@if($activeCategory) dalam kategori <b>{{ $activeCategory->name }}</b>@endif</p>
            @if($products->isEmpty())
                <section class="panel empty-state">
                    <h2>Produk belum tersedia</h2>
                    <p>Kategori ini belum memiliki produk aktif. Silakan pilih kategori lain atau kembali ke semua produk.</p>
                    <a href="{{ route('catalog') }}" class="button button-primary">Semua Produk</a>
                </section>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        @include('partials.product-card',['product'=>$product])
                    @endforeach
                </div>
                {{ $products->links() }}
            @endif
        </div>
    </div>
</section>
@endsection
