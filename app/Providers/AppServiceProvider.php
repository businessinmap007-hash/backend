<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Schema;
use Illuminate\Support\Facades\URL;

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
        // Fix MySQL index size issue
        Schema::defaultStringLength(191);

        // Set locale
        Carbon::setLocale('ar');

        // Detect locale from URL
        if (request()->segment(1) == "ar" || request()->segment(1) == "en") {
            app()->setLocale(request()->segment(1));
        } else {
            app()->setLocale('ar');
        }

        // 🌟 Force App URL for correct asset() paths
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }

        // Force HTTPS if APP_URL uses https
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
