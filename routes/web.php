<?php

use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
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
Route::get('/keranjang', [CartController::class, 'index'])->name('cart');
Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/keranjang/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{key}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/keranjang', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/checkout/penerima', [CheckoutController::class, 'create'])->name('checkout.recipient');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/pesanan/selesai/{order:public_token}', [CheckoutController::class, 'success'])->name('orders.success');
Route::view('/cek-pesanan', 'customer.track', compact('categories', 'products'))->name('orders.track');
Route::view('/status-pesanan', 'customer.order-status', compact('categories', 'products'))->name('orders.status');

Route::get('/admin', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('admin.login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest', 'throttle:6,1'])->name('admin.login.store');
Route::prefix('admin-preview')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders');
    Route::get('/pesanan/{order?}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/file-pesanan/{file}/download', [AdminOrderController::class, 'download'])->name('order-files.download');
    Route::get('/produk', [AdminProductController::class, 'index'])->name('products');
    Route::get('/produk/tambah', [AdminProductController::class, 'create'])->name('products.create');
    Route::view('/stok', 'admin.stock')->name('stock');
    Route::view('/buku-kas', 'admin.cashbook')->name('cashbook');
    Route::view('/tampilan-web', 'admin.appearance')->name('appearance');
    Route::view('/pengaturan', 'admin.settings')->name('settings');
});
