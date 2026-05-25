<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('local') && is_file(public_path('hot'))) {
            $viteUrl = rtrim((string) file_get_contents(public_path('hot')), '/');

            if ($viteUrl !== '') {
                URL::forceRootUrl($viteUrl);
            }
        }
    }
}
