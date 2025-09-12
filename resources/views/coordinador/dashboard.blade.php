
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header con Bienvenida -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">¡Bienvenido, Coordinador!</h1>
            <p class="text-lg text-gray-600">Gestiona y analiza el rendimiento de los estudiantes con nuestras herramientas avanzadas.</p>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-4 rounded-full bg-white/20 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-blue-100 text-sm uppercase tracking-wide">Total Estudiantes</p>
                        <p class="text-3xl font-bold">{{ $totalEstudiantes ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-4 rounded-full bg-white/20 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-green-100 text-sm uppercase tracking-wide">Tests Realizados</p>
                        <p class="text-3xl font-bold">{{ $totalTests ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-4 rounded-full bg-white/20 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-purple-100 text-sm uppercase tracking-wide">Tasa de Finalización</p>
                        <p class="text-3xl font-bold">{{ $tasaConversion ?? '0%' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Herramientas y Recursos -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Herramientas y Recursos
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Estadísticas del Sistema -->
                    <a href="{{ route('admin.estadisticas.index') }}" class="group block p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border border-indigo-200 rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="flex items-center">
                            <div class="p-4 rounded-full bg-indigo-200 group-hover:bg-indigo-300 transition-colors duration-300 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 group-hover:text-indigo-700 transition-colors duration-300">Estadísticas del Sistema</h3>
                                <p class="mt-2 text-gray-600">Analiza tendencias y métricas clave del sistema.</p>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Informes Avanzados -->
                    <a href="{{ route('admin.informes-avanzados.index') }}" class="group block p-6 bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="flex items-center">
                            <div class="p-4 rounded-full bg-amber-200 group-hover:bg-amber-300 transition-colors duration-300 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 group-hover:text-amber-700 transition-colors duration-300">Informes Avanzados</h3>
                                <p class="mt-2 text-gray-600">Genera reportes detallados y herramientas de análisis.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Nota Motivacional -->
        <div class="mt-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <div>
                    <h3 class="text-xl font-bold mb-2">¡Impulsa el Éxito Educativo!</h3>
                    <p class="text-green-100">Utiliza estas herramientas para apoyar a los estudiantes en su camino hacia el éxito profesional.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection