<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport;

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
        // Forzar HTTPS en producción (Render) para evitar conflictos de certificados o redirecciones mixtas
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Registrar el driver de Brevo API mediante DSN estático
        Mail::extend('brevo', function (array $config = []) {
            return Transport::fromDsn(
                'brevo+api://' . config('services.brevo.key') . '@default'
            );
        });
    }
}