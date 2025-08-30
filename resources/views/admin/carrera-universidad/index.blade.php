@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Gestión de Carreras por Universidad</h1>
    <div class="flex flex-col md:flex-row md:justify-between mb-6 gap-4">
        <form method="GET" action="{{ route('admin.carrera-universidad.index') }}" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <select name="universidad_id" id="universidad_id" class="rounded-md border-gray-300 shadow-sm bg-gray-50 text-gray-800 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas las universidades</option>
                @foreach($universidades as $uni)
                    <option value="{{ $uni->id }}" {{ request('universidad_id') == $uni->id ? 'selected' : '' }}>
                        {{ $uni->nombre }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="q" id="q" value="{{ request('q') }}"
                   class="rounded-md border-gray-300 shadow-sm bg-gray-50 text-gray-800 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="Buscar carrera...">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded shadow transition">Filtrar</button>
        </form>
        <a href="{{ route('admin.carrera-universidad.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded shadow transition h-10 flex items-center justify-center">
            + Asignar Carrera
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        @if(isset($carrerasUniversidades) && $carrerasUniversidades->count())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase">Carrera</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase">Universidad</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase">Modalidad</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase">Duración</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase">Costo/Semestre</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase">Requisitos</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase">Disponible</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($carrerasUniversidades as $rel)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $rel->carrera->nombre ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $rel->universidad->nombre ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $rel->modalidad ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $rel->duracion ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $rel->costo_semestre ? 'Bs ' . number_format($rel->costo_semestre, 2) : '-' }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $rel->requisitos ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if($rel->disponible)
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Sí</span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <a href="{{ route('admin.carrera-universidad.edit', $rel->id) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs font-bold shadow transition">Editar</a>
                                <form action="{{ route('admin.carrera-universidad.destroy', $rel->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta asignación?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold shadow transition">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-indigo-700 text-center py-10 text-lg font-semibold">No hay carreras asignadas a universidades aún.</p>
        @endif
    </div>
</div>
@endsection