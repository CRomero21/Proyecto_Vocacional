@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="container mx-auto py-8 px-4">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Resultados de tu Test Vocacional</h1>
                    <p class="text-blue-100 mt-1">Descubre las carreras que mejor se adaptan a tu perfil RIASEC</p>
                </div>
                <a href="{{ route('test.exportarPDF', $test->id) }}" 
                   class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-all duration-300 font-semibold shadow-lg transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Descargar PDF
                </a>
            </div>
        </div>

        {{-- Perfil RIASEC --}}
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Tu perfil RIASEC 
            </h2>
            @php
                $porcentajes = $resultados['porcentajes'] ?? [];
                arsort($porcentajes);
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($porcentajes as $tipo => $porcentaje)
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div class="text-2xl font-bold text-blue-700">{{ $tipo }}</div>
                            <div class="text-3xl font-extrabold text-blue-900">{{ $porcentaje }}%</div>
                        </div>
                        <div class="text-sm text-gray-600 mt-3">{{ $tiposPersonalidad[$tipo] ?? 'Descripción no disponible' }}</div>
                        <div class="mt-4 bg-blue-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ $porcentaje }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Carreras principales --}}
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-green-700 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Carreras principales recomendadas
            </h2>
            @if(isset($carrerasPrincipales) && count($carrerasPrincipales) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($carrerasPrincipales as $recomendacion)
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 border border-green-200">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <span class="font-bold text-xl text-green-800">{{ $recomendacion['nombre'] ?? 'Nombre no disponible' }}</span>
                                    <span class="ml-2 text-xs text-gray-500 bg-green-100 px-2 py-1 rounded">{{ $recomendacion['area'] ?? 'Área no disponible' }}</span>
                                </div>
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-700 mb-3">{{ $recomendacion['descripcion'] ?? 'Descripción no disponible' }}</div>
                            <div class="text-xs text-gray-500 mb-4">Tipos de la carrera: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $recomendacion['tipos'] ?? 'N/A' }}</span></div>
                            
                            {{-- Universidades asociadas --}}
                            @if(!empty($recomendacion['universidades']))
                                <div class="mt-4">
                                    <button class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-all duration-300 text-sm font-medium" 
                                            onclick="toggleUniversidades('carrera-{{ $recomendacion['carrera_id'] ?? 'unknown' }}')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Ver universidades ({{ count($recomendacion['universidades']) }})
                                    </button>
                                    <div id="carrera-{{ $recomendacion['carrera_id'] ?? 'unknown' }}" class="hidden mt-4 space-y-3">
                                        @foreach($recomendacion['universidades'] as $universidad)
                                            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-400">
                                                <p class="font-medium text-blue-700">{{ is_array($universidad) ? ($universidad['nombre'] ?? 'Nombre no disponible') : ($universidad->nombre ?? 'Nombre no disponible') }}</p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    {{ is_array($universidad) ? ($universidad['departamento'] ?? 'N/A') : ($universidad->departamento ?? 'N/A') }} - 
                                                    {{ is_array($universidad) ? ($universidad['tipo'] ?? 'N/A') : ($universidad->tipo ?? 'N/A') }}
                                                    @if((is_array($universidad) ? ($universidad['acreditada'] ?? false) : ($universidad->acreditada ?? false)))
                                                        <span class="text-green-600 font-semibold">• Acreditada</span>
                                                    @endif
                                                </p>
                                                @if((is_array($universidad) ? ($universidad['sitio_web'] ?? null) : ($universidad->sitio_web ?? null)))
                                                    <a href="{{ is_array($universidad) ? $universidad['sitio_web'] : $universidad->sitio_web }}" target="_blank" 
                                                    class="inline-flex items-center mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        Visitar sitio web
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 text-xs text-gray-400 bg-gray-50 p-3 rounded">No hay universidades registradas para esta carrera.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.46-.88-6.08-2.33" />
                    </svg>
                    <p class="text-gray-500">No se encontraron carreras principales para tu perfil. Verifica tu configuración o intenta de nuevo.</p>
                </div>
            @endif
        </div>

        {{-- Carreras relacionadas --}}
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-blue-700 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Otras carreras relacionadas
            </h2>
            @if(isset($carrerasSecundarias) && count($carrerasSecundarias) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($carrerasSecundarias as $recomendacion)
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 border border-blue-200">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <span class="font-bold text-xl text-blue-800">{{ $recomendacion['nombre'] ?? 'Nombre no disponible' }}</span>
                                    <span class="ml-2 text-xs text-gray-500 bg-blue-100 px-2 py-1 rounded">{{ $recomendacion['area'] ?? 'Área no disponible' }}</span>
                                </div>
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-700 mb-3">{{ $recomendacion['descripcion'] ?? 'Descripción no disponible' }}</div>
                            <div class="text-xs text-gray-500 mb-4">Tipos de la carrera: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $recomendacion['tipos'] ?? 'N/A' }}</span></div>
                            
                            {{-- Universidades asociadas --}}
                            @if(!empty($recomendacion['universidades']))
                                <div class="mt-4">
                                    <button class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-all duration-300 text-sm font-medium" 
                                            onclick="toggleUniversidades('carrera-{{ $recomendacion['carrera_id'] ?? 'unknown' }}-rel')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Ver universidades ({{ count($recomendacion['universidades']) }})
                                    </button>
                                    <div id="carrera-{{ $recomendacion['carrera_id'] ?? 'unknown' }}-rel" class="hidden mt-4 space-y-3">
                                        @foreach($recomendacion['universidades'] as $universidad)
                                            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-400">
                                                <p class="font-medium text-blue-700">{{ is_array($universidad) ? ($universidad['nombre'] ?? 'Nombre no disponible') : ($universidad->nombre ?? 'Nombre no disponible') }}</p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    {{ is_array($universidad) ? ($universidad['departamento'] ?? 'N/A') : ($universidad->departamento ?? 'N/A') }} - 
                                                    {{ is_array($universidad) ? ($universidad['tipo'] ?? 'N/A') : ($universidad->tipo ?? 'N/A') }}
                                                    @if((is_array($universidad) ? ($universidad['acreditada'] ?? false) : ($universidad->acreditada ?? false)))
                                                        <span class="text-green-600 font-semibold">• Acreditada</span>
                                                    @endif
                                                </p>
                                                @if((is_array($universidad) ? ($universidad['sitio_web'] ?? null) : ($universidad->sitio_web ?? null)))
                                                    <a href="{{ is_array($universidad) ? $universidad['sitio_web'] : $universidad->sitio_web }}" target="_blank" 
                                                    class="inline-flex items-center mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        Visitar sitio web
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 text-xs text-gray-400 bg-gray-50 p-3 rounded">No hay universidades registradas para esta carrera.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.46-.88-6.08-2.33" />
                    </svg>
                    <p class="text-gray-500">No se encontraron otras carreras relacionadas para tu perfil. Verifica tu configuración o intenta de nuevo.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleUniversidades(id) {
    var el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
    }
}
</script>
@endsection