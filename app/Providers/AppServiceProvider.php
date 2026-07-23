<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\EstudianteActualizado;
use App\Listeners\EnviarCorreoEstudiante;
use Illuminate\Support\Facades\Event;
use Illuminate\Routing\UrlGenerator;

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
    public function boot(UrlGenerator $url): void
    {
        // Registro del Evento y Listener de notificaciones al estudiante
        Event::listen(
            EstudianteActualizado::class,
            EnviarCorreoEstudiante::class,
        );

        if (env('APP_ENV') === 'production') {
            $url->forceScheme('https');
        }
    }
}