
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Gestión de Carreras por Universidad</h1>

    <div class="mb-8">
        <a href="{{ route('admin.carreras.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a Carreras
        </a>
    </div>

    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <form method="GET" action="{{ route('admin.carrera-universidad.index') }}" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <div>
                <label for="universidad_id" class="block text-xs font-semibold text-gray-600 mb-1">Filtrar por universidad</label>
                <select name="universidad_id" id="universidad_id" class="rounded-md border-gray-300 shadow-sm w-full md:w-56">
                    <option value="">Todas</option>
                    @foreach($universidades as $uni)
                        <option value="{{ $uni->id }}" {{ request('universidad_id') == $uni->id ? 'selected' : '' }}>
                            {{ $uni->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="q" class="block text-xs font-semibold text-gray-600 mb-1">Buscar carrera</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}"
                       class="rounded-md border-gray-300 shadow-sm w-full md:w-56"
                       placeholder="Nombre de la carrera...">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                    Filtrar
                </button>
            </div>
        </form>
        <a href="{{ route('admin.carrera-universidad.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition h-10 flex items-center justify-center">
            + Asignar Carrera a Universidad
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        @if(isset($carrerasUniversidades) && $carrerasUniversidades->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Carrera</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Universidad</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Modalidad</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Duración</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Costo/Semestre</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Requisitos</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Disponible</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($carrerasUniversidades as $rel)
                            <tr>
                                <td class="px-4 py-2">{{ $rel->carrera->nombre ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $rel->universidad->nombre ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $rel->modalidad ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $rel->duracion ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $rel->costo_semestre ? 'Bs ' . number_format($rel->costo_semestre, 2) : '-' }}</td>
                                <td class="px-4 py-2">{{ $rel->requisitos ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    @if($rel->disponible)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Sí</span>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 flex gap-2">
                                    <a href="{{ route('admin.carrera-universidad.edit', $rel->id) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs font-bold">Editar</a>
                                    <form action="{{ route('admin.carrera-universidad.destroy', $rel->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta asignación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-10">No hay carreras asignadas a universidades aún.</p>
        @endif
    </div>
</div>
@endsection