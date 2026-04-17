<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('contact-form.page_title', 'Contactez-nous') }} — {{ config('contact-form.site_name') }}</title>
    <meta name="description" content="Contactez l'equipe {{ config('contact-form.site_name') }}. Formulaire de contact securise. Reponse sous 24h.">
    <meta name="robots" content="index, follow">
    @if(function_exists('vite'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ config('contact-form.page_title', 'Contactez-nous') }}</h1>
            <p class="mt-2 text-gray-600">{{ config('contact-form.page_subtitle', 'Une question ? Ecrivez-nous.') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-8">
            @include('contact-form::_form-content')
        </div>
        <p class="text-center text-xs text-gray-400 mt-6">&copy; {{ date('Y') }} {{ config('contact-form.site_name') }}. Tous droits reserves.</p>
    </div>
</body>
</html>
