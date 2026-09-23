<?php

namespace Tests\Feature;

use App\Actions\RecordPaymentAction;
use App\Actions\VerifyPaymentAction;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RevisionMediaCashbookArchiveTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_banner_upload_replacement_and_visibility(): void
    {
        Storage::fake('public_uploads');
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'Promo Banner',
            'description' => 'Deskripsi banner',
            'button_label' => 'Pesan',
            'button_url' => '/katalog',
            'sort_order' => 1,
            'is_active' => '1',
            'image' => UploadedFile::fake()->create('banner.webp', 20, 'image/webp'),
        ])->assertRedirect();

        $banner = Banner::firstOrFail();
        Storage::disk('public_uploads')->assertExists($banner->image_path);
        $this->get(route('home'))->assertOk()->assertSee('Promo Banner');

        $oldPath = $banner->image_path;
        $this->actingAs($admin)->patch(route('admin.banners.update', $banner), [
            'title' => 'Banner Baru',
            'sort_order' => 2,
            'is_active' => '1',
            'image' => UploadedFile::fake()->create('banner.png', 20, 'image/png'),
        ])->assertRedirect();

        Storage::disk('public_uploads')->assertMissing($oldPath);
        Storage::disk('public_uploads')->assertExists($banner->refresh()->image_path);
        $banner->update(['is_active' => false]);
        $this->get(route('home'))->assertDontSee('Banner Baru');
        $this->actingAs($admin)->post(route('admin.banners.store'), ['title' => 'Bad', 'sort_order' => 1, 'image' => UploadedFile::fake()->create('bad.php', 1, 'application/x-php')])->assertSessionHasErrors('image');
    }

    public function test_product_category_and_qris_uploads(): void
    {
        Storage::fake('public_uploads');
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create(['name' => 'Aktif', 'slug' => 'aktif']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Produk Gambar',
            'slug' => 'produk-gambar',
            'sku' => 'IMG-1',
            'base_price' => 10000,
            'unit' => 'pcs',
            'minimum_order' => 1,
            'stock_on_hand' => 5,
            'stock_minimum' => 1,
            'tone' => 'blue',
            'sort_order' => 1,
            'is_active' => '1',
            'image' => UploadedFile::fake()->create('produk.jpg', 20, 'image/jpeg'),
        ])->assertRedirect();
        $product = Product::where('sku', 'IMG-1')->firstOrFail();
        Storage::disk('public_uploads')->assertExists($product->image_path);

        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Kategori Baru',
            'slug' => 'kategori-baru',
            'description' => 'Deskripsi',
            'sort_order' => 2,
            'is_active' => '1',
            'image' => UploadedFile::fake()->create('kategori.png', 20, 'image/png'),
        ])->assertRedirect();
        Storage::disk('public_uploads')->assertExists($category->refresh()->image_path);
        $this->actingAs($admin)->post(route('admin.categories.store'), ['name' => 'Duplikat', 'slug' => 'kategori-baru', 'sort_order' => 1])->assertSessionHasErrors('slug');

        $this->actingAs($admin)->patch(route('admin.appearance.payment'), [
            'type' => 'qris',
            'name' => 'QRIS DMT',
            'is_active' => '1',
            'sort_order' => 1,
            'instructions' => 'Scan QRIS',
            'image' => UploadedFile::fake()->create('qris.png', 20, 'image/png'),
        ])->assertRedirect();
        $qris = PaymentMethod::where('type', 'qris')->firstOrFail();
        Storage::disk('public_uploads')->assertExists($qris->image_path);
        $this->actingAs($admin)->patch(route('admin.appearance.payment'), ['type' => 'qris', 'name' => 'QRIS', 'sort_order' => 1, 'image' => UploadedFile::fake()->create('bad.php', 1, 'application/x-php')])->assertSessionHasErrors('image');
    }

    public function test_manual_cashbook_and_payment_verification_are_separate(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create(['final_total' => 100000]);
        $payment = app(RecordPaymentAction::class)->execute($order, 'qris', 'full', 100000);

        app(VerifyPaymentAction::class)->verify($payment, $admin);
        $this->assertDatabaseCount('cashbook_entries', 0);
        app(VerifyPaymentAction::class)->cancelVerification($payment, $admin);
        $this->assertDatabaseCount('cashbook_entries', 0);

        $this->actingAs($admin)->post(route('admin.cashbook.store'), ['entry_date' => now()->format('Y-m-d'), 'direction' => 'income', 'category' => 'Modal', 'amount' => 50000, 'payment_method' => 'cash'])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.cashbook.store'), ['entry_date' => now()->format('Y-m-d'), 'direction' => 'expense', 'category' => 'Tinta', 'amount' => 20000, 'payment_method' => 'cash'])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.cashbook.store'), ['entry_date' => now()->format('Y-m-d'), 'direction' => 'income', 'category' => 'Salah', 'amount' => 0])->assertSessionHasErrors('amount');
        $this->actingAs($admin)->get(route('admin.cashbook.export'))->assertDownload('buku-kas.csv');
        auth()->logout();
        $this->post(route('admin.cashbook.store'), [])->assertRedirect(route('admin.login'));
    }

    public function test_order_archive_restore_and_relations_remain(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $active = Order::factory()->create(['status' => 'production']);
        $completed = Order::factory()->create(['status' => 'completed']);
        OrderItem::factory()->for($completed)->create();

        $this->actingAs($admin)->get(route('admin.orders'))
            ->assertOk()
            ->assertSee($active->order_number)
            ->assertSee($completed->order_number)
            ->assertSee('Arsipkan pesanan '.$completed->order_number)
            ->assertDontSee('Arsipkan pesanan '.$active->order_number);
        $this->actingAs($admin)->get(route('admin.orders.show', $active))
            ->assertOk()
            ->assertSee('Pesanan aktif belum dapat diarsipkan.')
            ->assertDontSee('Arsipkan pesanan '.$active->order_number);
        $this->actingAs($admin)->get(route('admin.orders.show', $completed))
            ->assertOk()
            ->assertSee('Arsipkan pesanan '.$completed->order_number);
        $this->actingAs($admin)->post(route('admin.orders.archive', $active))->assertStatus(422);
        $this->actingAs($admin)->post(route('admin.orders.archive', $completed))->assertRedirect(route('admin.orders'));
        $this->actingAs($admin)->get(route('admin.orders'))->assertDontSee($completed->order_number);
        $this->actingAs($admin)->get(route('admin.orders', ['archived' => 1]))
            ->assertOk()
            ->assertSee($completed->order_number)
            ->assertSee('Pulihkan')
            ->assertDontSee('Arsipkan pesanan '.$completed->order_number);
        $this->actingAs($admin)->get(route('admin.orders.show', $completed->refresh()))
            ->assertOk()
            ->assertSee('Pulihkan')
            ->assertDontSee('Arsipkan pesanan '.$completed->order_number);
        $this->assertSame(1, $completed->items()->count());
        auth()->logout();
        $this->post(route('admin.orders.archive', $completed))->assertRedirect(route('admin.login'));
        $this->actingAs($admin)->post(route('admin.orders.restore', $completed))->assertRedirect();
        $this->assertNull($completed->refresh()->archived_at);
    }

    public function test_social_links_and_mysql_configuration(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->patch(route('admin.appearance.social'), ['instagram' => 'https://instagram.com/dmt', 'tiktok' => 'https://tiktok.com/@dmt', 'show_instagram' => '1', 'show_tiktok' => '1'])->assertRedirect();
        $this->get(route('home'))->assertSee('rel="noopener noreferrer"', false)->assertSee('https://instagram.com/dmt', false)->assertSee('https://tiktok.com/@dmt', false);
        SiteSetting::where('key', 'social')->delete();
        $this->get(route('home'))->assertDontSee('https://instagram.com/dmt', false);
        $this->actingAs($admin)->patch(route('admin.appearance.social'), ['instagram' => 'not-a-url'])->assertSessionHasErrors('instagram');

        $this->assertStringContainsString('DB_CONNECTION=mysql', file_get_contents(base_path('.env.example')));
        foreach (glob(database_path('migrations/*.php')) as $migration) {
            $this->assertStringNotContainsString('sqlite_', strtolower(file_get_contents($migration)));
        }
    }
}
