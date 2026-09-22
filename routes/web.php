<?php

use App\Actions\AdjustInventoryAction;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\StorefrontController;
use App\Models\CashbookEntry;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
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
Route::get('/status-pesanan', [OrderTrackingController::class, 'show'])->name('orders.status');
Route::post('/pesanan/{order}/setujui', [OrderTrackingController::class, 'approve'])->name('orders.approve');
Route::post('/pesanan/{order}/revisi', [OrderTrackingController::class, 'revise'])->name('orders.revise');
Route::post('/pesanan/{order}/pembayaran', [OrderTrackingController::class, 'payment'])->name('orders.payment');

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
    Route::get('/pesanan/{order?}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/harga', [AdminOrderController::class, 'price'])->name('orders.price');
    Route::post('/pesanan/{order}/produksi', [AdminOrderController::class, 'production'])->name('orders.production');
    Route::post('/pembayaran/{payment}/verifikasi', [AdminOrderController::class, 'verifyPayment'])->name('payments.verify');
    Route::post('/pembayaran/{payment}/tolak', [AdminOrderController::class, 'rejectPayment'])->name('payments.reject');
    Route::post('/pembayaran/{payment}/batalkan-verifikasi', [AdminOrderController::class, 'cancelPaymentVerification'])->name('payments.cancel-verification');
    Route::get('/file-pesanan/{file}/download', [AdminOrderController::class, 'download'])->name('order-files.download');
    Route::get('/produk', [AdminProductController::class, 'index'])->name('products');
    Route::get('/produk/tambah', [AdminProductController::class, 'create'])->name('products.create');
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
    Route::get('/buku-kas', fn () => view('admin.cashbook', [
        'entries' => CashbookEntry::query()->latest()->paginate(20),
        'cashIn' => CashbookEntry::query()->where('direction', 'in')->whereNull('reversed_at')->sum('amount'),
        'cashOut' => CashbookEntry::query()->where('direction', 'out')->whereNull('reversed_at')->sum('amount'),
    ]))->name('cashbook');
    Route::view('/tampilan-web', 'admin.appearance')->name('appearance');
    Route::view('/pengaturan', 'admin.settings')->name('settings');
});
