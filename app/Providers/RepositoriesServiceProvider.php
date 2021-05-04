<?php

namespace App\Providers;

use App\Repository\CashRegisterRepository;
use App\Repository\CashRegisterRepositoryInterface;
use App\Repository\TransactionRepository;
use App\Repository\TransactionRepositoryInterface;
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
        $this->app->bind(TransactionRepositoryInterface::class,TransactionRepository::class);

    }
}
