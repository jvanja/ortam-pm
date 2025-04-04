<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    Model::shouldBeStrict(!$this->app->isProduction());
    DB::prohibitDestructiveCommands(!$this->app->isProduction());
    Inertia::share([
      'defaultLocale' => Config::get('app.locale'), // Get default locale from .env
    ]);
  }
}
