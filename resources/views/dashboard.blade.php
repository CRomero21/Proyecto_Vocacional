
@extends('layouts.app')

@section('title', 'Mi Portal de Orientación Vocacional')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6">
    <!-- Alerta de éxito mejorada -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Panel principal para estudiantes -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5">
            <h1 class="text-2xl font-bold text-white">Mi Perfil Vocacional</h1>
            <p class="text-blue-100 mt-1">Descubre tus intereses profesionales con el test RIASEC</p>
        </div>
                
        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-3/5 mb-6 md:mb-0 md:pr-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">¿Qué carrera es adecuada para ti?</h2>
                    <p class="text-gray-600 mb-3">
                        El test RIASEC te ayuda a identificar tus intereses y habilidades para encontrar 
                        carreras que se alineen con tu perfil personal.
                    </p>
                    <ul class="space-y-2 mb-4">
                        <li class="flex items-center text-sm">
                            <span class="text-green-600 mr-2">✓</span>
                            <span class="text-gray-700">Responde preguntas sobre tus preferencias</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <span class="text-green-600 mr-2">✓</span>
                            <span class="text-gray-700">Descubre tus áreas de interés dominantes</span>
                        </li>
                        <li class="flex items-center text-sm">
                            <span class="text-green-600 mr-2">✓</span>
                            <span class="text-gray-700">Explora carreras que coincidan con tu perfil</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Botón de test destacado -->
                <div class="md:w-2/5 flex flex-col items-center justify-center">
                    <div class="text-center bg-gray-50 p-5 rounded-lg mb-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-2">Tiempo: 10-15 minutos</div>
                        <a href="{{ route('test.iniciar') }}" class="block bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-lg font-bold">
                            Iniciar Test RIASEC
                        </a>
                        <p class="text-sm text-gray-500 mt-2">
                            Responde honestamente para mejores resultados
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de tests del estudiante -->
    @if(isset($tests) && $tests->count())
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Mi Historial de Tests</h2>
                <p class="text-purple-100 text-sm">Has completado {{ $tests->count() }} test(s)</p>
            </div>
            
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preguntas Respondidas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultados</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tests as $test)
                            <tr class="hover:bg-purple-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ date('d/m/Y', strtotime($test->fecha)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ date('H:i', strtotime($test->fecha)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $test->respuestas_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Ver resultados</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection