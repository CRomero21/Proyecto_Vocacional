
@extends('layouts.app')

@section('title', 'Estadísticas del Sistema')

@section('content')
<div class="bg-gradient-to-br from-blue-900 to-indigo-900 min-h-screen p-4 md:p-8">
    <div class="mx-auto max-w-6xl">
        <!-- Mensaje de error en caso de problemas -->
        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ $error }}</span>
            </div>
        @endif
        
        <!-- Breadcrumb y Botón de Regreso -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center text-sm text-white/70">
                <a href="{{ route('informes.index') }}" class="hover:text-white">Dashboard</a>
                <svg class="w-3 h-3 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white">Estadísticas</span>
            </div>
            <a href="{{ route('informes.index') }}" class="flex items-center text-white/80 hover:text-white">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Volver al Dashboard</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6">
                <h1 class="text-2xl font-bold text-white">Estadísticas del Sistema</h1>
                <p class="text-purple-100 mt-1">Análisis y tendencias del sistema de orientación vocacional</p>
            </div>
            
            <!-- Tarjetas de métricas principales -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Tarjeta Tests Completados -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-blue-600 text-sm font-medium">Tests Completados</p>
                                <h3 class="text-3xl font-bold text-blue-800 mt-1">{{ $totalTests }}</h3>
                            </div>
                            <div class="bg-blue-500 rounded-full p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="relative pt-3">
                            <div class="bg-blue-200 h-2 rounded-full w-full">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $testsUltimaSemana/max(1, $totalTests/10) * 100) }}%"></div>
                            </div>
                        </div>
                        <p class="text-blue-600 text-sm mt-3">
                            <span class="font-medium">{{ $testsUltimaSemana }}</span> en la última semana
                        </p>
                    </div>
                    
                    <!-- Tarjeta Usuarios Registrados -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-green-600 text-sm font-medium">Usuarios Registrados</p>
                                <h3 class="text-3xl font-bold text-green-800 mt-1">{{ $totalUsuarios }}</h3>
                            </div>
                            <div class="bg-green-500 rounded-full p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="relative pt-3">
                            <div class="bg-green-200 h-2 rounded-full w-full">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, ($totalTests > 0 && $totalUsuarios > 0) ? ($totalTests/$totalUsuarios * 20) : 0) }}%"></div>
                            </div>
                        </div>
                        <p class="text-green-600 text-sm mt-3">
                            @if($totalUsuarios > 0)
                                <span class="font-medium">{{ round($totalTests/$totalUsuarios, 1) }}</span> tests por usuario
                            @else
                                <span class="font-medium">0</span> tests por usuario
                            @endif
                        </p>
                    </div>
                    
                    <!-- Tarjeta Tasa de Finalización -->
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-6 rounded-xl border border-amber-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-amber-600 text-sm font-medium">Tasa de Finalización</p>
                                <h3 class="text-3xl font-bold text-amber-800 mt-1">{{ $tasaConversion }}%</h3>
                            </div>
                            <div class="bg-amber-500 rounded-full p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="relative pt-3">
                            <div class="bg-amber-200 h-2 rounded-full w-full">
                                <div class="bg-amber-600 h-2 rounded-full" style="width: {{ $tasaConversion }}%"></div>
                            </div>
                        </div>
                        <p class="text-amber-600 text-sm mt-3">
                            <span class="font-medium">{{ $testsIniciados - $testsCompletados }}</span> tests sin completar
                        </p>
                    </div>
                </div>
                
                <!-- Gráficos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Gráfico de Tendencia de Tests -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                            Tendencia de Tests por Mes
                        </h3>
                        <div class="chart-container" style="position: relative; height:250px; width:100%">
                            <canvas id="chartTendenciaTests"></canvas>
                        </div>
                    </div>
                    
                    <!-- Gráfico de Tipos de Personalidad -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Distribución por Tipo de Personalidad
                        </h3>
                        <div class="chart-container" style="position: relative; height:250px; width:100%">
                            <canvas id="chartTiposPersonalidad"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Estudiantes por Departamento -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Estudiantes por Departamento
                        </h3>
                        <div class="chart-container" style="position: relative; height:250px; width:100%">
                            <canvas id="chartEstudiantesDepartamento"></canvas>
                        </div>
                    </div>
                    
                    <!-- Áreas de Conocimiento -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Áreas de Conocimiento
                        </h3>
                        <div class="chart-container" style="position: relative; height:250px; width:100%">
                            <canvas id="chartAreasConocimiento"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de Satisfacción -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Nivel de Satisfacción con los Resultados
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="chart-container" style="position: relative; height:220px; width:100%">
                            <canvas id="chartSatisfaccion"></canvas>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">Resumen de Retroalimentación</h4>
                            <ul class="space-y-2">
                                <li class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-600">Puntuación promedio:</span>
                                    <span class="font-semibold">{{ isset($promedioSatisfaccion) ? number_format($promedioSatisfaccion, 1) : 'N/A' }}/5</span>
                                </li>
                                <li class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-600">Total de retroalimentaciones:</span>
                                    <span class="font-semibold">{{ $totalRetroalimentaciones ?? 0 }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Carreras sugeridas por usuarios:</span>
                                    <span class="font-semibold">{{ $totalCarrerasSugeridas ?? 0 }}</span>
                                </li>
                            </ul>
                            
                            @if(isset($carrerasSugeridas) && count($carrerasSugeridas) > 0)
                                <h4 class="font-medium text-gray-700 mt-4 mb-2">Top Carreras Sugeridas</h4>
                                <div class="space-y-2">
                                    @foreach($carrerasSugeridas as $index => $carrera)
                                        <div class="flex items-center">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center {{ ['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-amber-100 text-amber-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800'][$index] }}">{{ $index + 1 }}</span>
                                            <span class="ml-2 text-gray-700 flex-grow">{{ $carrera->nombre ?? 'Sin nombre' }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full {{ ['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-amber-100 text-amber-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800'][$index] }}">
                                                {{ $carrera->total ?? 0 }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 mt-4">No hay sugerencias disponibles</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Top Carreras Recomendadas -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Top 5 Carreras Más Recomendadas
                    </h3>
                    <div class="overflow-x-auto bg-gray-50 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carrera</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recomendaciones</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match Promedio</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($carrerasMasRecomendadas ?? [] as $index => $carrera)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center {{ ['bg-blue-100 text-blue-800', 'bg-green-100 text-green-800', 'bg-yellow-100 text-yellow-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800'][$index % 5] }} font-medium mx-auto">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">
                                            {{ $carrera->nombre ?? 'Sin nombre' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $carrera->total ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @php $matchPromedio = isset($carrera->match_promedio) ? round($carrera->match_promedio, 1) : 0; @endphp
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2 max-w-[100px]">
                                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $matchPromedio }}%"></div>
                                                </div>
                                                <span>{{ $matchPromedio }}%</span>
                                            </div>
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
                
                <!-- Botones de acción -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.informes.index') }}" class="flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow-md hover:bg-indigo-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informes Avanzados
                    </a>
                    <a href="{{ route('informes.index') }}" class="flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg shadow-md hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para las gráficas -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para las gráficas
        const testsPorMes = <?php echo json_encode($testsPorMes ?? []); ?>;
        const porTipoPersonalidad = <?php echo json_encode($porTipoPersonalidad ?? []); ?>;
        const estudiantesPorDepartamento = <?php echo json_encode($estudiantesPorDepartamento ?? []); ?>;
        const porAreaConocimiento = <?php echo json_encode($porAreaConocimiento ?? []); ?>;
        const satisfaccionPorEstrellas = <?php echo json_encode($satisfaccionPorEstrellas ?? [0, 0, 0, 0, 0]); ?>;
        
        // Colores para las gráficas
        const backgroundColors = [
            'rgba(54, 162, 235, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 99, 132, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgba(199, 199, 199, 0.5)',
            'rgba(83, 102, 255, 0.5)',
            'rgba(40, 159, 32, 0.5)',
            'rgba(210, 50, 70, 0.5)'
        ];
        
        const borderColors = [
            'rgba(54, 162, 235, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(199, 199, 199, 1)',
            'rgba(83, 102, 255, 1)',
            'rgba(40, 159, 32, 1)',
            'rgba(210, 50, 70, 1)'
        ];
        
        // Gráfica de tendencia de tests por mes
        if(document.getElementById('chartTendenciaTests')) {
            new Chart(document.getElementById('chartTendenciaTests'), {
                type: 'line',
                data: {
                    labels: testsPorMes.map(item => item.mes),
                    datasets: [{
                        label: 'Tests completados',
                        data: testsPorMes.map(item => item.total),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
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
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Gráfica de tipos de personalidad
        if(document.getElementById('chartTiposPersonalidad')) {
            new Chart(document.getElementById('chartTiposPersonalidad'), {
                type: 'doughnut',
                data: {
                    labels: porTipoPersonalidad.map(item => item.tipo_primario),
                    datasets: [{
                        data: porTipoPersonalidad.map(item => item.total),
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
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
        }
        
        // Gráfica de estudiantes por departamento
        if(document.getElementById('chartEstudiantesDepartamento')) {
            new Chart(document.getElementById('chartEstudiantesDepartamento'), {
                type: 'bar',
                data: {
                    labels: estudiantesPorDepartamento.map(item => item.departamento),
                    datasets: [{
                        label: 'Estudiantes',
                        data: estudiantesPorDepartamento.map(item => item.total),
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
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
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Gráfica de áreas de conocimiento
        if(document.getElementById('chartAreasConocimiento')) {
            new Chart(document.getElementById('chartAreasConocimiento'), {
                type: 'pie',
                data: {
                    labels: porAreaConocimiento.map(item => item.area_conocimiento),
                    datasets: [{
                        data: porAreaConocimiento.map(item => item.total),
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
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
        }
        
        // Gráfica de satisfacción
        if(document.getElementById('chartSatisfaccion')) {
            new Chart(document.getElementById('chartSatisfaccion'), {
                type: 'bar',
                data: {
                    labels: ['1 Estrella', '2 Estrellas', '3 Estrellas', '4 Estrellas', '5 Estrellas'],
                    datasets: [{
                        label: 'Calificaciones',
                        data: satisfaccionPorEstrellas,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(255, 159, 64, 0.5)',
                            'rgba(255, 205, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(54, 162, 235, 0.5)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)'
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
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection