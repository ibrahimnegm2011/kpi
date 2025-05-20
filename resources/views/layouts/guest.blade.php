<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                min-height: 100vh;
                font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
                background: radial-gradient(ellipse at bottom, #00a886 20%, #090a0f 100%);
                overflow: hidden;
                font-size: 110%;
                color: #ecf0f1;
                width: 100%;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased ">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="block fill-current text-gray-500">
            <x-application-logo/>
        </div>
        <h3 class="mt-5 text-3xl text-gray-100"> KPIs Management System</h3>

        <div class="w-full sm:max-w-md mt-6 p-8 bg-white shadow-xl overflow-hidden sm:rounded-xl">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
