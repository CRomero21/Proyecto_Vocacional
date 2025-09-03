@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl p-8">
            <h1 class="text-3xl font-bold text-blue-700 mb-2">Test Vocacionales</h1>
            <p class="text-gray-600 mb-6">Responde según tu nivel de identificación con cada afirmación. ¡No hay respuestas correctas o incorrectas!</p>

            <form action="{{ route('test.guardar') }}" method="POST">
                @csrf
                <input type="hidden" name="test_id" value="{{ $test_id }}">

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="space-y-8">
                    @foreach($preguntas as $index => $pregunta)
                        <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-100 transition hover:shadow-md">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-blue-600 font-semibold">Pregunta {{ $index+1 }} de {{ count($preguntas) }}</span>
                                <div class="w-32 h-2 bg-blue-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-400" style="width: {{ (($index+1)/count($preguntas))*100 }}%"></div>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-800 text-lg mb-4">{{ $pregunta->texto }}</p>
                            <div class="flex flex-col md:flex-row gap-3">
                                <label class="flex-1 flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-100 cursor-pointer transition">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="0" class="text-blue-600 focus:ring-blue-500" required>
                                    <span class="ml-3 text-gray-700">No me identifica</span>
                                </label>
                                <label class="flex-1 flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-100 cursor-pointer transition">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="1" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-3 text-gray-700">Me identifica un poco</span>
                                </label>
                                <label class="flex-1 flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-100 cursor-pointer transition">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="2" class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-3 text-gray-700">Me identifica mucho</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow transition">
                        Guardar y Ver Resultados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection