
@extends('layouts.app')

@section('title', 'Informes Avanzados')

@section('content')
<div class="bg-gradient-to-br from-blue-900 to-indigo-900 min-h-screen p-4 md:p-8">
    <div class="mx-auto max-w-7xl">
        <!-- Breadcrumb y Botón de Regreso -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center text-sm text-white/70">
                <a href="{{ route('informes.index') }}" class="hover:text-white">Dashboard</a>
                <svg class="w-3 h-3 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white">Informes Avanzados</span>
            </div>
            <a href="{{ route('informes.index') }}" class="flex items-center text-white/80 hover:text-white">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Volver al Dashboard</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 p-6">
                <h1 class="text-2xl font-bold text-white">Informes Avanzados</h1>
                <p class="text-purple-100 mt-1">Análisis detallados y personalizables para la toma de decisiones</p>
            </div>
            
            <!-- Panel de filtros -->
            <div class="p-6 border-b border-gray-200 bg-gray-50" x-data="{ filtersOpen: true }">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Filtros de Informe</h2>
                    <button @click="filtersOpen = !filtersOpen" class="text-gray-500 hover:text-gray-700">
                        <span x-show="!filtersOpen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                        <span x-show="filtersOpen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                
                <form x-show="filtersOpen" action="{{ route('admin.informes-avanzados.generar') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Filtro por Fecha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Fechas</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-500">Desde</label>
                                <input type="date" name="fecha_inicio" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Hasta</label>
                                <input type="date" name="fecha_fin" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtro por Ubicación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <select name="departamento" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos los departamentos</option>
                                    <option value="Antioquia">Antioquia</option>
                                    <option value="Bogotá">Bogotá D.C.</option>
                                    <option value="Valle">Valle del Cauca</option>
                                    <option value="Atlántico">Atlántico</option>
                                </select>
                            </div>
                            <div>
                                <select name="ciudad" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todas las ciudades</option>
                                    <option value="Bogotá">Bogotá</option>
                                    <option value="Medellín">Medellín</option>
                                    <option value="Cali">Cali</option>
                                    <option value="Barranquilla">Barranquilla</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtro por Tipo de Personalidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Personalidad</label>
                        <select name="tipo_personalidad" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todos los tipos</option>
                            <option value="R">Realista (R)</option>
                            <option value="I">Investigador (I)</option>
                            <option value="A">Artístico (A)</option>
                            <option value="S">Social (S)</option>
                            <option value="E">Emprendedor (E)</option>
                            <option value="C">Convencional (C)</option>
                        </select>
                    </div>
                    
                    <!-- Segunda fila de filtros -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                        <select name="genero" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todos</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">Otro</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Edad</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input type="number" name="edad_min" placeholder="Mín." class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <input type="number" name="edad_max" placeholder="Máx." class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Informe</label>
                        <select name="tipo_informe" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="demografico">Distribución Demográfica</option>
                            <option value="carreras">Tendencias de Carreras</option>
                            <option value="personalidad">Análisis por Personalidad</option>
                            <option value="conversion">Tasas de Conversión</option>
                            <option value="instituciones">Comparativa Institucional</option>
                            <option value="tendencias">Análisis Temporal</option>
                        </select>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="md:col-span-3 flex flex-wrap justify-end gap-3 mt-2">
                        <button type="reset" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Limpiar Filtros
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Generar Informe
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Informes guardados -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informes Guardados</h2>
                
                <div class="overflow-x-auto bg-gray-50 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filtros</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Distribución de estudiantes Q3 2025</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Demográfico</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    25 Ago 2025
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Fecha, Departamento, Ciudad
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                        <a href="#" class="text-green-600 hover:text-green-900">Excel</a>
                                        <a href="#" class="text-red-600 hover:text-red-900">PDF</a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Análisis de personalidades por ciudad</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Personalidad</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    18 Ago 2025
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Tipo de Personalidad, Ciudad
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                        <a href="#" class="text-green-600 hover:text-green-900">Excel</a>
                                        <a href="#" class="text-red-600 hover:text-red-900">PDF</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Contenido del informe (aparece cuando se genera) -->
            @if(isset($datos))
            <div class="p-6" id="resultados-informe" x-data="{ activeTab: 'tabla' }">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Resultados del Informe</h2>
                    <div class="flex space-x-2">
                        <button @click="activeTab = 'tabla'" :class="{'bg-indigo-600 text-white': activeTab === 'tabla', 'bg-gray-200 text-gray-700': activeTab !== 'tabla'}" class="px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Tabla
                        </button>
                        <button @click="activeTab = 'grafico'" :class="{'bg-indigo-600 text-white': activeTab === 'grafico', 'bg-gray-200 text-gray-700': activeTab !== 'grafico'}" class="px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Gráfico
                        </button>
                    </div>
                </div>
                
                <!-- Acciones para el informe -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <input type="text" placeholder="Nombre del informe" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                        <button class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Guardar
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.informes-avanzados.exportar', ['formato' => 'excel']) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Excel
                        </a>
                        <a href="{{ route('admin.informes-avanzados.exportar', ['formato' => 'pdf']) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>
                
                <!-- Vista de Tabla -->
                <div x-show="activeTab === 'tabla'" class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    @if(isset($datos['tabla']) && count($datos['tabla']) > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Estudiantes</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests Completados</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests Incompletos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa Conversión</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Primario Dominante</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($datos['tabla'] as $fila)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $fila['ciudad'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fila['total_estudiantes'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fila['tests_completados'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fila['tests_incompletos'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fila['tasa_conversion'] }}%</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $fila['tipo_primario_dominante'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-6 text-center text-gray-500">
                        No hay datos disponibles para mostrar.
                    </div>
                    @endif
                </div>
                
                <!-- Vista de Gráfico -->
                <div x-show="activeTab === 'grafico'" class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="chart-container" style="position: relative; height:400px; width:100%">
                        <canvas id="chartInforme"></canvas>
                    </div>
                </div>
                
                <!-- Sección de insights y análisis -->
                <div class="mt-8 bg-indigo-50 rounded-lg p-6 border border-indigo-100">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-3">Insights y Recomendaciones</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-700">La ciudad de <strong>Medellín</strong> presenta la mayor tasa de conversión (89.1%), sugiriendo un mayor nivel de compromiso con el test vocacional.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-700">El tipo de personalidad <strong>Social (S)</strong> predomina en Bogotá, mientras que en Medellín destaca el perfil <strong>Emprendedor (E)</strong>.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Se recomienda fortalecer la campaña de difusión en <strong>Cali</strong>, donde se observa la menor tasa de conversión (81.7%).</span>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Scripts para gráficos -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('chartInforme')) {
            const ctx = document.getElementById('chartInforme').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Bogotá', 'Medellín', 'Cali', 'Barranquilla'],
                    datasets: [{
                        label: 'Total Estudiantes',
                        data: [458, 312, 186, 124],
                        backgroundColor: 'rgba(99, 102, 241, 0.5)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1
                    }, {
                        label: 'Tests Completados',
                        data: [392, 278, 152, 102],
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Estudiantes y Tests por Ciudad'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection