<?php

it('renders every customer preview page', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    '/',
    '/katalog',
    '/produk/stempel-flash-k3',
    '/keranjang',
    '/checkout/penerima',
    '/checkout/pembayaran',
    '/cek-pesanan',
    '/status-pesanan',
]);

it('renders every admin preview page', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    '/admin',
    '/admin-preview',
    '/admin-preview/pesanan',
    '/admin-preview/pesanan/DMT-240901-A12B',
    '/admin-preview/produk',
    '/admin-preview/produk/tambah',
    '/admin-preview/stok',
    '/admin-preview/buku-kas',
    '/admin-preview/tampilan-web',
    '/admin-preview/pengaturan',
]);
