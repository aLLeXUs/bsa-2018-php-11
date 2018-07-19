<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Repository\Contracts\CurrencyRepository::class,
            \App\Repository\DatabaseCurrencyRepository::class);
        $this->app->bind(\App\Repository\Contracts\LotRepository::class,
            \App\Repository\DatabaseLotRepository::class);
        $this->app->bind(\App\Repository\Contracts\MoneyRepository::class,
            \App\Repository\DatabaseMoneyRepository::class);
        $this->app->bind(\App\Repository\Contracts\TradeRepository::class,
            \App\Repository\DatabaseTradeRepository::class);
        $this->app->bind(\App\Repository\Contracts\UserRepository::class,
            \App\Repository\DatabaseUserRepository::class);
        $this->app->bind(\App\Repository\Contracts\WalletRepository::class,
            \App\Repository\DatabaseWalletRepository::class);

        $this->app->bind(\App\Service\Contracts\CurrencyService::class,
            \App\Service\CurrencyService::class);
        $this->app->bind(\App\Service\Contracts\MarketService::class,
            \App\Service\MarketService::class);
        $this->app->bind(\App\Service\Contracts\WalletService::class,
            \App\Service\WalletService::class);
    }
}
