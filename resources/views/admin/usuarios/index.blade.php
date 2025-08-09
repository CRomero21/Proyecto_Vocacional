
@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@section('content')
<div class="flex min-h-screen bg-gradient-to-r from-blue-900 via-blue-500 to-white">
    <!-- Menú lateral -->
    <aside class="w-1/5 bg-blue-900 text-white flex flex-col items-center py-10">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="mb-8 w-32">
        <h2 class="text-lg font-bold mb-8">Menú de administrador</h2>
        <a href="{{ route('informes.index') }}" class="mb-4 px-6 py-2 bg-blue-700 rounded hover:bg-blue-800 transition w-4/5 text-center">Ver Informes</a>
        <a href="{{ route('admin.usuarios.index') }}" class="mb-4 px-6 py-2 bg-blue-700 rounded hover:bg-blue-800 transition w-4/5 text-center">Gestionar Usuarios</a>
        <a href="#" class="mb-4 px-6 py-2 bg-blue-700 rounded hover:bg-blue-800 transition w-4/5 text-center">Estadísticas</a>
        <a href="{{ route('admin.preguntas.index') }}" class="px-6 py-2 bg-green-600 rounded hover:bg-green-700 transition w-4/5 text-center">Gestionar Preguntas</a>
    </aside>

    <!-- Contenido principal -->
    <section class="w-4/5 flex items-center justify-center">
        <div class="w-full max-w-3xl mx-auto bg-white rounded shadow p-8">
            <h1 class="text-2xl font-bold text-blue-900 mb-4">Gestionar Usuarios</h1>
            <p class="text-gray-700 mb-6">Administra los usuarios y sus permisos en el sistema.</p>

            <!-- Barra de búsqueda -->
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="mb-6 flex flex-wrap gap-2 items-center justify-center">
                <input type="text" name="buscar" value="{{ $query ?? '' }}" placeholder="Buscar por nombre o email"
                    class="border border-blue-300 rounded px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 transition">Buscar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="text-blue-700 hover:underline ml-2">Ver todos</a>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded shadow border border-gray-300">
                    <thead>
                        <tr class="bg-blue-100 border-b border-gray-300 text-blue-900">
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Rol</th>
                            <th class="py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr class="hover:bg-blue-50 border-b border-gray-200">
                            <td class="py-2 px-4 font-semibold">{{ $usuario->name }}</td>
                            <td class="py-2 px-4">{{ $usuario->email }}</td>
                            <td class="py-2 px-4 capitalize">
                                <span class="px-2 py-1 rounded 
                                    {{ $usuario->role === 'superadmin' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $usuario->role === 'superadmin' ? 'Superadmin' : 'Estudiante' }}
                                </span>
                            </td>
                            <td class="py-2 px-4 flex gap-2 justify-center">
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                   class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition">Editar</a>
                                <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">No se encontraron usuarios.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection