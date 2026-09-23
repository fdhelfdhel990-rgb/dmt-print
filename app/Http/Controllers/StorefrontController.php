<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home(): View
    {
        return view('customer.home', [
            'categories' => $this->categories(),
            'banners' => Banner::query()->where('is_active', true)->whereNull('archived_at')->orderBy('sort_order')->limit(2)->get(),
            'products' => Product::query()->with('category')->where('is_active', true)->whereHas('category', fn ($query) => $query->where('is_active', true)->whereNull('archived_at'))->orderBy('sort_order')->get(),
        ]);
    }

    public function catalog(Request $request): View
    {
        $categories = $this->categories();
        $activeCategory = null;

        if ($request->filled('kategori')) {
            $activeCategory = $categories->firstWhere('slug', $request->string('kategori')->toString());
            abort_if($activeCategory === null, 404);
        }

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true)->whereNull('archived_at'))
            ->when($activeCategory, fn ($query) => $query->where('category_id', $activeCategory->id))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->trim()->toString();
                $query->where(fn ($query) => $query->where('name', 'like', '%'.$term.'%')->orWhere('short_description', 'like', '%'.$term.'%'));
            })
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        return view('customer.catalog', [
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'products' => $products,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active && $product->category?->is_active, 404);

        return view('customer.product-order', [
            'categories' => $this->categories(),
            'product' => $product->load([
                'category',
                'options' => fn ($query) => $query
                    ->where('is_active', true)
                    ->whereHas('values', fn ($query) => $query->where('is_active', true)),
                'options.values' => fn ($query) => $query->where('is_active', true),
            ]),
        ]);
    }

    /** @return Collection<int, Category> */
    private function categories(): Collection
    {
        return Category::query()->where('is_active', true)->whereNull('archived_at')->orderBy('sort_order')->get();
    }
}
