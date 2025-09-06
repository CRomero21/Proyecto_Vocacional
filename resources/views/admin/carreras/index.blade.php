@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
            </svg>
            Gestión de Carreras
        </h1>
        <a href="{{ route('admin.carreras.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg shadow-md transition flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Agregar Carrera
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm flex items-start" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Formulario de Filtros -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
            </svg>
            Filtros de búsqueda
        </h2>
        <form action="{{ route('admin.carreras.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Búsqueda por nombre -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por nombre</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-3 py-2 border-gray-300 rounded-lg"
                           placeholder="Nombre de la carrera...">
                </div>
            </div>
            
            <!-- Filtro por Área de Conocimiento -->
            <div>
                <label for="area_conocimiento" class="block text-sm font-medium text-gray-700 mb-1">Área de Conocimiento</label>
                <select name="area_conocimiento" id="area_conocimiento" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-lg">
                    <option value="">Todas las áreas</option>
                    @foreach($areas_conocimiento as $area)
                        <option value="{{ $area }}" {{ request('area_conocimiento') == $area ? 'selected' : '' }}>
                            {{ $area }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filtro por Tipo Institucional -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Carrera</label>
                <div class="flex space-x-4 mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="es_institucional" value="" 
                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                               {{ request('es_institucional') === null ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Todas</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="es_institucional" value="1"
                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                               {{ request('es_institucional') === '1' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Institucionales</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="es_institucional" value="0"
                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                               {{ request('es_institucional') === '0' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">No institucionales</span>
                    </label>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="md:col-span-3 flex justify-end mt-4 space-x-3">
                <a href="{{ route('admin.carreras.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Limpiar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                    </svg>
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Carreras -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipos RIASEC</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($carreras as $carrera)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $carrera->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $carrera->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ Str::limit($carrera->descripcion, 100) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($carrera->area_conocimiento)
                                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $carrera->area_conocimiento }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No asignada</span>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                @if($carrera->carreraTipos->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($carrera->carreraTipos as $tipo)
                                            <div class="mb-1">
                                                @if($tipo->tipo_primario)
                                                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium bg-blue-600">
                                                        {{ $tipo->tipo_primario }}
                                                    </span>
                                                @endif
                                                @if($tipo->tipo_secundario)
                                                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium bg-green-600">
                                                        {{ $tipo->tipo_secundario }}
                                                    </span>
                                                @endif
                                                @if($tipo->tipo_terciario)
                                                    <span class="px-2 py-1 rounded-full text-white text-xs font-medium bg-yellow-600">
                                                        {{ $tipo->tipo_terciario }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <a href="{{ route('admin.carreras.tipos.edit', $carrera) }}" class="text-xs text-blue-600 hover:text-blue-800 hover:underline flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Gestionar ({{ $carrera->carreraTipos->count() }})
                                    </a>
                                @else
                                    <div class="flex items-center">
                                        <span class="text-gray-400 mr-2">No asignado</span>
                                        <a href="{{ route('admin.carreras.tipos.edit', $carrera) }}" class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Asignar
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $carrera->es_institucional 
                                        ? 'bg-green-100 text-green-800 border border-green-200' 
                                        : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                                    {{ $carrera->es_institucional ? 'Institucional' : 'No institucional' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.carreras.show', $carrera) }}" 
                                       class="text-blue-600 hover:text-blue-900 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.carreras.edit', $carrera) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.carreras.destroy', $carrera) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta carrera?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-lg font-medium">No hay carreras registradas</span>
                                    <p class="text-gray-500 mt-1">Utiliza el botón "Agregar Carrera" para crear una nueva</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $carreras->appends(request()->query())->links() }}
    </div>
</div>
@endsection