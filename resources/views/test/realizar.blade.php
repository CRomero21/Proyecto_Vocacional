
@extends('layouts.app')

@section('title', 'Test RIASEC - Descubre tu perfil vocacional')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6" x-data="{ 
    currentStep: 1,
    totalSteps: {{ ceil($preguntas->count() / 5) }},
    showConfirmation: false,
    pendingQuestions: false,
    respuestas: {}, // Objeto para almacenar las respuestas
    checkCompletion() {
        let currentQuestions = this.getQuestionsForStep(this.currentStep);
        this.pendingQuestions = currentQuestions.some(id => !this.respuestas[id]);
    },
    getQuestionsForStep(step) {
        let allIds = [{{ $preguntas->pluck('id')->join(',') }}];
        let start = (step - 1) * 5;
        return allIds.slice(start, start + 5);
    }
}">
    <!-- Cabecera con instrucciones - sin cambios -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
            <h1 class="text-2xl font-bold text-white">Test RIASEC de Orientación Vocacional</h1>
            <p class="text-blue-100 mt-1">Descubre tus intereses y aptitudes profesionales</p>
        </div>
        
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Instrucciones:</h2>
            <div class="space-y-2 text-gray-600">
                <p>• Responde a todas las preguntas según tus preferencias personales.</p>
                <p>• No hay respuestas correctas o incorrectas, solo queremos conocer tus intereses.</p>
                <p>• Sé honesto/a para obtener resultados más precisos.</p>
                <p>• El test consta de {{ $preguntas->count() }} preguntas divididas en {{ ceil($preguntas->count() / 5) }} bloques.</p>
            </div>
        </div>
    </div>

    <!-- Barra de progreso - sin cambios -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Progreso</span>
                <span class="text-sm font-medium text-indigo-600" x-text="`${Math.round((currentStep / totalSteps) * 100)}%`"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300 ease-in-out" 
                     :style="`width: ${(currentStep / totalSteps) * 100}%`"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>Bloque <span x-text="currentStep"></span> de <span x-text="totalSteps"></span></span>
                <span><span x-text="Math.min(currentStep * 5, {{ $preguntas->count() }})"></span> de {{ $preguntas->count() }} preguntas</span>
            </div>
        </div>
    </div>

    <!-- Formulario del test -->
    <form method="POST" action="{{ route('test.guardar') }}" id="test-form">
        @csrf
        <input type="hidden" name="test_id" value="{{ $test_id }}">
        
        <!-- Preguntas agrupadas por pasos -->
        @foreach($preguntas->chunk(5) as $index => $chunk)
            <div x-show="currentStep === {{ $index + 1 }}" x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 transform translate-y-4" 
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                @foreach($chunk as $pregunta)
                    <div class="mb-6 bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex">
                                <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    {{ ($index * 5) + $loop->iteration }}
                                </span>
                                {{ $pregunta->texto }}
                            </h3>
                            
                            <!-- Nuevos radio buttons usando x-model -->
                            <div class="mt-4 flex flex-col sm:flex-row justify-between gap-2" @change="checkCompletion()">
                                <label class="flex-1 flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200 
                                        hover:bg-red-50 hover:border-red-300"
                                       :class="{ 'bg-red-50 border-red-500': respuestas[{{ $pregunta->id }}] === '0' }">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="0" class="sr-only"
                                           x-model="respuestas[{{ $pregunta->id }}]">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3"
                                         :class="{ 'border-red-500 bg-red-500': respuestas[{{ $pregunta->id }}] === '0' }">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20" 
                                             x-show="respuestas[{{ $pregunta->id }}] === '0'">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-800 font-medium">No</span>
                                </label>
                                
                                <label class="flex-1 flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200 
                                        hover:bg-yellow-50 hover:border-yellow-300"
                                       :class="{ 'bg-yellow-50 border-yellow-500': respuestas[{{ $pregunta->id }}] === '1' }">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="1" class="sr-only"
                                           x-model="respuestas[{{ $pregunta->id }}]">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3"
                                         :class="{ 'border-yellow-500 bg-yellow-500': respuestas[{{ $pregunta->id }}] === '1' }">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20" 
                                             x-show="respuestas[{{ $pregunta->id }}] === '1'">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-800 font-medium">Tal vez</span>
                                </label>
                                
                                <label class="flex-1 flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200 
                                        hover:bg-green-50 hover:border-green-300"
                                       :class="{ 'bg-green-50 border-green-500': respuestas[{{ $pregunta->id }}] === '2' }">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="2" class="sr-only"
                                           x-model="respuestas[{{ $pregunta->id }}]">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3"
                                         :class="{ 'border-green-500 bg-green-500': respuestas[{{ $pregunta->id }}] === '2' }">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20" 
                                             x-show="respuestas[{{ $pregunta->id }}] === '2'">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-800 font-medium">Sí</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Botones de navegación - sin cambios -->
                <div class="flex justify-between mt-6">
                    <button type="button" 
                            x-show="currentStep > 1" 
                            @click="currentStep--" 
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                    
                    <button type="button" 
                            x-show="currentStep < totalSteps" 
                            @click="checkCompletion(); if(!pendingQuestions) { currentStep++; }" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 flex items-center"
                            :class="{ 'opacity-50 cursor-not-allowed': pendingQuestions }">
                        Siguiente
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    
                    <button type="button" 
                            x-show="currentStep === totalSteps" 
                            @click="checkCompletion(); if(!pendingQuestions) { showConfirmation = true }" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200 flex items-center"
                            :class="{ 'opacity-50 cursor-not-allowed': pendingQuestions }">
                        Finalizar Test
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </form>
    
    <!-- Modal de confirmación - sin cambios -->
    <div x-show="showConfirmation" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Finalizar test
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Estás a punto de enviar tus respuestas. Una vez enviadas, no podrás modificarlas.
                                    ¿Estás seguro/a de que deseas finalizar el test?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="document.getElementById('test-form').submit();" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Sí, enviar respuestas
                    </button>
                    <button type="button" @click="showConfirmation = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Revisar respuestas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection