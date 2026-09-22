<?php

use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\StorefrontController;
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

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/katalog', [StorefrontController::class, 'catalog'])->name('catalog');
Route::get('/produk/{product}', [StorefrontController::class, 'show'])->name('product.show');
Route::view('/keranjang', 'customer.cart', compact('categories', 'products'))->name('cart');
Route::view('/checkout/penerima', 'customer.checkout-recipient', compact('categories', 'products'))->name('checkout.recipient');
Route::view('/checkout/pembayaran', 'customer.checkout-payment', compact('categories', 'products'))->name('checkout.payment');
Route::view('/cek-pesanan', 'customer.track', compact('categories', 'products'))->name('orders.track');
Route::view('/status-pesanan', 'customer.order-status', compact('categories', 'products'))->name('orders.status');

Route::get('/admin', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('admin.login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest', 'throttle:6,1'])->name('admin.login.store');
Route::prefix('admin-preview')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::view('/pesanan', 'admin.orders')->name('orders');
    Route::view('/pesanan/DMT-240901-A12B', 'admin.order-detail')->name('orders.show');
    Route::get('/produk', [AdminProductController::class, 'index'])->name('products');
    Route::get('/produk/tambah', [AdminProductController::class, 'create'])->name('products.create');
    Route::view('/stok', 'admin.stock')->name('stock');
    Route::view('/buku-kas', 'admin.cashbook')->name('cashbook');
    Route::view('/tampilan-web', 'admin.appearance')->name('appearance');
    Route::view('/pengaturan', 'admin.settings')->name('settings');
});
