
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Carreras</h1>
        <a href="{{ route('admin.carreras.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Añadir Carrera
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                <tr>
                    <th class="py-3 px-6 text-left">Nombre</th>
                    <th class="py-3 px-6 text-left">Área</th>
                    <th class="py-3 px-6 text-left">Tipo RIASEC</th>
                    <th class="py-3 px-6 text-left">Institucional</th>
                    <th class="py-3 px-6 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($carreras as $carrera)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-6 text-left">
                            {{ $carrera->nombre }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $carrera->area_conocimiento ?? 'No especificada' }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            @if($carrera->tipoPrimario || $carrera->tipoSecundario || $carrera->tipoTerciario)
                                @if($carrera->tipoPrimario)
                                    <span class="px-2 py-1 rounded text-white text-xs font-bold"
                                          style="background-color: {{ $carrera->tipoPrimario->color_hex }}">
                                        {{ $carrera->tipoPrimario->codigo }}
                                    </span>
                                @endif
                                @if($carrera->tipoSecundario)
                                    <span class="px-2 py-1 rounded text-white text-xs font-bold"
                                          style="background-color: {{ $carrera->tipoSecundario->color_hex }}">
                                        {{ $carrera->tipoSecundario->codigo }}
                                    </span>
                                @endif
                                @if($carrera->tipoTerciario)
                                    <span class="px-2 py-1 rounded text-white text-xs font-bold"
                                          style="background-color: {{ $carrera->tipoTerciario->color_hex }}">
                                        {{ $carrera->tipoTerciario->codigo }}
                                    </span>
                                @endif
                            @else
                                No asignado
                            @endif
                        </td>
                        <td class="py-3 px-6 text-left">
                            @if($carrera->es_institucional)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Sí</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">No</span>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <a href="{{ route('admin.carreras.show', $carrera) }}" class="w-6 mr-2 transform hover:text-blue-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.carreras.edit', $carrera) }}" class="w-6 mr-2 transform hover:text-yellow-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.carreras.destroy', $carrera) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-6 transform hover:text-red-500 hover:scale-110" 
                                            onclick="return confirm('¿Estás seguro que deseas eliminar esta carrera?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 px-6 text-center text-gray-500">
                            No hay carreras registradas. ¡Comienza creando una!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $carreras->links() }}
    </div>
</div>
@endsection