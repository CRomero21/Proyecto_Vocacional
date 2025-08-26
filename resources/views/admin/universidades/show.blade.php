@extends('layouts.app')

@section('title', 'Detalles de Universidad')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.universidades.index') }}" class="text-blue-600 hover:underline flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al listado
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.universidades.edit', $universidad) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow transition">
                Editar
            </a>
            <form action="{{ route('admin.universidades.destroy', $universidad) }}" method="POST" onsubmit="return confirm('¿Eliminar universidad?');">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow transition">
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <div class="flex items-center gap-6">
            @if($universidad->logo)
                <img src="{{ Storage::url($universidad->logo) }}" class="h-20 w-20 rounded-full object-cover border" alt="Logo">
            @else
                <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center text-3xl font-bold text-blue-600">
                    {{ substr($universidad->nombre, 0, 1) }}
                </div>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $universidad->nombre }}</h1>
                <div class="flex gap-2 mt-2">
                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm">{{ $universidad->tipo }}</span>
                    @if($universidad->acreditada)
                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm">Acreditada</span>
                    @endif
                </div>
                <div class="mt-2 text-gray-500 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $universidad->departamento }} - {{ $universidad->municipio }}
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-gray-500">Dirección</div>
                <div class="font-medium">{{ $universidad->direccion }}</div>
            </div>
            <div>
                <div class="text-gray-500">Teléfono</div>
                <div class="font-medium">{{ $universidad->telefono ?? 'No disponible' }}</div>
            </div>
            <div>
                <div class="text-gray-500">Sitio Web</div>
                @if($universidad->sitio_web)
                    <a href="{{ $universidad->sitio_web }}" target="_blank" class="text-blue-600 hover:underline">{{ $universidad->sitio_web }}</a>
                @else
                    <span class="text-gray-400">No disponible</span>
                @endif
            </div>
        </div>
        @if($universidad->descripcion)
            <div class="mt-6">
                <div class="text-gray-500">Descripción</div>
                <div class="text-gray-800">{{ $universidad->descripcion }}</div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Carreras Ofrecidas</h2>
            <a href="{{ route('admin.carrera-universidad.create', ['universidad_id' => $universidad->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow transition">
                Asociar Carrera
            </a>
        </div>
        @if($universidad->carreras && $universidad->carreras->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($universidad->carreras as $carrera)
                    <li class="py-4 flex justify-between items-center">
                        <div>
                            <div class="font-medium text-gray-900">{{ $carrera->nombre }}</div>
                            <div class="text-gray-500 text-sm">{{ $carrera->area_conocimiento ?? 'Sin área' }}</div>
                        </div>
                        @php $pivotId = $carrera->pivot->id ?? null; @endphp
                        @if($pivotId)
                        <form action="{{ route('admin.carrera-universidad.destroy', $pivotId) }}" method="POST" onsubmit="return confirm('¿Desasociar esta carrera?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 px-3 py-1 rounded transition">Quitar</button>
                        </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-gray-500 text-center py-8">Esta universidad aún no tiene carreras asociadas.</div>
        @endif
    </div>
</div>
@endsection