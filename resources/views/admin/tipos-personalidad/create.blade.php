
@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-gray-900 mb-8">Nuevo Tipo de Personalidad RIASEC</h1>
    <form action="{{ route('admin.tipos-personalidad.store') }}" method="POST" class="bg-white shadow rounded-lg p-8">
        @csrf
        <div class="mb-5">
            <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código*</label>
            <input type="text" name="codigo" id="codigo" maxlength="1" required value="{{ old('codigo') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm">
            @error('codigo')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-5">
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre*</label>
            <input type="text" name="nombre" id="nombre" required value="{{ old('nombre') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm">
            @error('nombre')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-5">
            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="2"
                      class="w-full rounded-md border-gray-300 shadow-sm">{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-5">
            <label for="caracteristicas" class="block text-sm font-medium text-gray-700 mb-1">Características</label>
            <textarea name="caracteristicas" id="caracteristicas" rows="2"
                      class="w-full rounded-md border-gray-300 shadow-sm">{{ old('caracteristicas') }}</textarea>
            @error('caracteristicas')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-5">
            <label for="color_hex" class="block text-sm font-medium text-gray-700 mb-1">Color (HEX)*</label>
            <input type="color" name="color_hex" id="color_hex" value="{{ old('color_hex', '#cccccc') }}"
                   class="w-16 h-10 p-0 border-0">
            @error('color_hex')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                Guardar
            </button>
        </div>
    </form>
</div>
@endsection