<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Descomenta si usas paginación Bootstrap
use Illuminate\Support\Facades\URL;      // Descomenta si fuerzas HTTPS en Render

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
        // Si fuerzas HTTPS en producción (Render):
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Si usas paginador de Bootstrap 5:
        // Paginator::useBootstrapFive();
    }
}