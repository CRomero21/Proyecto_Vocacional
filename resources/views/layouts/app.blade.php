
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Test'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles <!-- Debe ir dentro del <head> -->
</head>
<body class="font-sans antialiased min-h-screen bg-gradient-to-r from-blue-900 via-blue-500 to-white">
    @include('layouts.navigation') <!-- Barra de navegación principal -->
    <main class="max-w-5xl mx-auto px-4 py-6">
        @yield('content') <!-- Contenido de cada página -->
    </main>
    @livewireScripts <!-- Debe ir antes de cerrar </body> -->
</body>
</html>