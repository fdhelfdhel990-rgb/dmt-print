<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::query()->each(function (Product $product): void {
            foreach ($this->optionsFor($product) as $sortOrder => $optionData) {
                $option = $product->options()->updateOrCreate(
                    ['name' => $optionData['name']],
                    ['is_required' => $optionData['required'], 'is_active' => true, 'sort_order' => $sortOrder + 1],
                );

                foreach ($optionData['values'] as $valueOrder => $valueData) {
                    $option->values()->updateOrCreate(
                        ['name' => $valueData['name']],
                        ['price_adjustment' => $valueData['price_adjustment'], 'is_active' => true, 'sort_order' => $valueOrder + 1],
                    );
                }
            }
        });
    }

    /**
     * @return array<int, array{name: string, required: bool, values: array<int, array{name: string, price_adjustment: int}>}>
     */
    private function optionsFor(Product $product): array
    {
        return match ($product->slug) {
            'cetak-stiker-vinyl' => [
                ['name' => 'Bahan stiker', 'required' => true, 'values' => [
                    ['name' => 'Vinyl putih', 'price_adjustment' => 0],
                    ['name' => 'Transparan', 'price_adjustment' => 8000],
                ]],
                ['name' => 'Cutting', 'required' => false, 'values' => [
                    ['name' => 'Tanpa cutting', 'price_adjustment' => 0],
                    ['name' => 'Kiss cut', 'price_adjustment' => 10000],
                ]],
            ],
            'kartu-nama-premium' => [
                ['name' => 'Jenis kertas', 'required' => true, 'values' => [
                    ['name' => 'Art carton 260 gsm', 'price_adjustment' => 0],
                    ['name' => 'Linen premium', 'price_adjustment' => 15000],
                ]],
                ['name' => 'Sisi cetak', 'required' => true, 'values' => [
                    ['name' => 'Satu sisi', 'price_adjustment' => 0],
                    ['name' => 'Dua sisi', 'price_adjustment' => 20000],
                ]],
                ['name' => 'Finishing', 'required' => false, 'values' => [
                    ['name' => 'Tanpa finishing', 'price_adjustment' => 0],
                    ['name' => 'Laminasi doff', 'price_adjustment' => 10000],
                ]],
            ],
            'x-banner-indoor' => [
                ['name' => 'Ukuran', 'required' => true, 'values' => [
                    ['name' => '60 x 160 cm', 'price_adjustment' => 0],
                    ['name' => '80 x 180 cm', 'price_adjustment' => 30000],
                ]],
                ['name' => 'Bahan', 'required' => true, 'values' => [
                    ['name' => 'Flexi china', 'price_adjustment' => 0],
                    ['name' => 'Flexi korea', 'price_adjustment' => 25000],
                ]],
            ],
            'brosur-a5-full-color' => [
                ['name' => 'Jenis kertas', 'required' => true, 'values' => [
                    ['name' => 'HVS 100 gsm', 'price_adjustment' => 0],
                    ['name' => 'Art paper 120 gsm', 'price_adjustment' => 12000],
                ]],
                ['name' => 'Sisi cetak', 'required' => true, 'values' => [
                    ['name' => 'Satu sisi', 'price_adjustment' => 0],
                    ['name' => 'Dua sisi', 'price_adjustment' => 15000],
                ]],
            ],
            'stempel-flash-k3' => [
                ['name' => 'Warna', 'required' => true, 'values' => [
                    ['name' => 'Hitam', 'price_adjustment' => 0],
                    ['name' => 'Biru', 'price_adjustment' => 0],
                    ['name' => 'Merah', 'price_adjustment' => 0],
                ]],
                ['name' => 'Ukuran', 'required' => true, 'values' => [
                    ['name' => '30 x 30 mm', 'price_adjustment' => 0],
                    ['name' => '40 x 40 mm', 'price_adjustment' => 20000],
                ]],
            ],
            'paper-bag-custom' => [
                ['name' => 'Bahan', 'required' => true, 'values' => [
                    ['name' => 'Kraft coklat', 'price_adjustment' => 0],
                    ['name' => 'Ivory putih', 'price_adjustment' => 2500],
                ]],
                ['name' => 'Ukuran', 'required' => true, 'values' => [
                    ['name' => 'Small', 'price_adjustment' => 0],
                    ['name' => 'Medium', 'price_adjustment' => 3000],
                ]],
            ],
            'tumbler-custom' => [
                ['name' => 'Warna tumbler', 'required' => true, 'values' => [
                    ['name' => 'Putih', 'price_adjustment' => 0],
                    ['name' => 'Hitam', 'price_adjustment' => 0],
                ]],
            ],
            'poster-a3-plus' => [
                ['name' => 'Ukuran', 'required' => true, 'values' => [
                    ['name' => 'A3+', 'price_adjustment' => 0],
                    ['name' => 'A2', 'price_adjustment' => 18000],
                ]],
                ['name' => 'Bahan', 'required' => true, 'values' => [
                    ['name' => 'Art paper', 'price_adjustment' => 0],
                    ['name' => 'Matte paper', 'price_adjustment' => 5000],
                ]],
                ['name' => 'Finishing', 'required' => false, 'values' => [
                    ['name' => 'Tanpa finishing', 'price_adjustment' => 0],
                    ['name' => 'Laminasi', 'price_adjustment' => 7000],
                ]],
            ],
            default => [],
        };
    }
}
