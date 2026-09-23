<?php

namespace Tests\Feature;

use App\Actions\RecordPaymentAction;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductMediaPaymentInvoiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_uploaded_product_image_renders_across_customer_pages_and_order_snapshot(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/stiker.jpg', 'image-content');
        $category = Category::factory()->create(['name' => 'Stiker', 'slug' => 'stiker']);
        $product = Product::factory()->for($category)->create(['name' => 'Stiker Foto', 'slug' => 'stiker-foto', 'image_path' => 'products/stiker.jpg', 'base_price' => 25000]);

        $this->get(route('home'))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
        $this->get(route('catalog', ['kategori' => 'stiker']))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
        $this->get(route('product.show', $product))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
        $this->post(route('cart.store', $product), ['quantity' => 2])->assertRedirect(route('cart'));
        $this->get(route('cart'))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
        $this->get(route('checkout.recipient'))->assertOk()->assertSee('/storage/products/stiker.jpg', false);

        $this->post(route('checkout.store'), ['name' => 'Nadia', 'phone' => '081234567890', 'fulfillment_method' => 'pickup', 'approved' => '1'])->assertRedirect();

        $order = Order::query()->with('items')->firstOrFail();
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_image_disk' => 'public', 'product_image_path' => 'products/stiker.jpg']);
        Storage::disk('public')->put('products/pengganti.jpg', 'new-image-content');
        $product->update(['is_active' => false, 'image_path' => 'products/pengganti.jpg']);

        $this->get(route('orders.success', ['order' => $order->public_token]))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
        $this->get(route('orders.status', ['order_number' => $order->order_number, 'phone' => $order->customer->phone]))->assertOk()->assertSee('/storage/products/stiker.jpg', false);

        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $this->actingAs($admin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
    }

    public function test_missing_product_image_uses_local_placeholder(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create(['image_path' => 'products/missing.jpg']);

        $this->get(route('product.show', $product))->assertOk()->assertSee('images/placeholders/product.svg', false);
    }

    public function test_replacing_product_image_deletes_old_file_safely(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create(['image_path' => 'products/old.jpg']);
        Storage::disk('public')->put('products/old.jpg', 'old');

        $this->actingAs($admin)->patch(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'base_price' => $product->base_price,
            'unit' => $product->unit,
            'minimum_order' => $product->minimum_order,
            'stock_on_hand' => $product->stock_on_hand,
            'stock_minimum' => $product->stock_minimum,
            'tone' => $product->tone,
            'sort_order' => $product->sort_order,
            'is_active' => '1',
            'image' => UploadedFile::fake()->create('new.jpg', 12, 'image/jpeg'),
        ])->assertRedirect();

        Storage::disk('public')->assertMissing('products/old.jpg');
        Storage::disk('public')->assertExists($product->refresh()->image_path);
    }

    public function test_admin_can_download_private_payment_proof_and_access_is_protected(): void
    {
        Storage::fake('local');
        $order = Order::factory()->create();
        $payment = app(RecordPaymentAction::class)->execute($order, 'qris', 'full', 50000, UploadedFile::fake()->create('Bukti Bayar.JPG', 16, 'image/jpeg'));
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->get(route('admin.payments.proof.download', $payment))->assertRedirect(route('admin.login'));
        $inactiveAdmin = User::factory()->create(['is_admin' => true, 'is_active' => false]);
        $this->actingAs($inactiveAdmin)->get(route('admin.payments.proof.download', $payment))->assertForbidden();

        $response = $this->actingAs($admin)->get(route('admin.payments.proof.download', $payment));
        $response->assertDownload('bukti-bayar.jpg');
        $this->assertStringNotContainsString($payment->proof_path, $response->headers->get('content-disposition', ''));
        $this->assertDatabaseHas('activity_logs', ['event' => 'payment.proof_downloaded', 'subject_type' => Order::class, 'subject_id' => $order->id]);
        $this->actingAs($admin)->get(route('admin.payments.proof.preview', $payment))->assertOk();
    }

    public function test_payment_proof_download_rejects_missing_or_public_files(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $withoutFile = Payment::factory()->create();
        $missingFile = Payment::factory()->create(['proof_disk' => 'local', 'proof_path' => 'payment-proofs/missing.jpg', 'proof_original_name' => 'missing.jpg']);
        $publicFile = Payment::factory()->create(['proof_disk' => 'public', 'proof_path' => 'payment-methods/qris.png', 'proof_original_name' => 'qris.png']);

        $this->actingAs($admin)->get(route('admin.payments.proof.download', $withoutFile))->assertNotFound();
        $this->actingAs($admin)->get(route('admin.payments.proof.download', $missingFile))->assertNotFound();
        $this->actingAs($admin)->get(route('admin.payments.proof.download', $publicFile))->assertNotFound();
    }

    public function test_invoice_displays_snapshots_totals_and_no_internal_notes_or_selects(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/invoice.jpg', 'image-content');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $otherOrder = Order::factory()->create(['order_number' => 'DMT-OTHER']);
        $order = Order::factory()->create(['order_number' => 'DMT-260923-AB12', 'final_total' => 120000, 'estimated_subtotal' => 100000, 'estimated_total' => 120000, 'shipping_cost' => 20000, 'amount_paid' => 60000, 'internal_note' => 'Catatan rahasia admin', 'customer_note' => 'Tolong cepat']);
        $item = OrderItem::factory()->for($order)->create(['product_name' => 'Banner Invoice', 'quantity' => 2, 'unit' => 'pcs', 'unit_estimate' => 50000, 'subtotal' => 100000, 'product_image_disk' => 'public', 'product_image_path' => 'products/invoice.jpg']);
        OrderItemOption::create(['order_item_id' => $item->id, 'option_name' => 'Ukuran', 'option_value' => 'A3', 'price_adjustment' => 0]);
        Payment::factory()->for($order)->create(['status' => 'verified', 'amount' => 60000]);

        $this->get(route('admin.orders.invoice', $order))->assertRedirect(route('admin.login'));

        $this->actingAs($admin)->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('INV-DMT-260923-AB12')
            ->assertSee('Darul Muttaqien Printing')
            ->assertSee($order->customer->name)
            ->assertSee('Banner Invoice')
            ->assertSee('Ukuran: A3')
            ->assertSee('/storage/products/invoice.jpg', false)
            ->assertSee('Rp 120.000')
            ->assertSee('Rp 60.000')
            ->assertDontSee('Catatan rahasia admin')
            ->assertDontSee('<select', false)
            ->assertDontSee($otherOrder->order_number);
    }

    public function test_order_pages_show_option_snapshot_without_reconfiguration_and_payment_ignores_fake_options(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('payment-methods/qris.png', 'qris');
        $product = Product::factory()->create(['base_price' => 100000]);
        $option = ProductOption::create(['product_id' => $product->id, 'name' => 'Ukuran', 'is_required' => true, 'is_active' => true]);
        $value = ProductOptionValue::create(['product_option_id' => $option->id, 'name' => 'A3', 'price_adjustment' => 20000, 'is_active' => true]);

        $this->get(route('product.show', $product))->assertOk()->assertSee('name="options['.$option->id.']"', false);
        $this->post(route('cart.store', $product), ['quantity' => 1, 'options' => [$option->id => $value->id]]);

        $this->get(route('checkout.recipient'))
            ->assertOk()
            ->assertSee('Ukuran: A3')
            ->assertDontSee('name="options['.$option->id.']"', false);

        $this->post(route('checkout.store'), ['name' => 'Nadia', 'phone' => '081234567890', 'fulfillment_method' => 'pickup', 'approved' => '1'])->assertRedirect();
        $order = Order::query()->with('items.options')->firstOrFail();
        $order->update(['status' => 'waiting_payment', 'final_total' => 120000, 'amount_paid' => 0]);
        PaymentMethod::factory()->create(['type' => 'qris', 'name' => 'QRIS DMT', 'image_path' => 'payment-methods/qris.png', 'is_active' => true]);

        $this->get(route('orders.status', ['order_number' => $order->order_number, 'phone' => $order->customer->phone]))
            ->assertOk()
            ->assertSee('Ukuran: A3')
            ->assertDontSee('name="options['.$option->id.']"', false);

        $this->post(route('orders.payment', $order), [
            'method' => 'qris',
            'payment_type' => 'full',
            'options' => [$option->id => 999999],
            'amount' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'amount' => 120000]);
    }
}
