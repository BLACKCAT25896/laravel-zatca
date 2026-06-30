<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ZatcaService;
use App\Services\InvoiceService;
use App\Services\CryptoService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ZatcaService::class, function ($app) {
            return new ZatcaService();
        });

        $this->app->singleton(InvoiceService::class, function ($app) {
            return new InvoiceService();
        });

        $this->app->singleton(CryptoService::class, function ($app) {
            return new CryptoService();
        });
    }

    public function boot(): void
    {
        //
    }
}
