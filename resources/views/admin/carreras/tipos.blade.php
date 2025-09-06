
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestionar Combinaciones RIASEC para {{ $carrera->nombre }}</h1>
        <a href="{{ route('admin.carreras.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow transition">
            Volver a Carreras
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Combinaciones Existentes -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Combinaciones RIASEC Actuales</h2>
        
        @if($carrera->carreraTipos->isEmpty())
            <p class="text-gray-500">No hay combinaciones RIASEC asignadas a esta carrera.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($carrera->carreraTipos as $tipo)
                    <div class="border rounded-lg p-4 bg-gray-50 relative">
                        <div class="absolute top-2 right-2">
                            <form action="{{ route('admin.carreras.tipos.destroy', [$carrera, $tipo]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" 
                                        onclick="return confirm('¿Eliminar esta combinación?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mb-2">
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
                        
                        <div class="text-sm text-gray-600">
                            <p><strong>Primario:</strong> {{ $tipo->tipo_primario ?? 'No asignado' }}</p>
                            <p><strong>Secundario:</strong> {{ $tipo->tipo_secundario ?? 'No asignado' }}</p>
                            <p><strong>Terciario:</strong> {{ $tipo->tipo_terciario ?? 'No asignado' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Formulario para Agregar Nueva Combinación -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Agregar Nueva Combinación RIASEC</h2>
        
        <form action="{{ route('admin.carreras.tipos.store', $carrera) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Primario (Principal)</label>
                    <select name="tipo_primario" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                        <option value="">Seleccionar...</option>
                        @foreach(['R', 'I', 'A', 'S', 'E', 'C'] as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }} - {{ $tiposDescripcion[$tipo] }}</option>
                        @endforeach
                    </select>
                    @error('tipo_primario')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Secundario</label>
                    <select name="tipo_secundario" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                        <option value="">Seleccionar...</option>
                        @foreach(['R', 'I', 'A', 'S', 'E', 'C'] as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }} - {{ $tiposDescripcion[$tipo] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Terciario</label>
                    <select name="tipo_terciario" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                        <option value="">Seleccionar...</option>
                        @foreach(['R', 'I', 'A', 'S', 'E', 'C'] as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }} - {{ $tiposDescripcion[$tipo] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
                    Agregar Combinación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection