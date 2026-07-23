<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\EstudianteActualizado;
use App\Listeners\EnviarCorreoEstudiante;
use App\Models\RiesgoDesercion;
use App\Observers\RiesgoDesercionObserver;
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
        // Registrar el Observer para que reaccione cuando cambie el riesgo de deserte
        RiesgoDesercion::observe(RiesgoDesercionObserver::class);

        // Registro del Evento y Listener de notificaciones al estudiante
        Event::listen(
            EstudianteActualizado::class,
            EnviarCorreoEstudiante::class,
        );

        // Forzar HTTPS en producción sin depender directamente de env()
        if ($this->app->environment('production')) {
            $url->forceScheme('https');
        }
    }
}