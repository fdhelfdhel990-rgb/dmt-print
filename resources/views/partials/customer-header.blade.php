<header class="store-header">
    <div class="site-container header-main">
        <a href="{{ route('home') }}" class="brand" aria-label="DMT Print beranda">
            <span class="brand-mark">d</span><span><b>DMT</b> Print<small>Darul Muttaqien Printing</small></span>
        </a>
        <form class="header-search" action="{{ route('catalog') }}">
            <label class="sr-only" for="global-search">Cari produk</label>
            <input id="global-search" name="q" placeholder="Cari produk cetak...">
            <button aria-label="Cari"><span class="icon icon-search"></span></button>
        </form>
        <nav class="header-actions" aria-label="Tautan cepat">
            <a href="#" aria-label="Instagram" class="social-dot">IG</a>
            <a href="#" aria-label="TikTok" class="social-dot">TT</a>
            <a href="{{ route('orders.track') }}" class="track-link"><span class="icon icon-receipt"></span>Cek Pesanan</a>
            <a href="{{ route('cart') }}" class="cart-link"><span class="icon icon-cart"></span><span class="cart-count">2</span></a>
        </nav>
        <button class="mobile-menu-button" type="button" aria-label="Buka menu" aria-expanded="false" data-menu-toggle><span></span><span></span><span></span></button>
    </div>
    <div class="category-nav" data-mobile-menu>
        <div class="site-container category-nav-inner">
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('catalog') }}">Katalog</a>
            @foreach(($categories ?? ['Stiker','Banner','Kartu Nama','Brosur','Merchandise']) as $category)
                <a href="{{ route('catalog', ['category' => is_string($category) ? $category : $category->slug]) }}">{{ is_string($category) ? $category : $category->name }}</a>
            @endforeach
            <a href="{{ route('orders.track') }}" class="mobile-only">Cek Pesanan</a>
        </div>
    </div>
</header>
