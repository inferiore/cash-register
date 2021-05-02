<?php

namespace App\Providers;

use App\CashRegisterBalance;
use App\Listeners\CalculateMoneyListener;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CashRegisterBalance::class, function ($app) {
            return  new CashRegisterBalance();
        });
//        $this->app->bind(CalculateMoneyListener::class, function ($app) {
//            $class = $app->make(CashRegisterBalance::class);
//            return  new CalculateMoneyListener($class);
//        });




    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
