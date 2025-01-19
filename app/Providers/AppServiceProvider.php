<?php

namespace App\Providers;

use App\Domains\Services\CoinalizeService;
use App\Domains\Services\CoinMarketCapService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('coinmarketcap', function ($app) {
            return new CoinMarketCapService();
        });
        $this->app->singleton('coinalize', function ($app) {
            return new CoinalizeService();
        });
    }

    public function boot(): void
    {
        //
    }
}
