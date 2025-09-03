

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <title>{{ config('app.name', 'Orientación Vocacional') }} - @yield('title', 'Plataforma Educativa')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Styles adicionales -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Paleta de colores institucional */
        :root {
            --color-primary: #0b3be9;
            --color-primary-dark: #051a9a;
            --color-primary-darker: #131e58;
            --color-primary-light: #0079f4;
            --color-primary-lighter: #00aeff;
            --color-gray: #c8c8c8;
            --color-gray-light: #f2f2f2;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Animaciones personalizadas */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        @keyframes slideInFromRight {
            0% { transform: translateX(10%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        
        .animate-slideIn {
            animation: slideInFromRight 0.3s ease-out forwards;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#f2f2f2] text-gray-800">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Flash Messages -->
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 animate-fadeIn">
                <div class="rounded-md bg-green-50 p-4 border border-green-200 shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button @click="show = false" type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
        
        <!-- Footer -->
        <footer class="bg-[#131e58] text-white py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <img src="{{ asset('images/logo_uno_blanco.png') }}" alt="Logo" class="h-8 mb-2">
                        <p class="text-sm text-[#f2f2f2]/70">
                            Orientación Vocacional para el futuro profesional
                        </p>
                    </div>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-8 text-sm">
                        <a href="#" class="hover:text-[#00aeff] transition-colors">Sobre Nosotros</a>
                        <a href="#" class="hover:text-[#00aeff] transition-colors">Privacidad</a>
                        <a href="#" class="hover:text-[#00aeff] transition-colors">Términos</a>
                        <a href="#" class="hover:text-[#00aeff] transition-colors">Contacto</a>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-[#f2f2f2]/10 text-xs text-center text-[#f2f2f2]/50">
                    &copy; {{ date('Y') }} Orientación Vocacional. Todos los derechos reservados.
                </div>
            </div>
        </footer>
    </div>
    
    @stack('scripts')
</body>
</html>