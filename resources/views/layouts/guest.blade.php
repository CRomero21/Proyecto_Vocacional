
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Orientación Vocacional') }}</title>
    
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
        
        /* Animación para los elementos flotantes */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-delay-1 {
            animation: float 7s ease-in-out 1s infinite;
        }
        
        .animate-float-delay-2 {
            animation: float 8s ease-in-out 2s infinite;
        }
        
        /* Efecto de glassmorphism */
        .glass-effect {
            background: rgba(240, 244, 250, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        /* Patrón de puntos */
        .dot-pattern {
            background-image: radial-gradient(#0079f4 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="font-sans text-gray-800 antialiased h-screen">
    <div class="h-full flex flex-col md:flex-row">
        <!-- Panel izquierdo decorativo - Visible solo en desktop -->
        <div class="hidden md:block md:w-1/2 bg-gradient-to-br from-[#051a9a] via-[#0b3be9] to-[#0079f4] relative overflow-hidden">
            <!-- Elementos decorativos flotantes -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 rounded-full bg-[#00aeff]/20 animate-float"></div>
            <div class="absolute bottom-1/3 right-1/5 w-40 h-40 rounded-full bg-[#0b3be9]/30 animate-float-delay-1"></div>
            <div class="absolute top-2/3 left-1/3 w-32 h-32 rounded-full bg-[#051a9a]/20 animate-float-delay-2"></div>
            
            <!-- Patrón de puntos -->
            <div class="absolute inset-0 dot-pattern opacity-10"></div>
            
            <!-- Contenido del panel -->
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-white p-8">
                <img src="{{ asset('images/logo_uno_blanco.png') }}" alt="Logo" class="h-16 mb-12">
                
                <h1 class="text-3xl font-bold mb-6">Descubre tu futuro profesional</h1>
                
                <div class="max-w-md">
                    <p class="text-[#f2f2f2]/90 mb-8 text-center">
                        Nuestro sistema de orientación vocacional te ayudará a encontrar las carreras 
                        más adecuadas para ti según tus intereses y habilidades.
                    </p>
                    
                    <!-- Tarjetas de beneficios -->
                    <div class="space-y-4">
                        <div class="glass-effect rounded-lg p-4 backdrop-blur-md bg-white/10">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-white/20 rounded-full p-2 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white mb-1">Tests profesionales</h3>
                                    <p class="text-sm text-[#f2f2f2]/80">Basados en metodologías reconocidas y estudios psicométricos avanzados</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="glass-effect rounded-lg p-4 backdrop-blur-md bg-white/10">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-white/20 rounded-full p-2 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white mb-1">+300 carreras analizadas</h3>
                                    <p class="text-sm text-[#f2f2f2]/80">Información detallada sobre programas académicos, universidades y oportunidades laborales</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho con formulario -->
        <div class="w-full md:w-1/2 bg-[#f0f4fa] flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Logo para móviles (visible solo en mobile) -->
                <div class="flex flex-col items-center mb-8 md:hidden">
                    <img src="{{ asset('images/logo_uno.png') }}" alt="Logo" class="h-16 mb-4">
                    <h1 class="text-2xl font-bold text-[#131e58]">Orientación Vocacional</h1>
                </div>
                
                <!-- Tarjeta del formulario -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <!-- Encabezado del formulario -->
                    <div class="bg-gradient-to-r from-[#051a9a] to-[#0b3be9] px-6 py-5">
                        <h2 class="text-xl font-semibold text-white">
                            {{ Route::currentRouteName() == 'login' ? 'Iniciar Sesión' : 'Crear Cuenta' }}
                        </h2>
                        <p class="text-[#f2f2f2]/80 text-sm">
                            {{ Route::currentRouteName() == 'login' ? 'Accede a tu cuenta personal' : 'Regístrate para comenzar' }}
                        </p>
                    </div>
                    
                    <!-- Cuerpo del formulario -->
                    <div class="p-6">
                        {{ $slot }}
                        
                        <!-- Links adicionales dentro del formulario -->
                        <div class="mt-6 text-center text-sm text-gray-600 pt-4 border-t border-gray-100">
                            @if(Route::has('login') && Route::currentRouteName() == 'register')
                                <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="font-medium text-[#0b3be9] hover:text-[#051a9a]">Inicia sesión</a></p>
                            @elseif(Route::has('register') && Route::currentRouteName() == 'login')
                                <p>¿No tienes una cuenta? <a href="{{ route('register') }}" class="font-medium text-[#0b3be9] hover:text-[#051a9a]">Regístrate</a></p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Footer para ambos tamaños -->
                <div class="mt-8 text-center text-xs text-gray-500">
                    &copy; {{ date('Y') }} Orientación Vocacional. Todos los derechos reservados.
                </div>
            </div>
        </div>
    </div>
</body>
</html>