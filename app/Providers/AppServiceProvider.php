<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment(['stage'])) {
            $this->app->usePublicPath(base_path().'/../public_html/kpi');
        } elseif ($this->app->environment(['production'])) {
            $this->app->usePublicPath(base_path().'/../public_html');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
