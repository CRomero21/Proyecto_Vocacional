
@extends('layouts.app')

@section('title', 'Informes Avanzados')

@section('content')
<div class="bg-gradient-to-br from-blue-900 to-indigo-900 min-h-screen p-4 md:p-8">
    <div class="mx-auto max-w-7xl">
        <!-- Breadcrumb y Botón de Regreso -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center text-sm text-white/70">
                <a href="{{ route('admin.informes-avanzados.index') }}" class="hover:text-white">Dashboard</a>
                <svg class="w-3 h-3 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white">Informes Avanzados</span>
            </div>
            <a href="{{ route('admin.informes-avanzados.index') }}" class="flex items-center text-white/80 hover:text-white">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Volver al Dashboard</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 p-6">
                <h1 class="text-2xl font-bold text-white">Informes Avanzados</h1>
                <p class="text-purple-100 mt-1">Análisis detallados y extracción de datos para la toma de decisiones</p>
            </div>
            
            <!-- Mensajes de éxito/error -->
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
            @endif

            @if(session('info'))
            <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                {{ session('info') }}
            </div>
            @endif
            
            <!-- Panel de filtros -->
            <div class="p-6 border-b border-gray-200 bg-gray-50" x-data="{ filtersOpen: true }">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Filtros de Informe</h2>
                    <button @click="filtersOpen = !filtersOpen" class="text-gray-500 hover:text-gray-700">
                        <span x-show="!filtersOpen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                        <span x-show="filtersOpen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                
                <form x-show="filtersOpen" action="{{ route('admin.informes-avanzados.generar') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tipo de Informe -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Informe</label>
                        <select name="tipo_informe" id="tipo_informe" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="usuarios_datos">Datos de Contacto de Usuarios</option>
                            <option value="instituciones_educativas">Usuarios por Institución Educativa</option>
                            <option value="distribucion_demografica">Distribución Geográfica</option>
                            <option value="tests_completados">Tests Completados vs Incompletos</option>
                            <option value="personalidades">Distribución de Tipos de Personalidad</option>
                            <option value="carreras">Carreras Recomendadas</option>
                        </select>
                    </div>
                    
                    <!-- Filtro por Fecha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Fechas</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-500">Desde</label>
                                <input type="date" name="fecha_inicio" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Hasta</label>
                                <input type="date" name="fecha_fin" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtro por Ubicación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <select name="departamento" id="departamento" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos los departamentos</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento }}">{{ $departamento }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="ciudad" id="ciudad" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todas las ciudades</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda fila de filtros -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Institución Educativa</label>
                        <select name="institucion" id="institucion" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todas las instituciones</option>
                            @foreach($instituciones as $institucion)
                                <option value="{{ $institucion }}">{{ $institucion }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                        <select name="genero" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todos</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">No especificado</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Edad</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input type="number" name="edad_min" placeholder="Mín." class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <input type="number" name="edad_max" placeholder="Máx." class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Área de Conocimiento</label>
                        <select name="area_conocimiento" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todas las áreas</option>
                            @foreach($areasConocimiento as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtros específicos -->
                    <div id="filtros_tipo_personalidad" class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Personalidad</label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="tipos_personalidad[]" value="R" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Realista (R)</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="tipos_personalidad[]" value="I" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Investigador (I)</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="tipos_personalidad[]" value="A" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Artístico (A)</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="tipos_personalidad[]" value="S" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Social (S)</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="tipos_personalidad[]" value="E" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Emprendedor (E)</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="tipos_personalidad[]" value="C" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Convencional (C)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado del Test</label>
                            <select name="estado_test" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos los estados</option>
                                <option value="completado">Completados</option>
                                <option value="incompleto">Incompletos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Campos a incluir</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="campos[]" value="telefono" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Teléfono</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="campos[]" value="email" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Email</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="md:col-span-3 flex flex-wrap justify-end gap-3 mt-4">
                        <button type="reset" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Limpiar Filtros
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Generar Informe
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Informes guardados -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informes Recientes</h2>
                
                <div class="overflow-x-auto bg-gray-50 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filtros</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($informesRecientes as $informe)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $informe->nombre }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                    @if($informe->tipo == 'usuarios_datos') bg-blue-100 text-blue-800
                                    @elseif($informe->tipo == 'instituciones_educativas') bg-green-100 text-green-800
                                    @elseif($informe->tipo == 'distribucion_demografica') bg-purple-100 text-purple-800
                                    @elseif($informe->tipo == 'tests_completados') bg-yellow-100 text-yellow-800
                                    @elseif($informe->tipo == 'personalidades') bg-pink-100 text-pink-800
                                    @elseif($informe->tipo == 'carreras') bg-indigo-100 text-indigo-800
                                    @endif
                                    ">{{ $informe->tipo_legible ?? $informe->tipo }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ is_string($informe->created_at) ? date('d M Y', strtotime($informe->created_at)) : $informe->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $informe->filtros ? 'Con filtros' : 'Sin filtros' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $informe->id }}">
                                            <input type="hidden" name="formato" value="excel">
                                            <button type="submit" class="text-green-600 hover:text-green-900">Excel</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.informes-avanzados.exportar') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $informe->id }}">
                                            <input type="hidden" name="formato" value="pdf">
                                            <button type="submit" class="text-red-600 hover:text-red-900">PDF</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if(count($informesRecientes) == 0)
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    No hay informes recientes para mostrar.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Contenido del informe (aparece cuando se genera) -->
            @if(isset($datos) && !empty($datos))
            <div class="p-6" id="resultados-informe" x-data="{ activeTab: 'tabla' }">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Resultados del Informe</h2>
                    <div class="flex space-x-2">
                        <button @click="activeTab = 'tabla'" :class="{'bg-indigo-600 text-white': activeTab === 'tabla', 'bg-gray-200 text-gray-700': activeTab !== 'tabla'}" class="px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Tabla
                        </button>
                        <button @click="activeTab = 'grafico'" :class="{'bg-indigo-600 text-white': activeTab === 'grafico', 'bg-gray-200 text-gray-700': activeTab !== 'grafico'}" class="px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Gráfico
                        </button>
                    </div>
                </div>
                
                <!-- Acciones para el informe -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <form action="{{ route('admin.informes-avanzados.guardar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="datos_informe" value="{{ isset($datos) ? json_encode($datos) : '[]' }}">
                            <input type="hidden" name="filtros" value="{{ isset($filtrosAplicados) ? json_encode($filtrosAplicados) : '{}' }}">
                            <input type="hidden" name="tipo_informe" value="{{ $tipoInforme ?? 'general' }}">
                            <input type="text" name="nombre_informe" placeholder="Nombre del informe" required class="px-4 py-2 border border-gray-300 rounded-md">
                            <button type="submit" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Guardar
                            </button>
                        </form>
                    </div>
                    <div class="flex space-x-2">
                        <!-- Formulario de exportación a Excel -->
                        <form id="exportFormExcel" method="POST" action="{{ route('admin.informes-avanzados.exportar') }}" class="inline">
                            @csrf
                            <input type="hidden" name="formato" value="excel">
                            <input type="hidden" name="tipo" value="{{ $tipoInforme ?? 'general' }}">
                            <input type="hidden" name="datos" id="exportDatosExcel" value="{{ isset($datos) ? json_encode($datos) : '' }}">
                            <input type="hidden" name="id" value="{{ $informeCargado->id ?? '' }}">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Excel
                            </button>
                        </form>
                        
                        <!-- Formulario de exportación a PDF -->
                        <form id="exportFormPDF" method="POST" action="{{ route('admin.informes-avanzados.exportar') }}" class="inline">
                            @csrf
                            <input type="hidden" name="formato" value="pdf">
                            <input type="hidden" name="tipo" value="{{ $tipoInforme ?? 'general' }}">
                            <input type="hidden" name="datos" id="exportDatosPDF" value="{{ isset($datos) ? json_encode($datos) : '' }}">
                            <input type="hidden" name="id" value="{{ $informeCargado->id ?? '' }}">
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                PDF
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Vista de Tabla -->
                <div x-show="activeTab === 'tabla'" class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    @if($tipoInforme === 'usuarios_datos')
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Género</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institución</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($datos as $usuario)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ is_array($usuario) ? ($usuario['name'] ?? 'N/A') : ($usuario->name ?? 'N/A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ is_array($usuario) ? ($usuario['email'] ?? 'N/A') : ($usuario->email ?? 'N/A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ is_array($usuario) ? ($usuario['phone'] ?? 'No especificado') : ($usuario->phone ?? 'No especificado') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @php
                                                    $sexo = is_array($usuario) ? ($usuario['sexo'] ?? '') : ($usuario->sexo ?? '');
                                                @endphp
                                                @if(strtolower($sexo) == 'm') Masculino
                                                @elseif(strtolower($sexo) == 'f') Femenino
                                                @else No especificado
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ is_array($usuario) ? ($usuario['departamento'] ?? 'No especificado') : ($usuario->departamento ?? 'No especificado') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ is_array($usuario) ? ($usuario['ciudad'] ?? 'No especificado') : ($usuario->ciudad ?? 'No especificado') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ is_array($usuario) ? ($usuario['unidad_educativa'] ?? 'No especificado') : ($usuario->unidad_educativa ?? 'No especificado') }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($tipoInforme === 'instituciones_educativas')
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institución</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Estudiantes</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests Completados</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests Incompletos</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($datos as $institucion)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $institucion->nombre ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $institucion->departamento ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $institucion->ciudad ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $institucion->total_estudiantes ?? 0 }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $institucion->tests_completados ?? 0 }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $institucion->tests_incompletos ?? 0 }}</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($tipoInforme === 'distribucion_demografica')
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Usuarios</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($datos as $region)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $region->departamento ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $region->ciudad ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $region->total ?? 0 }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ ($region->porcentaje ?? 0) }}%</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($tipoInforme === 'tests_completados')
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if(is_array($datos) && isset($datos['completados']))
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-green-700">Completados</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $datos['completados'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $datos['porcentaje_completados'] }}%</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-red-700">Incompletos</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $datos['incompletos'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $datos['porcentaje_incompletos'] }}%</div>
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                            No hay datos disponibles para mostrar
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @elseif($tipoInforme === 'personalidades')
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($datos as $tipo)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $tipo->tipo_primario ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $tipo->descripcion ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $tipo->total ?? 0 }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ ($tipo->porcentaje ?? 0) }}%</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($tipoInforme === 'carreras')
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carrera</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recomendaciones</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match Promedio</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($datos as $carrera)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $carrera->nombre ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $carrera->area_conocimiento ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $carrera->total ?? 0 }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ ($carrera->porcentaje ?? 0) }}%</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ ($carrera->match_promedio ?? 0) }}%</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            El tipo de informe seleccionado no está disponible.
                        </div>
                    @endif
                </div>
                
                <!-- Vista de Gráfico -->
                <div x-show="activeTab === 'grafico'" class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="chart-container" style="position: relative; height:400px; width:100%">
                        <canvas id="chartInforme"></canvas>
                    </div>
                </div>
                
                <!-- Sección de insights y análisis -->
                <div class="mt-8 bg-indigo-50 rounded-lg p-6 border border-indigo-100">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-3">Insights y Recomendaciones</h3>
                    <ul class="space-y-3">
                        @if(isset($insights) && is_array($insights))
                            @foreach($insights as $key => $insight)
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @if(is_array($insight) || is_object($insight))
                                        <span class="text-sm text-gray-700">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                            @if(is_countable($insight))
                                                {{ count($insight) }} elementos
                                            @else
                                                Datos complejos
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-700">{!! $insight !!}</span>
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-700">No hay insights disponibles para este informe.</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Scripts para gráficos y funcionalidad -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para validar y preparar datos antes del envío
    function prepararExportacion(form) {
        const datosInput = form.querySelector('input[name="datos"]');
        const idInput = form.querySelector('input[name="id"]');
        const formatoInput = form.querySelector('input[name="formato"]');
        
        console.log('=== PREPARANDO EXPORTACIÓN ===');
        console.log('Formato:', formatoInput.value);
        console.log('ID del informe:', idInput.value);
        console.log('Datos raw:', datosInput.value);
        
        // Si no hay ID y no hay datos, mostrar error
        if (!idInput.value && (!datosInput.value || datosInput.value === '' || datosInput.value === '[]')) {
            alert('No hay datos para exportar. Genere un informe primero.');
            return false;
        }
        
        // Si hay datos, intentar parsearlos para validar
        if (datosInput.value && datosInput.value !== '' && datosInput.value !== '[]') {
            try {
                const datosParsed = JSON.parse(datosInput.value);
                console.log('Datos parseados correctamente:', datosParsed.length, 'registros');
                
                if (!Array.isArray(datosParsed) || datosParsed.length === 0) {
                    alert('Los datos del informe están vacíos.');
                    return false;
                }
            } catch (e) {
                console.error('Error al parsear datos JSON:', e);
                alert('Error en los datos del informe. Intente regenerar el informe.');
                return false;
            }
        }
        
        return true;
    }
    
    // Agregar event listeners a los formularios de exportación
    const exportFormExcel = document.getElementById('exportFormExcel');
    const exportFormPDF = document.getElementById('exportFormPDF');
    
    if (exportFormExcel) {
        exportFormExcel.addEventListener('submit', function(e) {
            if (!prepararExportacion(this)) {
                e.preventDefault();
                return false;
            }
            console.log('Enviando formulario Excel...');
        });
    }
    
    if (exportFormPDF) {
        exportFormPDF.addEventListener('submit', function(e) {
            if (!prepararExportacion(this)) {
                e.preventDefault();
                return false;
            }
            console.log('Enviando formulario PDF...');
        });
    }
    
    // Configuración dinámica de filtros según el tipo de informe
    const tipoInformeSelect = document.getElementById('tipo_informe');
    const filtrosPersonalidad = document.getElementById('filtros_tipo_personalidad');
    
    if(tipoInformeSelect) {
        tipoInformeSelect.addEventListener('change', function() {
            if(this.value === 'personalidades' || this.value === 'carreras') {
                filtrosPersonalidad.style.display = 'grid';
            } else {
                filtrosPersonalidad.style.display = 'none';
            }
        });
        
        // Trigger change event on load
        tipoInformeSelect.dispatchEvent(new Event('change'));
    }
    
    // Creación de gráficos
    if(document.getElementById('chartInforme')) {
        const ctx = document.getElementById('chartInforme').getContext('2d');
        const tipoInforme = @json($tipoInforme ?? '');
        const datosGrafico = @json($datosGrafico ?? null);
        
        if(datosGrafico && datosGrafico.labels && datosGrafico.datos) {
            let config = {
                type: 'bar',
                data: {
                    labels: datosGrafico.labels,
                    datasets: [{
                        label: datosGrafico.titulo || 'Datos',
                        data: datosGrafico.datos,
                        backgroundColor: datosGrafico.colores || [
                            'rgba(99, 102, 241, 0.5)',
                            'rgba(16, 185, 129, 0.5)',
                            'rgba(244, 114, 182, 0.5)',
                            'rgba(251, 146, 60, 0.5)',
                            'rgba(147, 51, 234, 0.5)',
                            'rgba(37, 99, 235, 0.5)',
                        ],
                        borderColor: datosGrafico.bordes || [
                            'rgb(99, 102, 241)',
                            'rgb(16, 185, 129)',
                            'rgb(244, 114, 182)',
                            'rgb(251, 146, 60)',
                            'rgb(147, 51, 234)',
                            'rgb(37, 99, 235)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: datosGrafico.titulo || 'Gráfico del Informe'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw;
                                    
                                    if (datosGrafico.porcentajes && datosGrafico.porcentajes[context.dataIndex]) {
                                        label += ` (${datosGrafico.porcentajes[context.dataIndex]}%)`;
                                    }
                                    
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
            
            // Personalización según tipo de informe
            if(tipoInforme === 'distribucion_demografica' || tipoInforme === 'instituciones_educativas') {
                config.type = 'bar';
            } else if(tipoInforme === 'personalidades' || tipoInforme === 'tests_completados') {
                config.type = 'pie';
                config.options.scales = {}; // Quitar escalas para gráfico de pie
            } else if(tipoInforme === 'carreras') {
                config.type = 'bar';
            }
            
            new Chart(ctx, config);
        }
    }
});
</script>
@endpush
@endsection