
@extends('layouts.app')

@section('title', 'Editar Universidad')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Encabezado con degradado -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6">
            <h1 class="text-2xl font-bold text-white">Editar Universidad</h1>
            <p class="text-yellow-100 mt-1">Actualiza los datos de {{ $universidad->nombre }}</p>
        </div>

        <form action="{{ route('admin.universidades.update', $universidad) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Mensaje de error general -->
            @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-md">
                <p class="font-semibold">Por favor corrija los siguientes errores:</p>
                <ul class="list-disc pl-5 mt-2 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Nombre de la universidad -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Universidad <span class="text-red-600">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $universidad->nombre) }}" required 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('nombre') border-red-300 @enderror">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo de universidad -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-600">*</span></label>
                <select name="tipo" id="tipo" required 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('tipo') border-red-300 @enderror">
                    <option value="">Selecciona un tipo</option>
                    <option value="Pública" {{ old('tipo', $universidad->tipo) == 'Pública' ? 'selected' : '' }}>Pública</option>
                    <option value="Privada" {{ old('tipo', $universidad->tipo) == 'Privada' ? 'selected' : '' }}>Privada</option>
                </select>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Ubicación <span class="text-red-600">*</span></label>
                <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $universidad->ubicacion) }}" required 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('ubicacion') border-red-300 @enderror"
                    placeholder="Ciudad, Departamento">
                @error('ubicacion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sitio web -->
            <div>
                <label for="sitio_web" class="block text-sm font-medium text-gray-700 mb-1">Sitio Web</label>
                <input type="url" name="sitio_web" id="sitio_web" value="{{ old('sitio_web', $universidad->sitio_web) }}" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('sitio_web') border-red-300 @enderror"
                    placeholder="https://www.ejemplo.edu">
                @error('sitio_web')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('descripcion') border-red-300 @enderror"
                    placeholder="Breve descripción de la universidad...">{{ old('descripcion', $universidad->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.universidades.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors duration-300">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors duration-300">
                    Actualizar Universidad
                </button>
            </div>
        </form>
    </div>
</div>
@endsection