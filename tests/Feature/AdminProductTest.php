<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_product_page_renders_database_products_and_categories(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create(['name' => 'Banner']);
        Product::factory()->for($category)->create([
            'name' => 'Banner Database',
            'base_price' => 125000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products'))
            ->assertOk()
            ->assertSee('Banner Database')
            ->assertSee('Rp 125.000')
            ->assertSee('Banner');
    }
}
