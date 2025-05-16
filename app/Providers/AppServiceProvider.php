<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appName = Setting::getValue('APP_NAME', config('app.name'));
        $tax = Setting::getValue('TAX', config('app.tax'));
        $logo = Setting::getValue('APP_LOGO', config('app.logo'));

        config([
            'app.name' => $appName,
            'app.tax' => $tax,
            'app.logo' => $logo,
        ]);
    }
}
