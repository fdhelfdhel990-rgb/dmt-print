<?php

namespace App\Providers;

use App\Database\Connectors\TransientMySqlConnector;
use App\Models\SiteSetting;
use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('db.connector.mysql', TransientMySqlConnector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.customer', 'partials.customer-header', 'partials.customer-footer', 'customer.home'], function ($view): void {
            $view->with([
                'siteBusiness' => SiteSetting::value('business', ['business_name' => 'Darul Muttaqien Printing', 'phone' => '08xx-xxxx-xxxx', 'address' => 'Jl. Contoh No. 12, Sleman, DI Yogyakarta', 'opening_hours' => 'Senin-Sabtu, 08.00-20.00', 'maps_url' => '']),
                'siteSocial' => SiteSetting::value('social', []),
                'cartQuantity' => app(CartService::class)->quantity(),
            ]);
        });
    }
}
