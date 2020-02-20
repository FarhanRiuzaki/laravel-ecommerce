<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Apps;
use Illuminate\Support\Facades\View;

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
        $appSetting = Apps::orderBy('created_at', 'DESC')->first();
        
        View::share('appSetting', $appSetting);
    }
}
