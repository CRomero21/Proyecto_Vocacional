
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
                
                // Mapeo de tipos RIASEC para mostrar letra y nombre completo
                $tipoNombres = [
                    'R' => 'R (Realista)',
                    'I' => 'I (Investigador)',
                    'A' => 'A (Artista)',
                    'S' => 'S (Social)',
                    'E' => 'E (Emprendedor)',
                    'C' => 'C (Convencional)'
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($porcentajes as $tipo => $porcentaje)
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div class="text-2xl font-bold text-blue-700">{{ $tipoNombres[$tipo] ?? $tipo }}</div>
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

        {{-- Nueva Sección: Recomendación de Área de Estudio --}}
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-purple-700 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Recomendación de Área de Estudio
            </h2>
            @php
                $porcentajes = $resultados['porcentajes'] ?? [];
                arsort($porcentajes); // Ordenar de mayor a menor
                
                // Encontrar el porcentaje máximo
                $maxPorcentaje = reset($porcentajes);
                
                // Identificar todos los tipos que tienen el porcentaje máximo (manejar empates)
                $tiposDominantes = [];
                foreach ($porcentajes as $tipo => $porcentaje) {
                    if ($porcentaje === $maxPorcentaje) {
                        $tiposDominantes[] = $tipo;
                    } else {
                        break; // Como está ordenado, podemos parar cuando encontremos un porcentaje menor
                    }
                }
                
                // Mapeo de tipos RIASEC a áreas de estudio
                $mapeoAreas = [
                    'R' => ['area' => 'Ingeniería, Tecnología o Ciencias Naturales', 'descripcion' => 'Áreas prácticas y técnicas que involucran trabajo con objetos, máquinas y resolución de problemas concretos, alineadas con tu enfoque realista y orientado a la acción.'],
                    'I' => ['area' => 'Ciencias, Matemáticas o Investigación', 'descripcion' => 'Áreas analíticas y científicas que requieren pensamiento lógico, observación y resolución de problemas complejos, ideales para tu curiosidad intelectual.'],
                    'A' => ['area' => 'Artes, Humanidades o Diseño', 'descripcion' => 'Áreas creativas y expresivas que permiten la innovación, auto-expresión y trabajo sin estructuras rígidas, perfectas para tu sensibilidad artística.'],
                    'S' => ['area' => 'Ciencias Sociales, Educación o Salud', 'descripcion' => 'Áreas relacionadas con personas, servicio y empatía, donde puedes ayudar, enseñar y trabajar en entornos colaborativos, aprovechando tu amabilidad social.'],
                    'E' => ['area' => 'Administración, Economía o Negocios', 'descripcion' => 'Áreas de liderazgo, gestión y toma de riesgos, donde puedes convencer a otros y lograr objetivos ambiciosos, reflejando tu personalidad emprendedora.'],
                    'C' => ['area' => 'Contabilidad, Finanzas o Administración', 'descripcion' => 'Áreas organizadas y detalladas que involucran procedimientos establecidos, datos y precisión, ideales para tu enfoque convencional y meticuloso.']
                ];
            @endphp
            
            @if(count($tiposDominantes) > 0)
                @if(count($tiposDominantes) === 1)
                    <!-- Caso: Solo un tipo dominante -->
                    @php
                        $tipoDominante = $tiposDominantes[0];
                        $recomendacion = $mapeoAreas[$tipoDominante] ?? ['area' => 'Áreas generales de estudio', 'descripcion' => 'Consulta con un orientador para recomendaciones personalizadas.'];
                    @endphp
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 border border-purple-200">
                        <div class="flex items-center mb-4">
                            <span class="text-2xl font-bold text-purple-700 mr-3">{{ $tipoDominante }}</span>
                            <span class="text-lg font-semibold text-gray-800">Perfil Dominante</span>
                            <span class="ml-auto text-sm text-gray-500 bg-purple-100 px-2 py-1 rounded">{{ $maxPorcentaje }}%</span>
                        </div>
                        <p class="text-gray-700 mb-3"><strong>Área Recomendada:</strong> {{ $recomendacion['area'] }}</p>
                        <p class="text-sm text-gray-600">{{ $recomendacion['descripcion'] }}</p>
                        <div class="mt-4 text-xs text-gray-500 bg-purple-50 p-3 rounded">
                            <strong>Nota:</strong> Esta recomendación se basa en tu perfil RIASEC dominante y es un punto de partida. Considera tus intereses personales, habilidades y metas para una decisión final. Consulta con un orientador profesional para más orientación.
                        </div>
                    </div>
                @else
                    <!-- Caso: Múltiples tipos dominantes (empate) -->
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-800">¡Tienes múltiples perfiles dominantes!</span>
                            </div>
                            <p class="text-sm text-blue-700">Se encontraron {{ count($tiposDominantes) }} tipos con el mismo porcentaje máximo ({{ $maxPorcentaje }}%). Aquí tienes recomendaciones para cada uno:</p>
                        </div>
                        
                        @foreach($tiposDominantes as $tipo)
                            @php
                                $recomendacion = $mapeoAreas[$tipo] ?? ['area' => 'Áreas generales de estudio', 'descripcion' => 'Consulta con un orientador para recomendaciones personalizadas.'];
                            @endphp
                            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 border border-purple-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <span class="text-2xl font-bold text-purple-700 mr-3">{{ $tipo }}</span>
                                        <span class="text-lg font-semibold text-gray-800">Perfil Dominante</span>
                                    </div>
                                    <span class="text-sm text-gray-500 bg-purple-100 px-2 py-1 rounded">{{ $maxPorcentaje }}%</span>
                                </div>
                                <p class="text-gray-700 mb-3"><strong>Área Recomendada:</strong> {{ $recomendacion['area'] }}</p>
                                <p class="text-sm text-gray-600">{{ $recomendacion['descripcion'] }}</p>
                            </div>
                        @endforeach
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span class="text-sm font-medium text-yellow-800">Consejo importante</span>
                            </div>
                            <p class="text-sm text-yellow-700">Tener múltiples perfiles dominantes es común y positivo. Significa que tienes una personalidad versátil con intereses diversos. Te recomendamos explorar todas las áreas sugeridas y considerar cuál se alinea mejor con tus metas personales y oportunidades disponibles.</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.46-.88-6.08-2.33" />
                    </svg>
                    <p class="text-gray-500">No se pudo generar una recomendación de área de estudio debido a falta de datos de perfil.</p>
                </div>
            @endif
        </div>

        {{-- Sección de Retroalimentación Más Amigable --}}
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-orange-700 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                ¡Tu Opinión Nos Ayuda! 
            </h2>

            {{-- Mostrar retroalimentación existente --}}
            @if(isset($resultados['retroalimentacion']) && !empty($resultados['retroalimentacion']))
                <div class="bg-gradient-to-r from-orange-50 to-yellow-50 p-6 rounded-lg shadow-md mb-6 border border-orange-200">
                    <h3 class="text-lg font-semibold text-orange-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tu Retroalimentación Anterior
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-white p-3 rounded-lg border border-orange-100">
                            <span class="font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Utilidad:
                            </span>
                            <span class="ml-2 font-bold text-orange-700">{{ $resultados['retroalimentacion']['utilidad'] }}/5</span>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-orange-100">
                            <span class="font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Precisión:
                            </span>
                            <span class="ml-2 font-bold text-orange-700">{{ $resultados['retroalimentacion']['precision'] }}/5</span>
                        </div>
                    </div>
                    @if(isset($resultados['retroalimentacion']['comentario']) && !empty($resultados['retroalimentacion']['comentario']))
                        <div class="mb-4 bg-white p-3 rounded-lg border border-orange-100">
                            <span class="font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Comentario:
                            </span>
                            <p class="mt-1 text-gray-600">{{ $resultados['retroalimentacion']['comentario'] }}</p>
                        </div>
                    @endif
                    @if(isset($resultados['retroalimentacion']['carrera_seleccionada']) && !empty($resultados['retroalimentacion']['carrera_seleccionada']))
                        <div class="bg-white p-3 rounded-lg border border-orange-100">
                            <span class="font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Carrera Seleccionada:
                            </span>
                            <span class="ml-2 font-bold text-orange-700">{{ $resultados['retroalimentacion']['carrera_seleccionada'] }}</span>
                        </div>
                    @endif
                    <p class="text-xs text-gray-500 mt-4 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Enviado el {{ date('d/m/Y H:i', strtotime($resultados['retroalimentacion']['fecha'])) }}
                    </p>
                </div>
            @endif

            {{-- Formulario para nueva retroalimentación --}}
            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 p-6 rounded-lg shadow-md border border-orange-200">
                <h3 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    ¿Qué te pareció este test? ¡Tu opinión es valiosa!
                </h3>
                <p class="text-gray-600 mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Gracias por ayudarnos a mejorar. ¡Solo toma 1 minuto!
                </p>
                
                <form action="{{ route('test.retroalimentacion', $test->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label for="utilidad" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Utilidad del test (1-5)
                            </label>
                            <select name="utilidad" id="utilidad" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                                <option value="">Selecciona </option>
                                <option value="1">1 - Muy poco útil </option>
                                <option value="2">2 - Poco útil </option>
                                <option value="3">3 - Neutral </option>
                                <option value="4">4 - Útil </option>
                                <option value="5">5 - Muy útil </option>
                            </select>
                        </div>
                        <div>
                            <label for="precision" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Precisión de los resultados (1-5)
                            </label>
                            <select name="precision" id="precision" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                                <option value="">Selecciona </option>
                                <option value="1">1 - Muy impreciso </option>
                                <option value="2">2 - Poco preciso </option>
                                <option value="3">3 - Neutral </option>
                                <option value="4">4 - Preciso </option>
                                <option value="5">5 - Muy preciso </option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="comentario" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Comentario (opcional)
                        </label>
                        <textarea name="comentario" id="comentario" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="¡Comparte tus pensamientos! ¿Qué te gustó? ¿Qué podríamos mejorar? "></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="carrera_seleccionada" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Carrera que más te interesó (opcional)
                        </label>
                        <select name="carrera_seleccionada" id="carrera_seleccionada" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Selecciona una carrera </option>
                            @if(isset($carrerasPrincipales) && count($carrerasPrincipales) > 0)
                                @foreach($carrerasPrincipales as $carrera)
                                    <option value="{{ $carrera['carrera_id'] ?? $carrera['id'] }}">{{ $carrera['nombre'] }}</option>
                                @endforeach
                            @endif
                            @if(isset($carrerasSecundarias) && count($carrerasSecundarias) > 0)
                                @foreach($carrerasSecundarias as $carrera)
                                    <option value="{{ $carrera['carrera_id'] ?? $carrera['id'] }}">{{ $carrera['nombre'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-all duration-300 font-semibold shadow-lg transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Enviar Retroalimentación 
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Footer with Download Button --}}
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-6">
    <div class="container mx-auto px-4 text-center">
        <a href="{{ route('test.exportarPDF', $test->id) }}" 
           class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-all duration-300 font-semibold shadow-lg transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Descargar PDF
        </a>
    </div>
</div>

@endsection

<script>
function toggleUniversidades(id) {
    var el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
    }
}
</script>