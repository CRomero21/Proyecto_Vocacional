
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="p-6">
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
                                <span class="font-medium">{{ round($totalTests/$totalUsuarios, 1) }}</span> tests por usuario (promedio)
                            @else
                                <span class="font-medium">0</span> tests por usuario (promedio)
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
                
                <!-- Gráficos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Tendencia de Tests por Mes</h3>
                        <div class="h-60 flex items-center justify-center bg-gray-50 rounded-lg">
                            <p class="text-gray-500">Los datos de tendencia se mostrarán aquí.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Distribución por Tipo de Personalidad</h3>
                        <div class="h-60 flex items-center justify-center bg-gray-50 rounded-lg">
                            <p class="text-gray-500">Los datos de tipos de personalidad se mostrarán aquí.</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Top 5 Carreras Recomendadas</h3>
                        <div class="h-60 flex items-center justify-center bg-gray-50 rounded-lg">
                            <p class="text-gray-500">Los datos de carreras se mostrarán aquí.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Áreas de Conocimiento</h3>
                        <div class="h-60 flex items-center justify-center bg-gray-50 rounded-lg">
                            <p class="text-gray-500">Los datos de áreas de conocimiento se mostrarán aquí.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>