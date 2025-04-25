<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
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
        $this->app->singleton('home.redirect', function ($app) {
            return function (Request $request) {
                if ($request->user() && $request->user()->hasRole('hotelier')) {
                    return route('pro.dashboard');
                }

                return route('home');
            };
        });
    }
}
