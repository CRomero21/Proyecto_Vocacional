@extends('layouts.app')

@section('title', 'Mi Portal de Orientación Vocacional')

@section('content')
<div class="bg-[#f2f2f2] min-h-screen">
    <!-- Barra de bienvenida personalizada -->
    <div class="bg-gradient-to-r from-[#051a9a] via-[#0b3be9] to-[#0079f4] shadow-lg">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 rounded-full p-2 mr-4 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Bienvenido, {{ Auth::user()->name }}</h1>
                        <p class="text-[#f2f2f2]/90">{{ now()->format('l, d \d\e F \d\e Y') }}</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-[#f2f2f2] hover:text-white transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Configurar perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alerta de éxito mejorada -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-[#f2f2f2] border border-[#00aeff]/30 text-[#131e58] rounded-lg flex items-center shadow-sm animate-fade-in-down">
                <div class="mr-3 bg-[#00aeff]/20 flex-shrink-0 rounded-full p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#0079f4]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="font-medium">¡Excelente!</span>
                    <span class="ml-1">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Progreso del test -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-[#c8c8c8]/50 overflow-hidden flex flex-col">
                <div class="bg-gradient-to-br from-[#0079f4] to-[#0b3be9] p-4">
                    <div class="flex items-center">
                        <div class="rounded-full bg-white/20 p-2 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Progreso RIASEC</h3>
                    </div>
                </div>
                <div class="p-5 flex-grow">
                    @if(isset($tests) && $tests->count())
                        <div class="flex items-center mb-2">
                            <span class="text-3xl font-bold text-[#131e58]">{{ $tests->count() }}</span>
                            <span class="ml-2 text-gray-600">test(s) completado(s)</span>
                        </div>
                        <div class="w-full bg-[#c8c8c8]/30 rounded-full h-2.5 mb-4">
                            <div class="bg-[#0079f4] h-2.5 rounded-full" style="width: {{ min($tests->count() * 20, 100) }}%"></div>
                        </div>
                        <p class="text-sm text-gray-500">Continúa realizando tests para obtener resultados más precisos.</p>
                    @else
                        <div class="flex items-center mb-2">
                            <span class="text-3xl font-bold text-[#131e58]">0</span>
                            <span class="ml-2 text-gray-600">tests completados</span>
                        </div>
                        <div class="w-full bg-[#c8c8c8]/30 rounded-full h-2.5 mb-4">
                            <div class="bg-[#0079f4] h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p class="text-sm text-gray-500">Comienza tu primer test para descubrir tu perfil vocacional.</p>
                    @endif
                </div>
                <div class="border-t border-[#c8c8c8]/30 p-4 bg-[#f2f2f2]">
                    <a href="#historial" class="text-sm text-[#0b3be9] hover:text-[#051a9a] font-medium flex items-center">
                        Ver historial detallado
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Perfil vocacional -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-[#c8c8c8]/50 overflow-hidden flex flex-col">
                <div class="bg-gradient-to-br from-[#051a9a] to-[#131e58] p-4">
                    <div class="flex items-center">
                        <div class="rounded-full bg-white/20 p-2 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Mi Perfil Dominante</h3>
                    </div>
                </div>
                <div class="p-5 flex-grow">
                    @if(isset($perfil_riasec) && count($perfil_riasec))
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            @foreach($perfil_riasec as $sigla => $valor)
                                <div class="bg-[#0079f4]/10 rounded-lg p-3 text-center">
                                    <span class="text-xs font-medium text-[#0b3be9] uppercase">{{ $sigla }}</span>
                                    <div class="text-2xl font-bold text-[#051a9a]">{{ $valor }}%</div>
                                    <span class="text-xs text-[#0079f4]">
                                        {{ [
                                            'R' => 'Realista',
                                            'I' => 'Investigador',
                                            'A' => 'Artístico',
                                            'S' => 'Social',
                                            'E' => 'Emprendedor',
                                            'C' => 'Convencional'
                                        ][$sigla] ?? $sigla }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500">Tu perfil predominante indica tus áreas de mayor afinidad profesional.</p>
                    @else
                        <div class="flex flex-col items-center justify-center h-24">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#c8c8c8] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-500">Realiza tu primer test para ver tu perfil.</p>
                        </div>
                    @endif
                </div>
                <div class="border-t border-[#c8c8c8]/30 p-4 bg-[#f2f2f2]">
                    <a href="#" class="text-sm text-[#0b3be9] hover:text-[#051a9a] font-medium flex items-center">
                        Ver perfil completo
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Carreras recomendadas -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-[#c8c8c8]/50 overflow-hidden flex flex-col">
                <div class="bg-gradient-to-br from-[#0079f4] to-[#00aeff] p-4">
                    <div class="flex items-center">
                        <div class="rounded-full bg-white/20 p-2 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Carreras Sugeridas</h3>
                    </div>
                </div>
                <div class="p-5 flex-grow">
                    @if(isset($carreras_sugeridas) && count($carreras_sugeridas))
                        <ul class="space-y-2">
                            @foreach($carreras_sugeridas as $i => $carrera)
                                <li class="flex items-center text-sm">
                                    <span class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full bg-[#00aeff]/20 text-[#051a9a] font-semibold mr-2">{{ $i+1 }}</span>
                                    <span class="text-gray-700">
                                        {{ is_array($carrera) ? $carrera['nombre'] : $carrera->nombre }}
                                    </span>
                                    @if(is_array($carrera) && isset($carrera['match']))
                                        <span class="ml-auto text-[#0079f4] text-xs font-medium">{{ $carrera['match'] }}% match</span>
                                    @elseif(isset($carrera->match))
                                        <span class="ml-auto text-[#0079f4] text-xs font-medium">{{ $carrera->match }}% match</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex flex-col items-center justify-center h-24">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#c8c8c8] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-500">Las carreras aparecerán después de tu primer test.</p>
                        </div>
                    @endif
                </div>
                <div class="border-t border-[#c8c8c8]/30 p-4 bg-[#f2f2f2]">
                    <a href="#" class="text-sm text-[#0b3be9] hover:text-[#051a9a] font-medium flex items-center">
                        Explorar todas las carreras
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Panel principal para estudiantes - Mejorado -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 border border-[#c8c8c8]/50">
            <div class="bg-gradient-to-r from-[#051a9a] to-[#0b3be9] px-6 py-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-16 w-32 h-32 bg-white/10 rounded-full"></div>
                <div class="absolute bottom-0 right-10 -mb-8 w-16 h-16 bg-white/10 rounded-full"></div>
                <div class="relative">
                    <h1 class="text-2xl font-bold text-white">Test de Orientación Vocacional</h1>
                    <p class="text-[#f2f2f2] mt-1">Descubre tu perfil profesional con el método RIASEC (Holland)</p>
                </div>
            </div>
            <div class="p-6 md:p-8">
                <div class="flex flex-col lg:flex-row">
                    <div class="lg:w-3/5 lg:pr-8 mb-6 lg:mb-0">
                        <h2 class="text-xl font-semibold text-[#131e58] mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#0b3be9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Descubre tu carrera ideal
                        </h2>
                        <p class="text-gray-600 mb-4 leading-relaxed">
                            El test RIASEC, desarrollado por John Holland, evalúa seis tipos de personalidad: Realista, 
                            Investigador, Artístico, Social, Emprendedor y Convencional. Tu perfil único te ayudará a 
                            identificar las carreras más adecuadas para ti.
                        </p>
                        <div class="bg-[#0079f4]/5 rounded-lg p-4 mb-5 border border-[#0079f4]/20">
                            <h3 class="font-medium text-[#051a9a] mb-2 text-sm uppercase tracking-wide">¿Cómo funciona?</h3>
                            <ul class="space-y-3">
                                <li class="flex text-sm">
                                    <div class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#00aeff]/20 text-[#051a9a] mr-3 font-bold">1</div>
                                    <span class="text-gray-700">Responde preguntas sobre tus preferencias e intereses</span>
                                </li>
                                <li class="flex text-sm">
                                    <div class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#00aeff]/20 text-[#051a9a] mr-3 font-bold">2</div>
                                    <span class="text-gray-700">Recibe un análisis detallado de tu perfil RIASEC</span>
                                </li>
                                <li class="flex text-sm">
                                    <div class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-[#00aeff]/20 text-[#051a9a] mr-3 font-bold">3</div>
                                    <span class="text-gray-700">Explora carreras compatibles con tus intereses y habilidades</span>
                                </li>
                            </ul>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Duración aproximada: 10-15 minutos
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                60 preguntas simples
                            </div>
                        </div>
                    </div>
                    <!-- Sección de iniciar test mejorada -->
                    <div class="lg:w-2/5 flex flex-col">
                        <div class="bg-gradient-to-b from-[#f2f2f2] to-[#0079f4]/5 rounded-xl p-6 border border-[#0079f4]/20 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-[#00aeff]/10 rounded-full -mt-10 -mr-10"></div>
                            <div class="absolute bottom-0 left-0 w-16 h-16 bg-[#0b3be9]/10 rounded-full -mb-8 -ml-8"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-[#131e58]">Test RIASEC</h3>
                                    <span class="bg-[#0079f4]/20 text-[#051a9a] text-xs font-semibold px-2.5 py-0.5 rounded-full">Recomendado</span>
                                </div>
                                <ul class="mb-6 space-y-2">
                                    <li class="flex items-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0b3be9] mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-gray-600">Resultados personalizados</span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0b3be9] mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-gray-600">Coincidencia con +300 carreras</span>
                                    </li>
                                    <li class="flex items-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0b3be9] mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-gray-600">Informe detallado de tu perfil</span>
                                    </li>
                                </ul>
                                <a href="{{ route('test.iniciar') }}" class="block text-center bg-[#0b3be9] hover:bg-[#051a9a] focus:ring-4 focus:ring-[#0b3be9]/30 text-white font-medium rounded-lg px-5 py-3.5 transition duration-200 shadow-sm hover:shadow">
                                    Iniciar Test RIASEC
                                </a>
                                <p class="text-xs text-gray-500 mt-4 text-center">
                                    Responde honestamente para obtener los mejores resultados posibles.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de tests del estudiante -->
        @if(isset($tests) && $tests->count())
            <div id="historial" class="bg-white rounded-xl shadow-md overflow-hidden border border-[#c8c8c8]/50">
                <div class="bg-gradient-to-r from-[#0b3be9] to-[#0079f4] px-6 py-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-16 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute bottom-0 right-10 -mb-8 w-16 h-16 bg-white/10 rounded-full"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white">Mi Historial de Tests</h2>
                            <p class="text-[#f2f2f2]/90 text-sm">Has completado {{ $tests->count() }} test(s)</p>
                        </div>
                        <div class="hidden md:block">
                            <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Más tests = Mayor precisión
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#c8c8c8]/50">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#f2f2f2] rounded-tl-lg">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#f2f2f2]">Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#f2f2f2]">Preguntas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#f2f2f2]">Perfil dominante</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#f2f2f2] rounded-tr-lg">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#c8c8c8]/50">
                                @foreach($tests as $test)
                                <tr class="hover:bg-[#f2f2f2] transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                        {{ date('d/m/Y', strtotime($test->fecha ?? $test->created_at)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ date('H:i', strtotime($test->fecha ?? $test->created_at)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#00aeff]/20 text-[#051a9a]">
                                            {{ $test->respuestas_count ?? 'N/A' }} completadas
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#0079f4]/20 text-[#051a9a]">
                                          {{ $test->perfil_dominante ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" class="text-[#0b3be9] hover:text-[#051a9a] mr-3">
                                            Ver resultados
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Recursos adicionales -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-[#c8c8c8]/50 hover:shadow-md transition-shadow">
                <div class="text-[#0079f4] mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#131e58] mb-2">Guías y recursos</h3>
                <p class="text-gray-600 text-sm mb-4">Accede a nuestra biblioteca de recursos sobre orientación vocacional y desarrollo profesional.</p>
                <a href="#" class="text-[#0b3be9] hover:text-[#051a9a] text-sm font-medium flex items-center">
                    Ver recursos
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-[#c8c8c8]/50 hover:shadow-md transition-shadow">
                <div class="text-[#00aeff] mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#131e58] mb-2">Asesoría personalizada</h3>
                <p class="text-gray-600 text-sm mb-4">Agenda una sesión con nuestros orientadores vocacionales profesionales para guiar tus decisiones.</p>
                <a href="#" class="text-[#0b3be9] hover:text-[#051a9a] text-sm font-medium flex items-center">
                    Reservar cita
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-[#c8c8c8]/50 hover:shadow-md transition-shadow">
                <div class="text-[#0b3be9] mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#131e58] mb-2">Comunidad estudiantil</h3>
                <p class="text-gray-600 text-sm mb-4">Conecta con otros estudiantes, comparte experiencias y participa en foros de discusión sobre carreras.</p>
                <a href="#" class="text-[#0b3be9] hover:text-[#051a9a] text-sm font-medium flex items-center">
                    Unirse a la comunidad
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
<style>
@keyframes fade-in-down {
    0% { opacity: 0; transform: translateY(-10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down {
    animation: fade-in-down 0.5s ease-out;
}
</style>
@endsection