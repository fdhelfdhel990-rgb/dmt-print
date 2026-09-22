<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UiPreviewTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[DataProvider('customerPages')]
    public function test_renders_every_customer_page(string $url): void
    {
        $this->get($url)->assertOk();
    }

    #[DataProvider('adminPages')]
    public function test_renders_every_admin_page_for_an_admin(string $url): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get($url)->assertOk();
    }

    public static function customerPages(): array
    {
        return [
            'home' => ['/'],
            'catalog' => ['/katalog'],
            'cart' => ['/keranjang'],
            'recipient checkout' => ['/checkout/penerima'],
            'payment checkout' => ['/checkout/pembayaran'],
            'order tracking' => ['/cek-pesanan'],
            'order status' => ['/status-pesanan'],
        ];
    }

    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/admin-preview'],
            'orders' => ['/admin-preview/pesanan'],
            'order detail' => ['/admin-preview/pesanan/DMT-240901-A12B'],
            'products' => ['/admin-preview/produk'],
            'create product' => ['/admin-preview/produk/tambah'],
            'stock' => ['/admin-preview/stok'],
            'cashbook' => ['/admin-preview/buku-kas'],
            'appearance' => ['/admin-preview/tampilan-web'],
            'settings' => ['/admin-preview/pengaturan'],
        ];
    }
}
