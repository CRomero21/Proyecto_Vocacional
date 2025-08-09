
@extends('layouts.app')

@section('title', 'Panel Superadministrador')

@section('content')
<div class="flex min-h-screen bg-gray-50" x-data="{ opcion: 'bienvenida' }">
    <!-- Menú lateral mejorado -->
    <aside class="w-64 bg-gradient-to-b from-blue-800 to-blue-900 text-white py-8 px-4 flex flex-col items-start fixed left-0 top-16 bottom-0 z-10 shadow-lg">
        <div class="flex items-center mb-8 px-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-xl font-bold text-blue-100">Panel Admin</h2>
        </div>
        
        <nav class="w-full">
            <ul class="space-y-2 w-full">
                <li>
                    <button @click="opcion = 'informes'" class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200"
                    :class="opcion === 'informes' ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Ver Informes</span>
                    </button>
                </li>
                <li>
                    <button @click="opcion = 'usuarios'" class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200"
                    :class="opcion === 'usuarios' ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Gestionar Usuarios</span>
                    </button>
                </li>
                <li>
                    <button @click="opcion = 'estadisticas'" class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200"
                    :class="opcion === 'estadisticas' ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Estadísticas</span>
                    </button>
                </li>
                <li class="pt-2">
                    <a href="{{ route('admin.preguntas.index') }}" class="w-full flex items-center px-4 py-3 bg-green-700 hover:bg-green-800 rounded-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Gestionar Preguntas</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="mt-auto pt-6 w-full px-2">
            <div class="bg-blue-700/30 rounded-lg p-3 text-sm text-blue-200">
                <p>Sesión activa como:</p>
                <p class="font-semibold">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </aside>
    
    <!-- Contenido principal mejorado -->
    <main class="flex-1 ml-64 p-6">
        <div class="mx-auto max-w-4xl fade-in" x-show="opcion === 'bienvenida'">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                    <h1 class="text-3xl font-bold text-white">Panel del Superadministrador</h1>
                    <p class="text-blue-100 mt-2">Gestiona todos los aspectos del sistema de orientación vocacional</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                            <button @click="opcion = 'usuarios'" class="mt-3 text-green-700 hover:text-green-900 text-sm">Acceder →</button>
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
                </div>
            </div>
        </div>

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

        <div class="mx-auto max-w-5xl fade-in" x-show="opcion === 'usuarios'">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 mb-4">
                    <h1 class="text-2xl font-bold text-white">Gestión de Usuarios</h1>
                    <p class="text-green-100 mt-1">Administra los usuarios y sus permisos en el sistema</p>
                </div>
                @livewire('usuarios-gestion')
            </div>
        </div>

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

<style>
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(window.location.hash === '#usuarios') {
            document.querySelector('[x-data]').__x.$data.opcion = 'usuarios';
        }
    });
</script>
@endsection