
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Carreras</h1>
        <a href="{{ route('admin.carreras.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
            Agregar Carrera
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipos RIASEC</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($carreras as $carrera)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $carrera->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $carrera->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ Str::limit($carrera->descripcion, 100) }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                @if($carrera->carreraTipos->isNotEmpty())
                                    @foreach($carrera->carreraTipos as $index => $tipo)
                                        <div class="{{ $index > 0 ? 'mt-2 pt-2 border-t border-gray-200' : '' }}">
                                            @if($tipo->tipo_primario)
                                                <span class="px-2 py-1 rounded text-white text-xs font-bold bg-blue-600">
                                                    {{ $tipo->tipo_primario }}
                                                </span>
                                            @endif
                                            @if($tipo->tipo_secundario)
                                                <span class="px-2 py-1 rounded text-white text-xs font-bold bg-green-600">
                                                    {{ $tipo->tipo_secundario }}
                                                </span>
                                            @endif
                                            @if($tipo->tipo_terciario)
                                                <span class="px-2 py-1 rounded text-white text-xs font-bold bg-yellow-600">
                                                    {{ $tipo->tipo_terciario }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                    <a href="{{ route('admin.carreras.tipos.edit', $carrera) }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                        Gestionar tipos ({{ $carrera->carreraTipos->count() }})
                                    </a>
                                @else
                                    <span class="text-gray-400">No asignado</span>
                                    <a href="{{ route('admin.carreras.tipos.edit', $carrera) }}" class="text-xs text-blue-600 hover:underline ml-1">
                                        Asignar
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex space-x-3">
                                    <a href="{{ route('admin.carreras.edit', $carrera) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.carreras.destroy', $carrera) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta carrera?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No hay carreras registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $carreras->links() }}
    </div>
</div>
@endsection