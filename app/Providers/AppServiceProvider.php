<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.customer', 'partials.customer-footer', 'customer.home'], function ($view): void {
            $view->with([
                'siteBusiness' => SiteSetting::value('business', ['business_name' => 'Darul Muttaqien Printing', 'phone' => '08xx-xxxx-xxxx', 'address' => 'Jl. Contoh No. 12, Sleman, DI Yogyakarta', 'opening_hours' => 'Senin-Sabtu, 08.00-20.00', 'maps_url' => '']),
                'siteHero' => SiteSetting::value('hero', ['title' => 'Semua kebutuhan cetak dalam satu tempat', 'subtitle' => 'Mulai dari stiker, banner, kartu nama, hingga merchandise custom untuk usaha dan acara Anda.', 'cta_label' => 'Lihat Katalog']),
                'sitePayment' => SiteSetting::value('payment', ['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'Darul Muttaqien Printing', 'qris_note' => 'Konfirmasi pembayaran dilakukan melalui halaman pesanan.']),
                'siteSocial' => SiteSetting::value('social', []),
            ]);
        });
    }
}
