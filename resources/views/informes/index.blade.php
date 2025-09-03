
@extends('layouts.app')

@section('title', 'Panel Superadministrador')

@section('content')
<div class="bg-blue-900 min-h-screen" x-data="panelApp">
    <div class="flex">
        <!-- Panel lateral -->
        <div class="w-64 bg-blue-800 min-h-screen p-4 text-white">
            <h2 class="flex items-center text-xl mb-6">
                <span class="mr-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
                Panel Admin
            </h2>

            <!-- Menú lateral -->
            <nav class="mt-6">
                <a href="#" @click.prevent="opcion = 'informes'" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </span>
                    Ver Informes
                </a>

                <a href="#" @click.prevent="opcion = 'estadisticas'" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </span>
                    Estadísticas
                </a>

                <!-- Gestión de Usuarios -->
                <a href="{{ route('admin.usuarios.index') }}" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </span>
                    Gestionar Usuarios
                </a>

                <!-- Gestión de Preguntas -->
                <a href="{{ route('admin.preguntas.index') }}" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Gestionar Preguntas
                </a>

                <!-- Gestión de Carreras -->
                <a href="{{ route('admin.carreras.index') }}" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </span>
                    Gestionar Carreras
                </a>

                <!-- Gestión de Universidades -->
                <a href="{{ route('admin.universidades.index') }}" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </span>
                    Gestionar Universidades
                </a>

                <!-- Asociar Carreras-Universidades -->
                <a href="{{ route('admin.carrera-universidad.index') }}" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                    </span>
                    Asociar Carreras
                </a>

                <!-- Gestión de Tipos de Personalidad -->
                <a href="{{ route('admin.tipos-personalidad.index') }}" class="flex items-center p-3 mb-2 rounded hover:bg-blue-700">
                    <span class="mr-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </span>
                    Tipos de Personalidad
                </a>
            </nav>
        </div>

        <!-- Contenido principal -->
        <div class="flex-1 p-8">
            <!-- Panel de informes -->
            <div class="mx-auto max-w-5xl fade-in" x-show="opcion === 'informes'">
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                        <h1 class="text-2xl font-bold text-white">Panel de Informes</h1>
                        <p class="text-blue-100 mt-1">Información general y reportes del sistema</p>
                    </div>
                    <div class="p-6">
                        <p>Selecciona una opción del menú lateral para acceder a diferentes secciones del panel.</p>
                        
                        <!-- Resumen de recursos del sistema -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Tarjeta de usuarios -->
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-700">Usuarios</h3>
                                        <a href="{{ route('admin.usuarios.index') }}" class="text-blue-500 hover:underline">Administrar →</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tarjeta de carreras -->
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-700">Carreras</h3>
                                        <a href="{{ route('admin.carreras.index') }}" class="text-green-500 hover:underline">Administrar →</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tarjeta de universidades -->
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-700">Universidades</h3>
                                        <a href="{{ route('admin.universidades.index') }}" class="text-purple-500 hover:underline">Administrar →</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tarjeta de preguntas -->
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex items-center">
                                    <div class="bg-amber-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-700">Preguntas</h3>
                                        <a href="{{ route('admin.preguntas.index') }}" class="text-amber-500 hover:underline">Administrar →</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de estadísticas -->
            <div class="mx-auto max-w-5xl fade-in" x-show="opcion === 'estadisticas'">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                        <h1 class="text-2xl font-bold text-white">Panel de Estadísticas</h1>
                        <p class="text-purple-100 mt-1">Análisis y tendencias del sistema de orientación vocacional</p>
                    </div>
                    
                    <!-- Tarjetas de métricas principales -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <!-- Tarjeta Tests Completados -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-600 text-sm font-medium">Tests Completados</p>
                                        <h3 class="text-3xl font-bold text-blue-800">{{ $totalTests }}</h3>
                                    </div>
                                    <div class="bg-blue-500 rounded-full p-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-blue-600 text-sm mt-2">
                                    <span class="font-medium">{{ $testsUltimaSemana }}</span> en la última semana
                                </p>
                            </div>
                            
                            <!-- Tarjeta Usuarios Registrados -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-600 text-sm font-medium">Usuarios Registrados</p>
                                        <h3 class="text-3xl font-bold text-green-800">{{ $totalUsuarios }}</h3>
                                    </div>
                                    <div class="bg-green-500 rounded-full p-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-green-600 text-sm mt-2">
                                    @if($totalUsuarios > 0)
                                        <span class="font-medium">{{ round($totalTests/$totalUsuarios, 1) }}</span> tests por usuario
                                    @else
                                        <span class="font-medium">0</span> tests por usuario
                                    @endif
                                </p>
                            </div>
                            
                            <!-- Tarjeta Tasa de Finalización -->
                            <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-xl border border-amber-200 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-amber-600 text-sm font-medium">Tasa de Finalización</p>
                                        <h3 class="text-3xl font-bold text-amber-800">{{ $tasaConversion }}%</h3>
                                    </div>
                                    <div class="bg-amber-500 rounded-full p-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-amber-600 text-sm mt-2">
                                    <span class="font-medium">{{ $testsIniciados - $testsCompletados }}</span> tests sin completar
                                </p>
                            </div>
                        </div>
                        
                        <!-- Sección de Personalidades RIASEC -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Tipos de Personalidad RIASEC</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiantes</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $total = $porTipoPersonalidad->sum('total'); @endphp
                                        @forelse($porTipoPersonalidad as $tipo)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $tipo->tipo_primario }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ $tipo->total }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php $porcentaje = $total > 0 ? round(($tipo->total / $total) * 100, 1) : 0; @endphp
                                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-500">{{ $porcentaje }}%</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No hay datos disponibles.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Sección de Universidades -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Carreras por Universidad</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Universidad</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carreras Institucionales</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Carreras</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distribución</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($universidadesConCarreras as $universidad)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $universidad->nombre }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $universidad->carreras_institucionales }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $universidad->total_carreras }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($universidad->total_carreras > 0)
                                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                            <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ ($universidad->carreras_institucionales / $universidad->total_carreras) * 100 }}%"></div>
                                                        </div>
                                                        <span class="text-sm text-gray-500">
                                                            {{ round(($universidad->carreras_institucionales / $universidad->total_carreras) * 100) }}% institucionales
                                                        </span>
                                                    @else
                                                        <span class="text-sm text-gray-500">Sin carreras</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No hay datos disponibles.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Gráficos -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <!-- Top Carreras Recomendadas -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Top Carreras Recomendadas</h3>
                                <div class="overflow-auto max-h-60">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Carrera</th>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Recomendaciones</th>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Match Promedio</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($carrerasMasRecomendadas as $carrera)
                                                <tr>
                                                    <td class="px-4 py-2">{{ $carrera->nombre }}</td>
                                                    <td class="px-4 py-2">
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ $carrera->total }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        {{ round($carrera->match_promedio, 1) }}%
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-2 text-center text-sm text-gray-500">
                                                        No hay datos disponibles.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Áreas de Conocimiento -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Áreas de Conocimiento</h3>
                                <div class="overflow-auto max-h-60">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Distribución</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @php $totalAreas = $porAreaConocimiento->sum('total'); @endphp
                                            @forelse($porAreaConocimiento as $area)
                                                <tr>
                                                    <td class="px-4 py-2">{{ $area->area_conocimiento }}</td>
                                                    <td class="px-4 py-2">
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                            {{ $area->total }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        @php $porcentajeArea = $totalAreas > 0 ? round(($area->total / $totalAreas) * 100, 1) : 0; @endphp
                                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $porcentajeArea }}%"></div>
                                                        </div>
                                                        <span class="text-sm text-gray-500">{{ $porcentajeArea }}%</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-2 text-center text-sm text-gray-500">
                                                        No hay datos disponibles.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js para controlar las pestañas -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('panelApp', () => ({
            opcion: 'informes'
        }))
    })
</script>

<style>
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection