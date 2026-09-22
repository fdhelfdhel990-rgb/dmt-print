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
            $size = $product->options()->updateOrCreate(['name' => 'Ukuran'], ['is_required' => true, 'is_active' => true, 'sort_order' => 1]);
            $size->values()->updateOrCreate(['name' => 'Standar'], ['price_adjustment' => 0, 'is_active' => true, 'sort_order' => 1]);
            $size->values()->updateOrCreate(['name' => 'Besar'], ['price_adjustment' => 15000, 'is_active' => true, 'sort_order' => 2]);
            $finishing = $product->options()->updateOrCreate(['name' => 'Finishing'], ['is_required' => false, 'is_active' => true, 'sort_order' => 2]);
            $finishing->values()->updateOrCreate(['name' => 'Tanpa finishing'], ['price_adjustment' => 0, 'is_active' => true, 'sort_order' => 1]);
            $finishing->values()->updateOrCreate(['name' => 'Premium'], ['price_adjustment' => 10000, 'is_active' => true, 'sort_order' => 2]);
        });
    }
}
