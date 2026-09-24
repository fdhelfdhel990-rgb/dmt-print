<?php

namespace Tests\Feature;

use App\Actions\RecordPaymentAction;
use App\Models\Banner;
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
use App\Support\UploadDisk;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductMediaPaymentInvoiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_uploaded_product_image_renders_across_customer_pages_and_order_snapshot(): void
    {
        Storage::fake('public_uploads');
        Storage::disk('public_uploads')->put('products/stiker.jpg', 'image-content');
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
        Storage::disk('public_uploads')->put('products/pengganti.jpg', 'new-image-content');
        $product->update(['is_active' => false, 'image_path' => 'products/pengganti.jpg']);

        $this->get(route('orders.success', ['order' => $order->public_token]))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
        $this->get(route('orders.status', ['order_number' => $order->order_number, 'phone' => $order->customer->phone]))->assertOk()->assertSee('/storage/products/stiker.jpg', false);

        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $this->actingAs($admin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('/storage/products/stiker.jpg', false);
    }

    public function test_upload_disks_are_configured_for_local_development_and_r2_production(): void
    {
        $this->assertSame('public_uploads', UploadDisk::public());
        $this->assertSame('private_uploads', UploadDisk::private());
        $this->assertSame('local', config('filesystems.disks.public_uploads.driver'));
        $this->assertSame('local', config('filesystems.disks.private_uploads.driver'));
        $this->assertSame('/storage/products/sample.jpg', parse_url(Storage::disk(UploadDisk::public())->url('products/sample.jpg'), PHP_URL_PATH));

        config([
            'filesystems.disks.public_uploads.driver' => 's3',
            'filesystems.disks.public_uploads.bucket' => 'dmt-print-public',
            'filesystems.disks.public_uploads.root' => '',
            'filesystems.disks.public_uploads.url' => 'https://pub-b885074c7e424bceb5e8ad163c04ced7.r2.dev',
            'filesystems.disks.private_uploads.driver' => 's3',
            'filesystems.disks.private_uploads.bucket' => 'dmt-print',
            'filesystems.disks.private_uploads.root' => '',
            'filesystems.disks.private_uploads.url' => null,
        ]);
        Storage::forgetDisk('public_uploads');
        Storage::forgetDisk('private_uploads');

        $this->assertSame('https://pub-b885074c7e424bceb5e8ad163c04ced7.r2.dev/products/sample.jpg', Storage::disk(UploadDisk::public())->url('products/sample.jpg'));
        $this->assertSame('https://pub-b885074c7e424bceb5e8ad163c04ced7.r2.dev/products/sample.jpg', UploadDisk::publicUrl('products/sample.jpg'));
        $this->assertSame('dmt-print-public', config('filesystems.disks.public_uploads.bucket'));
        $this->assertSame('dmt-print', config('filesystems.disks.private_uploads.bucket'));
        $this->assertNull(config('filesystems.disks.private_uploads.url'));
    }

    public function test_public_media_path_validation_and_safe_url_resolution(): void
    {
        $this->assertTrue(UploadDisk::isValidPath('products/item.jpg'));
        $this->assertTrue(UploadDisk::isValidPath('banners/promo.png'));
        $this->assertFalse(UploadDisk::isValidPath(null));
        $this->assertFalse(UploadDisk::isValidPath(''));
        $this->assertFalse(UploadDisk::isValidPath('   '));
        $this->assertFalse(UploadDisk::isValidPath('0'));
        $this->assertFalse(UploadDisk::isValidPath(0));
        $this->assertFalse(UploadDisk::isValidPath('null'));
        $this->assertFalse(UploadDisk::isValidPath('undefined'));

        $placeholder = asset('images/placeholders/product.svg');
        $this->assertSame($placeholder, UploadDisk::publicUrl('0', UploadDisk::public(), $placeholder));
        $this->assertSame($placeholder, UploadDisk::publicUrl(null, UploadDisk::public(), $placeholder));
        $this->assertSame($placeholder, UploadDisk::publicUrl('', UploadDisk::public(), $placeholder));
        $this->assertNull(UploadDisk::publicUrl('0'));
        $this->assertNull(UploadDisk::publicUrl(null));
        $this->assertNull(UploadDisk::publicUrl(''));
    }

    public function test_r2_public_media_url_does_not_include_bucket_name_in_path(): void
    {
        config([
            'filesystems.disks.public_uploads.driver' => 's3',
            'filesystems.disks.public_uploads.bucket' => 'dmt-print-public',
            'filesystems.disks.public_uploads.root' => '',
            'filesystems.disks.public_uploads.endpoint' => 'https://example-account-id.r2.cloudflarestorage.com',
            'filesystems.disks.public_uploads.url' => 'https://pub-b885074c7e424bceb5e8ad163c04ced7.r2.dev',
            'filesystems.disks.public_uploads.use_path_style_endpoint' => true,
        ]);
        Storage::forgetDisk('public_uploads');

        $url = UploadDisk::publicUrl('products/stiker-vinyl.jpg');
        $this->assertSame('https://pub-b885074c7e424bceb5e8ad163c04ced7.r2.dev/products/stiker-vinyl.jpg', $url);
        $this->assertStringNotContainsString('dmt-print-public', $url);
    }

    public function test_filesystems_endpoint_sanitizer_removes_duplicate_bucket_suffix(): void
    {
        $config = require config_path('filesystems.php');
        $this->assertIsArray($config);
        $this->assertArrayHasKey('disks', $config);
        $this->assertArrayHasKey('public_uploads', $config['disks']);

        $originalEndpoint = getenv('PUBLIC_AWS_ENDPOINT');
        $originalBucket = getenv('PUBLIC_AWS_BUCKET');

        try {
            // Test configuration resolution with duplicate bucket suffix
            putenv('PUBLIC_AWS_ENDPOINT=https://account123.r2.cloudflarestorage.com/dmt-print-public');
            putenv('PUBLIC_AWS_BUCKET=dmt-print-public');
            $freshConfig = require config_path('filesystems.php');
            $this->assertSame('https://account123.r2.cloudflarestorage.com', $freshConfig['disks']['public_uploads']['endpoint']);

            // Test configuration resolution with clean endpoint
            putenv('PUBLIC_AWS_ENDPOINT=https://account123.r2.cloudflarestorage.com');
            $freshConfig = require config_path('filesystems.php');
            $this->assertSame('https://account123.r2.cloudflarestorage.com', $freshConfig['disks']['public_uploads']['endpoint']);

            // Test configuration resolution with trailing slash
            putenv('PUBLIC_AWS_ENDPOINT=https://account123.r2.cloudflarestorage.com/');
            $freshConfig = require config_path('filesystems.php');
            $this->assertSame('https://account123.r2.cloudflarestorage.com', $freshConfig['disks']['public_uploads']['endpoint']);
        } finally {
            if ($originalEndpoint !== false && $originalEndpoint !== null) {
                putenv("PUBLIC_AWS_ENDPOINT={$originalEndpoint}");
                $_ENV['PUBLIC_AWS_ENDPOINT'] = $originalEndpoint;
            } else {
                putenv('PUBLIC_AWS_ENDPOINT=');
                unset($_ENV['PUBLIC_AWS_ENDPOINT']);
            }

            if ($originalBucket !== false && $originalBucket !== null) {
                putenv("PUBLIC_AWS_BUCKET={$originalBucket}");
                $_ENV['PUBLIC_AWS_BUCKET'] = $originalBucket;
            } else {
                putenv('PUBLIC_AWS_BUCKET=');
                unset($_ENV['PUBLIC_AWS_BUCKET']);
            }
        }
    }

    public function test_new_uploads_store_relative_paths_and_resolve_to_public_cdn_url(): void
    {
        Storage::fake('public_uploads');

        // Simulate upload for product, banner, and category
        $productFile = UploadedFile::fake()->create('brosur-baru.jpg', 20, 'image/jpeg');
        $productPath = $productFile->store('products', 'public_uploads');

        $bannerFile = UploadedFile::fake()->create('banner-promo.png', 30, 'image/png');
        $bannerPath = $bannerFile->store('banners', 'public_uploads');

        $categoryFile = UploadedFile::fake()->create('kategori-stiker.jpg', 15, 'image/jpeg');
        $categoryPath = $categoryFile->store('categories', 'public_uploads');

        // Path stored in DB must be: products/{filename}, banners/{filename}, categories/{filename}
        $this->assertStringStartsWith('products/', $productPath);
        $this->assertStringStartsNotWith('dmt-print-public/', $productPath);
        $this->assertStringStartsNotWith('http', $productPath);

        $this->assertStringStartsWith('banners/', $bannerPath);
        $this->assertStringStartsNotWith('dmt-print-public/', $bannerPath);
        $this->assertStringStartsNotWith('http', $bannerPath);

        $this->assertStringStartsWith('categories/', $categoryPath);
        $this->assertStringStartsNotWith('dmt-print-public/', $categoryPath);
        $this->assertStringStartsNotWith('http', $categoryPath);

        // Files must exist on disk
        Storage::disk('public_uploads')->assertExists($productPath);
        Storage::disk('public_uploads')->assertExists($bannerPath);
        Storage::disk('public_uploads')->assertExists($categoryPath);

        // Public URL resolution under S3 configuration (simulates production R2 env)
        config([
            'filesystems.disks.public_uploads.driver' => 's3',
            'filesystems.disks.public_uploads.bucket' => 'dmt-print-public',
            'filesystems.disks.public_uploads.root' => '',
            'filesystems.disks.public_uploads.endpoint' => 'https://account-id.r2.cloudflarestorage.com',
            'filesystems.disks.public_uploads.url' => 'https://pub-cdn.r2.dev',
        ]);
        Storage::forgetDisk('public_uploads');

        $productUrl = UploadDisk::publicUrl($productPath);
        $bannerUrl = UploadDisk::publicUrl($bannerPath);
        $categoryUrl = UploadDisk::publicUrl($categoryPath);

        // URL must use PUBLIC_AWS_URL, not S3 API endpoint
        $this->assertSame('https://pub-cdn.r2.dev/'.$productPath, $productUrl);
        $this->assertSame('https://pub-cdn.r2.dev/'.$bannerPath, $bannerUrl);
        $this->assertSame('https://pub-cdn.r2.dev/'.$categoryPath, $categoryUrl);

        $this->assertStringNotContainsString('cloudflarestorage.com', $productUrl);
        $this->assertStringNotContainsString('dmt-print-public/', $productUrl);
        $this->assertStringNotContainsString('cloudflarestorage.com', $bannerUrl);
        $this->assertStringNotContainsString('cloudflarestorage.com', $categoryUrl);

        // Models must also resolve using public CDN URL
        $product = Product::factory()->create(['image_path' => $productPath]);
        $this->assertSame('https://pub-cdn.r2.dev/'.$productPath, $product->imageUrl());

        $banner = Banner::factory()->create(['image_path' => $bannerPath]);
        $this->assertSame('https://pub-cdn.r2.dev/'.$bannerPath, $banner->imageUrl());

        $category = Category::factory()->create(['image_path' => $categoryPath]);
        $this->assertSame('https://pub-cdn.r2.dev/'.$categoryPath, $category->imageUrl());

        // Legacy path with bucket prefix is safely normalized to CDN URL without duplicate bucket
        $legacyUrl = UploadDisk::publicUrl('dmt-print-public/'.$productPath);
        $this->assertSame('https://pub-cdn.r2.dev/'.$productPath, $legacyUrl);
    }

    public function test_private_media_disks_do_not_produce_public_urls(): void
    {
        $this->assertNull(UploadDisk::publicUrl('order-designs/token/design.pdf', 'private_uploads'));
        $this->assertNull(UploadDisk::publicUrl('payment-proofs/token/proof.jpg', 'local'));
        $this->assertNull(UploadDisk::publicUrl('order-designs/token/design.pdf', UploadDisk::private()));
    }

    public function test_homepage_and_catalog_do_not_crash_with_zero_or_empty_media_values(): void
    {
        $category = Category::factory()->create(['name' => 'Brosur', 'slug' => 'brosur', 'image_path' => '0']);
        $productZero = Product::factory()->for($category)->create(['name' => 'Brosur Lipat', 'slug' => 'brosur-lipat', 'image_path' => '0', 'is_active' => true, 'is_featured' => true]);
        $productNull = Product::factory()->for($category)->create(['name' => 'Brosur Kilat', 'slug' => 'brosur-kilat', 'image_path' => null, 'is_active' => true, 'is_featured' => true]);
        $banner = Banner::factory()->create(['image_path' => '0', 'is_active' => true]);

        $this->assertSame(asset('images/placeholders/product.svg'), $productZero->imageUrl());
        $this->assertSame(asset('images/placeholders/product.svg'), $productNull->imageUrl());
        $this->assertNull($category->imageUrl());
        $this->assertNull($banner->imageUrl());

        $this->get(route('home'))->assertOk()->assertSee('Brosur Lipat')->assertSee('images/placeholders/product.svg', false);
        $this->get(route('catalog'))->assertOk()->assertSee('Brosur Lipat');
        $this->get(route('product.show', $productZero))->assertOk()->assertSee('images/placeholders/product.svg', false);
    }

    public function test_missing_product_image_uses_local_placeholder(): void
    {
        Storage::fake('public_uploads');
        $product = Product::factory()->create(['image_path' => null]);

        $this->get(route('product.show', $product))->assertOk()->assertSee('images/placeholders/product.svg', false);
    }

    public function test_replacing_product_image_deletes_old_file_safely(): void
    {
        Storage::fake('public_uploads');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create(['image_path' => 'products/old.jpg']);
        Storage::disk('public_uploads')->put('products/old.jpg', 'old');

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

        Storage::disk('public_uploads')->assertMissing('products/old.jpg');
        Storage::disk('public_uploads')->assertExists($product->refresh()->image_path);
    }

    public function test_admin_can_download_private_payment_proof_and_access_is_protected(): void
    {
        Storage::fake('private_uploads');
        $order = Order::factory()->create();
        $payment = app(RecordPaymentAction::class)->execute($order, 'qris', 'full', 50000, UploadedFile::fake()->create('Bukti Bayar.JPG', 16, 'image/jpeg'));
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->assertSame('private_uploads', $payment->proof_disk);
        $this->assertStringStartsWith('payment-proofs/', $payment->proof_path);
        $this->get(route('admin.payments.proof.download', $payment))->assertRedirect(route('admin.login'));
        $inactiveAdmin = User::factory()->create(['is_admin' => true, 'is_active' => false]);
        $this->actingAs($inactiveAdmin)->get(route('admin.payments.proof.download', $payment))->assertForbidden();

        $response = $this->actingAs($admin)->get(route('admin.payments.proof.download', $payment));
        $response->assertDownload('bukti-bayar.jpg');
        $this->assertStringNotContainsString($payment->proof_path, $response->headers->get('content-disposition', ''));
        $this->assertDatabaseHas('activity_logs', ['event' => 'payment.proof_downloaded', 'subject_type' => Order::class, 'subject_id' => $order->id]);
        $this->actingAs($admin)->get(route('admin.payments.proof.preview', $payment))->assertOk();
    }

    public function test_legacy_local_private_payment_proof_disk_metadata_still_downloads_from_private_uploads(): void
    {
        Storage::fake('private_uploads');
        Storage::disk('private_uploads')->put('payment-proofs/legacy/proof.jpg', 'proof');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $payment = Payment::factory()->create([
            'proof_disk' => 'local',
            'proof_path' => 'payment-proofs/legacy/proof.jpg',
            'proof_original_name' => 'legacy-proof.jpg',
            'proof_mime_type' => 'image/jpeg',
            'proof_size' => 5,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.payments.proof.download', $payment));

        $response->assertDownload('legacy-proof.jpg');
    }

    public function test_payment_proof_download_rejects_missing_or_public_files(): void
    {
        Storage::fake('private_uploads');
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
        Storage::fake('public_uploads');
        Storage::disk('public_uploads')->put('products/invoice.jpg', 'image-content');
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
        Storage::fake('public_uploads');
        Storage::disk('public_uploads')->put('payment-methods/qris.png', 'qris');
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
