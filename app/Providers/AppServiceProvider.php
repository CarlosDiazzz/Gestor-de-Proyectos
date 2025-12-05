<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail; // <--- AGREGA ESTO
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory; // <--- AGREGA ESTO
use Symfony\Component\Mailer\Transport\Dsn; // <--- AGREGA ESTO

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
        // Forzar HTTPS en producción para que carguen los estilos
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        // Registrar el transportador de Brevo
        Mail::extend('brevo', function (array $config = []) {
            return (new BrevoTransportFactory)->create(
                Dsn::fromString('brevo+api://' . $config['key'] . '@default')
            );
        });
    }
}
