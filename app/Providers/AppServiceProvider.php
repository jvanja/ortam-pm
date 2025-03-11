<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Inertia::share([
            'defaultLocale' => Config::get('app.locale'), // Get default locale from .env
        ]);
    }
}
