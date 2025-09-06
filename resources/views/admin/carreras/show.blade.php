
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <div class="flex items-center mb-8">
        <a href="{{ route('admin.carreras.index') }}" class="text-blue-600 hover:text-blue-800 mr-4 flex items-center transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver
        </a>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $carrera->nombre }}</h1>
        
        <div class="ml-auto flex space-x-2">
            <a href="{{ route('admin.carreras.edit', $carrera) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
            </a>
            <form action="{{ route('admin.carreras.destroy', $carrera) }}" method="POST" onsubmit="return confirm('¿Estás seguro que deseas eliminar esta carrera?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Columna de Información Principal -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                <h2 class="ml-3 text-xl font-bold text-white">Información de la Carrera</h2>
            </div>
            
            <div class="p-6">
                <div class="mb-6">
                    <div class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                                {{ $carrera->es_institucional 
                                ? 'bg-green-100 text-green-800 border border-green-200' 
                                : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                        {{ $carrera->es_institucional ? 'Carrera Institucional' : 'Carrera No Institucional' }}
                    </div>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Área de Conocimiento
                        </h3>
                        <p class="mt-1 text-gray-700 bg-blue-50 px-4 py-2 rounded-lg">{{ $carrera->area_conocimiento }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Descripción
                        </h3>
                        <p class="mt-1 text-gray-700">{{ $carrera->descripcion }}</p>
                    </div>
                    
                    @if($carrera->duracion)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Duración
                        </h3>
                        <p class="mt-1 text-gray-700">{{ $carrera->duracion }}</p>
                    </div>
                    @endif
                    
                    @if($carrera->perfil_ingreso)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Perfil de Ingreso
                        </h3>
                        <p class="mt-1 text-gray-700">{{ $carrera->perfil_ingreso }}</p>
                    </div>
                    @endif
                    
                    @if($carrera->perfil_egreso)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                            Perfil de Egreso
                        </h3>
                        <p class="mt-1 text-gray-700">{{ $carrera->perfil_egreso }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Columna Lateral -->
        <div class="space-y-8">
            <!-- Imagen de la carrera -->
            @if($carrera->imagen)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Imagen Representativa</h2>
                    </div>
                    <div class="p-4 flex justify-center">
                        <img src="{{ asset('storage/'.$carrera->imagen) }}" alt="Imagen de {{ $carrera->nombre }}" 
                             class="rounded-lg shadow-md max-h-64 object-contain border border-gray-200 bg-white p-2">
                    </div>
                </div>
            @endif
            
            <!-- Perfiles RIASEC -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Perfiles RIASEC</h2>
                    <span class="bg-white text-indigo-700 text-xs font-bold px-2 py-1 rounded-full">
                        {{ $carrera->carreraTipos->count() }} combinación(es)
                    </span>
                </div>
                
                <div class="p-4">
                    @if($carrera->carreraTipos->count() > 0)
                        @foreach($carrera->carreraTipos as $index => $tipo)
                            <div class="mb-4 p-4 {{ $index % 2 == 0 ? 'bg-indigo-50' : 'bg-purple-50' }} rounded-lg">
                                <h3 class="font-semibold text-gray-800 mb-2">Combinación #{{ $index + 1 }}</h3>
                                
                                <div class="flex flex-wrap gap-2">
                                    @if($tipo->tipo_primario)
                                        <div class="flex items-center bg-blue-600 text-white px-3 py-1.5 rounded-full text-sm">
                                            <span class="font-bold mr-1">1°</span> {{ $tipo->tipo_primario }}
                                        </div>
                                    @endif
                                    
                                    @if($tipo->tipo_secundario)
                                        <div class="flex items-center bg-green-600 text-white px-3 py-1.5 rounded-full text-sm">
                                            <span class="font-bold mr-1">2°</span> {{ $tipo->tipo_secundario }}
                                        </div>
                                    @endif
                                    
                                    @if($tipo->tipo_terciario)
                                        <div class="flex items-center bg-yellow-600 text-white px-3 py-1.5 rounded-full text-sm">
                                            <span class="font-bold mr-1">3°</span> {{ $tipo->tipo_terciario }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-6 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>No hay perfiles RIASEC asignados</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Universidades (condicional) -->
            @if(isset($carrera->universidades) && $carrera->universidades->count() > 0)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Universidades</h2>
                    </div>
                    
                    <div class="p-4">
                        <ul class="space-y-2">
                            @foreach($carrera->universidades as $uni)
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $uni->nombre }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Acciones de Footer -->
    <div class="mt-8 text-center">
        <a href="{{ route('admin.carreras.tipos.edit', $carrera) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Gestionar Tipos RIASEC
        </a>
    </div>
</div>
@endsection