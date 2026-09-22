<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Merchandise & Souvenir', 'slug' => 'merchandise-souvenir'],
            ['name' => 'Print Warna', 'slug' => 'print-warna'],
            ['name' => 'Stiker', 'slug' => 'stiker'],
            ['name' => 'Packaging UMKM', 'slug' => 'packaging-umkm'],
            ['name' => 'Poster', 'slug' => 'poster'],
            ['name' => 'Kalender', 'slug' => 'kalender'],
            ['name' => 'Banner', 'slug' => 'banner'],
            ['name' => 'Kartu Nama', 'slug' => 'kartu-nama'],
            ['name' => 'Stempel', 'slug' => 'stempel'],
        ];

        foreach ($categories as $index => $attributes) {
            Category::query()->updateOrCreate(
                ['slug' => $attributes['slug']],
                $attributes + ['is_active' => true, 'sort_order' => $index + 1],
            );
        }

        $products = [
            ['name' => 'Cetak Stiker Vinyl', 'slug' => 'cetak-stiker-vinyl', 'sku' => 'DMT-STK-01', 'category' => 'stiker', 'base_price' => 35000, 'unit' => 'lembar', 'tone' => 'cyan', 'tag' => 'Terlaris'],
            ['name' => 'Kartu Nama Premium', 'slug' => 'kartu-nama-premium', 'sku' => 'DMT-KNM-01', 'category' => 'kartu-nama', 'base_price' => 65000, 'unit' => 'box', 'tone' => 'navy', 'tag' => 'Favorit'],
            ['name' => 'X-Banner Indoor', 'slug' => 'x-banner-indoor', 'sku' => 'DMT-BNR-01', 'category' => 'banner', 'base_price' => 115000, 'unit' => 'set', 'tone' => 'yellow', 'tag' => null],
            ['name' => 'Brosur A5 Full Color', 'slug' => 'brosur-a5-full-color', 'sku' => 'DMT-PRW-01', 'category' => 'print-warna', 'base_price' => 45000, 'unit' => '100 lembar', 'tone' => 'magenta', 'tag' => 'Cepat'],
            ['name' => 'Stempel Flash K3', 'slug' => 'stempel-flash-k3', 'sku' => 'DMT-STP-03', 'category' => 'stempel', 'base_price' => 95000, 'unit' => 'pcs', 'tone' => 'red', 'tag' => null],
            ['name' => 'Paper Bag Custom', 'slug' => 'paper-bag-custom', 'sku' => 'DMT-PKG-01', 'category' => 'packaging-umkm', 'base_price' => 8500, 'unit' => 'pcs', 'tone' => 'green', 'tag' => null],
            ['name' => 'Tumbler Custom', 'slug' => 'tumbler-custom', 'sku' => 'DMT-MER-01', 'category' => 'merchandise-souvenir', 'base_price' => 75000, 'unit' => 'pcs', 'tone' => 'blue', 'tag' => 'Baru'],
            ['name' => 'Poster A3+', 'slug' => 'poster-a3-plus', 'sku' => 'DMT-PST-01', 'category' => 'poster', 'base_price' => 12000, 'unit' => 'lembar', 'tone' => 'orange', 'tag' => null],
        ];

        foreach ($products as $index => $attributes) {
            $category = Category::query()->where('slug', $attributes['category'])->firstOrFail();

            Product::query()->updateOrCreate(
                ['slug' => $attributes['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $attributes['name'],
                    'sku' => $attributes['sku'],
                    'short_description' => 'Produk cetak berkualitas untuk kebutuhan usaha dan personal.',
                    'description' => 'Pilihan produk DMT Print dengan hasil rapi dan estimasi pengerjaan yang jelas.',
                    'base_price' => $attributes['base_price'],
                    'unit' => $attributes['unit'],
                    'minimum_order' => 1,
                    'production_estimate' => '1-2 hari',
                    'tone' => $attributes['tone'],
                    'tag' => $attributes['tag'],
                    'is_active' => true,
                    'is_featured' => $index < 4,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
