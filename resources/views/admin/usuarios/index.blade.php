
@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{ 
    showDeleteModal: false, 
    userToDelete: null,
    searchTerm: '{{ $query ?? '' }}',
    showFilters: false,
    roleFilter: 'todos'
}">
    <!-- Cabecera de la página -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Gestión de Usuarios</h1>
                    <p class="text-blue-100 mt-1">Administra los usuarios y sus permisos en el sistema</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('admin.usuarios.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tarjetas de estadísticas -->
        <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-gray-500 uppercase">Total Usuarios</div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $usuarios->count() }}</div>
                        <div class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Activos</div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-gray-500 uppercase">Estudiantes</div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $usuarios->where('role', 'estudiante')->count() }}</div>
                        <div class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Usuarios</div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-gray-500 uppercase">Administradores</div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $usuarios->where('role', 'admin')->count() }}</div>
                        <div class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Staff</div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-gray-500 uppercase">Super Admins</div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $usuarios->where('role', 'superadmin')->count() }}</div>
                        <div class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Privilegiados</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notificación de éxito -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md flex items-center shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    <!-- Panel de búsqueda y filtros -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex items-center">
                        <div class="relative rounded-md shadow-sm flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="buscar" value="{{ $query ?? '' }}" x-model="searchTerm" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Buscar por nombre o email">
                        </div>
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Buscar
                        </button>
                        @if($query ?? false)
                            <a href="{{ route('admin.usuarios.index') }}" class="ml-2 text-sm text-indigo-600 hover:text-indigo-900">Limpiar</a>
                        @endif
                    </form>
                </div>
                
                <div class="flex items-center">
                    <button @click="showFilters = !showFilters" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtros
                        <svg x-show="!showFilters" xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="showFilters" xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" style="display: none;">
                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Filtros expandibles -->
            <div x-show="showFilters" class="mt-4 pt-4 border-t border-gray-200" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="roleFilter" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por rol</label>
                        <select x-model="roleFilter" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="todos">Todos los roles</option>
                            <option value="user">Estudiantes</option>
                            <option value="admin">Administradores</option>
                            <option value="superadmin">Super Administradores</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="sortOrder" class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                        <select id="sortOrder" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="name_asc">Nombre (A-Z)</option>
                            <option value="name_desc">Nombre (Z-A)</option>
                            <option value="email_asc">Email (A-Z)</option>
                            <option value="email_desc">Email (Z-A)</option>
                            <option value="created_at_desc">Más recientes primero</option>
                            <option value="created_at_asc">Más antiguos primero</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Aplicar filtros
                        </button>
                        <button type="button" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabla de usuarios -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rol
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Registro
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" x-data="{
                    get filteredUsers() {
                        return Array.from(this.$el.children).filter(row => {
                            const role = row.querySelector('[data-role]')?.dataset.role;
                            const text = row.textContent.toLowerCase();
                            
                            const matchesSearch = searchTerm === '' || text.includes(searchTerm.toLowerCase());
                            const matchesRole = roleFilter === 'todos' || role === roleFilter;
                            
                            return matchesSearch && matchesRole;
                        });
                    },
                    updateVisibility() {
                        Array.from(this.$el.children).forEach(row => {
                            row.style.display = this.filteredUsers.includes(row) ? '' : 'none';
                        });
                    }
                }" x-init="$watch('searchTerm', () => updateVisibility()); $watch('roleFilter', () => updateVisibility())">
                    @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $usuario->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $usuario->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $usuario->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-role="{{ $usuario->role }}">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($usuario->role === 'superadmin') bg-yellow-100 text-yellow-800
                                @elseif($usuario->role === 'admin') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800 @endif">
                                @if($usuario->role === 'superadmin') Super Admin
                                @elseif($usuario->role === 'admin') Administrador
                                @else Estudiante @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $usuario->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </a>
                                @if(auth()->id() != $usuario->id)
                                <button @click="userToDelete = {{ $usuario->id }}; showDeleteModal = true" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>No se encontraron usuarios que coincidan con tu búsqueda.</p>
                                @if($query ?? false)
                                    <a href="{{ route('admin.usuarios.index') }}" class="mt-2 text-indigo-600 hover:text-indigo-900">Ver todos los usuarios</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($usuarios->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>
    
    <!-- Modal de confirmación para eliminar -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Eliminar usuario
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer y todos sus datos, incluyendo informes y resultados de tests, serán eliminados permanentemente.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        x-show="userToDelete"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                        @click="document.getElementById('delete-form-' + userToDelete).submit()">
                        Eliminar
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showDeleteModal = false">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Formularios ocultos para eliminar -->
    @foreach($usuarios as $usuario)
        <form id="delete-form-{{ $usuario->id }}" action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
@endsection