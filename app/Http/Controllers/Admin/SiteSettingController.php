<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Support\UploadDisk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.appearance', [
            'business' => SiteSetting::value('business', $this->businessDefaults()),
            'social' => SiteSetting::value('social', []),
            'banners' => Banner::query()->whereNull('archived_at')->orderBy('sort_order')->get(),
            'paymentMethods' => PaymentMethod::query()->orderBy('sort_order')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
            'featuredProductIds' => Product::query()->where('is_featured', true)->pluck('id')->all(),
        ]);
    }

    public function updateBusiness(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:1000'],
            'opening_hours' => ['required', 'string', 'max:255'],
            'maps_url' => ['nullable', 'url', 'max:500'],
        ]);

        SiteSetting::updateOrCreate(['key' => 'business'], ['value' => $validated]);

        return back()->with('status', 'Profil usaha disimpan.');
    }

    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:qris,bank_transfer,cash'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ]);
        $method = PaymentMethod::query()->firstOrNew(['type' => $validated['type']]);
        $oldPath = $method->image_path;
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->boolean('remove_image')) {
            $validated['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('payment-methods', UploadDisk::public());
        }

        $method->fill($validated)->save();

        if (($request->hasFile('image') || $request->boolean('remove_image')) && $oldPath) {
            Storage::disk(UploadDisk::public())->delete($oldPath);
        }

        return back()->with('status', 'Informasi pembayaran disimpan.');
    }

    public function updateSocial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'instagram' => ['nullable', 'url', 'max:500'],
            'tiktok' => ['nullable', 'url', 'max:500'],
            'show_instagram' => ['nullable', 'boolean'],
            'show_tiktok' => ['nullable', 'boolean'],
        ]);
        $validated['show_instagram'] = $request->boolean('show_instagram');
        $validated['show_tiktok'] = $request->boolean('show_tiktok');

        SiteSetting::updateOrCreate(['key' => 'social'], ['value' => $validated]);

        return back()->with('status', 'Media sosial disimpan.');
    }

    public function updateFeatured(Request $request): RedirectResponse
    {
        $validated = $request->validate(['products' => ['array', 'max:8'], 'products.*' => ['integer', 'exists:products,id']]);
        $ids = $validated['products'] ?? [];

        Product::query()->update(['is_featured' => false]);
        Product::query()->whereIn('id', $ids)->update(['is_featured' => true]);

        return back()->with('status', 'Produk unggulan disimpan.');
    }

    /**
     * @return array<string, string>
     */
    private function businessDefaults(): array
    {
        return ['business_name' => 'Darul Muttaqien Printing', 'phone' => '08xx-xxxx-xxxx', 'address' => 'Jl. Contoh No. 12, Sleman, DI Yogyakarta', 'opening_hours' => 'Senin-Sabtu, 08.00-20.00', 'maps_url' => ''];
    }

    /**
     * @return array<string, string>
     */
}
