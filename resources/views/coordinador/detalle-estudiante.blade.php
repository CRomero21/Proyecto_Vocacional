
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Detalle del Estudiante</h2>
                    <a href="{{ route('coordinador.informes') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Volver a la lista
                    </a>
                </div>
                
                <!-- Información del estudiante -->
                <div class="bg-blue-50 p-6 rounded-lg border border-blue-100 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Información Personal</h3>
                            <p class="mb-2"><span class="font-medium">Nombre:</span> {{ $estudiante->name }}</p>
                            <p class="mb-2"><span class="font-medium">Email:</span> {{ $estudiante->email }}</p>
                            <p class="mb-2"><span class="font-medium">Fecha de registro:</span> {{ $estudiante->created_at->format('d/m/Y') }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Estadísticas</h3>
                            <p class="mb-2"><span class="font-medium">Total de tests realizados:</span> {{ $estudiante->tests->count() }}</p>
                            <p class="mb-2"><span class="font-medium">Último test:</span> 
                                {{ $estudiante->tests->count() > 0 ? $estudiante->tests->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') : 'No ha realizado tests' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Historial de tests -->
                <h3 class="text-xl font-semibold mb-4">Historial de Tests</h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($estudiante->tests->sortByDesc('created_at') as $test)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $test->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ $test->resultado ?? 'No disponible' }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Este estudiante no ha realizado ningún test.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection