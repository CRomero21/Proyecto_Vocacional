
@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Editar Usuario</h1>
    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="max-w-lg mx-auto bg-white p-8 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $usuario->name) }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Rol</label>
            <select name="role" class="w-full border rounded px-3 py-2" required>
                <option value="estudiante" @if($usuario->role == 'estudiante') selected @endif>Estudiante</option>
                <option value="superadmin" @if($usuario->role == 'superadmin') selected @endif>Superadministrador</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded">Guardar cambios</button>
        <a href="{{ route('admin.usuarios.index') }}" class="ml-4 text-blue-700 hover:underline">Cancelar</a>
    </form>
</div>
@endsection