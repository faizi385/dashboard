<?php

namespace App\Providers;

use App\Models\LP;
use App\Models\User;
use App\Models\Offer;
use App\Models\Report;
use App\Models\Carveout;
use App\Models\Province;
use App\Models\Retailer;
use App\Observers\LpObserver;
use App\Observers\UserObserver;
use App\Observers\OfferObserver;
use App\Observers\ReportObserver;
use App\Observers\CarveoutObserver;
use App\Observers\ProvinceObserver;
use Illuminate\Support\Facades\View;
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
        LP::observe(LpObserver::class);
        Offer::observe(OfferObserver::class);
        Carveout::observe(CarveoutObserver::class);
        Report::observe(ReportObserver::class);

        View::composer(['layouts.app', 'reports.index'], function ($view) {
            // Assuming that the retailer ID is stored in session or some logic to fetch it
            $retailerId = session('current_retailer_id');
            $retailer = Retailer::find($retailerId);
            $view->with('retailer', $retailer);
        });
    }
}
