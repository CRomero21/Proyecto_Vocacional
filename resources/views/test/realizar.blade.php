@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Test Vocacional</h1>
            <p class="text-gray-600 mb-6">Responde las siguientes preguntas según qué tanto te identifiques con cada afirmación. Esto nos ayudará a determinar tu perfil vocacional.</p>

            <form action="{{ route('test.guardar') }}" method="POST">
                @csrf
                <input type="hidden" name="test_id" value="{{ $test_id }}">

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="space-y-6">
                    @foreach($preguntas as $pregunta)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-medium text-gray-800 mb-3">{{ $pregunta->texto }}</p>
                            
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:bg-gray-50">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="0" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2">No me identifica</span>
                                </label>
                                
                                <label class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:bg-gray-50">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="1" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2">Me identifica un poco</span>
                                </label>
                                
                                <label class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:bg-gray-50">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="2" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2">Me identifica mucho</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                        Guardar y Ver Resultados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection