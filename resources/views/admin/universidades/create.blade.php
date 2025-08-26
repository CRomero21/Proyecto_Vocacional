
@extends('layouts.app')

@section('title', 'Crear Universidad')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-2">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6">
            <h1 class="text-3xl font-bold text-white">Registrar Universidad</h1>
            <p class="text-yellow-100 mt-1">Completa los datos para agregar una nueva institución</p>
        </div>
        <form action="{{ route('admin.universidades.store') }}" method="POST" class="p-8 space-y-7" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">
                    <p class="font-semibold">Corrige los siguientes errores:</p>
                    <ul class="list-disc pl-5 mt-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-600">*</span></label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('nombre') border-red-300 @enderror" placeholder="Ej: UNO YACUIBA">
                    @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">Departamento <span class="text-red-600">*</span></label>
                    <select name="departamento" id="departamento" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('departamento') border-red-300 @enderror">
                        <option value="">Selecciona un departamento</option>
                        <option value="La Paz" {{ old('departamento') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                        <option value="Cochabamba" {{ old('departamento') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                        <option value="Santa Cruz" {{ old('departamento') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                        <option value="Oruro" {{ old('departamento') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                        <option value="Potosí" {{ old('departamento') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                        <option value="Chuquisaca" {{ old('departamento') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                        <option value="Tarija" {{ old('departamento') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                        <option value="Beni" {{ old('departamento') == 'Beni' ? 'selected' : '' }}>Beni</option>
                        <option value="Pando" {{ old('departamento') == 'Pando' ? 'selected' : '' }}>Pando</option>
                    </select>
                    @error('departamento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio <span class="text-red-600">*</span></label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <input type="text" name="municipio" id="municipio" value="{{ old('municipio') }}" required class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('municipio') border-red-300 @enderror" placeholder="Ingrese el municipio">
                    </div>
                    @error('municipio')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección <span class="text-red-600">*</span></label>
                    <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('direccion') border-red-300 @enderror" placeholder="Dirección completa">
                    @error('direccion')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-600">*</span></label>
                    <select name="tipo" id="tipo" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('tipo') border-red-300 @enderror">
                        <option value="">Selecciona un tipo</option>
                        <option value="Pública" {{ old('tipo') == 'Pública' ? 'selected' : '' }}>Pública</option>
                        <option value="Privada" {{ old('tipo') == 'Privada' ? 'selected' : '' }}>Privada</option>
                    </select>
                    @error('tipo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </span>
                        <input type="tel" name="telefono" id="telefono" value="{{ old('telefono') }}" class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('telefono') border-red-300 @enderror" placeholder="Ej: +591 12345678">
                    </div>
                    @error('telefono')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label for="sitio_web" class="block text-sm font-medium text-gray-700 mb-1">Sitio Web</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                            </svg>
                        </span>
                        <input type="url" name="sitio_web" id="sitio_web" value="{{ old('sitio_web') }}" class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 @error('sitio_web') border-red-300 @enderror" placeholder="https://www.ejemplo.edu">
                    </div>
                    @error('sitio_web')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" id="logo" class="w-full border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-yellow-200 focus:border-yellow-500 @error('logo') border-red-300 @enderror" accept="image/*">
                    <p class="mt-1 text-sm text-gray-500">Formatos: JPG, PNG, GIF. Máx: 2MB</p>
                    @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="flex items-center mt-2">
                        <input type="checkbox" name="acreditada" id="acreditada" value="1" {{ old('acreditada') ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Universidad Acreditada</span>
                    </label>
                    @error('acreditada')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-8">
                <a href="{{ route('admin.universidades.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-300">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 shadow transition-colors duration-300 font-semibold">Guardar Universidad</button>
            </div>
        </form>
    </div>
</div>
@endsection