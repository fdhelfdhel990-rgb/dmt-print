<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class StorefrontController extends Controller
{
    public function home(): View
    {
        return view('customer.home', [
            'categories' => $this->categories(),
            'products' => Product::query()->with('category')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function catalog(): View
    {
        return view('customer.catalog', [
            'categories' => $this->categories(),
            'products' => Product::query()->with('category')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('customer.product-order', [
            'categories' => $this->categories(),
            'product' => $product->load(['category', 'options' => fn ($query) => $query->where('is_active', true), 'options.values' => fn ($query) => $query->where('is_active', true)]),
        ]);
    }

    /** @return Collection<int, Category> */
    private function categories(): Collection
    {
        return Category::query()->where('is_active', true)->orderBy('sort_order')->get();
    }
}
