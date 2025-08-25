
@extends('layouts.app')

@section('title', 'Detalles de Universidad')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Botones de navegación -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('admin.universidades.index') }}" class="flex items-center text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al listado
            </a>
            <div class="flex space-x-3">
                <a href="{{ route('admin.universidades.edit', $universidad) }}" class="flex items-center bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                <form action="{{ route('admin.universidades.destroy', $universidad) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta universidad? Esta acción no se puede deshacer.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>

        <!-- Tarjeta principal con detalles -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6">
                <h1 class="text-2xl font-bold text-white">{{ $universidad->nombre }}</h1>
                <div class="mt-2 flex items-center">
                    <span class="px-3 py-1 bg-white bg-opacity-30 rounded-full text-sm font-semibold text-white">
                        {{ $universidad->tipo }}
                    </span>
                    <span class="ml-3 text-yellow-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $universidad->ubicacion }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <!-- Información general -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Información General</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nombre</p>
                                <p class="text-gray-800">{{ $universidad->nombre }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tipo</p>
                                <p class="text-gray-800">{{ $universidad->tipo }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Ubicación</p>
                                <p class="text-gray-800">{{ $universidad->ubicacion }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Municipio</p>
                                <p class="text-gray-800">{{ $universidad->municipio }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Sitio Web</p>
                                @if($universidad->sitio_web)
                                    <a href="{{ $universidad->sitio_web }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $universidad->sitio_web }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @else
                                    <p class="text-gray-500 italic">No disponible</p>
                                @endif
                            </div>
                        </div>
                        @if($universidad->descripcion)
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Descripción</p>
                                <p class="text-gray-800 mt-1">{{ $universidad->descripcion }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Carreras ofrecidas -->
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-semibold text-gray-800">Carreras Ofrecidas</h2>
                        <a href="{{ route('admin.carrera-universidad.create', ['universidad_id' => $universidad->id]) }}" class="text-sm text-yellow-600 hover:text-yellow-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Asociar Carrera
                        </a>
                    </div>

                    @if($universidad->carreras && $universidad->carreras->count() > 0)
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <ul class="divide-y divide-gray-200">
                                @foreach($universidad->carreras as $carrera)
                                    <li class="p-4 hover:bg-gray-100 flex justify-between items-center">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $carrera->nombre }}</p>
                                            <p class="text-sm text-gray-500">{{ $carrera->area_conocimiento ?? 'Sin área' }}</p>
                                        </div>
                                        @php
                                            // Obtener el modelo pivot de la relación carrera_universidad
                                            $pivotId = $carrera->pivot->id ?? null;
                                        @endphp
                                        @if($pivotId)
                                        <form action="{{ route('admin.carrera-universidad.destroy', $pivotId) }}" method="POST" class="inline" onsubmit="return confirm('¿Desasociar esta carrera de la universidad?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="bg-gray-50 p-8 rounded-lg text-center">
                            <p class="text-gray-500">Esta universidad aún no tiene carreras asociadas.</p>
                            <a href="{{ route('admin.carrera-universidad.create', ['universidad_id' => $universidad->id]) }}" class="mt-2 inline-block text-yellow-600 hover:text-yellow-800">
                                Asociar una carrera ahora
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection