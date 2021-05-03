<?php

namespace App\Providers;

use App\Repository\CashRegisterRepository;
use App\Repository\CashRegisterRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(CashRegisterRepositoryInterface::class,CashRegisterRepository::class);
    }
}
