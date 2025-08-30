@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-gray-900 mb-8">Detalle del Tipo de Personalidad RIASEC</h1>
    <div class="bg-white shadow rounded-lg p-8">
        <div class="mb-4">
            <span class="font-bold">Código:</span> {{ $tipoPersonalidad->codigo }}
        </div>
        <div class="mb-4">
            <span class="font-bold">Nombre:</span> {{ $tipoPersonalidad->nombre }}
        </div>
        <div class="mb-4">
            <span class="font-bold">Color:</span>
            <span class="inline-block w-6 h-6 rounded-full align-middle" style="background: {{ $tipoPersonalidad->color_hex }}"></span>
            <span class="ml-2 text-xs">{{ $tipoPersonalidad->color_hex }}</span>
        </div>
        <div class="mb-4">
            <span class="font-bold">Descripción:</span>
            <div class="text-gray-700">{{ $tipoPersonalidad->descripcion }}</div>
        </div>
        <div class="mb-4">
            <span class="font-bold">Características:</span>
            <div class="text-gray-700">{{ $tipoPersonalidad->caracteristicas }}</div>
        </div>
        <div class="mt-8 flex justify-between">
            <a href="{{ route('admin.tipos-personalidad.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-bold">Volver</a>
            <a href="{{ route('admin.tipos-personalidad.edit', ['tipos_personalidad' => $tipoPersonalidad->id]) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded font-bold">Editar</a>
        </div>
    </div>
</div>
@endsection