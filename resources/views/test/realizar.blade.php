
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 shadow-lg rounded-2xl p-8">
            <h1 class="text-3xl font-extrabold text-indigo-700 mb-2 text-center">Test Vocacional</h1>
            <p class="text-gray-700 mb-6 text-center">Responde cada afirmación según tu nivel de identificación. ¡Esto nos ayudará a recomendarte las mejores carreras!</p>

            <form action="{{ route('test.guardar') }}" method="POST">
                @csrf
                <input type="hidden" name="test_id" value="{{ $test_id }}">

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="space-y-8">
                    @foreach($preguntas as $index => $pregunta)
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mr-3">
                                    {{ $index + 1 }}
                                </div>
                                <p class="font-semibold text-gray-800 text-lg">{{ $pregunta->texto }}</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <label class="flex items-center p-3 bg-gray-50 rounded-md border border-gray-200 hover:bg-blue-50 transition cursor-pointer">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="0" class="text-blue-600 focus:ring-blue-500" required>
                                    <span class="ml-2 text-gray-700">No me identifica</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-md border border-gray-200 hover:bg-blue-50 transition cursor-pointer">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="1" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Me identifica un poco</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-md border border-gray-200 hover:bg-blue-50 transition cursor-pointer">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="2" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Me identifica mucho</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 flex justify-center">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow transition">
                        Guardar y Ver Resultados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection