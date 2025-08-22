
@extends('layouts.app')

@section('title', 'Editar Universidad')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Encabezado con degradado -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6">
            <h1 class="text-2xl font-bold text-white">Editar Universidad</h1>
            <p class="text-yellow-100 mt-1">Actualiza los datos de <span class="font-semibold">{{ $universidad->nombre }}</span></p>
        </div>

        <form action="{{ route('admin.universidades.update', $universidad) }}" method="POST" class="p-6 space-y-6" enctype="multipart/form-data">
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

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-600">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $universidad->nombre) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('nombre') border-red-300 @enderror">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Departamento -->
            <div>
                <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">Departamento <span class="text-red-600">*</span></label>
                <select name="departamento" id="departamento" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('departamento') border-red-300 @enderror">
                    <option value="">Selecciona un departamento</option>
                    @foreach(['La Paz','Cochabamba','Santa Cruz','Oruro','Potosí','Chuquisaca','Tarija','Beni','Pando'] as $dep)
                        <option value="{{ $dep }}" {{ old('departamento', $universidad->departamento) == $dep ? 'selected' : '' }}>{{ $dep }}</option>
                    @endforeach
                </select>
                @error('departamento')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Municipio -->
            <div>
                <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio <span class="text-red-600">*</span></label>
                <input type="text" name="municipio" id="municipio" value="{{ old('municipio', $universidad->municipio) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('municipio') border-red-300 @enderror"
                    placeholder="Municipio">
                @error('municipio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dirección -->
            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección <span class="text-red-600">*</span></label>
                <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $universidad->direccion) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('direccion') border-red-300 @enderror"
                    placeholder="Dirección completa">
                @error('direccion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo -->
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

            <!-- Teléfono -->
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" value="{{ old('telefono', $universidad->telefono) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('telefono') border-red-300 @enderror"
                    placeholder="Ej: +591 12345678">
                @error('telefono')
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

            <!-- Logo -->
            <div>
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                <input type="file" name="logo" id="logo"
                    class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-yellow-200 focus:border-yellow-500 @error('logo') border-red-300 @enderror"
                    accept="image/*">
                @if($universidad->logo)
                    <div class="mt-2">
                        <img src="{{ Storage::url($universidad->logo) }}" alt="Logo actual" class="h-16 rounded shadow border">
                        <p class="text-xs text-gray-500 mt-1">Logo actual</p>
                    </div>
                @endif
                <p class="mt-1 text-sm text-gray-500">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
                @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Acreditada -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="acreditada" id="acreditada" value="1" {{ old('acreditada', $universidad->acreditada) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Universidad Acreditada</span>
                </label>
                @error('acreditada')
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