<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_catalog_and_product_pages_render_database_products(): void
    {
        $category = Category::factory()->create(['name' => 'Stiker']);
        $product = Product::factory()->for($category)->create([
            'name' => 'Stiker Uji',
            'slug' => 'stiker-uji',
            'base_price' => 42000,
        ]);

        $this->get('/katalog')
            ->assertOk()
            ->assertSee('Stiker Uji')
            ->assertSee('Rp 42.000');

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Stiker Uji')
            ->assertSee('DMT-')
            ->assertDontSee('price-sticker', false)
            ->assertDontSee('Mulai dari');
    }

    public function test_inactive_product_is_not_publicly_available(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->get(route('product.show', $product))->assertNotFound();
    }

    public function test_category_links_filter_catalog_by_active_slug(): void
    {
        $sticker = Category::factory()->create(['name' => 'Stiker', 'slug' => 'stiker', 'is_active' => true]);
        $banner = Category::factory()->create(['name' => 'Banner', 'slug' => 'banner', 'is_active' => true]);
        $inactive = Category::factory()->create(['name' => 'Rahasia', 'slug' => 'rahasia', 'is_active' => false]);
        Product::factory()->for($sticker)->create(['name' => 'Stiker Vinyl']);
        Product::factory()->for($banner)->create(['name' => 'Banner Outdoor']);
        Product::factory()->for($inactive)->create(['name' => 'Produk Rahasia']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('catalog', ['kategori' => 'stiker']), false)
            ->assertSee('category-icon-svg', false)
            ->assertSee('d="M11 10h22l5 5v23H11V10Z"', false)
            ->assertDontSee('rahasia');

        $this->get(route('catalog', ['kategori' => 'stiker']))
            ->assertOk()
            ->assertSee('Stiker Vinyl')
            ->assertDontSee('Banner Outdoor')
            ->assertDontSee('Produk Rahasia')
            ->assertSee('Semua Produk');
    }

    public function test_invalid_or_inactive_category_slug_is_not_found(): void
    {
        Category::factory()->create(['slug' => 'nonaktif', 'is_active' => false]);

        $this->get(route('catalog', ['kategori' => 'tidak-ada']))->assertNotFound();
        $this->get(route('catalog', ['kategori' => 'nonaktif']))->assertNotFound();
    }

    public function test_category_pagination_preserves_filter(): void
    {
        $category = Category::factory()->create(['slug' => 'stiker']);
        Product::factory()->count(13)->for($category)->create();

        $this->get(route('catalog', ['kategori' => 'stiker']))
            ->assertOk()
            ->assertSee('kategori=stiker', false);
    }

    public function test_homepage_uses_first_two_active_banners_by_order(): void
    {
        Banner::factory()->create(['title' => 'Banner Ketiga', 'sort_order' => 3]);
        Banner::factory()->create(['title' => 'Banner Pertama', 'sort_order' => 1]);
        Banner::factory()->create(['title' => 'Banner Nonaktif', 'sort_order' => 0, 'is_active' => false]);
        Banner::factory()->create(['title' => 'Banner Kedua', 'sort_order' => 2]);

        $response = $this->get(route('home'))->assertOk();

        $response->assertSeeInOrder(['Banner Pertama', 'Banner Kedua']);
        $response->assertDontSee('Banner Ketiga');
        $response->assertDontSee('Banner Nonaktif');
        $this->assertSame(1, substr_count($response->getContent(), 'data-slider'));
        $this->assertSame(2, substr_count($response->getContent(), 'data-slide="'));
        $response->assertSee('data-slide-next', false);
    }

    public function test_homepage_categories_use_slug_icon_mapping_with_fallback(): void
    {
        Category::factory()->create(['name' => 'Merchandise & Souvenir', 'slug' => 'merchandise-souvenir', 'sort_order' => 1]);
        Category::factory()->create(['name' => 'Kategori Baru', 'slug' => 'kategori-baru', 'sort_order' => 2]);

        $response = $this->get(route('home'))->assertOk();

        $response->assertSee('Merchandise &amp; Souvenir', false);
        $response->assertSee('Kategori Baru');
        $response->assertSee('d="M10 21h28v19H10V21Z"', false);
        $response->assertSee('d="M11 11h11v11H11V11ZM26 11h11v11H26V11ZM11 26h11v11H11V26ZM26 26h11v11H26V26Z"', false);
    }

    public function test_product_options_are_dynamic_and_empty_option_groups_are_hidden(): void
    {
        $product = Product::factory()->create(['name' => 'Stempel Warna', 'base_price' => 95000]);
        $color = ProductOption::create(['product_id' => $product->id, 'name' => 'Warna', 'is_required' => true, 'is_active' => true, 'sort_order' => 1]);
        ProductOptionValue::create(['product_option_id' => $color->id, 'name' => 'Merah', 'price_adjustment' => 0, 'is_active' => true]);
        ProductOption::create(['product_id' => $product->id, 'name' => 'Finishing', 'is_required' => false, 'is_active' => true, 'sort_order' => 2]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Warna')
            ->assertSee('Merah')
            ->assertSee('name="options['.$color->id.']"', false)
            ->assertDontSee('Ukuran')
            ->assertDontSee('Finishing')
            ->assertDontSee('price-sticker', false)
            ->assertDontSee('Mulai dari');
    }

    public function test_product_without_options_does_not_render_empty_option_dropdowns(): void
    {
        $product = Product::factory()->create(['name' => 'Produk Polos']);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Produk Polos')
            ->assertDontSee('name="options[', false)
            ->assertDontSee('<select', false)
            ->assertDontSee('Pilih ukuran')
            ->assertDontSee('Pilih finishing');
    }
}
