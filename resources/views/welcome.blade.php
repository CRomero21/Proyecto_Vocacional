
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orientación Vocacional | Descubre tu camino profesional</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50">
    <!-- Barra de navegación fija -->
    <header class="fixed w-full bg-white/95 backdrop-blur-sm shadow-sm z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center">
                        <img src="{{ asset('images/logo_uno_se.png') }}" alt="UNO" class="h-12 w-auto">
                        <span class="ml-3 text-xl font-bold text-blue-900">Orientación<span class="text-blue-600">Vocacional</span></span>
                    </a>
                </div>
                
                <!-- Navegación desktop -->
                <nav class="hidden md:ml-6 md:flex md:items-center md:space-x-8">
                    <a href="#caracteristicas" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition duration-150">Características</a>
                    <a href="#como-funciona" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition duration-150">Cómo funciona</a>
                    <a href="#testimonios" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition duration-150">Testimonios</a>
                    <a href="#contacto" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition duration-150">Contacto</a>
                    
                    <div class="ml-6 flex items-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                            Ingresar
                        </a>
                        <a href="{{ route('register') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                            Registrarse
                        </a>
                    </div>
                </nav>
                
                <!-- Botón menú móvil -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-expanded="false">
                        <span class="sr-only">Abrir menú principal</span>
                        <svg class="h-6 w-6" x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menú móvil -->
        <div x-show="mobileMenuOpen" class="md:hidden bg-white shadow-lg" style="display: none;">
            <div class="pt-2 pb-3 space-y-1 px-2">
                <a href="#caracteristicas" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Características</a>
                <a href="#como-funciona" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Cómo funciona</a>
                <a href="#testimonios" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Testimonios</a>
                <a href="#contacto" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50">Contacto</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-4 space-x-3">
                    <a href="{{ route('login') }}" class="flex-1 block text-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 block text-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Registrarse
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero section -->
    <section class="relative pt-16 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/3.jpeg') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-indigo-800/80"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="md:flex md:items-center md:space-x-12">
                <div class="md:w-1/2 text-center md:text-left">
                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold mb-5">Test Vocacional RIASEC</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                        Descubre tu <span class="text-blue-300">camino profesional</span>
                    </h1>
                    <p class="mt-6 text-lg md:text-xl text-blue-50 max-w-2xl">
                        Toma nuestro test vocacional científico para conocer tus aptitudes y afinidades profesionales. Obtén un informe personalizado que te guiará hacia tu futuro profesional ideal.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row justify-center md:justify-start gap-4">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 text-center">
                            Comenzar ahora
                        </a>
                        <a href="#como-funciona" class="px-8 py-4 bg-white/10 text-white font-semibold rounded-lg border border-white/30 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-700 transition duration-150 text-center backdrop-blur-sm">
                            Conocer más
                        </a>
                    </div>
                    <div class="mt-8 flex flex-wrap justify-center md:justify-start gap-8">
                        <div class="flex items-center">
                            <div class="flex -space-x-2">
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white" src="https://randomuser.me/api/portraits/women/17.jpg" alt="">
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white" src="https://randomuser.me/api/portraits/men/4.jpg" alt="">
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white" src="https://randomuser.me/api/portraits/women/3.jpg" alt="">
                            </div>
                            <span class="ml-3 text-sm text-blue-100">Más de <span class="font-semibold text-white">5,000+</span> estudiantes</span>
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="ml-2 text-sm text-blue-100">4.8 de valoración media</span>
                        </div>
                    </div>
                </div>
                <div class="mt-12 md:mt-0 md:w-1/2 flex justify-center">
                    <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md">
                        <div class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs px-3 py-1 rounded-full">Actualizado 2025</div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Test de Orientación Vocacional</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700">Duración aproximada: 15-20 minutos</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700">Informe detallado personalizado</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                        <i class="fas fa-university"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700">Recomendaciones de carreras</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700">Análisis de aptitudes profesionales</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                                <i class="fas fa-user-plus mr-2"></i> Crear cuenta gratuita
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave separator -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 150">
                <path fill="#ffffff" fill-opacity="1" d="M0,128L80,117.3C160,107,320,85,480,90.7C640,96,800,128,960,128C1120,128,1280,96,1360,80L1440,64L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
            </svg>
        </div>
    </section>

    <!-- Características -->
    <section id="caracteristicas" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">¿Por qué elegir nuestro test vocacional?</h2>
                <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                    Descubre las ventajas de nuestra herramienta científica basada en el modelo RIASEC para guiar tu futuro profesional.
                </p>
            </div>
            
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="bg-blue-50 rounded-xl p-8 transition-all duration-300 hover:shadow-lg">
                    <div class="h-12 w-12 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-5">
                        <i class="fas fa-brain text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Respaldo científico</h3>
                    <p class="text-gray-600">
                        Basado en la teoría RIASEC de Holland, reconocida mundialmente por su precisión en la orientación vocacional.
                    </p>
                </div>
                
                <div class="bg-blue-50 rounded-xl p-8 transition-all duration-300 hover:shadow-lg">
                    <div class="h-12 w-12 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-5">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Informe personalizado</h3>
                    <p class="text-gray-600">
                        Recibe un análisis completo de tus intereses, habilidades y recomendaciones específicas para tu perfil.
                    </p>
                </div>
                
                <div class="bg-blue-50 rounded-xl p-8 transition-all duration-300 hover:shadow-lg">
                    <div class="h-12 w-12 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-5">
                        <i class="fas fa-graduation-cap text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Sugerencias de carreras</h3>
                    <p class="text-gray-600">
                        Obtendrás recomendaciones concretas de carreras universitarias y caminos profesionales alineados con tu perfil.
                    </p>
                </div>
                
                <div class="bg-blue-50 rounded-xl p-8 transition-all duration-300 hover:shadow-lg">
                    <div class="h-12 w-12 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-5">
                        <i class="fas fa-lock text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Privacidad garantizada</h3>
                    <p class="text-gray-600">
                        Tus datos y resultados están protegidos. Solo tú tienes acceso a tu información personal.
                    </p>
                </div>
                
                <div class="bg-blue-50 rounded-xl p-8 transition-all duration-300 hover:shadow-lg">
                    <div class="h-12 w-12 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-5">
                        <i class="fas fa-mobile-alt text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Acceso desde cualquier dispositivo</h3>
                    <p class="text-gray-600">
                        Realiza el test y consulta tus resultados desde tu ordenador, tablet o smartphone en cualquier momento.
                    </p>
                </div>
                
                <div class="bg-blue-50 rounded-xl p-8 transition-all duration-300 hover:shadow-lg">
                    <div class="h-12 w-12 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-5">
                        <i class="fas fa-sync-alt text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Actualizaciones constantes</h3>
                    <p class="text-gray-600">
                        Nuestro sistema se actualiza regularmente con las últimas tendencias del mercado laboral y nuevas profesiones.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Cómo funciona -->
    <section id="como-funciona" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">¿Cómo funciona?</h2>
                <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                    En solo tres sencillos pasos podrás descubrir las carreras y profesiones que mejor se adaptan a tu personalidad.
                </p>
            </div>
            
            <div class="relative">
                <!-- Línea conectora -->
                <div class="hidden md:block absolute top-1/2 left-0 right-0 h-1 bg-blue-200 -translate-y-1/2 z-0"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                    <div class="bg-white rounded-xl shadow-md p-8 transition-all duration-300 hover:shadow-xl">
                        <div class="h-14 w-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold mb-6 mx-auto">1</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 text-center">Crea tu cuenta</h3>
                        <p class="text-gray-600 text-center">
                            Regístrate en nuestra plataforma para tener acceso al test vocacional y poder guardar tus resultados.
                        </p>
                        <div class="mt-6 flex justify-center">
                            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Crear cuenta" class="h-24 opacity-75">
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-md p-8 transition-all duration-300 hover:shadow-xl">
                        <div class="h-14 w-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold mb-6 mx-auto">2</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 text-center">Realiza el test</h3>
                        <p class="text-gray-600 text-center">
                            Contesta honestamente todas las preguntas del test RIASEC para obtener resultados precisos.
                        </p>
                        <div class="mt-6 flex justify-center">
                            <img src="https://cdn-icons-png.flaticon.com/512/3596/3596149.png" alt="Realizar test" class="h-24 opacity-75">
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-md p-8 transition-all duration-300 hover:shadow-xl">
                        <div class="h-14 w-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold mb-6 mx-auto">3</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 text-center">Recibe tu informe</h3>
                        <p class="text-gray-600 text-center">
                            Analiza tus resultados y explora las carreras y profesiones recomendadas según tu perfil.
                        </p>
                        <div class="mt-6 flex justify-center">
                            <img src="https://cdn-icons-png.flaticon.com/512/3588/3588614.png" alt="Ver resultados" class="h-24 opacity-75">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Comenzar ahora <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Testimonios -->
    
    
    <!-- CTA final -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-indigo-700 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" viewBox="0 0 800 800">
                <defs>
                    <pattern id="pattern-circles" x="0" y="0" width="200" height="200" patternUnits="userSpaceOnUse" patternContentUnits="userSpaceOnUse">
                        <circle id="pattern-circle" cx="10" cy="10" r="8" fill="none" stroke="white" stroke-width="1"></circle>
                    </pattern>
                </defs>
                <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-circles)"></rect>
            </svg>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                ¿Listo para descubrir tu vocación?
            </h2>
            <p class="mt-4 text-lg text-blue-100 max-w-3xl mx-auto">
                Da el primer paso hacia tu futuro profesional. Crea una cuenta gratuita y realiza el test para recibir tu informe personalizado.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-700 font-semibold rounded-lg shadow-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 transition duration-150 text-center">
                    Crear cuenta gratuita
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 bg-transparent text-white font-semibold rounded-lg border border-white/30 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 transition duration-150 text-center">
                    Ya tengo cuenta
                </a>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer id="contacto" class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center">
                        <img src="{{ asset('images/logo_uno_se.png') }}" alt="Logo" class="h-10 w-auto">
                        <span class="ml-3 text-xl font-bold text-white">Orientación<span class="text-blue-400">Vocacional</span></span>
                    </div>
                    <p class="mt-4 text-sm text-gray-400 max-w-md">
                        Plataforma de orientación vocacional basada en el modelo RIASEC para ayudar a estudiantes a encontrar su camino profesional ideal.
                    </p>
                    <div class="mt-6 flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Información</h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white text-sm">Quiénes somos</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white text-sm">Método RIASEC</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white text-sm">Base científica</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white text-sm">Preguntas frecuentes</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white text-sm">Blog</a>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Contacto</h3>
                    <ul class="mt-4 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-400"></i>
                            <span class="text-sm text-gray-400">Universidad Nacional, Ciudad Universitaria</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2 text-gray-400"></i>
                            <span class="text-sm text-gray-400">correoprueba@gmail.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-2 text-gray-400"></i>
                            <span class="text-sm text-gray-400">aqui va el numero de informaciones</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Orientación Vocacional. Todos los derechos reservados.
                </p>
                <div class="mt-4 md:mt-0 flex space-x-6">
                    <a href="#" class="text-sm text-gray-400 hover:text-white">Política de Privacidad</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white">Términos de Servicio</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white">Cookies</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>