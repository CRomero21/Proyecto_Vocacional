@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Historial de Tests Vocacionales</h1>
            <a href="{{ route('test.iniciar') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Nuevo Test
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if($tests->isEmpty())
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <p class="text-gray-600">No has realizado ningún test vocacional aún.</p>
                <a href="{{ route('test.iniciar') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Realizar mi primer test
                </a>
            </div>
        @else
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Fecha</th>
                            <th class="py-3 px-6 text-left">Perfil RIASEC</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($tests as $test)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($test->fecha_completado)->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <span class="px-2 py-1 rounded text-white text-xs font-bold" 
                                          style="background-color: {{ isset($tiposPersonalidad[$test->tipo_primario]['color']) ? $tiposPersonalidad[$test->tipo_primario]['color'] : '#3498db' }}">
                                        {{ $test->tipo_primario }}
                                    </span>
                                    @if($test->tipo_secundario)
                                        - 
                                        <span class="px-2 py-1 rounded text-white text-xs font-bold" 
                                              style="background-color: {{ isset($tiposPersonalidad[$test->tipo_secundario]['color']) ? $tiposPersonalidad[$test->tipo_secundario]['color'] : '#2ecc71' }}">
                                            {{ $test->tipo_secundario }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex item-center justify-center">
                                        <a href="{{ route('test.resultados', $test->id) }}" class="w-6 mr-2 transform hover:text-blue-500 hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('test.eliminar', $test->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-6 transform hover:text-red-500 hover:scale-110" 
                                                    onclick="return confirm('¿Estás seguro que deseas eliminar este test?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $tests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection