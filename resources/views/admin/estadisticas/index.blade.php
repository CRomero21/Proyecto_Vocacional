
@extends('layouts.app')

@section('title', 'Análisis de Datos')

@section('content')
<div class="bg-gradient-to-br from-blue-900 to-indigo-900 min-h-screen p-4 md:p-8">
    <div class="mx-auto max-w-7xl">
    <!-- ...contenido HTML... -->
        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ $error }}</span>
            </div>
        @endif
        
        <!-- Breadcrumb y Título -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center text-sm text-white/70">
                <a href="{{ route('informes.index') }}" class="hover:text-white">Dashboard</a>
                <svg class="w-3 h-3 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white">Análisis de Datos</span>
            </div>
            <div class="flex space-x-2">
                <button id="export-pdf" class="flex items-center text-white/80 hover:text-white bg-indigo-600 rounded-md px-3 py-1.5">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Exportar PDF</span>
                </button>
                <a href="{{ route('informes.index') }}" class="flex items-center text-white/80 hover:text-white bg-white/10 rounded-md px-3 py-1.5">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Volver</span>
                </a>
            </div>
        </div>

        <!-- Panel de Filtros -->
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-6 border border-white/20">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <h1 class="text-xl md:text-2xl font-bold text-white">Análisis de Datos del Sistema</h1>
                
                <div class="flex flex-wrap gap-2">
                    <select id="periodo" class="bg-white/10 text-white border border-white/20 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-400">
                        <option value="7">Últimos 7 días</option>
                        <option value="30" selected>Últimos 30 días</option>
                        <option value="90">Últimos 90 días</option>
                        <option value="365">Último año</option>
                    </select>
                    
                    <select id="departamento" class="bg-white/10 text-white border border-white/20 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-400">
                        <option value="">Todos los departamentos</option>
                        @foreach($departamentos ?? [] as $depto)
                            <option value="{{ $depto }}" {{ ($departamentoFiltro ?? '') == $depto ? 'selected' : '' }}>{{ $depto }}</option>
                        @endforeach
                    </select>
                    
                    <button id="aplicar-filtros" class="bg-indigo-500 hover:bg-indigo-600 text-white rounded-md px-4 py-1.5 text-sm">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Resumen General -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6">
            <div class="border-b border-gray-200 bg-white p-4">
                <h2 class="text-xl font-semibold text-gray-800">Resumen General</h2>
                <p class="text-sm text-gray-500">Estadísticas del sistema</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Usuarios -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-800 text-sm font-medium">Total Usuarios</p>
                                <h3 class="text-2xl font-bold text-blue-900 mt-1">{{ $totalUsuarios }}</h3>
                            </div>
                            <div class="bg-blue-500 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tests Iniciados -->
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-800 text-sm font-medium">Tests Iniciados</p>
                                <h3 class="text-2xl font-bold text-purple-900 mt-1">{{ $testsIniciados }}</h3>
                            </div>
                            <div class="bg-purple-500 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tests Completados -->
                    <div class="bg-pink-50 p-4 rounded-lg border border-pink-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-pink-800 text-sm font-medium">Tests Completados</p>
                                <h3 class="text-2xl font-bold text-pink-900 mt-1">{{ $testsCompletados }}</h3>
                            </div>
                            <div class="bg-pink-500 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tasa de Completitud -->
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-800 text-sm font-medium">Tasa de Completitud</p>
                                <h3 class="text-2xl font-bold text-green-900 mt-1">
                                    @if(isset($testsIniciados) && $testsIniciados > 0)
                                        {{ round(($testsCompletados / $testsIniciados) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </h3>
                            </div>
                            <div class="bg-green-500 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Satisfacción y Retroalimentación -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6">
            <div class="border-b border-gray-200 bg-white p-4">
                <h2 class="text-xl font-semibold text-gray-800">Satisfacción del Usuario</h2>
                <p class="text-sm text-gray-500">Feedback y valoraciones del test</p>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Valoración promedio -->
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                    <h3 class="text-md font-medium text-indigo-800 mb-2">Valoración Promedio</h3>
                    <div class="flex items-center">
                        <div class="text-3xl font-bold text-indigo-900">{{ number_format($utilidadPromedio ?? 0, 2) }}</div>
                        <div class="ml-2 flex">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ ($utilidadPromedio ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <div class="ml-2 text-sm text-gray-600">({{ $totalValoraciones ?? 0 }} valoraciones)</div>
                    </div>
                </div>

                <!-- Promedios de utilidad y precisión de retroalimentaciones -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <h3 class="text-md font-medium text-blue-800 mb-2">Promedios de Retroalimentación</h3>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center">
                            <span class="text-blue-700 font-semibold mr-2">Utilidad:</span>
                            <span class="text-2xl font-bold text-blue-900">{{ number_format($utilidadPromedio ?? 0, 2) }}</span>
                            <span class="ml-2 text-gray-500 text-sm">/ 5</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-blue-700 font-semibold mr-2">Precisión:</span>
                            <span class="text-2xl font-bold text-blue-900">{{ number_format($precisionPromedio ?? 0, 2) }}</span>
                            <span class="ml-2 text-gray-500 text-sm">/ 5</span>
                        </div>
                    </div>
                </div>
                
                <!-- Distribución de valoraciones -->
                <div class="space-y-2">
                    @for($i = 5; $i >= 1; $i--)
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 w-10">{{ $i }} ★</span>
                            <div class="flex-grow h-4 mx-2 bg-gray-200 rounded-full overflow-hidden">
                                @php
                                    $porcentaje = isset($distribucionValoraciones) && isset($distribucionValoraciones[$i]) && $totalValoraciones > 0 
                                        ? ($distribucionValoraciones[$i] / $totalValoraciones) * 100 
                                        : 0;
                                @endphp
                                <div class="h-full bg-yellow-400" style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-10">
                                {{ isset($distribucionValoraciones) && isset($distribucionValoraciones[$i]) ? $distribucionValoraciones[$i] : 0 }}
                            </span>
                        </div>
                    @endfor
                </div>
            </div>
            
            <!-- Comentarios recientes -->
            <div class="px-6 pb-6">
                <h3 class="text-md font-medium text-gray-700 mb-3 border-t pt-4">Comentarios recientes</h3>
                <div class="shadow-lg border border-gray-300 rounded-xl bg-white max-h-64 overflow-y-auto p-4 space-y-3" style="scrollbar-width: thin;">
                    @forelse($comentariosRecientes ?? [] as $comentario)
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    <div class="text-gray-700 font-medium">{{ $comentario->usuario }}</div>
                                    <div class="ml-2 flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ ($comentario->valoracion ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $comentario->fecha }}</div>
                            </div>
                            <p class="text-gray-600 text-sm mt-1">{{ $comentario->texto }}</p>
                        </div>
                    @empty
                        <div class="text-gray-500 text-center py-4">No hay comentarios recientes</div>
                    @endforelse
                </div>
            </div>
        </div>
            
        <!-- Análisis de Segmentación -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Segmentación Demográfica -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="border-b border-gray-200 bg-white p-4">
                    <h2 class="text-xl font-semibold text-gray-800">Segmentación de Usuarios</h2>
                    <p class="text-sm text-gray-500">Análisis por demografía y comportamiento</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Distribución por Edad -->
                        <div>
                            <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Distribución por Edad
                            </h3>
                            <div class="chart-container" style="position: relative; height:200px; width:100%">
                                <canvas id="chartEdad"></canvas>
                            </div>
                        </div>
                        
                        <!-- Distribución por Género -->
                        <div>
                            <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Distribución por Género
                            </h3>
                            <div class="flex flex-row items-center gap-6">
                                <div class="chart-container" style="position: relative; height:200px; width:200px; min-width:200px;">
                                    <canvas id="chartGenero"></canvas>
                                </div>
                                <div id="leyendaGenero" class="flex flex-col gap-2 text-sm"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Análisis de Instituciones -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="border-b border-gray-200 bg-white p-4">
                    <h2 class="text-xl font-semibold text-gray-800">Instituciones Principales</h2>
                    <p class="text-sm text-gray-500">Distribución por institución</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Top 5 Instituciones -->
                        <div>
                            <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Top 5 Instituciones con Mayor Participación
                            </h3>
                            <div class="mt-3 space-y-3">
                                @forelse($topInstituciones ?? [] as $index => $institucion)
                                    <div class="flex items-center">
                                        <span class="text-gray-600 text-sm mr-2 w-28 truncate">{{ $institucion->nombre }}</span>
                                        <div class="relative flex-grow h-6 bg-gray-200 rounded">
                                            <div class="absolute top-0 left-0 h-6 rounded bg-indigo-500" style="width: {{ $institucion->porcentaje }}%"></div>
                                            <span class="absolute inset-0 flex items-center justify-end px-2 text-xs font-semibold text-gray-800">{{ $institucion->usuarios }} ({{ $institucion->porcentaje }}%)</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-gray-500 text-sm">No hay datos disponibles</div>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Análisis de Departamentos -->
                        <div>
                            <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Distribución por Departamento
                            </h3>
                            <div class="chart-container" style="position: relative; height:200px; width:100%">
                                <canvas id="chartDepartamentos"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Análisis de Personalidad y Recomendaciones -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Tipos de Personalidad -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="border-b border-gray-200 bg-white p-4">
                    <h2 class="text-xl font-semibold text-gray-800">Tipos de Personalidad</h2>
                    <p class="text-sm text-gray-500">Análisis psicográfico</p>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-row items-center gap-6">
                        <div class="chart-container" style="position: relative; height:300px; width:300px; min-width:200px;">
                            <canvas id="chartTiposPersonalidad"></canvas>
                        </div>
                        <div id="leyendaPersonalidad" class="flex flex-col gap-2 text-sm"></div>
                    </div>
                    
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <h3 class="text-md font-medium text-gray-700 mb-2">Insights para Marketing</h3>
                        <ul class="space-y-2">
                            @if(isset($tipoPersonalidadDominante) && isset($porcentajeDominante))
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">El tipo de personalidad <span class="font-medium">{{ $tipoPersonalidadDominante }}</span> predomina con un {{ $porcentajeDominante }}% de usuarios.</span>
                            </li>
                            @endif
                            
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Recomendación: Adaptar el contenido para destacar aspectos de autoconocimiento y crecimiento personal.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Carreras más Recomendadas -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="border-b border-gray-200 bg-white p-4">
                    <h2 class="text-xl font-semibold text-gray-800">Carreras más Recomendadas</h2>
                    <p class="text-sm text-gray-500">Análisis de recomendaciones del sistema</p>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-row items-center gap-6">
                        <div class="chart-container" style="position: relative; height:300px; width:300px; min-width:200px;">
                            <canvas id="chartCarreras"></canvas>
                        </div>
                        <div id="leyendaCarreras" class="flex flex-col gap-2 text-sm"></div>
                    </div>
                    
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <h3 class="text-md font-medium text-gray-700 mb-3">Top 5 Carreras Recomendadas</h3>
                        <div id="listaCarreras" class="space-y-3">
                            <!-- Se llenarán dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
</script>
</script>
        </div>
        
        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.estadisticas.excel') }}" id="exportar-excel" class="flex items-center justify-center px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-md hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exportar a Excel
            </a>
            <a href="{{ route('admin.informes.index') }}" class="flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow-md hover:bg-indigo-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Informes Avanzados
            </a>
        </div>
    </div>
</div>

<!-- Scripts para las gráficas -->
<script>
// Variables generadas por Blade para los gráficos
const carrerasSeleccionadasTop = @json($carrerasSeleccionadasTop ?? []);
const estudiantesPorDepartamento = @json($estudiantesPorDepartamento ?? []);
const porTipoPersonalidad = @json($porTipoPersonalidad ?? []);
const carrerasMasRecomendadas = @json($carrerasMasRecomendadas ?? []);
const distribucionPorEdad = @json($distribucionPorEdad ?? []);
const distribucionPorGenero = @json($distribucionPorGenero ?? []);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            console.log('DOM Cargado - Iniciando gráficos');
        } catch(e) {
            console.error('Error al inicializar:', e);
        }
        
        // Configuración de colores mejorada
        const colores = {
            azules: ['rgba(59, 130, 246, 0.8)', 'rgba(37, 99, 235, 0.8)', 'rgba(29, 78, 216, 0.8)'],
            morados: ['rgba(139, 92, 246, 0.8)', 'rgba(124, 58, 237, 0.8)', 'rgba(109, 40, 217, 0.8)'],
            verdes: ['rgba(16, 185, 129, 0.8)', 'rgba(5, 150, 105, 0.8)', 'rgba(4, 120, 87, 0.8)'],
            naranjas: ['rgba(249, 115, 22, 0.8)', 'rgba(234, 88, 12, 0.8)', 'rgba(194, 65, 12, 0.8)'],
            rosas: ['rgba(236, 72, 153, 0.8)', 'rgba(219, 39, 119, 0.8)', 'rgba(190, 24, 93, 0.8)'],
            grises: ['rgba(107, 114, 128, 0.8)', 'rgba(75, 85, 99, 0.8)', 'rgba(55, 65, 81, 0.8)'],
            pastel: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(199, 199, 199, 0.8)',
                'rgba(83, 102, 255, 0.8)',
                'rgba(78, 235, 133, 0.8)',
                'rgba(255, 99, 255, 0.8)'
            ]
        };
        
        // Función para crear leyendas personalizadas
        function createCustomLegend(legendId, chart) {
            const legendElement = document.getElementById(legendId);
            if (!legendElement || !chart) return;

            const data = chart.data.datasets[0].data;
            const total = data.reduce((a, b) => a + b, 0);
            const colors = chart.data.datasets[0].backgroundColor;
            
            legendElement.innerHTML = chart.data.labels.map((label, idx) => {
                const value = data[idx];
                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                const color = Array.isArray(colors) ? colors[idx] : colors;
                
                return `
                    <div class="flex items-center gap-2 mb-2">
                        <span style="display:inline-block;width:16px;height:16px;border-radius:50%;background:${color};border:1px solid #ccc;"></span>
                        <span class="font-medium text-gray-700">${label}</span>
                        <span class="text-blue-600 font-semibold ml-auto">${value} (${percentage}%)</span>
                    </div>
                `;
            }).join('');
        }

        // Datos geográficos
        let estudiantesPorDepartamento = {!! json_encode($distribucionPorDepartamento ?? []) !!};
        let departamentosLabels = [];
        let departamentosData = [];
        
        try {
            console.log('Datos de departamentos crudos:', estudiantesPorDepartamento);
            if (estudiantesPorDepartamento && Array.isArray(estudiantesPorDepartamento)) {
                estudiantesPorDepartamento.forEach(item => {
                    if (item && item.departamento !== undefined && item.total !== undefined) {
                        departamentosLabels.push(item.departamento);
                        departamentosData.push(item.total);
                    }
                });
            }
            console.log('Datos de departamentos procesados:', {labels: departamentosLabels, data: departamentosData});
        } catch(e) {
            console.error('Error al procesar datos geográficos:', e);
        }
        
        const datosGeograficos = {
            labels: departamentosLabels,
            datasets: [{
                label: 'Estudiantes',
                data: departamentosData,
                backgroundColor: colores.verdes,
                borderWidth: 1
            }]
        };

        // Datos personalidad
        let porTipoPersonalidad = [];
        let personalidadLabels = [];
        let personalidadData = [];
        
        try {
            // ...existing code...
            if (porTipoPersonalidad && Array.isArray(porTipoPersonalidad)) {
                porTipoPersonalidad.forEach(item => {
                    personalidadLabels.push(item.tipo_primario);
                    personalidadData.push(item.total);
                });
            }
        } catch(e) {
            console.error('Error al procesar datos de personalidad:', e);
        }
        
        const datosPersonalidad = {
            labels: personalidadLabels,
            datasets: [{
                label: 'Distribución',
                data: personalidadData,
                backgroundColor: colores.pastel.slice(0, personalidadLabels.length),
                borderWidth: 1
            }]
        };


        // Datos carreras - CORREGIDO
        let carrerasMasRecomendadas = {!! json_encode($carrerasMasRecomendadas ?? []) !!};
        let carrerasLabels = [];
        let carrerasData = [];
        let carrerasMatch = [];
        
        // Reemplazar la sección donde procesa los datos de carreras
        try {
            // Obtener datos y mostrar para depuración
            console.log('Datos de carreras crudos:', carrerasMasRecomendadas);

            // Siempre asumimos que es un array simple (no un objeto complejo)
            if (carrerasMasRecomendadas && Array.isArray(carrerasMasRecomendadas) && carrerasMasRecomendadas.length > 0) {
                // Extraer propiedades de forma segura
                carrerasLabels = carrerasMasRecomendadas.map(item => item.nombre || 'Sin nombre');
                carrerasData = carrerasMasRecomendadas.map(item => parseInt(item.total || 0));
                carrerasMatch = carrerasMasRecomendadas.map(item => parseFloat(item.match_promedio || 0));

                console.log('Datos de carreras procesados correctamente');
            } else {
                console.warn('No hay datos de carreras disponibles');
                carrerasLabels = ['No hay datos disponibles'];
                carrerasData = [0];
            }
        } catch(e) {
            console.error('Error al procesar datos de carreras:', e);
            carrerasLabels = ['Error de procesamiento'];
            carrerasData = [0];
        }
        
        // Datasets para el gráfico de carreras
        const datosCarreras = {
            labels: carrerasLabels,
            datasets: [{
                label: 'Total Recomendaciones',
                data: carrerasData,
                backgroundColor: colores.pastel,
                borderWidth: 1,
                borderColor: colores.pastel.map(color => color.replace('0.8', '1')),
                barPercentage: 0.7,
                categoryPercentage: 0.8
            }]
        };
        
        // Obtener datos de edad desde el controlador
        let distribucionEdadLabels = ['16-18', '19-21', '22-25', '26-30', '31+'];
        let distribucionEdadData = [0, 0, 0, 0, 0];
        
        try {
            // ...existing code...
            console.log('Datos de edad crudos:', distribucionPorEdad);
            
            if (distribucionPorEdad && Array.isArray(distribucionPorEdad)) {
                // Si los datos vienen como array de objetos con rango y total
                if (distribucionPorEdad.length > 0 && 'rango' in distribucionPorEdad[0]) {
                    distribucionEdadLabels = [];
                    distribucionEdadData = [];
                    distribucionPorEdad.forEach(item => {
                        distribucionEdadLabels.push(item.rango);
                        distribucionEdadData.push(item.total);
                    });
                } 
                // Si los datos vienen como conteos directos
                else if (distribucionPorEdad.length === 5) {
                    distribucionEdadData = distribucionPorEdad;
                }
            }
            console.log('Datos de edad procesados:', {labels: distribucionEdadLabels, data: distribucionEdadData});
        } catch(e) {
            console.error('Error al procesar datos de edad:', e);
        }
        
        const distribucionEdad = {
            labels: distribucionEdadLabels,
            datasets: [{
                label: 'Usuarios por Rango de Edad',
                data: distribucionEdadData,
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(104, 109, 224, 0.8)',
                    'rgba(126, 214, 223, 0.8)',
                    'rgba(129, 236, 236, 0.8)',
                    'rgba(72, 219, 251, 0.8)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        };
        
        // Obtener datos de género desde el controlador - CORREGIDO
        let distribucionGeneroLabels = ['Femenino', 'Masculino', 'No especificado'];
        let distribucionGeneroData = [0, 0, 0];
        
        try {
            // ...existing code...
            console.log('Datos de género crudos:', distribucionPorGenero);
            
            // Si es un array de objetos con genero y total (formato nuevo)
            if (distribucionPorGenero && Array.isArray(distribucionPorGenero)) {
                distribucionGeneroLabels = [];
                distribucionGeneroData = [];
                
                // Asegurarnos de que los géneros aparezcan en el orden correcto para aplicar los colores correctamente
                const ordenGeneros = {
                    'Femenino': 0,
                    'Masculino': 1,
                    'No especificado': 2
                };
                
                // Ordenar el array por el orden predefinido
                const generoOrdenado = [...distribucionPorGenero].sort((a, b) => {
                    const ordenA = ordenGeneros[a.genero] !== undefined ? ordenGeneros[a.genero] : 999;
                    const ordenB = ordenGeneros[b.genero] !== undefined ? ordenGeneros[b.genero] : 999;
                    return ordenA - ordenB;
                });
                
                generoOrdenado.forEach(item => {
                    distribucionGeneroLabels.push(item.genero);
                    distribucionGeneroData.push(item.total);
                });
            }
            console.log('Datos de género procesados:', {labels: distribucionGeneroLabels, data: distribucionGeneroData});
        } catch(e) {
            console.error('Error al procesar datos de género:', e);
        }
        
        // Colores específicos para cada género - Rosa para Femenino, Azul para Masculino, Gris para No especificado
        const coloresGenero = [];
        distribucionGeneroLabels.forEach(genero => {
            if (genero === 'Femenino') {
                coloresGenero.push('rgba(236, 72, 153, 0.8)'); // Rosa
            } else if (genero === 'Masculino') {
                coloresGenero.push('rgba(59, 130, 246, 0.8)'); // Azul
            } else {
                coloresGenero.push('rgba(107, 114, 128, 0.8)'); // Gris
            }
        });
        
        const distribucionGenero = {
            labels: distribucionGeneroLabels,
            datasets: [{
                data: distribucionGeneroData,
                backgroundColor: coloresGenero,
                borderWidth: 1
            }]
        };
        
        // Crear gráficos con opciones mejoradas
        try {
            // Gráfico de Edad
            if(document.getElementById('chartEdad')) {
                new Chart(document.getElementById('chartEdad'), {
                    type: 'bar',
                    data: distribucionEdad,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((context.raw / total) * 100);
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1500
                        }
                    }
                });
            }

            // Gráfico de Género
            let generoChart = null;
            if(document.getElementById('chartGenero')) {
                generoChart = new Chart(document.getElementById('chartGenero'), {
                    type: 'doughnut',
                    data: distribucionGenero,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                display: false // Oculta la leyenda automática
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((context.raw / total) * 100);
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1500
                        }
                    }
                });
                // Mostrar leyenda personalizada alineada a la derecha
                setTimeout(function() {
                    const leyendaGenero = document.getElementById('leyendaGenero');
                    if (leyendaGenero && generoChart) {
                        const data = generoChart.data.datasets[0].data;
                        const total = data.reduce((a, b) => a + b, 0);
                        const colores = generoChart.data.datasets[0].backgroundColor;
                        leyendaGenero.innerHTML = generoChart.data.labels.map((label, idx) => {
                            const val = data[idx];
                            const porcentaje = total > 0 ? Math.round((val / total) * 100) : 0;
                            const color = colores[idx];
                            return `<span class=\"flex items-center gap-2 mb-1\"><span style=\"display:inline-block;width:16px;height:16px;border-radius:50%;background:${color};border:1px solid #ccc;\"></span><span class='font-semibold'>${label}:</span> <span class='text-blue-700 font-bold'>${porcentaje}%</span></span>`;
                        }).join('');
                    }
                }, 300);
            }
            
            // Gráfico de Departamentos
            if(document.getElementById('chartDepartamentos') && departamentosData.length > 0 && departamentosData.some(val => val > 0)) {
                console.log('Creando gráfico de departamentos...');
                const departamentosChart = new Chart(document.getElementById('chartDepartamentos'), {
                    type: 'bar',
                    data: datosGeograficos,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        animation: {
                            duration: 1500
                        }
                    }
                });
                
                // Crear leyenda personalizada
                setTimeout(() => createCustomLegend('leyendaDepartamentos', departamentosChart), 100);
            } else {
                console.log('No hay datos de departamentos o elemento no encontrado');
                const leyenda = document.getElementById('leyendaDepartamentos');
                if (leyenda) leyenda.innerHTML = '<p class="text-gray-500">No hay datos de departamentos disponibles</p>';
            }
            
            // Gráfico de Tipos de Personalidad
            if(document.getElementById('chartTiposPersonalidad') && personalidadData.length > 0 && personalidadData.some(val => val > 0)) {
                console.log('Creando gráfico de personalidad...');
                const personalidadChart = new Chart(document.getElementById('chartTiposPersonalidad'), {
                    type: 'polarArea',
                    data: datosPersonalidad,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((context.raw / total) * 100);
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            duration: 1500
                        }
                    }
                });
                
                // Crear leyenda personalizada
                setTimeout(() => createCustomLegend('leyendaPersonalidad', personalidadChart), 100);
            } else {
                console.log('No hay datos de personalidad o elemento no encontrado');
                const leyenda = document.getElementById('leyendaPersonalidad');
                if (leyenda) leyenda.innerHTML = '<p class="text-gray-500">No hay datos de personalidad disponibles</p>';
            }
            
            // Gráfico de Carreras
            if(document.getElementById('chartCarreras') && carrerasData.length > 0 && carrerasData.some(val => val > 0)) {
                console.log('Creando gráfico de carreras...');
                const carrerasChart = new Chart(document.getElementById('chartCarreras'), {
                    type: 'bar',
                    data: datosCarreras,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Total recomendaciones: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        const label = this.getLabelForValue(value);
                                        if (label && label.length > 25) {
                                            return label.substring(0, 22) + '...';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1500
                        }
                    }
                });
                
                // Crear leyenda personalizada
                setTimeout(() => createCustomLegend('leyendaCarreras', carrerasChart), 100);
                
                // Crear lista de carreras
                const listaCarreras = document.getElementById('listaCarreras');
                if (listaCarreras && carrerasLabels.length > 0) {
                    listaCarreras.innerHTML = carrerasLabels.map((carrera, index) => {
                        const valor = carrerasData[index];
                        return `
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <span class="font-medium text-gray-700">${carrera}</span>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-semibold">${valor} recomendaciones</span>
                            </div>
                        `;
                    }).join('');
                }
            } else {
                console.log('No hay datos de carreras o elemento no encontrado');
                const leyenda = document.getElementById('leyendaCarreras');
                if (leyenda) leyenda.innerHTML = '<p class="text-gray-500">No hay datos de carreras disponibles</p>';
                
                const lista = document.getElementById('listaCarreras');
                if (lista) lista.innerHTML = '<p class="text-gray-500 text-center py-4">No hay carreras recomendadas para mostrar</p>';
            }
        } catch (error) {
            console.error('Error al crear gráficos:', error);
        }
        
        // Exportación a PDF
        document.getElementById('export-pdf').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            
            // Mostrar mensaje de carga
            const loadingMessage = document.createElement('div');
            loadingMessage.style.position = 'fixed';
            loadingMessage.style.top = '0';
            loadingMessage.style.left = '0';
            loadingMessage.style.width = '100%';
            loadingMessage.style.height = '100%';
            loadingMessage.style.backgroundColor = 'rgba(0,0,0,0.7)';
            loadingMessage.style.display = 'flex';
            loadingMessage.style.justifyContent = 'center';
            loadingMessage.style.alignItems = 'center';
            loadingMessage.style.zIndex = '9999';
            loadingMessage.innerHTML = '<div style="background-color: white; padding: 20px; border-radius: 10px;"><h2>Generando PDF...</h2><p>Esto puede tomar unos momentos.</p></div>';
            document.body.appendChild(loadingMessage);
            
            // Dar tiempo para que se muestre el mensaje antes de comenzar la generación
            setTimeout(() => {
                // Capturar cada sección por separado para un mejor manejo
                const sections = document.querySelectorAll('.bg-white.rounded-xl');
                const pdf = new jsPDF('p', 'mm', 'a4');
                let currentPage = 0;
                let verticalPosition = 10;
                
                // Procesar cada sección
                const processSection = (index) => {
                    if (index >= sections.length) {
                        // Terminamos con todas las secciones
                        pdf.save('estadisticas-vocacional.pdf');
                        document.body.removeChild(loadingMessage);
                        return;
                    }
                    
                    const section = sections[index];
                    
                    // Configurar cada sección para la captura
                    const originalStyles = {
                        transform: section.style.transform,
                        transition: section.style.transition,
                        width: section.style.width
                    };
                    
                    section.style.transform = 'none';
                    section.style.transition = 'none';
                    section.style.width = '700px'; // Ancho fijo para la captura
                    
                    html2canvas(section, {
                        scale: 1,
                        useCORS: true,
                        logging: false
                    }).then(canvas => {
                        // Restaurar estilos originales
                        section.style.transform = originalStyles.transform;
                        section.style.transition = originalStyles.transition;
                        section.style.width = originalStyles.width;
                        
                        // Ajustar tamaño para PDF
                        const imgData = canvas.toDataURL('image/png');
                        const imgWidth = 190; // margen de 10mm por lado en A4
                        const imgHeight = canvas.height * imgWidth / canvas.width;
                        
                        // Verificar si necesitamos una nueva página
                        if (verticalPosition + imgHeight > 280) { // A4 height - margins
                            pdf.addPage();
                            currentPage++;
                            verticalPosition = 10;
                        }
                        
                        // Añadir imagen al PDF
                        pdf.addImage(imgData, 'PNG', 10, verticalPosition, imgWidth, imgHeight);
                        verticalPosition += imgHeight + 10;
                        
                        // Procesar la siguiente sección
                        processSection(index + 1);
                    });
                };
                
                // Comenzar con la primera sección
                processSection(0);
            }, 500);
        });
        
        // Filtros
        document.getElementById('aplicar-filtros').addEventListener('click', function() {
            const periodo = document.getElementById('periodo').value;
            const departamento = document.getElementById('departamento').value;
            
            // Construir URL con parámetros
            let url = window.location.pathname + '?';
            if (periodo) url += `periodo=${periodo}&`;
            if (departamento) url += `departamento=${departamento}&`;
            
            // Redireccionar
            window.location.href = url.slice(0, -1);
        });
    });
</script>
@endsection
@section('scripts')
<!-- Eliminado: ahora el bloque de variables está antes de los scripts principales -->
@endsection