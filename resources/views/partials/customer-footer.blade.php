<footer class="store-footer">
    <div class="site-container footer-grid">
        <section><h3>DMT Print</h3><p>Layanan cetak untuk kebutuhan sekolah, usaha, organisasi, dan acara Anda.</p><div class="footer-links"><a href="{{ route('home') }}">Tentang kami</a><a href="{{ route('catalog') }}">Katalog</a><a href="{{ route('orders.track') }}">Status pesanan</a></div></section>
        <section><h3>Kontak</h3><p>{{ $siteBusiness['address'] }}</p><p>{{ $siteBusiness['opening_hours'] }}</p><a href="#">WhatsApp: {{ $siteBusiness['phone'] }}</a></section>
        <section><h3>Metode Pembayaran</h3><div class="payment-logos"><span>Transfer</span><span>QRIS</span><span>Cash</span></div><p>Metode pembayaran aktif ditampilkan pada halaman pembayaran pelanggan.</p></section>
        <section><h3>Media Sosial</h3><div class="footer-social">@if(($siteSocial['show_instagram'] ?? false) && filled($siteSocial['instagram'] ?? null))<a href="{{ $siteSocial['instagram'] }}" target="_blank" rel="noopener noreferrer">Instagram</a>@endif @if(($siteSocial['show_tiktok'] ?? false) && filled($siteSocial['tiktok'] ?? null))<a href="{{ $siteSocial['tiktok'] }}" target="_blank" rel="noopener noreferrer">TikTok</a>@endif</div><p class="footer-note">Harga yang tampil merupakan estimasi dan dapat disesuaikan setelah pemeriksaan.</p></section>
    </div>
    <div class="footer-bottom"><div class="site-container">&copy; {{ date('Y') }} Darul Muttaqien Printing. Seluruh hak dilindungi.</div></div>
</footer>
