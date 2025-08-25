
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-gray-900 mb-8">Tipos de Personalidad RIASEC</h1>
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.tipos-personalidad.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
            + Nuevo Tipo
        </a>
    </div>
    <div class="bg-white shadow rounded-lg p-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Código</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Color</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($tiposPersonalidad as $tipo)
                    <tr>
                        <td class="px-4 py-2 font-bold">{{ $tipo->codigo }}</td>
                        <td class="px-4 py-2">{{ $tipo->nombre }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block w-6 h-6 rounded-full" style="background: {{ $tipo->color_hex }}"></span>
                            <span class="ml-2 text-xs">{{ $tipo->color_hex }}</span>
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('admin.tipos-personalidad.show', ['tipos_personalidad' => $tipo->id]) }}" ...>Ver</a>
                            <a href="{{ route('admin.tipos-personalidad.edit', ['tipos_personalidad' => $tipo->id]) }}" ...>Editar</a>
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('admin.tipos-personalidad.show', ['tipos_personalidad' => $tipo->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold">Ver</a>
                            <a href="{{ route('admin.tipos-personalidad.edit', ['tipos_personalidad' => $tipo->id]) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs font-bold">Editar</a>
                            <form action="{{ route('admin.tipos-personalidad.destroy', ['tipos_personalidad' => $tipo->id]) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este tipo?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection