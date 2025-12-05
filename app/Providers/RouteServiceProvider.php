<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * مسار API الرئيسي
     */
    public const HOME = '/home';

    /**
     * Boot
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register Routes
     */
    public function map()
    {
        $this->mapApiV1Routes();
        $this->mapApiV2Routes(); // جاهز مستقبلاً
    }

    /**
     * -----------------------------
     * تحميل API V1
     * -----------------------------
     */
    protected function mapApiV1Routes()
    {
        $path = base_path('routes/api/v1');

        Route::prefix('api/v1')
            ->middleware('api')
            ->group(function () use ($path) {
                foreach (scandir($path) as $file) {

                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        require "$path/$file";
                    }
                }
            });
    }

    /**
     * -----------------------------
     * تحميل API V2 (جاهز للترقية)
     * -----------------------------
     */
    protected function mapApiV2Routes()
    {
        $path = base_path('routes/api/v2');

        if (!is_dir($path)) {
            return; // لا توجد نسخة V2 حتى الآن
        }

        Route::prefix('api/v2')
            ->middleware('api')
            ->group(function () use ($path) {
                foreach (scandir($path) as $file) {

                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        require "$path/$file";
                    }
                }
            });
    }
}
