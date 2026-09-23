<?php

use App\Actions\AdjustInventoryAction;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CashbookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\StorefrontController;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
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
Route::get('/cek-pesanan', [OrderTrackingController::class, 'create'])->name('orders.track');
Route::get('/status-pesanan', [OrderTrackingController::class, 'show'])->middleware('throttle:30,1')->name('orders.status');
Route::post('/pesanan/{order}/setujui', [OrderTrackingController::class, 'approve'])->middleware('throttle:10,1')->name('orders.approve');
Route::post('/pesanan/{order}/revisi', [OrderTrackingController::class, 'revise'])->middleware('throttle:10,1')->name('orders.revise');
Route::post('/pesanan/{order}/pembayaran', [OrderTrackingController::class, 'payment'])->middleware('throttle:10,1')->name('orders.payment');

Route::get('/admin', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('admin.login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest', 'throttle:6,1'])->name('admin.login.store');
Route::prefix('admin-preview')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', fn () => view('admin.dashboard', [
        'newOrdersCount' => Order::query()->where('status', 'pending_review')->count(),
        'pendingPaymentsCount' => Payment::query()->where('status', 'pending')->count(),
        'productionCount' => Order::query()->where('status', 'production')->count(),
        'lowStockCount' => Product::query()->whereColumn('stock_on_hand', '<=', 'stock_minimum')->count(),
        'recentOrders' => Order::query()->with('customer', 'items')->latest()->limit(5)->get(),
    ]))->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders');
    Route::get('/pesanan/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/pesanan/{order?}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/harga', [AdminOrderController::class, 'price'])->name('orders.price');
    Route::post('/pesanan/{order}/produksi', [AdminOrderController::class, 'production'])->name('orders.production');
    Route::post('/pesanan/{order}/arsip', [AdminOrderController::class, 'archive'])->name('orders.archive');
    Route::post('/pesanan/{order}/pulihkan', [AdminOrderController::class, 'restore'])->name('orders.restore');
    Route::post('/pembayaran/{payment}/verifikasi', [AdminOrderController::class, 'verifyPayment'])->name('payments.verify');
    Route::post('/pembayaran/{payment}/tolak', [AdminOrderController::class, 'rejectPayment'])->name('payments.reject');
    Route::post('/pembayaran/{payment}/batalkan-verifikasi', [AdminOrderController::class, 'cancelPaymentVerification'])->name('payments.cancel-verification');
    Route::get('/pembayaran/{payment}/bukti/lihat', [AdminOrderController::class, 'previewPaymentProof'])->name('payments.proof.preview');
    Route::get('/pembayaran/{payment}/bukti/download', [AdminOrderController::class, 'downloadPaymentProof'])->name('payments.proof.download');
    Route::get('/file-pesanan/{file}/download', [AdminOrderController::class, 'download'])->name('order-files.download');
    Route::get('/produk', [AdminProductController::class, 'index'])->name('products');
    Route::get('/produk/tambah', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/produk', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/produk/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::patch('/produk/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/produk/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/kategori', [CategoryController::class, 'index'])->name('categories');
    Route::get('/kategori/tambah', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/kategori/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::match(['put', 'patch'], '/kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/kategori/{category}/status', [CategoryController::class, 'status'])->name('categories.status');
    Route::get('/stok', fn () => view('admin.stock', [
        'products' => Product::query()->withMax('inventoryMovements', 'created_at')->orderBy('name')->get(),
        'lowStockCount' => Product::query()->whereColumn('stock_on_hand', '<=', 'stock_minimum')->count(),
        'movements' => InventoryMovement::query()->with('product')->latest()->limit(10)->get(),
    ]))->name('stock');
    Route::post('/stok/{product}/mutasi', function (Request $request, Product $product) {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'not_in:0'], 'note' => ['nullable', 'string', 'max:1000']]);
        app(AdjustInventoryAction::class)->execute($product, (int) $validated['quantity'], (int) $validated['quantity'] > 0 ? 'manual_in' : 'manual_out', 'manual:'.uniqid('', true), $request->user(), $validated['note'] ?? null);

        return back()->with('status', 'Mutasi stok disimpan.');
    })->name('stock.adjust');
    Route::get('/buku-kas', [CashbookController::class, 'index'])->name('cashbook');
    Route::post('/buku-kas', [CashbookController::class, 'store'])->name('cashbook.store');
    Route::patch('/buku-kas/{entry}', [CashbookController::class, 'update'])->name('cashbook.update');
    Route::delete('/buku-kas/{entry}', [CashbookController::class, 'destroy'])->name('cashbook.destroy');
    Route::get('/buku-kas/export/csv', [CashbookController::class, 'export'])->name('cashbook.export');
    Route::get('/tampilan-web', [SiteSettingController::class, 'edit'])->name('appearance');
    Route::post('/tampilan-web/banner', [BannerController::class, 'store'])->name('banners.store');
    Route::patch('/tampilan-web/banner/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/tampilan-web/banner/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::patch('/tampilan-web/profil', [SiteSettingController::class, 'updateBusiness'])->name('appearance.business');
    Route::patch('/tampilan-web/pembayaran', [SiteSettingController::class, 'updatePayment'])->name('appearance.payment');
    Route::patch('/tampilan-web/sosial', [SiteSettingController::class, 'updateSocial'])->name('appearance.social');
    Route::patch('/tampilan-web/unggulan', [SiteSettingController::class, 'updateFeatured'])->name('appearance.featured');
    Route::get('/pengaturan', fn () => view('admin.settings', ['admins' => User::query()->where('is_admin', true)->orderBy('name')->get()]))->name('settings');
    Route::post('/pengaturan/admin', [AdminUserController::class, 'store'])->name('admins.store');
    Route::patch('/pengaturan/admin/{user}', [AdminUserController::class, 'update'])->name('admins.update');
    Route::delete('/pengaturan/admin/{user}', [AdminUserController::class, 'destroy'])->name('admins.destroy');
});
