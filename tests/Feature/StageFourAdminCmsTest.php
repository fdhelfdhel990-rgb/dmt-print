<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StageFourAdminCmsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_manage_catalog_cms_and_admin_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true, 'role' => 'owner']);
        $category = Category::factory()->create(['name' => 'Stiker']);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Stiker Hologram',
            'sku' => 'STK-HOLO',
            'short_description' => 'Stiker tahan air',
            'description' => 'Deskripsi lengkap',
            'base_price' => 35000,
            'unit' => 'lembar',
            'minimum_order' => 2,
            'stock_on_hand' => 20,
            'stock_minimum' => 5,
            'production_estimate' => '1 hari',
            'tone' => 'cyan',
            'sort_order' => 1,
            'is_active' => '1',
            'is_featured' => '1',
        ]);

        $product = Product::query()->where('sku', 'STK-HOLO')->firstOrFail();
        $response->assertRedirect(route('admin.products.edit', $product));
        $this->assertSame('stiker-hologram', $product->slug);

        $this->patch(route('admin.appearance.hero'), [
            'title' => 'Cetak kilat DMT',
            'subtitle' => 'Pesan cetak dari rumah',
            'cta_label' => 'Pesan Sekarang',
        ])->assertRedirect();

        $this->get(route('home'))->assertOk()->assertSee('Cetak kilat DMT')->assertSee('Pesan Sekarang');
        $this->assertDatabaseHas('site_settings', ['key' => 'hero']);

        $this->post(route('admin.admins.store'), [
            'name' => 'Admin Finance',
            'email' => 'finance@example.test',
            'role' => 'finance',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'finance@example.test', 'is_admin' => true, 'role' => 'finance', 'is_active' => true]);
    }

    public function test_inactive_admin_cannot_log_in_or_access_admin_area(): void
    {
        $admin = User::factory()->create([
            'email' => 'inactive@example.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_active' => false,
        ]);

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_business_settings_render_in_footer(): void
    {
        SiteSetting::create(['key' => 'business', 'value' => ['business_name' => 'DMT Print', 'phone' => '081234567890', 'address' => 'Jl. Baru 1', 'opening_hours' => 'Setiap hari', 'maps_url' => '']]);

        $this->get(route('home'))->assertOk()->assertSee('Jl. Baru 1')->assertSee('081234567890');
    }
}
