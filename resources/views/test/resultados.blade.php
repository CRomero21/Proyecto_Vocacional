@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Resultados de tu Test Vocacional</h1>
        <a href="{{ route('test.historial') }}" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            Volver a mi historial
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Tu perfil RIASEC</h2>
        <p class="text-gray-600 mb-6">
            El test RIASEC identifica tus intereses y preferencias ocupacionales según la teoría de Holland.
            Tu perfil destaca en los siguientes tipos:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <h3 class="text-lg font-bold text-blue-800 mb-2">Tipo primario: {{ $test->tipo_primario }}</h3>
                <p class="text-sm text-gray-700">
                    {{ $tiposPersonalidad[$test->tipo_primario] ?? 'Descripción no disponible' }}
                </p>
            </div>
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                <h3 class="text-lg font-bold text-indigo-800 mb-2">Tipo secundario: {{ $test->tipo_secundario }}</h3>
                <p class="text-sm text-gray-700">
                    {{ $tiposPersonalidad[$test->tipo_secundario] ?? 'Descripción no disponible' }}
                </p>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-3">Distribución de tus intereses</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            @foreach($test->resultados['porcentajes'] as $tipo => $porcentaje)
                <div class="bg-gray-50 p-3 rounded-lg text-center border">
                    <div class="text-2xl font-bold {{ $tipo == $test->tipo_primario ? 'text-blue-700' : ($tipo == $test->tipo_secundario ? 'text-indigo-700' : 'text-gray-700') }}">
                        {{ $porcentaje }}%
                    </div>
                    <div class="text-sm font-semibold text-gray-600">{{ $tipo }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Carreras recomendadas para tu perfil</h2>
        
        {{-- Carreras principales --}}
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-indigo-800 mb-4">Recomendaciones principales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($test->resultados['recomendaciones'] as $recomendacion)
                    @if($recomendacion['es_primaria'] ?? true)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border {{ $recomendacion['es_institucional'] ? 'border-indigo-500' : 'border-gray-200' }}">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-lg font-bold text-gray-800">{{ $recomendacion['nombre'] }}</h4>
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        {{ $recomendacion['match'] }}% compatible
                                    </span>
                                </div>
                                
                                @if(!empty($recomendacion['area']))
                                    <p class="text-sm text-gray-600 mb-3">{{ $recomendacion['area'] }}</p>
                                @endif
                                
                                @if($recomendacion['es_institucional'] ?? false)
                                    <div class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold mb-3 px-2 py-1 rounded">
                                        Carrera Institucional
                                    </div>
                                @endif
                                
                                @if(!empty($recomendacion['descripcion']))
                                    <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ $recomendacion['descripcion'] }}</p>
                                @endif
                                
                                @if(isset($recomendacion['universidades']) && count($recomendacion['universidades']) > 0)
                                    <div class="mt-3">
                                        <p class="text-xs font-semibold text-gray-600">Disponible en:</p>
                                        <ul class="mt-1 space-y-1">
                                            @foreach($recomendacion['universidades'] as $universidad)
                                                <li class="text-xs text-gray-600">
                                                    {{ $universidad['nombre'] }} 
                                                    <span class="text-gray-500">
                                                        ({{ $universidad['modalidad'] }})
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        {{-- Carreras relacionadas por área --}}
        <div>
            <h3 class="text-xl font-semibold text-purple-800 mb-4">Carreras relacionadas por área</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($test->resultados['recomendaciones'] as $recomendacion)
                    @if(isset($recomendacion['es_primaria']) && !$recomendacion['es_primaria'])
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border {{ $recomendacion['es_institucional'] ? 'border-indigo-500' : 'border-purple-200' }}">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-lg font-bold text-gray-800">{{ $recomendacion['nombre'] }}</h4>
                                    <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        {{ $recomendacion['match'] }}% compatible
                                    </span>
                                </div>
                                
                                @if(!empty($recomendacion['area']))
                                    <p class="text-sm text-gray-600 mb-3">{{ $recomendacion['area'] }}</p>
                                @endif
                                
                                @if($recomendacion['es_institucional'] ?? false)
                                    <div class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold mb-3 px-2 py-1 rounded">
                                        Carrera Institucional
                                    </div>
                                @endif
                                
                                @if(!empty($recomendacion['descripcion']))
                                    <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ $recomendacion['descripcion'] }}</p>
                                @endif
                                
                                @if(isset($recomendacion['universidades']) && count($recomendacion['universidades']) > 0)
                                    <div class="mt-3">
                                        <p class="text-xs font-semibold text-gray-600">Disponible en:</p>
                                        <ul class="mt-1 space-y-1">
                                            @foreach($recomendacion['universidades'] as $universidad)
                                                <li class="text-xs text-gray-600">
                                                    {{ $universidad['nombre'] }} 
                                                    <span class="text-gray-500">
                                                        ({{ $universidad['modalidad'] }})
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-12 mb-6 bg-gray-50 rounded-xl p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">¿Qué hacer ahora?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h3 class="font-bold text-lg text-blue-800 mb-2">Explora las carreras</h3>
                <p class="text-gray-700 mb-3">Investiga más sobre las carreras recomendadas. Busca planes de estudio, campo laboral y universidades.</p>
                <a href="#" class="text-blue-600 hover:underline font-medium">Ver catálogo de carreras →</a>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h3 class="font-bold text-lg text-blue-800 mb-2">Habla con orientadores</h3>
                <p class="text-gray-700 mb-3">Agenda una cita con nuestros orientadores vocacionales para resolver tus dudas.</p>
                <a href="#" class="text-blue-600 hover:underline font-medium">Solicitar orientación →</a>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h3 class="font-bold text-lg text-blue-800 mb-2">Comparte tus resultados</h3>
                <p class="text-gray-700 mb-3">Descarga un PDF con tus resultados para compartirlo con familiares o asesores.</p>
                <a href="#" class="text-blue-600 hover:underline font-medium">Descargar PDF →</a>
            </div>
        </div>
    </div>
</div>
@endsection