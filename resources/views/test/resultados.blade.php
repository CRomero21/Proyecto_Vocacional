
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Resultados de tu Test Vocacional</h1>
        
        <div class="mb-6">
            <p class="text-gray-600">
                Fecha de realización: {{ \Carbon\Carbon::parse($test->fecha_completado)->format('d/m/Y H:i') }}
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Basado en la Teoría RIASEC de Holland, estos resultados representan tus intereses y preferencias vocacionales.
            </p>
        </div>

        <!-- Perfil RIASEC -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Tu perfil RIASEC</h2>
            
            <div class="flex flex-wrap -mx-2">
                @php
                    $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                    $porcentajes = $resultados['porcentajes'] ?? [];
                    arsort($porcentajes);
                @endphp
                
                @foreach($porcentajes as $tipo => $porcentaje)
                    <div class="w-full md:w-1/2 lg:w-1/3 px-2 mb-4">
                        <div class="bg-gray-50 rounded-lg p-4 h-full border border-gray-200">
                            <div class="flex items-center mb-2">
                                <div class="w-12 h-12 flex items-center justify-center rounded-full 
                                    @if($tipo == 'R') bg-red-100 text-red-700 
                                    @elseif($tipo == 'I') bg-blue-100 text-blue-700 
                                    @elseif($tipo == 'A') bg-purple-100 text-purple-700 
                                    @elseif($tipo == 'S') bg-green-100 text-green-700 
                                    @elseif($tipo == 'E') bg-yellow-100 text-yellow-700 
                                    @elseif($tipo == 'C') bg-gray-100 text-gray-700 
                                    @endif
                                    font-bold text-xl">
                                    {{ $tipo }}
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-bold">
                                        @if($tipo == 'R') Realista
                                        @elseif($tipo == 'I') Investigativo
                                        @elseif($tipo == 'A') Artístico
                                        @elseif($tipo == 'S') Social
                                        @elseif($tipo == 'E') Emprendedor
                                        @elseif($tipo == 'C') Convencional
                                        @endif
                                    </h3>
                                    <div class="text-sm text-gray-500">{{ $porcentaje }}%</div>
                                </div>
                            </div>
                            
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full 
                                    @if($tipo == 'R') bg-red-600 
                                    @elseif($tipo == 'I') bg-blue-600 
                                    @elseif($tipo == 'A') bg-purple-600 
                                    @elseif($tipo == 'S') bg-green-600 
                                    @elseif($tipo == 'E') bg-yellow-600 
                                    @elseif($tipo == 'C') bg-gray-600 
                                    @endif" 
                                    style="width: {{ $porcentaje }}%">
                                </div>
                            </div>
                            
                            <p class="text-sm mt-3 text-gray-600">
                                {{ $tiposPersonalidad[$tipo] ?? '' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Descripción de tipos principales -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Tus tipos principales</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full 
                            @if($test->tipo_primario == 'R') bg-red-100 text-red-700 
                            @elseif($test->tipo_primario == 'I') bg-blue-100 text-blue-700 
                            @elseif($test->tipo_primario == 'A') bg-purple-100 text-purple-700 
                            @elseif($test->tipo_primario == 'S') bg-green-100 text-green-700 
                            @elseif($test->tipo_primario == 'E') bg-yellow-100 text-yellow-700 
                            @elseif($test->tipo_primario == 'C') bg-gray-100 text-gray-700 
                            @endif
                            font-bold text-lg">
                            {{ $test->tipo_primario }}
                        </div>
                        <h3 class="ml-3 font-bold text-lg">Tipo Primario: 
                            @if($test->tipo_primario == 'R') Realista
                            @elseif($test->tipo_primario == 'I') Investigativo
                            @elseif($test->tipo_primario == 'A') Artístico
                            @elseif($test->tipo_primario == 'S') Social
                            @elseif($test->tipo_primario == 'E') Emprendedor
                            @elseif($test->tipo_primario == 'C') Convencional
                            @endif
                        </h3>
                    </div>
                    <p class="text-gray-700">
                        {{ $tiposPersonalidad[$test->tipo_primario] ?? '' }}
                    </p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full 
                            @if($test->tipo_secundario == 'R') bg-red-100 text-red-700 
                            @elseif($test->tipo_secundario == 'I') bg-blue-100 text-blue-700 
                            @elseif($test->tipo_secundario == 'A') bg-purple-100 text-purple-700 
                            @elseif($test->tipo_secundario == 'S') bg-green-100 text-green-700 
                            @elseif($test->tipo_secundario == 'E') bg-yellow-100 text-yellow-700 
                            @elseif($test->tipo_secundario == 'C') bg-gray-100 text-gray-700 
                            @endif
                            font-bold text-lg">
                            {{ $test->tipo_secundario }}
                        </div>
                        <h3 class="ml-3 font-bold text-lg">Tipo Secundario: 
                            @if($test->tipo_secundario == 'R') Realista
                            @elseif($test->tipo_secundario == 'I') Investigativo
                            @elseif($test->tipo_secundario == 'A') Artístico
                            @elseif($test->tipo_secundario == 'S') Social
                            @elseif($test->tipo_secundario == 'E') Emprendedor
                            @elseif($test->tipo_secundario == 'C') Convencional
                            @endif
                        </h3>
                    </div>
                    <p class="text-gray-700">
                        {{ $tiposPersonalidad[$test->tipo_secundario] ?? '' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Carreras recomendadas -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Carreras recomendadas para tu perfil</h2>
            
            @if(empty($resultados['recomendaciones']))
                <div class="bg-yellow-50 rounded-xl p-6 border border-yellow-200 mb-8">
                    <h3 class="text-lg font-bold text-yellow-800 mb-2">No se encontraron recomendaciones específicas</h3>
                    <p class="text-gray-700">
                        No hemos podido encontrar carreras que coincidan exactamente con tu perfil RIASEC. 
                        Te sugerimos hablar con un orientador vocacional para explorar opciones adicionales.
                    </p>
                </div>
            @else
                <!-- Carreras primarias -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Carreras principales</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php 
                            $carrerasPrincipales = collect($resultados['recomendaciones'])
                                ->where('es_primaria', true)
                                ->sortByDesc('match')
                                ->take(6);
                        @endphp

                        @foreach($carrerasPrincipales as $recomendacion)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                                <div class="px-5 py-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-lg text-gray-900">{{ $recomendacion['nombre'] }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($recomendacion['match'] >= 80) bg-green-100 text-green-800
                                            @elseif($recomendacion['match'] >= 60) bg-blue-100 text-blue-800
                                            @elseif($recomendacion['match'] >= 40) bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $recomendacion['match'] }}% match
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-500">{{ $recomendacion['area'] }}</p>
                                    
                                    <div class="mt-3 text-sm text-gray-600">
                                        {{ $recomendacion['descripcion'] }}
                                    </div>
                                    
                                    @if(!empty($recomendacion['habilidades']))
                                        <div class="mt-3">
                                            <p class="text-xs text-gray-500 mb-1">Habilidades requeridas:</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($recomendacion['habilidades'] as $habilidad)
                                                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                                        {{ $habilidad }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(!empty($recomendacion['campo_laboral']))
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600">
                                                <span class="font-medium">Campo laboral:</span> 
                                                {{ $recomendacion['campo_laboral'] }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    @if(!empty($recomendacion['universidades']))
                                        <div class="mt-3">
                                            <button class="text-sm text-blue-600 hover:text-blue-800" 
                                                    onclick="toggleUniversidades('carrera-{{ $recomendacion['carrera_id'] }}')">
                                                Ver universidades ({{ count($recomendacion['universidades']) }})
                                            </button>
                                            
                                            <div id="carrera-{{ $recomendacion['carrera_id'] }}" class="hidden mt-2 border-t pt-2">
                                                @foreach($recomendacion['universidades'] as $universidad)
                                                    <div class="mb-2 text-sm">
                                                        <p class="font-medium">{{ $universidad->nombre }}</p>
                                                        <p class="text-xs text-gray-600">
                                                            {{ $universidad->departamento }} - {{ $universidad->tipo }}
                                                            @if($universidad->acreditada)
                                                                <span class="text-green-600">• Acreditada</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $universidad->modalidad }} - {{ $universidad->duracion }}
                                                        </p>
                                                        @if($universidad->sitio_web)
                                                            <a href="{{ $universidad->sitio_web }}" target="_blank" 
                                                               class="text-xs text-blue-600 hover:underline">
                                                                Visitar sitio web
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Carreras secundarias -->
                @php 
                    $carrerasSecundarias = collect($resultados['recomendaciones'])
                        ->where('es_primaria', false)
                        ->sortByDesc('match')
                        ->take(3);
                @endphp
                
                @if(count($carrerasSecundarias) > 0)
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Otras carreras relacionadas</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($carrerasSecundarias as $recomendacion)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                                    <div class="px-5 py-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-lg text-gray-900">{{ $recomendacion['nombre'] }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $recomendacion['match'] }}% match
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-500">{{ $recomendacion['area'] }}</p>
                                        
                                        <div class="mt-3 text-sm text-gray-600">
                                            {{ $recomendacion['descripcion'] }}
                                        </div>
                                        
                                        @if(!empty($recomendacion['campo_laboral']))
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-600">
                                                    <span class="font-medium">Campo laboral:</span> 
                                                    {{ $recomendacion['campo_laboral'] }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
        
        <!-- Retroalimentación -->
        <div class="mt-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">¿Qué te parecieron los resultados?</h2>
            
            @if(!empty($resultados['retroalimentacion']))
                <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                    <h3 class="text-lg font-semibold text-green-800 mb-2">¡Gracias por tu retroalimentación!</h3>
                    <p class="text-gray-700">Has valorado estos resultados con {{ $resultados['retroalimentacion']['utilidad'] }}/5 en utilidad 
                    y {{ $resultados['retroalimentacion']['precision'] }}/5 en precisión.</p>
                </div>
            @else
                <form action="{{ route('test.retroalimentacion', $test->id) }}" method="POST" class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">¿Qué tan útiles fueron estos resultados?</label>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="mr-4 flex items-center">
                                        <input type="radio" name="utilidad" value="{{ $i }}" class="mr-1" required>
                                        <span>{{ $i }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">¿Qué tan precisos te parecieron?</label>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="mr-4 flex items-center">
                                        <input type="radio" name="precision" value="{{ $i }}" class="mr-1" required>
                                        <span>{{ $i }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">¿Algún comentario adicional?</label>
                        <textarea name="comentario" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                    </div>
                    
                    @if(!empty($resultados['recomendaciones']))
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">¿Cuál de las carreras recomendadas te interesa más?</label>
                            <select name="carrera_seleccionada" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Selecciona una carrera</option>
                                @foreach($resultados['recomendaciones'] as $recomendacion)
                                    <option value="{{ $recomendacion['carrera_id'] }}">{{ $recomendacion['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <button type="submit" class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enviar retroalimentación
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    function toggleUniversidades(id) {
        const element = document.getElementById(id);
        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    }
</script>
@endsection