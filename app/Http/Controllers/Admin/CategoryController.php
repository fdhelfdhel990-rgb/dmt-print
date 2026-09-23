<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.categories', [
            'categories' => Category::query()->withCount('products')->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))->orderBy('sort_order')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.category-form', ['category' => new Category(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image_path'] = $request->file('image')?->store('categories', 'public');
        Category::create($data);

        return redirect()->route('admin.categories')->with('status', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category): View
    {
        return view('admin.category-form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        $oldPath = $category->image_path;

        if ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        if (($request->hasFile('image') || $request->boolean('remove_image')) && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', 'Kategori berhasil diperbarui.');
    }

    public function status(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            $category->update(['is_active' => false]);
        } else {
            $category->update(['archived_at' => now(), 'is_active' => false]);
        }

        return back()->with('status', 'Status kategori diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:categories,slug,'.($category?->id ?? 'NULL')],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ], ['slug.unique' => 'Slug kategori sudah digunakan.', 'image.mimes' => 'Gambar harus berupa JPG, JPEG, PNG, atau WebP.']);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }
}
