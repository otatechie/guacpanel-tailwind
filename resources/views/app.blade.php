<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="title" content="GuacPanel">
    <meta name="description"
        content="A lightweight, customizable admin dashboard built with Laravel, Inertia, Vue, and Tailwind CSS.">
    <meta name="keywords" content="Laravel, Inertia.js, Vue.js, Tailwind CSS, Admin Dashboard, SaaS">
    <meta name="author" content="Your Name">
    <meta name="theme-color" content="#3B82F6">
    <meta name="color-scheme" content="light dark">

    <!-- Tailwind CSS Configuration -->
    <meta name="tailwind-theme" content="modern">
    <meta name="tailwind-version" content="3.x">
    <meta name="tailwind-components" content="admin-dashboard">
    <meta name="tailwind-plugins" content="forms,typography,aspect-ratio,line-clamp">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="http://localhost:8000">
    <meta property="og:title" content="GuacPanel">
    <meta property="og:description"
        content="A lightweight, customizable admin dashboard built with Laravel, Inertia, Vue, and Tailwind CSS.">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="GuacPanel">
    <meta property="twitter:description"
        content="A lightweight, customizable admin dashboard built with Laravel, Inertia, Vue, and Tailwind CSS.">
    <meta property="twitter:image" content="{{ asset('images/og-image.png') }}">

    <!-- Favicon -->
    @if($personalisation->favicon)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $personalisation->favicon) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    @endif
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Scripts and Styles -->
    @vite(['resources/js/app.js', 'resources/js/darkMode.js', 'resources/css/app.css'])
    @inertiaHead
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nata+Sans:wght@100..900&display=swap" rel="stylesheet">
</head>


<body class="antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    @routes
    @inertia
</body>

</html>