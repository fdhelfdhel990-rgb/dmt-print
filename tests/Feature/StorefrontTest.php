<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
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
            ->assertSee('DMT-');
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
        $response->assertSee('data-slide-next', false);
    }
}
