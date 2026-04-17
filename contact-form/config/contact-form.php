<?php

return [
    // Identite du site
    'site_name'       => env('CONTACT_SITE_NAME', config('app.name', 'Mon Site')),
    'recipient_email' => env('CONTACT_RECIPIENT_EMAIL', 'contact@example.com'),
    'recipient_name'  => env('CONTACT_RECIPIENT_NAME', 'Equipe'),

    // Champ sujet : 'select' (liste deroulante), 'text' (libre), false (masque)
    'subject_field'   => 'select',
    'subject_options' => [
        'general' => 'Question generale',
        'devis'   => 'Demande de devis',
        'support' => 'Support technique',
        'autre'   => 'Autre',
    ],

    // Layout Blade : null = page autonome, ou nom du layout (ex: 'layouts.auth')
    'layout'         => null,
    'layout_section' => 'content',
    'page_title'     => 'Contactez-nous',
    'page_subtitle'  => 'Une question ? Un besoin specifique ? Ecrivez-nous.',

    // Routes
    'routes' => [
        'web'            => true,
        'api'            => false,
        'prefix'         => 'contact',
        'middleware'      => ['web'],
        'api_prefix'     => 'api/contact',
        'api_middleware'  => ['api', 'throttle:10,1'],
    ],

    // CAPTCHA
    'captcha' => [
        'enabled'        => true,
        'rotation_days'  => 3,
        'session_key'    => 'contact_captcha',
    ],

    // Email
    'email_subject_prefix' => null,
    'email_header_color'   => '#1d4ed8',
];
