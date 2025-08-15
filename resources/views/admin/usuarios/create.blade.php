
@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6">
    <!-- Cabecera de la página -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-white">Crear Nuevo Usuario</h1>
                    <p class="text-blue-100 mt-1">Ingrese los datos del nuevo usuario</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de creación -->
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

            <form method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf
                
                <div class="space-y-8">
                    <!-- Información personal -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Información personal</h3>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror" placeholder="Nombre del usuario" required>
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
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md @error('email') border-red-300 @enderror" placeholder="ejemplo@correo.com" required>
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ubicación -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Ubicación</h3>
                        
                        <div>
                            <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <select name="departamento" id="departamento" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md @error('departamento') border-red-300 @enderror" required>
                                    <option value="" disabled selected>Seleccione un departamento</option>
                                    <option value="La Paz" {{ old('departamento') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                                    <option value="Santa Cruz" {{ old('departamento') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                                    <option value="Cochabamba" {{ old('departamento') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                                    <option value="Chuquisaca" {{ old('departamento') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca (Sucre)</option>
                                    <option value="Oruro" {{ old('departamento') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                                    <option value="Potosí" {{ old('departamento') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                                    <option value="Tarija" {{ old('departamento') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                                    <option value="Beni" {{ old('departamento') == 'Beni' ? 'selected' : '' }}>Beni</option>
                                    <option value="Pando" {{ old('departamento') == 'Pando' ? 'selected' : '' }}>Pando</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('departamento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Acceso al sistema -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Acceso al sistema</h3>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input type="password" name="password" id="password" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md @error('password') border-red-300 @enderror" placeholder="Contraseña segura" required>
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
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Repetir contraseña" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Seguridad de contraseña -->
                        <div class="mt-4">
                            <div class="bg-blue-50 rounded-md p-4 border border-blue-200">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            La contraseña debe tener al menos 8 caracteres y combinar letras, números y símbolos.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Permisos y roles -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Permisos y acceso</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Rol del usuario</label>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="relative border rounded-lg p-4 flex cursor-pointer focus:outline-none @error('role') border-red-300 @enderror">
                                    <div class="flex items-center h-5">
                                        <input id="role-estudiante" name="role" value="estudiante" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('role') == 'estudiante' ? 'checked' : '' }} checked>
                                    </div>
                                    <div class="ml-3 flex flex-col">
                                        <label for="role-estudiante" class="text-sm font-medium text-gray-900">Estudiante</label>
                                        <span class="text-xs text-gray-500">Acceso a tests y resultados personales</span>
                                    </div>
                                </div>
                                
                                <div class="relative border rounded-lg p-4 flex cursor-pointer focus:outline-none">
                                    <div class="flex items-center h-5">
                                        <input id="role-coordinador" name="role" value="coordinador" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('role') == 'coordinador' ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 flex flex-col">
                                        <label for="role-coordinador" class="text-sm font-medium text-gray-900">Coordinador</label>
                                        <span class="text-xs text-gray-500">Gestión de informes y reportes</span>
                                    </div>
                                </div>
                                
                                <div class="relative border rounded-lg p-4 flex cursor-pointer focus:outline-none">
                                    <div class="flex items-center h-5">
                                        <input id="role-superadmin" name="role" value="superadmin" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('role') == 'superadmin' ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 flex flex-col">
                                        <label for="role-superadmin" class="text-sm font-medium text-gray-900">Superadmin</label>
                                        <span class="text-xs text-gray-500">Acceso completo al sistema</span>
                                    </div>
                                </div>
                            </div>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="pt-5 border-t border-gray-200 flex items-center justify-between">
                        <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Crear Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection