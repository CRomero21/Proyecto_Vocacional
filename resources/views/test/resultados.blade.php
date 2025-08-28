
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-5xl mx-auto">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Resultados de tu Test Vocacional</h1>
        <p class="text-gray-600 mb-6">
            Test completado el {{ \Carbon\Carbon::parse($test->fecha_completado)->format('d/m/Y') }}
        </p>

        <!-- Perfil RIASEC -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Tu Perfil RIASEC</h2>
                <p class="text-sm text-gray-600">
                    Tu tipo primario es 
                    <span class="font-bold px-2 py-1 rounded text-white" 
                          style="background-color: {{ $tiposPersonalidad[$test->tipo_primario]['color'] ?? '#3498db' }}">
                        {{ $test->tipo_primario }} - {{ $tiposPersonalidad[$test->tipo_primario]['nombre'] ?? $test->tipo_primario }}
                    </span>
                    @if($test->tipo_secundario)
                        y tu tipo secundario es 
                        <span class="font-bold px-2 py-1 rounded text-white" 
                              style="background-color: {{ $tiposPersonalidad[$test->tipo_secundario]['color'] ?? '#2ecc71' }}">
                            {{ $test->tipo_secundario }} - {{ $tiposPersonalidad[$test->tipo_secundario]['nombre'] ?? $test->tipo_secundario }}
                        </span>
                    @endif
                </p>
            </div>
            
            <div class="p-6">
                <!-- Gráfica de barras para los puntajes -->
                <div class="mb-6">
                    <h3 class="font-semibold mb-3">Tus puntuaciones en cada dimensión</h3>
                    <div class="space-y-4">
                        @foreach($test->resultados['porcentajes'] as $tipo => $porcentaje)
                            <div>
                                <div class="flex items-center">
                                    <span class="w-8 font-semibold">{{ $tipo }}</span>
                                    <div class="flex-grow mx-2">
                                        <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
                                            <div class="h-4 rounded-full" 
                                                 style="width: {{ $porcentaje }}%; background-color: {{ $tiposPersonalidad[$tipo]['color'] ?? '#3498db' }}"></div>
                                        </div>
                                    </div>
                                    <span class="w-10 text-right">{{ $porcentaje }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Interpretación del tipo primario -->
                <div class="mb-6">
                    <h3 class="font-semibold mb-2">Sobre tu tipo primario ({{ $test->tipo_primario }})</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p>{{ $tiposPersonalidad[$test->tipo_primario]['descripcion'] ?? 'Descripción no disponible' }}</p>
                    </div>
                </div>
                
                <!-- Interpretación del tipo secundario -->
                @if($test->tipo_secundario)
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">Sobre tu tipo secundario ({{ $test->tipo_secundario }})</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p>{{ $tiposPersonalidad[$test->tipo_secundario]['descripcion'] ?? 'Descripción no disponible' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recomendaciones de carreras -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Carreras Recomendadas</h2>
                <p class="text-sm text-gray-600">Basadas en tu perfil {{ $test->tipo_primario }}{{ $test->tipo_secundario ? '-'.$test->tipo_secundario : '' }}</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-6">
                    @foreach($test->resultados['recomendaciones'] as $recomendacion)
                        <div class="bg-white p-6 rounded-lg shadow border">
                            <h3 class="text-lg font-bold">{{ $recomendacion['nombre'] }}</h3>
                            <p class="text-gray-600">{{ $recomendacion['area'] }}</p>
                            <div class="flex items-center mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $recomendacion['match'] }}%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium">{{ $recomendacion['match'] }}% de coincidencia</span>
                            </div>
                            <p class="mt-2">{{ $recomendacion['descripcion'] }}</p>
                            
                            @if(isset($recomendacion['universidades']) && count($recomendacion['universidades']) > 0)
                                <div class="mt-4">
                                    <h4 class="font-semibold text-gray-700">Universidades que ofrecen esta carrera:</h4>
                                    <div class="mt-2 space-y-2">
                                        @foreach($recomendacion['universidades'] as $universidad)
                                            <div class="p-3 bg-gray-50 rounded border">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <h5 class="font-medium">{{ $universidad['nombre'] ?? 'Sin nombre' }}</h5>
                                                        <p class="text-sm text-gray-600">{{ $universidad['departamento'] ?? '' }} - {{ $universidad['tipo'] ?? '' }}</p>
                                                    </div>
                                                    @if(!empty($universidad['acreditada']))
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Acreditada</span>
                                                    @endif
                                                </div>
                                                <div class="mt-2 text-sm">
                                                    <p>Modalidad: <span class="font-medium">{{ $universidad['modalidad'] ?? 'No especificado' }}</span></p>
                                                    @if(!empty($universidad['duracion']))
                                                        <p>Duración: <span class="font-medium">{{ $universidad['duracion'] }}</span></p>
                                                    @endif
                                                    @if(isset($universidad['costo_semestre']) && $universidad['costo_semestre'] !== null)
                                                        <p>Costo aproximado: <span class="font-medium">${{ number_format($universidad['costo_semestre'], 0, ',', '.') }}</span></p>
                                                    @endif
                                                </div>
                                                @if(!empty($universidad['sitio_web']))
                                                    <a href="{{ $universidad['sitio_web'] }}" target="_blank" class="inline-block mt-2 text-sm text-blue-600 hover:underline">Visitar sitio web</a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 flex justify-center">
                    <a href="{{ route('test.historial') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded mr-2">
                        Ver historial de tests
                    </a>
                    <a href="{{ route('test.iniciar') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                        Realizar nuevo test
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection