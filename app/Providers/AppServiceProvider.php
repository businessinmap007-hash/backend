<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
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

        // Set Carbon locale (دا شغال في كل الحالات)
        Carbon::setLocale('ar');

        // لو التطبيق شغال من خلال HTTP request (مش من الكونسول)
        if (! $this->app->runningInConsole()) {

            // Detect locale from URL: /ar/... or /en/...
            $firstSegment = request()->segment(1);
            if ($firstSegment === 'ar' || $firstSegment === 'en') {
                app()->setLocale($firstSegment);
            } else {
                app()->setLocale('ar');
            }
        }

        // Force App URL for correct asset() paths
        $appUrl = config('app.url');
        if ($appUrl) {
            URL::forceRootUrl($appUrl);

            // Force HTTPS فقط لو التطبيق في production و APP_URL https
            if (config('app.env') === 'production' && str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }
    }
}
