<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\EstudianteActualizado;
use App\Listeners\EnviarCorreoEstudiante;
use Illuminate\Support\Facades\Event;

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
        // Registro del Evento y Listener de notificaciones al estudiante
        Event::listen(
            EstudianteActualizado::class,
            EnviarCorreoEstudiante::class,
        );
    }
}