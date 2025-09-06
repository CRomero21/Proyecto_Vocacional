
@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .metric-card {
        transition: all 0.3s ease;
    }
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Cabecera con título y filtros -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Panel de Marketing e Insights
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Analítica avanzada para estrategias de marketing y toma de decisiones
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <div class="relative inline-block text-left mr-2">
                    <select id="periodo" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="7">Últimos 7 días</option>
                        <option value="30" selected>Últimos 30 días</option>
                        <option value="90">Últimos 90 días</option>
                        <option value="365">Último año</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>
                <div id="fecha-personalizada" class="hidden">
                    <input type="text" id="fecha-rango" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" placeholder="Seleccionar rango">
                </div>
                <button type="button" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Exportar Informe
                </button>
            </div>
        </div>
        
        <!-- Tarjetas KPI -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Conversión de Tests -->
            <div class="bg-white overflow-hidden shadow rounded-lg metric-card">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Tasa de Conversión
                                </dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ $tasaConversion ?? '68%' }}
                                    </div>
                                    <div class="flex items-center text-sm mt-1">
                                        <span class="text-green-500 flex items-center">
                                            <svg class="self-center flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-1">12%</span>
                                        </span>
                                        <span class="text-gray-500 ml-2">vs periodo anterior</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tests Completados -->
            <div class="bg-white overflow-hidden shadow rounded-lg metric-card">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Tests Completados
                                </dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ $totalTests ?? '1,248' }}
                                    </div>
                                    <div class="flex items-center text-sm mt-1">
                                        <span class="text-green-500 flex items-center">
                                            <svg class="self-center flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-1">8.2%</span>
                                        </span>
                                        <span class="text-gray-500 ml-2">vs periodo anterior</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nuevos Usuarios -->
            <div class="bg-white overflow-hidden shadow rounded-lg metric-card">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Nuevos Usuarios
                                </dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ $nuevosUsuarios ?? '842' }}
                                    </div>
                                    <div class="flex items-center text-sm mt-1">
                                        <span class="text-green-500 flex items-center">
                                            <svg class="self-center flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-1">5.3%</span>
                                        </span>
                                        <span class="text-gray-500 ml-2">vs periodo anterior</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tiempo Promedio -->
            <div class="bg-white overflow-hidden shadow rounded-lg metric-card">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Tiempo Promedio en Test
                                </dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ $tiempoPromedio ?? '12.4 min' }}
                                    </div>
                                    <div class="flex items-center text-sm mt-1">
                                        <span class="text-red-500 flex items-center">
                                            <svg class="self-center flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-1">2.1%</span>
                                        </span>
                                        <span class="text-gray-500 ml-2">vs periodo anterior</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos y análisis -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 mb-8">
            <!-- Tendencia de Usuarios y Tests -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tendencia de Actividad</h3>
                    <div class="relative inline-block text-left">
                        <select id="tendencia-filtro" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="semanal">Semanal</option>
                            <option value="mensual" selected>Mensual</option>
                            <option value="trimestral">Trimestral</option>
                        </select>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="chart-container">
                        <canvas id="tendencia-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribución por Tipo de Personalidad -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tipos de Personalidad</h3>
                    <div class="relative inline-block text-left">
                        <select id="personalidad-filtro" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="top5" selected>Top 5</option>
                            <option value="top10">Top 10</option>
                            <option value="todos">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="chart-container">
                        <canvas id="personalidad-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmentación Demográfica y Geográfica -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3 mb-8">
            <!-- Distribución por Género -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Distribución por Género</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="chart-container" style="height: 200px;">
                        <canvas id="genero-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribución por Edad -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Distribución por Edad</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="chart-container" style="height: 200px;">
                        <canvas id="edad-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribución Geográfica -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Top Departamentos</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="chart-container" style="height: 200px;">
                        <canvas id="geografia-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis de Embudo de Conversión -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Embudo de Conversión</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Proceso de conversión desde registro hasta finalización</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="h-20 flex items-center space-x-2">
                    <div class="h-16 bg-blue-500 text-white px-4 py-2 rounded-md flex items-center justify-center flex-1 text-center" style="min-width: 150px;">
                        <div>
                            <div class="font-bold">Registros</div>
                            <div>100%</div>
                            <div class="text-xs">({{ $totalUsuarios ?? '2,845' }})</div>
                        </div>
                    </div>

                    <div class="text-gray-400">→</div>
                    
                    <div class="h-16 bg-indigo-500 text-white px-4 py-2 rounded-md flex items-center justify-center flex-1 text-center" style="min-width: 150px;">
                        <div>
                            <div class="font-bold">Inician Test</div>
                            <div>78%</div>
                            <div class="text-xs">({{ $testsIniciados ?? '2,219' }})</div>
                        </div>
                    </div>
                    
                    <div class="text-gray-400">→</div>
                    
                    <div class="h-16 bg-purple-500 text-white px-4 py-2 rounded-md flex items-center justify-center flex-1 text-center" style="min-width: 150px;">
                        <div>
                            <div class="font-bold">Completan</div>
                            <div>68%</div>
                            <div class="text-xs">({{ $testsCompletados ?? '1,934' }})</div>
                        </div>
                    </div>
                    
                    <div class="text-gray-400">→</div>
                    
                    <div class="h-16 bg-green-500 text-white px-4 py-2 rounded-md flex items-center justify-center flex-1 text-center" style="min-width: 150px;">
                        <div>
                            <div class="font-bold">Satisfechos</div>
                            <div>56%</div>
                            <div class="text-xs">({{ $usuariosSatisfechos ?? '1,593' }})</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla Detallada con Filtros -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Detalle de Estudiantes</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Análisis detallado con filtros avanzados</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <select id="filtro-departamento" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="">Todos los departamentos</option>
                            @foreach($departamentos ?? ['La Paz', 'Santa Cruz', 'Cochabamba'] as $departamento)
                                <option value="{{ $departamento }}">{{ $departamento }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <select id="filtro-estado" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="">Todos los estados</option>
                            <option value="completado">Test completado</option>
                            <option value="pendiente">Test pendiente</option>
                            <option value="sin-test">Sin test</option>
                        </select>
                    </div>
                    <div>
                        <div class="relative rounded-md shadow-sm">
                            <input type="text" id="buscar" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Buscar estudiante...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 sm:px-6 pb-5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Personalidad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($estudiantes ?? [] as $estudiante)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">{{ substr($estudiante->name ?? 'Usuario', 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $estudiante->name ?? 'Nombre del Estudiante' }}</div>
                                            <div class="text-sm text-gray-500">{{ $estudiante->email ?? 'email@ejemplo.com' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $estudiante->departamento ?? 'La Paz' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(isset($estudiante->tests_count) && $estudiante->tests_count > 0)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completado
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $estudiante->tipo_personalidad ?? 'ISTJ' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ isset($estudiante->created_at) ? $estudiante->created_at->format('d/m/Y') : '15/08/2023' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('coordinador.estudiante', $estudiante->id ?? 1) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver detalle</a>
                                    <a href="#" class="text-gray-600 hover:text-gray-900">Exportar</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay datos disponibles en este momento.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                @if(isset($estudiantes) && method_exists($estudiantes, 'links'))
                    <div class="mt-4">
                        {{ $estudiantes->links() }}
                    </div>
                @else
                    <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </a>
                            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Siguiente
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium">1</span> a <span class="font-medium">10</span> de <span class="font-medium">97</span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Anterior</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        1
                                    </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        2
                                    </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        3
                                    </a>
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        10
                                    </a>
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Siguiente</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración de Flatpickr para rango de fechas
        flatpickr("#fecha-rango", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [new Date().fp_incr(-30), new Date()]
        });
        
        // Mostrar/ocultar selector de fecha personalizada
        document.getElementById('periodo').addEventListener('change', function() {
            const customDateContainer = document.getElementById('fecha-personalizada');
            if (this.value === 'custom') {
                customDateContainer.classList.remove('hidden');
            } else {
                customDateContainer.classList.add('hidden');
            }
        });
        
        // Gráfico de Tendencia
        const tendenciaCtx = document.getElementById('tendencia-chart').getContext('2d');
        const tendenciaChart = new Chart(tendenciaCtx, {
            type: 'line',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre'],
                datasets: [
                    {
                        label: 'Usuarios Registrados',
                        data: [65, 78, 90, 115, 112, 124, 130, 140, 152],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Tests Completados',
                        data: [42, 55, 69, 85, 81, 90, 102, 113, 125],
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Gráfico de Tipos de Personalidad
        const personalidadCtx = document.getElementById('personalidad-chart').getContext('2d');
        const personalidadChart = new Chart(personalidadCtx, {
            type: 'bar',
            data: {
                labels: ['INFJ', 'INTJ', 'ENTJ', 'ENFJ', 'INFP'],
                datasets: [{
                    label: 'Cantidad de Estudiantes',
                    data: [142, 123, 110, 98, 85],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Gráfico de Género
        const generoCtx = document.getElementById('genero-chart').getContext('2d');
        const generoChart = new Chart(generoCtx, {
            type: 'doughnut',
            data: {
                labels: ['Femenino', 'Masculino', 'No especificado'],
                datasets: [{
                    data: [55, 42, 3],
                    backgroundColor: [
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        // Gráfico de Edad
        const edadCtx = document.getElementById('edad-chart').getContext('2d');
        const edadChart = new Chart(edadCtx, {
            type: 'bar',
            data: {
                labels: ['16-18', '19-21', '22-25', '26-30', '31+'],
                datasets: [{
                    label: 'Estudiantes por Edad',
                    data: [24, 35, 25, 10, 6],
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Gráfico Geográfico
        const geografiaCtx = document.getElementById('geografia-chart').getContext('2d');
        const geografiaChart = new Chart(geografiaCtx, {
            type: 'pie',
            data: {
                labels: ['La Paz', 'Santa Cruz', 'Cochabamba', 'Oruro', 'Otros'],
                datasets: [{
                    data: [30, 25, 20, 15, 10],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        // Eventos para los filtros de la tabla
        document.getElementById('filtro-departamento').addEventListener('change', filtrarTabla);
        document.getElementById('filtro-estado').addEventListener('change', filtrarTabla);
        document.getElementById('buscar').addEventListener('keyup', filtrarTabla);
        
        function filtrarTabla() {
            // Aquí iría la lógica para filtrar la tabla (en un entorno real enviarías
            // una petición AJAX o usarías JavaScript para filtrar las filas)
            console.log('Filtrando tabla...');
        }
    });
</script>
@endsection