
@extends('layouts.app')

@section('title', 'Nueva Pregunta')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6" x-data="{
    preguntaTexto: '',
    preguntaTipo: '',
    get tipoNombre() {
        const tipos = {
            'R': 'Realista',
            'I': 'Investigador',
            'A': 'Artístico',
            'S': 'Social',
            'E': 'Emprendedor',
            'C': 'Convencional'
        };
        return tipos[this.preguntaTipo] || '';
    },
    get tipoColor() {
        const colores = {
            'R': 'red',
            'I': 'yellow',
            'A': 'green',
            'S': 'blue',
            'E': 'purple',
            'C': 'gray'
        };
        return colores[this.preguntaTipo] || 'indigo';
    }
}">
    <!-- Cabecera de la página -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-white">Nueva Pregunta</h1>
                    <p class="text-blue-100 mt-1">Crea una nueva pregunta para el test RIASEC</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Formulario principal -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                            <div class="font-medium">Hay errores en el formulario:</div>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.preguntas.store') }}">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="texto" class="block text-sm font-medium text-gray-700 mb-1">Texto de la pregunta</label>
                            <textarea 
                                name="texto" 
                                id="texto" 
                                rows="3" 
                                x-model="preguntaTexto"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('texto') border-red-300 @enderror" 
                                placeholder="Escribe la pregunta que se mostrará en el test..."
                                required
                            >{{ old('texto') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Ejemplo: "Me gusta reparar objetos electrónicos"</p>
                            
                            @error('texto')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Categoría RIASEC</label>
                            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                                <label @click="preguntaTipo = 'R'" class="relative flex flex-col items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                       :class="preguntaTipo === 'R' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300 hover:bg-red-50'">
                                    <input type="radio" name="tipo" value="R" class="sr-only" :checked="preguntaTipo === 'R'">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold bg-red-500 mb-1">R</span>
                                    <span class="text-xs font-medium">Realista</span>
                                    <svg x-show="preguntaTipo === 'R'" class="absolute top-1 right-1 w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </label>
                                
                                <label @click="preguntaTipo = 'I'" class="relative flex flex-col items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                       :class="preguntaTipo === 'I' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-300 hover:bg-yellow-50'">
                                    <input type="radio" name="tipo" value="I" class="sr-only" :checked="preguntaTipo === 'I'">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold bg-yellow-500 mb-1">I</span>
                                    <span class="text-xs font-medium">Investigador</span>
                                    <svg x-show="preguntaTipo === 'I'" class="absolute top-1 right-1 w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </label>
                                
                                <label @click="preguntaTipo = 'A'" class="relative flex flex-col items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                       :class="preguntaTipo === 'A' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300 hover:bg-green-50'">
                                    <input type="radio" name="tipo" value="A" class="sr-only" :checked="preguntaTipo === 'A'">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold bg-green-500 mb-1">A</span>
                                    <span class="text-xs font-medium">Artístico</span>
                                    <svg x-show="preguntaTipo === 'A'" class="absolute top-1 right-1 w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </label>
                                
                                <label @click="preguntaTipo = 'S'" class="relative flex flex-col items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                       :class="preguntaTipo === 'S' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50'">
                                    <input type="radio" name="tipo" value="S" class="sr-only" :checked="preguntaTipo === 'S'">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold bg-blue-500 mb-1">S</span>
                                    <span class="text-xs font-medium">Social</span>
                                    <svg x-show="preguntaTipo === 'S'" class="absolute top-1 right-1 w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </label>
                                
                                <label @click="preguntaTipo = 'E'" class="relative flex flex-col items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                       :class="preguntaTipo === 'E' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300 hover:bg-purple-50'">
                                    <input type="radio" name="tipo" value="E" class="sr-only" :checked="preguntaTipo === 'E'">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold bg-purple-500 mb-1">E</span>
                                    <span class="text-xs font-medium">Emprendedor</span>
                                    <svg x-show="preguntaTipo === 'E'" class="absolute top-1 right-1 w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </label>
                                
                                <label @click="preguntaTipo = 'C'" class="relative flex flex-col items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                       :class="preguntaTipo === 'C' ? 'border-gray-500 bg-gray-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'">
                                    <input type="radio" name="tipo" value="C" class="sr-only" :checked="preguntaTipo === 'C'">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-white font-bold bg-gray-500 mb-1">C</span>
                                    <span class="text-xs font-medium">Convencional</span>
                                    <svg x-show="preguntaTipo === 'C'" class="absolute top-1 right-1 w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </label>
                            </div>
                            @error('tipo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.preguntas.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Volver
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                    :class="{ 'opacity-50 cursor-not-allowed': !preguntaTexto || !preguntaTipo }"
                                    :disabled="!preguntaTexto || !preguntaTipo">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Crear Pregunta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Panel lateral -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Vista previa</h3>
                </div>
                <div class="p-6">
                    <!-- Vista previa de la pregunta en el test -->
                    <div class="mb-6 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                        <div class="p-4">
                            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center" x-show="preguntaTexto || preguntaTipo">
                                <span x-show="preguntaTipo" :class="`bg-${tipoColor}-100 text-${tipoColor}-800 w-6 h-6 rounded-full flex items-center justify-center mr-3 flex-shrink-0`" x-text="preguntaTipo"></span>
                                <span x-text="preguntaTexto || 'Ingrese el texto de su pregunta'"></span>
                            </h3>
                            <div x-show="!preguntaTexto && !preguntaTipo" class="text-gray-400 italic">
                                La vista previa aparecerá cuando comience a escribir la pregunta y seleccione un tipo RIASEC.
                            </div>
                            
                            <!-- Opciones de respuesta de ejemplo (visible solo si hay texto) -->
                            <div class="mt-4 flex flex-col gap-2" x-show="preguntaTexto">
                                <div class="flex items-center p-2 border rounded-lg border-red-300 bg-red-50">
                                    <div class="w-4 h-4 rounded-full border-2 border-red-500 mr-3"></div>
                                    <span class="text-gray-800 text-sm font-medium">No</span>
                                </div>
                                <div class="flex items-center p-2 border rounded-lg border-yellow-300 bg-yellow-50">
                                    <div class="w-4 h-4 rounded-full border-2 border-yellow-500 mr-3"></div>
                                    <span class="text-gray-800 text-sm font-medium">Tal vez</span>
                                </div>
                                <div class="flex items-center p-2 border rounded-lg border-green-300 bg-green-50">
                                    <div class="w-4 h-4 rounded-full border-2 border-green-500 mr-3"></div>
                                    <span class="text-gray-800 text-sm font-medium">Sí</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información sobre el tipo seleccionado -->
                    <div x-show="preguntaTipo" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-2" x-text="`Tipo: ${preguntaTipo} - ${tipoNombre}`"></h4>
                        <div class="text-sm text-gray-600">
                            <template x-if="preguntaTipo === 'R'">
                                <p>Interés por actividades prácticas, mecánicas, y técnicas que requieren destreza manual, persistencia y contacto con objetos, máquinas o herramientas.</p>
                            </template>
                            <template x-if="preguntaTipo === 'I'">
                                <p>Preferencia por actividades analíticas, intelectuales e investigativas. Valora el pensamiento abstracto, la precisión y la curiosidad científica.</p>
                            </template>
                            <template x-if="preguntaTipo === 'A'">
                                <p>Inclinación hacia formas de expresión creativas, con énfasis en la estética, la innovación y la originalidad. Disfruta de ambientes poco estructurados.</p>
                            </template>
                            <template x-if="preguntaTipo === 'S'">
                                <p>Interés por interactuar, ayudar, enseñar o servir a otros. Valora el trabajo en equipo y posee habilidades interpersonales y de comunicación.</p>
                            </template>
                            <template x-if="preguntaTipo === 'E'">
                                <p>Preferencia por actividades que implican persuasión, liderazgo y gestión. Busca influir en otros, tomar decisiones y asumir riesgos.</p>
                            </template>
                            <template x-if="preguntaTipo === 'C'">
                                <p>Inclinación hacia tareas detalladas, organizadas y sistemáticas. Valora la precisión, la estabilidad y prefiere seguir procedimientos establecidos.</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de ejemplos de preguntas -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mt-6">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ejemplos de preguntas</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4 text-sm">
                        <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                            <span class="font-semibold text-red-700">R (Realista):</span>
                            <p class="mt-1 text-gray-700">"Me gusta reparar objetos electrónicos o mecánicos."</p>
                        </div>
                        
                        <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <span class="font-semibold text-yellow-700">I (Investigador):</span>
                            <p class="mt-1 text-gray-700">"Disfruto resolviendo problemas matemáticos o científicos."</p>
                        </div>
                        
                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                            <span class="font-semibold text-green-700">A (Artístico):</span>
                            <p class="mt-1 text-gray-700">"Me expreso bien a través de la escritura, música o arte."</p>
                        </div>
                        
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <span class="font-semibold text-blue-700">S (Social):</span>
                            <p class="mt-1 text-gray-700">"Me gusta ayudar a las personas con sus problemas."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection