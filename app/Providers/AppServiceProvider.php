<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Province;
use App\Models\Retailer;
use App\Observers\UserObserver;
use App\Observers\ProvinceObserver;
use App\Observers\RetailerLogObserver;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Province::observe(ProvinceObserver::class);
        Retailer::observe(RetailerLogObserver::class);
    }
}
