<?php

namespace PnDev\ContactForm;

use Illuminate\Support\ServiceProvider;
use PnDev\ContactForm\Services\CaptchaService;

class ContactFormServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/contact-form.php', 'contact-form');

        $this->app->singleton(CaptchaService::class);
    }

    public function boot(): void
    {
        // Publier la config
        $this->publishes([
            __DIR__ . '/../config/contact-form.php' => config_path('contact-form.php'),
        ], 'contact-form-config');

        // Publier les vues (override optionnel)
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/contact-form'),
        ], 'contact-form-views');

        // Charger les vues du package
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'contact-form');

        // Routes web
        if (config('contact-form.routes.web', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        // Routes API
        if (config('contact-form.routes.api', false)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }
    }
}
