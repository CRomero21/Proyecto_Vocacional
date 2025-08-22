
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-8">
        <a href="{{ route('admin.carreras.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver
        </a>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Editar Carrera</h1>
    </div>

    <div class="bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg rounded-2xl p-10 border border-blue-100">
        <form action="{{ route('admin.carreras.update', $carrera) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="flex flex-col md:flex-row gap-10">
                <div class="flex-1 space-y-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Carrera*</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $carrera->nombre) }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="area_conocimiento" class="block text-sm font-medium text-gray-700 mb-1">Área de Conocimiento*</label>
                        <div class="flex">
                            <select name="area_conocimiento" id="area_conocimiento"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                    onchange="document.getElementById('nueva_area').value='';">
                                <option value="">Seleccionar área...</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area }}" {{ (old('area_conocimiento', $carrera->area_conocimiento) == $area) ? 'selected' : '' }}>
                                        {{ $area }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="mx-2 text-gray-400 self-center">o</span>
                            <input type="text" name="nueva_area" id="nueva_area" placeholder="Nueva área"
                                   value="{{ old('nueva_area') }}"
                                   class="w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                   oninput="if(this.value.length){document.getElementById('area_conocimiento').selectedIndex=0;}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Selecciona un área existente o escribe una nueva.</p>
                        @error('area_conocimiento')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('nueva_area')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción*</label>
                        <textarea name="descripcion" id="descripcion" rows="4"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>{{ old('descripcion', $carrera->descripcion) }}</textarea>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duracion" class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                        <input type="text" name="duracion" id="duracion" value="{{ old('duracion', $carrera->duracion) }}"
                               placeholder="Ej: 5 años, 10 semestres"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        @error('duracion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="perfil_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Perfil de Ingreso</label>
                        <textarea name="perfil_ingreso" id="perfil_ingreso" rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('perfil_ingreso', $carrera->perfil_ingreso) }}</textarea>
                        <p class="text-gray-500 text-xs mt-1">¿Qué características debe tener el estudiante que ingresa?</p>
                        @error('perfil_ingreso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="perfil_egreso" class="block text-sm font-medium text-gray-700 mb-1">Perfil de Egreso</label>
                        <textarea name="perfil_egreso" id="perfil_egreso" rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('perfil_egreso', $carrera->perfil_egreso) }}</textarea>
                        <p class="text-gray-500 text-xs mt-1">¿Qué competencias tendrá el egresado?</p>
                        @error('perfil_egreso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center mt-4">
                        <input type="checkbox" name="es_institucional" id="es_institucional" value="1" {{ old('es_institucional', $carrera->es_institucional) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <label for="es_institucional" class="ml-2 block text-sm text-gray-700">
                            Carrera institucional (priorizada en resultados)
                        </label>
                    </div>
                </div>

                <div class="flex-1 flex flex-col items-center space-y-8">
                    <div class="w-full">
                        <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">Imagen de la Carrera</label>
                        @if($carrera->imagen)
                            <div class="mb-4 flex justify-center">
                                <img src="{{ asset('storage/'.$carrera->imagen) }}" alt="Imagen actual"
                                     class="rounded-xl shadow border border-blue-200 bg-white p-2"
                                     style="max-width: 320px; max-height: 240px; width: 100%; height: auto; object-fit: contain;">
                            </div>
                        @endif
                        <input type="file" name="imagen" id="imagen" accept="image/*"
                               class="w-full text-gray-700 px-3 py-2 border rounded-md">
                        <p class="text-gray-500 text-xs mt-1">Imagen representativa (opcional, max: 2MB)</p>
                        @error('imagen')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full mt-8 border-t border-blue-200 pt-6">
                        <h3 class="text-lg font-medium text-blue-900">Perfil RIASEC de la Carrera</h3>
                        <p class="text-sm text-gray-500 mb-4">Asocia esta carrera con los tipos de personalidad vocacional más relevantes.</p>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="tipo_primario" class="block text-sm font-medium text-gray-700 mb-1">Tipo Primario*</label>
                                <select name="tipo_primario" id="tipo_primario" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tiposPersonalidad as $tipo)
                                        <option value="{{ $tipo->codigo }}" 
                                                style="background-color: {{ $tipo->color_hex }}20" 
                                                {{ old('tipo_primario', $carrera->tipo_primario) == $tipo->codigo ? 'selected' : '' }}>
                                            {{ $tipo->codigo }} - {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_primario')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tipo_secundario" class="block text-sm font-medium text-gray-700 mb-1">Tipo Secundario</label>
                                <select name="tipo_secundario" id="tipo_secundario"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">Ninguno (opcional)</option>
                                    @foreach($tiposPersonalidad as $tipo)
                                        <option value="{{ $tipo->codigo }}"
                                                style="background-color: {{ $tipo->color_hex }}20"
                                                {{ old('tipo_secundario', $carrera->tipo_secundario) == $tipo->codigo ? 'selected' : '' }}>
                                            {{ $tipo->codigo }} - {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="tipo_terciario" class="block text-sm font-medium text-gray-700 mb-1">Tipo Terciario</label>
                                <select name="tipo_terciario" id="tipo_terciario"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">Ninguno (opcional)</option>
                                    @foreach($tiposPersonalidad as $tipo)
                                        <option value="{{ $tipo->codigo }}"
                                                style="background-color: {{ $tipo->color_hex }}20"
                                                {{ old('tipo_terciario', $carrera->tipo_terciario) == $tipo->codigo ? 'selected' : '' }}>
                                            {{ $tipo->codigo }} - {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection