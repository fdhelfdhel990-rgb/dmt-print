@extends('layouts.customer')
@section('title', 'DMT Print - Digital Printing')
@section('content')
<section class="hero-slider" data-slider>
    @forelse($banners as $banner)
    <article class="hero-slide {{ $loop->first ? 'active' : '' }}">
        <div class="site-container hero-slide-inner">
            @if($banner->imageUrl())
                <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title ?: 'Banner DMT Print' }}" class="hero-artwork-img" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
            @else
                <div class="hero-art-fallback">
                    <div class="hero-fallback-brand">
                        <span class="hero-fallback-mark">d</span>
                        <b>DMT PRINT</b>
                        <small>{{ $banner->title ?: 'Digital Printing & Custom Merchandise' }}</small>
                    </div>
                </div>
            @endif
        </div>
    </article>
    @empty
    <article class="hero-slide active">
        <div class="site-container hero-slide-inner">
            <div class="hero-art-fallback">
                <div class="hero-fallback-brand">
                    <span class="hero-fallback-mark">d</span>
                    <b>DMT PRINT</b>
                    <small>Semua kebutuhan cetak dalam satu tempat</small>
                </div>
            </div>
        </div>
    </article>
    @endforelse
    @if($banners->count() > 1)
        <button class="slider-arrow slider-prev" type="button" data-slide-prev aria-label="Banner sebelumnya">‹</button>
        <button class="slider-arrow slider-next" type="button" data-slide-next aria-label="Banner berikutnya">›</button>
        <div class="slider-dots">@foreach($banners as $i => $banner)<button class="{{ $loop->first ? 'active' : '' }}" data-slide="{{ $i }}" aria-label="Banner {{ $loop->iteration }}"></button>@endforeach</div>
    @endif
</section>
<section class="category-strip"><div class="site-container category-list">
    @foreach($categories as $category)<a href="{{ route('catalog', ['kategori' => $category->slug]) }}" class="category-item"><x-category-icon :slug="$category->slug" /><span>{{ $category->name }}</span></a>@endforeach
</div></section>
<section class="page-section"><div class="site-container"><div class="section-heading"><div><p class="eyebrow blue">Pilihan populer</p><h2>Produk unggulan</h2></div><a href="{{ route('catalog') }}">Lihat semua <span>→</span></a></div><div class="product-grid">@foreach($products->take(4) as $product) @include('partials.product-card', ['product'=>$product]) @endforeach</div></div></section>
<section class="promo-band"><div class="site-container promo-band-grid"><div class="promo-poster"><small>DMT PRINT</small><strong>CETAK<br>UNTUK<br>USAHA</strong><span>Harga bersahabat</span></div><div><p class="eyebrow">Solusi UMKM</p><h2>Buat brand Anda tampil lebih profesional</h2><p>Siapkan kebutuhan promosi dan kemasan dengan pilihan bahan, ukuran, dan finishing yang fleksibel.</p><div class="benefit-row"><span><b>01</b>Konsultasi produk</span><span><b>02</b>Estimasi transparan</span><span><b>03</b>Pengerjaan terpantau</span></div><a href="{{ route('catalog') }}" class="button button-primary">Jelajahi Produk</a></div></div></section>
<section class="page-section"><div class="site-container"><div class="section-heading"><div><p class="eyebrow blue">Lengkapi kebutuhan Anda</p><h2>Produk lainnya</h2></div></div><div class="product-grid">@foreach($products->skip(4)->take(4) as $product) @include('partials.product-card', ['product'=>$product]) @endforeach</div></div></section>
<section class="how-section"><div class="site-container"><div class="section-heading centered"><div><p class="eyebrow blue">Mudah dan jelas</p><h2>Cara memesan</h2></div></div><div class="steps-grid"><article><span>1</span><h3>Pilih produk</h3><p>Tentukan ukuran, bahan, finishing, dan jumlah.</p></article><article><span>2</span><h3>Kirim detail</h3><p>Isi data serta unggah desain atau tautan file.</p></article><article><span>3</span><h3>Konfirmasi harga</h3><p>Admin memeriksa dan menetapkan harga final.</p></article><article><span>4</span><h3>Produksi</h3><p>Pantau status sampai siap diambil atau dikirim.</p></article></div></div></section>
@endsection
