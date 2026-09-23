<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\UploadDisk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image_path'] = $request->file('image')?->store('banners', UploadDisk::public());
        Banner::create($data);

        return back()->with('status', 'Banner berhasil ditambahkan.');
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $this->validated($request, false);
        $oldPath = $banner->image_path;

        if ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('banners', UploadDisk::public());
        }

        $banner->update($data);

        if (($request->hasFile('image') || $request->boolean('remove_image')) && $oldPath) {
            Storage::disk(UploadDisk::public())->delete($oldPath);
        }

        return back()->with('status', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->update(['archived_at' => now(), 'is_active' => false]);

        return back()->with('status', 'Banner diarsipkan.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $imageRequired = true): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'button_label' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ], ['image.mimes' => 'Gambar harus berupa JPG, JPEG, PNG, atau WebP.', 'image.max' => 'Ukuran gambar maksimal 4 MB.']) + ['is_active' => $request->boolean('is_active')];
    }
}
