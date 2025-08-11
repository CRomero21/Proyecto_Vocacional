
@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6">
    <!-- Cabecera de la página -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-white">Editar Usuario</h1>
                    <p class="text-purple-100 mt-1">Modifica los datos del usuario y asigna permisos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Formulario de edición -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                            <div class="font-medium">Hay errores en el formulario:</div>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <!-- Información básica -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Información básica</h3>
                                
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror" placeholder="Nombre del usuario" required>
                                        </div>
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md @error('email') border-red-300 @enderror" placeholder="ejemplo@correo.com" required>
                                        </div>
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Permisos y roles -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Permisos y acceso</h3>
                                
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="role" class="block text-sm font-medium text-gray-700 mb-3">Rol del usuario</label>
                                        <div class="space-y-3">
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="role-estudiante" name="role" value="estudiante" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('role', $usuario->role) == 'estudiante' ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="role-estudiante" class="font-medium text-gray-700">Estudiante</label>
                                                    <p class="text-gray-500">Acceso limitado para realizar tests y ver resultados personales.</p>
                                                </div>
                                            </div>
                                            
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="role-admin" name="role" value="admin" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('role', $usuario->role) == 'admin' ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="role-admin" class="font-medium text-gray-700">Administrador</label>
                                                    <p class="text-gray-500">Acceso a la gestión de informes y análisis de resultados.</p>
                                                </div>
                                            </div>
                                            
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="role-superadmin" name="role" value="superadmin" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('role', $usuario->role) == 'superadmin' ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="role-superadmin" class="font-medium text-gray-700">Superadministrador</label>
                                                    <p class="text-gray-500">Acceso completo a todas las funciones del sistema.</p>
                                                </div>
                                            </div>
                                        </div>
                                        @error('role')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Última actividad</label>
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-gray-700">Usuario creado el:</p>
                                                    <p class="text-sm font-medium text-gray-900">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Cambio de contraseña (opcional) -->
                            <div x-data="{ showPasswordFields: false }">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Cambiar contraseña</h3>
                                    <button type="button" @click="showPasswordFields = !showPasswordFields" class="text-sm text-indigo-600 hover:text-indigo-900">
                                        <span x-text="showPasswordFields ? 'Cancelar' : 'Cambiar'"></span>
                                    </button>
                                </div>
                                
                                <div x-show="showPasswordFields" class="space-y-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </div>
                                            <input type="password" name="password" id="password" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md @error('password') border-red-300 @enderror" placeholder="Nueva contraseña">
                                        </div>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </div>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Repetir contraseña">
                                        </div>
                                    </div>
                                    
                                    <div class="bg-yellow-50 rounded-md p-4 border border-yellow-200">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    Si no deseas cambiar la contraseña del usuario, deja estos campos en blanco.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 pt-5 border-t border-gray-200 flex items-center justify-between">
                            <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Volver
                            </a>
                            <button type="submit" class="inline-flex items-center px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Panel lateral de información -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Perfil de usuario</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-center mb-6">
                        <div class="h-24 w-24 rounded-full bg-gradient-to-r from-purple-400 to-indigo-500 flex items-center justify-center text-white text-3xl font-bold">
                            {{ strtoupper(substr($usuario->name, 0, 1)) }}
                        </div>
                    </div>
                    
                    <div class="text-center mb-6">
                        <h4 class="text-lg font-medium text-gray-900">{{ $usuario->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $usuario->email }}</p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-gray-900 mb-2">Información de rol actual</h5>
                            <div class="flex items-center mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $usuario->role == 'superadmin' ? 'bg-purple-100 text-purple-800' : 
                                    ($usuario->role == 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') 
                                }}">
                                    {{ 
                                        $usuario->role == 'superadmin' ? 'Superadministrador' : 
                                        ($usuario->role == 'admin' ? 'Administrador' : 'Estudiante') 
                                    }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ 
                                    $usuario->role == 'superadmin' ? 'Acceso completo a todas las funciones del sistema, incluida la gestión de usuarios y configuración.' : 
                                    ($usuario->role == 'admin' ? 'Puede gestionar informes y ver estadísticas, pero no tiene acceso a la configuración del sistema.' : 'Solo puede realizar tests y ver sus propios resultados.') 
                                }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Acciones adicionales</h5>
                        @if(auth()->id() !== $usuario->id)
                            <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full mt-2 flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar usuario
                                </button>
                            </form>
                        @else
                            <div class="bg-blue-50 p-3 rounded-md text-sm text-blue-700">
                                <p>No puedes eliminar tu propio usuario.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection