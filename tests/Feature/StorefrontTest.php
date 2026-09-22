<?php

namespace Tests\Feature;

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
}
