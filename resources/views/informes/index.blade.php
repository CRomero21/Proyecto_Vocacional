
@extends('layouts.app')

@section('title', 'Panel Superadministrador')

@section('content')
<!-- Contenedor principal con Alpine.js para manejar el estado -->
<div class="flex min-h-screen bg-gray-50" x-data="{ opcion: 'bienvenida', sidebarOpen: true }">
    <!-- Menú lateral con animación para mostrarse/ocultarse -->
    <aside class="bg-gradient-to-b from-blue-800 to-blue-900 text-white py-8 px-4 flex flex-col items-start fixed left-0 top-16 bottom-0 z-10 shadow-lg transition-all duration-300"
           :class="sidebarOpen ? 'w-64' : 'w-20'">
        
        <!-- Cabecera del menú lateral -->
        <div class="flex items-center mb-8 px-2 justify-between w-full">
            <!-- Logo y título - se oculta el texto cuando la barra está colapsada -->
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-xl font-bold text-blue-100 transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Panel Admin</h2>
            </div>
            
            <!-- Botón para colapsar/expandir el menú -->
            <button @click="sidebarOpen = !sidebarOpen" class="text-blue-300 hover:text-white focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          :d="sidebarOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'" />
                </svg>
            </button>
        </div>
        
        <!-- Menú de navegación -->
        <nav class="w-full">
            <ul class="space-y-2 w-full">
                <!-- Elemento de menú con texto que se oculta cuando la barra está colapsada -->
                <li>
                    <button @click="opcion = 'informes'" class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200"
                    :class="opcion === 'informes' ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Ver Informes</span>
                    </button>
                </li>
                <li>
                    <button @click="opcion = 'estadisticas'" class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200"
                    :class="opcion === 'estadisticas' ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Estadísticas</span>
                    </button>
                </li>
                <li>
                    <!-- Enlace directo con texto que se oculta -->
                    <a href="{{ route('admin.usuarios.index') }}" class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200 hover:bg-blue-700/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Gestionar Usuarios</span>
                    </a>
                </li>
                
                <!-- Sección de gestión académica con iconos descriptivos -->
                <li class="pt-2">
                    <a href="{{ route('admin.preguntas.index') }}" class="w-full flex items-center px-4 py-3 bg-green-700 hover:bg-green-800 rounded-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Gestionar Preguntas</span>
                    </a>
                </li>
                
                <li class="pt-2">
                    <a href="{{ route('admin.carreras.index') }}" class="w-full flex items-center px-4 py-3 bg-indigo-700 hover:bg-indigo-800 rounded-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Gestionar Carreras</span>
                    </a>
                </li>

                <li class="pt-2">
                    <a href="{{ route('admin.universidades.index') }}" class="w-full flex items-center px-4 py-3 bg-yellow-600 hover:bg-yellow-700 rounded-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Gestionar Universidades</span>
                    </a>
                </li>

                <li class="pt-2">
                    <a href="{{ route('admin.carrera-universidad.index') }}" class="w-full flex items-center px-4 py-3 bg-pink-600 hover:bg-pink-700 rounded-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Asociar Carreras-Universidades</span>
                    </a>
                </li>
                
                <li class="pt-2">
                    <a href="{{ route('admin.tipos-personalidad.index') }}" class="w-full flex items-center px-4 py-3 bg-orange-600 hover:bg-orange-700 rounded-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Gestionar RIASEC</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Información del usuario conectado - Se oculta el nombre cuando la barra está colapsada -->
        <div class="mt-auto pt-6 w-full px-2">
            <div class="bg-blue-700/30 rounded-lg p-3 text-sm text-blue-200">
                <p class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Sesión activa como:</p>
                <p class="font-semibold truncate transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">{{ Auth::user()->name }}</p>
                <!-- Mostrar solo las iniciales cuando está colapsado -->
                <p class="font-semibold text-center transition-opacity duration-300" :class="sidebarOpen ? 'opacity-0 hidden' : 'opacity-100'">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </p>
            </div>
        </div>
    </aside>
    
    <!-- Contenido principal - Se ajusta automáticamente cuando la barra cambia de tamaño -->
    <main class="flex-1 p-6 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-20'">
        <!-- Panel de bienvenida -->
        <div class="mx-auto max-w-5xl fade-in" x-show="opcion === 'bienvenida'">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Encabezado con degradado -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                    <h1 class="text-3xl font-bold text-white">Panel del Superadministrador</h1>
                    <p class="text-blue-100 mt-2">Gestiona todos los aspectos del sistema de orientación vocacional</p>
                </div>
                
                <!-- Sección de tarjetas de acceso rápido -->
                <div class="p-6">
                    <!-- Primera fila: Informes, Usuarios, Estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="font-semibold text-lg">Informes</h3>
                            <p class="text-gray-600 text-sm">Visualiza los resultados de los tests</p>
                            <button @click="opcion = 'informes'" class="mt-3 text-blue-700 hover:text-blue-900 text-sm">Acceder →</button>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-green-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="font-semibold text-lg">Usuarios</h3>
                            <p class="text-gray-600 text-sm">Administra cuentas y permisos</p>
                            <a href="{{ route('admin.usuarios.index') }}" class="mt-3 inline-block text-green-700 hover:text-green-900 text-sm">Acceder →</a>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-purple-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="font-semibold text-lg">Estadísticas</h3>
                            <p class="text-gray-600 text-sm">Analiza resultados y tendencias</p>
                            <button @click="opcion = 'estadisticas'" class="mt-3 text-purple-700 hover:text-purple-900 text-sm">Acceder →</button>
                        </div>
                    </div>
                    
                    <!-- Segunda fila: Preguntas, Carreras, Universidades -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-green-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="font-semibold text-lg">Preguntas</h3>
                            <p class="text-gray-600 text-sm">Configura las preguntas del test</p>
                            <a href="{{ route('admin.preguntas.index') }}" class="mt-3 inline-block text-green-700 hover:text-green-900 text-sm">Acceder →</a>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-indigo-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h3 class="font-semibold text-lg">Carreras</h3>
                            <p class="text-gray-600 text-sm">Gestiona las carreras disponibles</p>
                            <a href="{{ route('admin.carreras.index') }}" class="mt-3 inline-block text-indigo-700 hover:text-indigo-900 text-sm">Acceder →</a>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-yellow-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="font-semibold text-lg">Universidades</h3>
                            <p class="text-gray-600 text-sm">Administra las instituciones educativas</p>
                            <a href="{{ route('admin.universidades.index') }}" class="mt-3 inline-block text-yellow-700 hover:text-yellow-900 text-sm">Acceder →</a>
                        </div>
                    </div>
                    
                    <!-- Tercera fila: Asociación Carreras-Universidades, Perfiles RIASEC -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-pink-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-pink-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                            <h3 class="font-semibold text-lg">Asociar Carreras-Universidades</h3>
                            <p class="text-gray-600 text-sm">Vincular carreras con las universidades que las ofrecen</p>
                            <a href="{{ route('admin.carrera-universidad.index') }}" class="mt-3 inline-block text-pink-700 hover:text-pink-900 text-sm">Acceder →</a>
                        </div>

                        <div class="bg-orange-50 p-4 rounded-lg text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-orange-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                            </svg>
                            <h3 class="font-semibold text-lg">Tipos de Personalidad</h3>
                            <p class="text-gray-600 text-sm">Gestiona los perfiles RIASEC y sus descripciones</p>
                            <a href="{{ route('admin.tipos-personalidad.index') }}" class="mt-3 inline-block text-orange-700 hover:text-orange-900 text-sm">Acceder →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de informes -->
        <div class="mx-auto max-w-4xl fade-in" x-show="opcion === 'informes'">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                    <h1 class="text-2xl font-bold text-white">Informes</h1>
                    <p class="text-blue-100 mt-1">Visualiza y descarga los resultados de los tests</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-6">Aquí puedes ver y descargar los informes generados por los usuarios.</p>
                    <!-- Contenido de los informes -->
                </div>
            </div>
        </div>

        <!-- Sección de usuarios (se mantiene para compatibilidad) -->
        <div class="mx-auto max-w-5xl fade-in" x-show="opcion === 'usuarios'">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 mb-4">
                    <h1 class="text-2xl font-bold text-white">Gestión de Usuarios</h1>
                    <p class="text-green-100 mt-1">Administra los usuarios y sus permisos en el sistema</p>
                </div>
                @livewire('usuarios-gestion')
            </div>
        </div>

        <!-- Sección de estadísticas -->
        <div class="mx-auto max-w-4xl fade-in" x-show="opcion === 'estadisticas'">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                    <h1 class="text-2xl font-bold text-white">Estadísticas</h1>
                    <p class="text-purple-100 mt-1">Analiza tendencias y métricas</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-6">Consulta estadísticas y reportes generales del sistema.</p>
                    <!-- Contenido de las estadísticas -->
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Estilos para las animaciones -->
<style>
    /* Animación de entrada suave para los paneles */
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Scripts adicionales para manejar eventos de la página -->
<script>
    // Detectar si hay un hash en la URL para mostrar una sección específica
    document.addEventListener('DOMContentLoaded', function() {
        if(window.location.hash === '#usuarios') {
            document.querySelector('[x-data]').__x.$data.opcion = 'usuarios';
        }
    });
</script>
@endsection