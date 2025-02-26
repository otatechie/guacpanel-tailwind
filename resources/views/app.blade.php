<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title inertia>{{ config('app.name', 'Obomaa') }}</title>
    <meta name="title" content="Obomaa - Admin Dashboard">
    <meta name="description"
        content="Modern admin dashboard built with Laravel, Inertia.js, and Vue.js. Streamline your business operations with our powerful management tools.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="Obomaa - Admin Dashboard">
    <meta property="og:description"
        content="Modern admin dashboard built with Laravel, Inertia.js, and Vue.js. Streamline your business operations with our powerful management tools.">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url') }}">
    <meta property="twitter:title" content="Obomaa - Admin Dashboard">
    <meta property="twitter:description"
        content="Modern admin dashboard built with Laravel, Inertia.js, and Vue.js. Streamline your business operations with our powerful management tools.">
    <meta property="twitter:image" content="{{ asset('images/og-image.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Scripts and Styles -->
    @vite(['resources/js/darkMode.js'])
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
</head>

@googlefonts

<body class="antialiased dark:bg-gray-800">
    @routes
    @inertia
</body>

</html>
