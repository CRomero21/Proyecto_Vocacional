
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-gray-900 mb-8">Editar Asignación Carrera-Universidad</h1>

    <div class="bg-white shadow rounded-lg p-8">
        <form action="{{ route('admin.carrera-universidad.update', $carreraUniversidad->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Carrera*</label>
                <input type="text" value="{{ $carreraUniversidad->carrera->nombre }}" disabled
                       class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Universidad*</label>
                <input type="text" value="{{ $carreraUniversidad->universidad->nombre }}" disabled
                       class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
            </div>

            <div class="mb-5">
                <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1">Modalidad*</label>
                <select name="modalidad" id="modalidad" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">Seleccionar modalidad...</option>
                    <option value="Presencial" {{ old('modalidad', $carreraUniversidad->modalidad) == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                    <option value="Virtual" {{ old('modalidad', $carreraUniversidad->modalidad) == 'Virtual' ? 'selected' : '' }}>Virtual</option>
                    <option value="Mixta" {{ old('modalidad', $carreraUniversidad->modalidad) == 'Mixta' ? 'selected' : '' }}>Mixta</option>
                </select>
                @error('modalidad')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="duracion" class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                <input type="text" name="duracion" id="duracion" value="{{ old('duracion', $carreraUniversidad->duracion) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                       placeholder="Ej: 5 años, 10 semestres">
                @error('duracion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="costo_semestre" class="block text-sm font-medium text-gray-700 mb-1">Costo por Semestre</label>
                <input type="number" step="0.01" name="costo_semestre" id="costo_semestre" value="{{ old('costo_semestre', $carreraUniversidad->costo_semestre) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                       placeholder="Ej: 1500.00">
                @error('costo_semestre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="requisitos" class="block text-sm font-medium text-gray-700 mb-1">Requisitos</label>
                <textarea name="requisitos" id="requisitos" rows="2"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                          placeholder="Ej: Título de bachiller, examen de admisión, etc.">{{ old('requisitos', $carreraUniversidad->requisitos) }}</textarea>
                @error('requisitos')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5 flex items-center">
                <input type="checkbox" name="disponible" id="disponible" value="1" {{ old('disponible', $carreraUniversidad->disponible) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                <label for="disponible" class="ml-2 block text-sm text-gray-700">
                    Disponible
                </label>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection