<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%')->orWhere('sku', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->string('status')->toString() === 'active'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products', [
            'categories' => Category::query()->orderBy('sort_order')->get(),
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.product-form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'product' => new Product(['minimum_order' => 1, 'unit' => 'pcs', 'is_active' => true, 'tone' => 'blue']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['image_path'] = $request->file('image')?->store('products', 'public');
        $product = Product::create($data);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produk berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('admin.product-form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedData($request, $product);
        $oldPath = $product->image_path;

        if ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        if (($request->hasFile('image') || $request->boolean('remove_image')) && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return redirect()->route('admin.products')->with('status', 'Produk dinonaktifkan.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:products,slug,'.($product?->id ?? 'NULL')],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'minimum_order' => ['required', 'integer', 'min:1'],
            'stock_on_hand' => ['required', 'integer', 'min:-1000000'],
            'stock_minimum' => ['required', 'integer', 'min:0'],
            'production_estimate' => ['nullable', 'string', 'max:100'],
            'tone' => ['required', 'string', 'max:50'],
            'tag' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ], ['slug.unique' => 'Slug produk sudah digunakan.', 'sku.unique' => 'SKU produk sudah digunakan.', 'image.mimes' => 'Gambar harus berupa JPG, JPEG, PNG, atau WebP.']);

        $validated['slug'] = ($validated['slug'] ?? null) ?: ($product?->slug ?? $this->uniqueSlug($validated['name']));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
