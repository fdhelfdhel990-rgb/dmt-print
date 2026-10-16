<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\CashbookEntry;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Support\UploadDisk;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPermanentDeletionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_permanently_delete_order_and_its_storage_files(): void
    {
        Storage::fake(UploadDisk::private());
        Storage::fake(UploadDisk::public());

        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create(['order_number' => 'DMT-TEST-DEL']);
        $item = OrderItem::factory()->for($order)->create();

        $file = OrderFile::create([
            'order_item_id' => $item->id,
            'disk' => UploadDisk::private(),
            'path' => 'orders/designs/customer-design.pdf',
            'original_name' => 'customer-design.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
        ]);
        Storage::disk(UploadDisk::private())->put('orders/designs/customer-design.pdf', 'fake-design-content');

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_type' => 'full',
            'method' => 'bank_transfer',
            'amount' => 100000,
            'status' => 'pending',
            'proof_disk' => UploadDisk::private(),
            'proof_path' => 'payments/proof-receipt.jpg',
            'proof_original_name' => 'proof-receipt.jpg',
            'proof_mime_type' => 'image/jpeg',
            'proof_size' => 2048,
        ]);
        Storage::disk(UploadDisk::private())->put('payments/proof-receipt.jpg', 'fake-proof-content');

        $cashbook = CashbookEntry::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'user_id' => $admin->id,
            'entry_date' => now(),
            'direction' => 'income',
            'category' => 'Pembayaran Pesanan',
            'amount' => 100000,
            'reference' => 'PAY-'.$payment->id,
            'source' => 'system',
        ]);

        $order->activityLogs()->create([
            'event' => 'order.created',
            'user_id' => $admin->id,
        ]);

        Storage::disk(UploadDisk::private())->assertExists('orders/designs/customer-design.pdf');
        Storage::disk(UploadDisk::private())->assertExists('payments/proof-receipt.jpg');

        $response = $this->actingAs($admin)->delete(route('admin.orders.destroy', $order));

        $response->assertRedirect(route('admin.orders'));
        $response->assertSessionHas('status');

        // Check storage cleanup
        Storage::disk(UploadDisk::private())->assertMissing('orders/designs/customer-design.pdf');
        Storage::disk(UploadDisk::private())->assertMissing('payments/proof-receipt.jpg');

        // Check database cleanup
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('order_files', ['id' => $file->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
        $this->assertDatabaseMissing('cashbook_entries', ['id' => $cashbook->id]);
    }

    public function test_admin_can_permanently_delete_product_and_its_image(): void
    {
        Storage::fake(UploadDisk::public());

        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Stempel Custom',
            'image_path' => 'products/stempel-test.png',
        ]);
        Storage::disk(UploadDisk::public())->put('products/stempel-test.png', 'fake-image');

        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products'));
        Storage::disk(UploadDisk::public())->assertMissing('products/stempel-test.png');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_toggle_product_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->patch(route('admin.products.status', $product));
        $this->assertFalse($product->refresh()->is_active);

        $this->actingAs($admin)->patch(route('admin.products.status', $product));
        $this->assertTrue($product->refresh()->is_active);
    }

    public function test_admin_can_delete_category_without_products_and_cleans_image(): void
    {
        Storage::fake(UploadDisk::public());

        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create([
            'image_path' => 'categories/cat-test.jpg',
        ]);
        Storage::disk(UploadDisk::public())->put('categories/cat-test.jpg', 'fake-cat-image');

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories'));
        Storage::disk(UploadDisk::public())->assertMissing('categories/cat-test.jpg');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_active_products(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertSessionHasErrors('category');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_permanently_delete_banner_and_its_image(): void
    {
        Storage::fake(UploadDisk::public());

        $admin = User::factory()->create(['is_admin' => true]);
        $banner = Banner::create([
            'title' => 'Banner Promo',
            'image_path' => 'banners/banner-test.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Storage::disk(UploadDisk::public())->put('banners/banner-test.jpg', 'fake-banner-image');

        $response = $this->actingAs($admin)->delete(route('admin.banners.destroy', $banner));

        $response->assertRedirect();
        Storage::disk(UploadDisk::public())->assertMissing('banners/banner-test.jpg');
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    public function test_admin_can_permanently_delete_manual_cashbook_entry(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $entry = CashbookEntry::create([
            'user_id' => $admin->id,
            'entry_date' => now(),
            'direction' => 'expense',
            'category' => 'Beli Kertas',
            'amount' => 50000,
            'reference' => 'KAS-MANUAL-1',
            'source' => 'manual',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.cashbook.destroy', $entry));

        $response->assertRedirect();
        $this->assertDatabaseMissing('cashbook_entries', ['id' => $entry->id]);
    }

    public function test_admin_can_permanently_delete_other_admin_account(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true]);
        $staffAdmin = User::factory()->create(['is_admin' => true, 'name' => 'Staff Admin']);

        // Cannot delete self
        $this->actingAs($superAdmin)->delete(route('admin.admins.destroy', $superAdmin))
            ->assertSessionHasErrors('admin');
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);

        // Can delete other admin
        $this->actingAs($superAdmin)->delete(route('admin.admins.destroy', $staffAdmin))
            ->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $staffAdmin->id]);
    }
}
