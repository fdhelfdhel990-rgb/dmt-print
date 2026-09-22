<footer class="store-footer">
    <div class="site-container footer-grid">
        <section><h3>DMT Print</h3><p>Layanan cetak untuk kebutuhan sekolah, usaha, organisasi, dan acara Anda.</p><div class="footer-links"><a href="{{ route('home') }}">Tentang kami</a><a href="{{ route('catalog') }}">Katalog</a><a href="{{ route('orders.track') }}">Status pesanan</a></div></section>
        <section><h3>Kontak</h3><p>{{ $siteBusiness['address'] }}</p><p>{{ $siteBusiness['opening_hours'] }}</p><a href="#">WhatsApp: {{ $siteBusiness['phone'] }}</a></section>
        <section><h3>Metode Pembayaran</h3><div class="payment-logos"><span>{{ $sitePayment['bank_name'] }}</span><span>QRIS</span><span>Cash</span></div><p>{{ $sitePayment['qris_note'] }}</p></section>
        <section><h3>Media Sosial</h3><div class="footer-social"><a href="{{ $siteSocial['instagram'] ?? '#' }}">Instagram</a><a href="{{ $siteSocial['tiktok'] ?? '#' }}">TikTok</a><a href="{{ $siteSocial['youtube'] ?? '#' }}">YouTube</a></div><p class="footer-note">Harga yang tampil merupakan estimasi dan dapat disesuaikan setelah pemeriksaan.</p></section>
    </div>
    <div class="footer-bottom"><div class="site-container">&copy; {{ date('Y') }} Darul Muttaqien Printing. Seluruh hak dilindungi.</div></div>
</footer>
