<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['h-full'])>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- Primary Meta Tags -->
        <title inertia>{{ config('app.name', 'GuacPanel') }}</title>
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
        <meta name="title" content="{{ config('app.name', 'GuacPanel') }}" />
        <meta name="description" content="{{ config('guacpanel.meta.description') }}" />
        <meta name="keywords" content="{{ config('guacpanel.meta.keywords') }}" />
        <meta name="author" content="{{ config('guacpanel.meta.author') }}" />
        <meta name="theme-color" content="{{ config('guacpanel.meta.theme-color') }}" />
        <meta name="color-scheme" content="light dark" />

        <!-- Tailwind CSS Configuration -->
        <meta name="tailwind-theme" content="modern" />
        <meta name="tailwind-version" content="3.x" />
        <meta name="tailwind-components" content="admin-dashboard" />
        <meta name="tailwind-plugins" content="forms,typography,aspect-ratio,line-clamp" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ config('app.url') }}" />
        <meta property="og:title" content="{{ config('app.name', 'GuacPanel') }}" />
        <meta property="og:description" content="{{ config('guacpanel.meta.description') }}" />
        <meta property="og:image" content="{{ asset('images/og-image.png') }}" />

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:title" content="{{ config('app.name', 'GuacPanel') }}" />
        <meta property="twitter:description" content="{{ config('guacpanel.meta.description') }}" />
        <meta property="twitter:image" content="{{ asset('images/og-image.png') }}" />

        <!-- Favicon -->

        @if ($personalisation->favicon)
            <link rel="icon" type="image/png" href="{{ asset('storage/' . $personalisation->favicon) }}" />
        @else
            <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
            <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" />
            <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}" />
        @endif
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />

        <!-- Scripts and Styles -->
        @vite([
            'resources/js/app.js',
            'resources/css/app.css',
        ])
        @inertiaHead
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
            rel="stylesheet" />
    </head>

    <body @class(['antialiased', 'h-full', 'bg-gray-50', 'dark:bg-gray-900', 'text-gray-900', 'dark:text-gray-100'])>
        @routes
        @inertia
    </body>
</html>
