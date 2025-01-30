<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    @inertiaHead
    <script>
        window.logoUrl = "{{ asset('images/afropulse-logo.svg') }}";
    </script>
    <style>
        .datatable-header {
            @apply flex justify-between items-center;
        }
    </style>


</head>

<body>
    @routes
    @inertia
</body>

</html>
