<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.appearance', [
            'business' => SiteSetting::value('business', $this->businessDefaults()),
            'hero' => SiteSetting::value('hero', $this->heroDefaults()),
            'payment' => SiteSetting::value('payment', $this->paymentDefaults()),
            'social' => SiteSetting::value('social', []),
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

    public function updateHero(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['required', 'string', 'max:255'],
            'cta_label' => ['required', 'string', 'max:80'],
        ]);

        SiteSetting::updateOrCreate(['key' => 'hero'], ['value' => $validated]);

        return back()->with('status', 'Banner beranda disimpan.');
    }

    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:255'],
            'qris_note' => ['nullable', 'string', 'max:500'],
        ]);

        SiteSetting::updateOrCreate(['key' => 'payment'], ['value' => $validated]);

        return back()->with('status', 'Informasi pembayaran disimpan.');
    }

    public function updateSocial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'instagram' => ['nullable', 'url', 'max:500'],
            'tiktok' => ['nullable', 'url', 'max:500'],
            'youtube' => ['nullable', 'url', 'max:500'],
            'facebook' => ['nullable', 'url', 'max:500'],
        ]);

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
    private function heroDefaults(): array
    {
        return ['title' => 'Cetak cepat untuk kebutuhan sekolah, usaha, dan acara', 'subtitle' => 'Upload desain, cek harga, dan pantau produksi dari satu tempat.', 'cta_label' => 'Mulai Pesan'];
    }

    /**
     * @return array<string, string>
     */
    private function paymentDefaults(): array
    {
        return ['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'Darul Muttaqien Printing', 'qris_note' => 'QRIS tersedia setelah admin mengonfirmasi harga.'];
    }
}
