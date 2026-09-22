<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderingWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_product_options_render_and_required_option_is_validated(): void
    {
        [$product, $option, $value] = $this->productWithOption();

        $this->get(route('product.show', $product))->assertOk()->assertSee('Ukuran')->assertSee('A3');
        $this->post(route('cart.store', $product), ['quantity' => 1])->assertSessionHasErrors('options.'.$option->id);
        $this->post(route('cart.store', $product), ['quantity' => 1, 'options' => [$option->id => $value->id]])->assertRedirect(route('cart'));
    }

    public function test_option_from_another_product_and_browser_price_are_rejected(): void
    {
        [$product, $option] = $this->productWithOption();
        [, , $foreignValue] = $this->productWithOption();

        $this->post(route('cart.store', $product), ['quantity' => 1, 'options' => [$option->id => $foreignValue->id]])->assertSessionHasErrors('options');
        $this->post(route('cart.store', $product), ['quantity' => 1, 'price' => 1])->assertSessionHasErrors('price');
    }

    public function test_cart_uses_server_price_and_supports_update_and_remove(): void
    {
        [$product, $option, $value] = $this->productWithOption();
        $key = hash('sha256', $product->id.'|'.collect([$option->id => $value->id])->sortKeys()->toJson());

        $this->post(route('cart.store', $product), ['quantity' => 2, 'options' => [$option->id => $value->id]])->assertRedirect(route('cart'));
        $this->get(route('cart'))->assertSee('Rp 240.000');
        $this->patch(route('cart.update', $key), ['quantity' => 3])->assertSessionHasNoErrors();
        $this->delete(route('cart.destroy', $key))->assertSessionHasNoErrors();
        $this->get(route('cart'))->assertSee('Keranjang masih kosong');
    }

    public function test_inactive_product_and_below_minimum_quantity_are_rejected(): void
    {
        $inactive = Product::factory()->create(['is_active' => false]);
        $minimum = Product::factory()->create(['minimum_order' => 5]);

        $this->post(route('cart.store', $inactive), ['quantity' => 1])->assertNotFound();
        $this->post(route('cart.store', $minimum), ['quantity' => 4])->assertSessionHasErrors('quantity');
    }

    public function test_guest_checkout_creates_snapshots_history_upload_and_clears_cart(): void
    {
        Storage::fake('local');
        $product = Product::factory()->create(['name' => 'Poster Uji', 'base_price' => 50000]);
        $this->post(route('cart.store', $product), ['quantity' => 2]);
        $key = hash('sha256', $product->id.'|[]');

        $response = $this->post(route('checkout.store'), [
            'name' => 'Nadia Putri', 'phone' => '0812-3456-7890', 'fulfillment_method' => 'pickup',
            'approved' => '1', 'design_files' => [$key => UploadedFile::fake()->create('desain.pdf', 10, 'application/pdf')],
        ]);

        $order = Order::with('items.files')->firstOrFail();
        $response->assertRedirect(route('orders.success', ['order' => $order->public_token]));
        $this->assertSame('6281234567890', $order->customer->phone);
        $this->assertSame('Poster Uji', $order->items->first()->product_name);
        $this->assertSame(100000, $order->estimated_total);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'status' => 'pending_review']);
        $this->assertEmpty(session('cart', []));
        Storage::disk('local')->assertExists($order->items->first()->files->first()->path);
    }

    public function test_complete_http_checkout_flow_is_visible_to_admin(): void
    {
        [$product, $option, $value] = $this->productWithOption();

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee(route('cart.store', $product), false);

        $this->post(route('cart.store', $product), [
            'quantity' => 2,
            'options' => [$option->id => $value->id],
        ])->assertRedirect(route('cart'));

        $this->get(route('checkout.recipient'))
            ->assertOk()
            ->assertSee('method="POST"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('type="submit"', false);

        $response = $this->from(route('checkout.recipient'))->post(route('checkout.store'), [
            'name' => 'Nadia Putri',
            'phone' => '0812-3456-7890',
            'email' => 'nadia@example.test',
            'fulfillment_method' => 'pickup',
            'approved' => '1',
        ]);

        $order = Order::query()->with('customer')->firstOrFail();
        $response->assertRedirect(route('orders.success', ['order' => $order->public_token]));
        $this->assertDatabaseHas('customers', ['id' => $order->customer_id, 'name' => 'Nadia Putri']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending_review']);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id]);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'status' => 'pending_review']);
        $this->assertEmpty(session('cart', []));

        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.orders'))->assertOk()->assertSee($order->order_number);
        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee($order->order_number);
    }

    public function test_empty_cart_is_rejected_and_validation_returns_to_checkout(): void
    {
        $this->post(route('checkout.store'), [
            'name' => 'Nadia',
            'phone' => '0812',
            'fulfillment_method' => 'pickup',
            'approved' => '1',
        ])->assertRedirect(route('cart'));

        $product = Product::factory()->create();
        $this->post(route('cart.store', $product), ['quantity' => 1]);
        $this->from(route('checkout.recipient'))->post(route('checkout.store'), [
            'name' => '',
            'phone' => '',
            'fulfillment_method' => 'shipping',
        ])->assertRedirect(route('checkout.recipient'))->assertSessionHasErrors([
            'name', 'phone', 'shipping_address', 'shipping_region', 'approved',
        ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertNotEmpty(session('cart', []));
    }

    public function test_shipping_fields_are_required_but_pickup_does_not_require_them(): void
    {
        $product = Product::factory()->create();
        $this->post(route('cart.store', $product), ['quantity' => 1]);
        $base = ['name' => 'Nadia', 'phone' => '0812', 'approved' => '1'];

        $this->post(route('checkout.store'), $base + ['fulfillment_method' => 'shipping'])->assertSessionHasErrors(['shipping_address', 'shipping_region']);
        $this->post(route('checkout.store'), $base + ['fulfillment_method' => 'pickup'])->assertSessionHasNoErrors();
    }

    public function test_order_files_and_admin_orders_require_admin_access(): void
    {
        Storage::fake('local');
        $product = Product::factory()->create();
        $this->post(route('cart.store', $product), ['quantity' => 1]);
        $key = hash('sha256', $product->id.'|[]');
        $this->post(route('checkout.store'), ['name' => 'Nadia', 'phone' => '0812', 'fulfillment_method' => 'pickup', 'approved' => '1', 'design_files' => [$key => UploadedFile::fake()->create('design.pdf', 10, 'application/pdf')]]);
        $order = Order::with('items.files')->firstOrFail();
        $file = $order->items->first()->files->first();

        $this->get(route('admin.orders'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.order-files.download', $file))->assertRedirect(route('admin.login'));
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.orders'))->assertOk()->assertSee($order->order_number);
        $this->actingAs($admin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('Nadia');
        $this->actingAs($admin)->get(route('admin.order-files.download', $file))->assertDownload('design.pdf');
    }

    public function test_dangerous_upload_is_rejected(): void
    {
        $product = Product::factory()->create();
        $this->post(route('cart.store', $product), ['quantity' => 1]);
        $key = hash('sha256', $product->id.'|[]');
        $this->post(route('checkout.store'), ['name' => 'Nadia', 'phone' => '0812', 'fulfillment_method' => 'pickup', 'approved' => '1', 'design_files' => [$key => UploadedFile::fake()->create('shell.php', 1, 'application/x-php')]])->assertSessionHasErrors('design_files.'.$key);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_numbers_are_unique(): void
    {
        $product = Product::factory()->create();
        foreach ([1, 2] as $iteration) {
            $this->post(route('cart.store', $product), ['quantity' => 1]);
            $this->post(route('checkout.store'), ['name' => 'Customer '.$iteration, 'phone' => '0812'.$iteration, 'fulfillment_method' => 'pickup', 'approved' => '1']);
        }
        $this->assertSame(2, Order::query()->distinct()->count('order_number'));
    }

    public function test_transaction_rolls_back_and_removes_uploaded_file_on_metadata_failure(): void
    {
        Storage::fake('local');
        Schema::table('order_files', fn (Blueprint $table) => $table->unique('original_name'));
        $existingOrder = Order::factory()->create();
        $existingItem = OrderItem::factory()->for($existingOrder)->create();
        $existingItem->files()->create(['disk' => 'local', 'path' => 'existing.pdf', 'original_name' => 'duplicate.pdf', 'mime_type' => 'application/pdf', 'size' => 1]);
        $product = Product::factory()->create();
        $this->post(route('cart.store', $product), ['quantity' => 1]);
        $key = hash('sha256', $product->id.'|[]');
        $before = Order::count();
        $this->from(route('checkout.recipient'))->post(route('checkout.store'), ['name' => 'Nadia', 'phone' => '0812', 'fulfillment_method' => 'pickup', 'approved' => '1', 'design_files' => [$key => UploadedFile::fake()->create('duplicate.pdf', 10, 'application/pdf')]])
            ->assertRedirect(route('checkout.recipient'))
            ->assertSessionHasErrors('checkout');
        $this->assertSame($before, Order::count());
        $this->assertSame([], Storage::disk('local')->allFiles('order-designs'));
        $this->assertNotEmpty(session('cart', []));
    }

    private function productWithOption(): array
    {
        $product = Product::factory()->create(['base_price' => 100000]);
        $option = ProductOption::create(['product_id' => $product->id, 'name' => 'Ukuran', 'is_required' => true, 'is_active' => true]);
        $value = ProductOptionValue::create(['product_option_id' => $option->id, 'name' => 'A3', 'price_adjustment' => 20000, 'is_active' => true]);

        return [$product, $option, $value];
    }
}
