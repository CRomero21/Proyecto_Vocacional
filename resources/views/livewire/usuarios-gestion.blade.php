
<div>
    <h1 class="text-2xl font-bold text-blue-900 mb-4">Gestionar Usuarios</h1>
    <p class="text-gray-700 mb-6">Administra los usuarios y sus permisos en el sistema.</p>
    <div class="mb-6 flex flex-wrap gap-2 items-center justify-center">
        <input type="text" wire:model.debounce.500ms="buscar" placeholder="Buscar por nombre o email"
            class="border border-blue-300 rounded px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-400" />
    </div>
    <table class="w-full bg-white rounded shadow border border-gray-300 mb-6">
        <thead>
            <tr class="bg-blue-100 border-b border-gray-300">
                <th class="py-2 px-4">Nombre</th>
                <th class="py-2 px-4">Email</th>
                <th class="py-2 px-4">Rol</th>
                <th class="py-2 px-4">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr class="border-b">
                <td class="py-2 px-4 font-semibold">{{ $usuario->name }}</td>
                <td class="py-2 px-4">{{ $usuario->email }}</td>
                <td class="py-2 px-4 capitalize">
                    <span class="px-2 py-1 rounded 
                        {{ $usuario->role === 'superadmin' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $usuario->role === 'superadmin' ? 'Superadmin' : 'Estudiante' }}
                    </span>
                </td>
                <td class="py-2 px-4 flex gap-2 justify-center">
                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">Editar</a>
                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>