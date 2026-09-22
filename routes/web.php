<?php

use Illuminate\Support\Facades\Route;

$categories = ['Merchandise & Souvenir', 'Print Warna', 'Stiker', 'Packaging UMKM', 'Poster', 'Kalender', 'Banner', 'Kartu Nama'];
$products = [
    ['name' => 'Cetak Stiker Vinyl', 'category' => 'Stiker', 'price' => 35000, 'unit' => 'lembar', 'tone' => 'cyan', 'tag' => 'Terlaris'],
    ['name' => 'Kartu Nama Premium', 'category' => 'Kartu Nama', 'price' => 65000, 'unit' => 'box', 'tone' => 'navy', 'tag' => 'Favorit'],
    ['name' => 'X-Banner Indoor', 'category' => 'Banner', 'price' => 115000, 'unit' => 'set', 'tone' => 'yellow', 'tag' => ''],
    ['name' => 'Brosur A5 Full Color', 'category' => 'Print Warna', 'price' => 45000, 'unit' => '100 lembar', 'tone' => 'magenta', 'tag' => 'Cepat'],
    ['name' => 'Stempel Flash K3', 'category' => 'Stempel', 'price' => 95000, 'unit' => 'pcs', 'tone' => 'red', 'tag' => ''],
    ['name' => 'Paper Bag Custom', 'category' => 'Packaging UMKM', 'price' => 8500, 'unit' => 'pcs', 'tone' => 'green', 'tag' => ''],
    ['name' => 'Tumbler Custom', 'category' => 'Merchandise', 'price' => 75000, 'unit' => 'pcs', 'tone' => 'blue', 'tag' => 'Baru'],
    ['name' => 'Poster A3+', 'category' => 'Poster', 'price' => 12000, 'unit' => 'lembar', 'tone' => 'orange', 'tag' => ''],
];

Route::view('/', 'customer.home', compact('categories', 'products'))->name('home');
Route::view('/katalog', 'customer.catalog', compact('categories', 'products'))->name('catalog');
Route::view('/produk/stempel-flash-k3', 'customer.product', compact('categories', 'products'))->name('product.show');
Route::view('/keranjang', 'customer.cart', compact('categories', 'products'))->name('cart');
Route::view('/checkout/penerima', 'customer.checkout-recipient', compact('categories', 'products'))->name('checkout.recipient');
Route::view('/checkout/pembayaran', 'customer.checkout-payment', compact('categories', 'products'))->name('checkout.payment');
Route::view('/cek-pesanan', 'customer.track', compact('categories', 'products'))->name('orders.track');
Route::view('/status-pesanan', 'customer.order-status', compact('categories', 'products'))->name('orders.status');

Route::view('/admin', 'admin.login')->name('admin.login');
Route::prefix('admin-preview')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/pesanan', 'admin.orders')->name('orders');
    Route::view('/pesanan/DMT-240901-A12B', 'admin.order-detail')->name('orders.show');
    Route::view('/produk', 'admin.products')->name('products');
    Route::view('/produk/tambah', 'admin.product-form')->name('products.create');
    Route::view('/stok', 'admin.stock')->name('stock');
    Route::view('/buku-kas', 'admin.cashbook')->name('cashbook');
    Route::view('/tampilan-web', 'admin.appearance')->name('appearance');
    Route::view('/pengaturan', 'admin.settings')->name('settings');
});
