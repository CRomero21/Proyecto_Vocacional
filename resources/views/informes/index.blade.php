
@extends('layouts.app')

@section('title', 'Panel Superadministrador')

@section('content')
<div class="bg-gradient-to-br from-blue-900 to-indigo-900 min-h-screen" x-data="panelApp">
    <div class="flex flex-col md:flex-row">
        <!-- Panel lateral con efecto de vidrio -->
        <div class="w-full md:w-64 bg-white/10 backdrop-blur-xl md:min-h-screen p-5 text-white border-r border-white/10">
            <div class="flex items-center justify-between md:justify-start mb-8">
                <h2 class="flex items-center text-xl font-bold">
                    <span class="mr-3 bg-blue-500 p-2 rounded-lg shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Panel Admin
                </h2>
                <button @click="menuOpen = !menuOpen" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!menuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        <path x-show="menuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Menú lateral con animaciones -->
            <nav class="mt-6" x-bind:class="{'hidden': !menuOpen, 'block': menuOpen}" x-data="{menuOpen: true}">
                <div class="space-y-1.5">
                    <a href="#" @click.prevent="opcion = 'informes'" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out" 
                       :class="opcion === 'informes' ? 'bg-blue-600 shadow-md' : 'hover:bg-white/10'">
                        <span class="mr-3 text-white/80">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </span>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.estadisticas.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                        <span class="mr-3 text-white/80">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </span>
                        <span class="font-medium">Estadísticas</span>
                    </a>

                    <a href="{{ route('admin.informes-avanzados.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                    <span class="mr-3 text-white/80">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </span>
                    <span class="font-medium">Informes Avanzados</span>
                </a>    
                </div>
                
                <div class="mt-8">
                    <h3 class="text-xs uppercase text-white/50 font-semibold px-4 mb-3">Administración</h3>
                    <div class="space-y-1.5">
                        <!-- Gestión de Usuarios -->
                        <a href="{{ route('admin.usuarios.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                            <span class="mr-3 text-white/80">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </span>
                            <span class="font-medium">Usuarios</span>
                        </a>

                        <!-- Gestión de Preguntas -->
                        <a href="{{ route('admin.preguntas.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                            <span class="mr-3 text-white/80">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <span class="font-medium">Preguntas</span>
                        </a>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="text-xs uppercase text-white/50 font-semibold px-4 mb-3">Carreras y Universidades</h3>
                    <div class="space-y-1.5">
                        <!-- Gestión de Carreras -->
                        <a href="{{ route('admin.carreras.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                            <span class="mr-3 text-white/80">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </span>
                            <span class="font-medium">Carreras</span>
                        </a>

                        <!-- Gestión de Universidades -->
                        <a href="{{ route('admin.universidades.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                            <span class="mr-3 text-white/80">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </span>
                            <span class="font-medium">Universidades</span>
                        </a>

                        <!-- Asociar Carreras-Universidades -->
                        <a href="{{ route('admin.carrera-universidad.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                            <span class="mr-3 text-white/80">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </span>
                            <span class="font-medium">Asociar Carreras</span>
                        </a>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="text-xs uppercase text-white/50 font-semibold px-4 mb-3">Perfiles</h3>
                    <div class="space-y-1.5">
                        <!-- Gestión de Tipos de Personalidad -->
                        <a href="{{ route('admin.tipos-personalidad.index') }}" class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 ease-in-out hover:bg-white/10">
                            <span class="mr-3 text-white/80">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </span>
                            <span class="font-medium">Tipos de Personalidad</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Contenido principal -->
        <div class="flex-1 p-4 md:p-8 overflow-y-auto">
            <!-- Breadcrumbs -->
            <div class="mb-6 flex items-center text-sm text-white/70">
                <span>Dashboard</span>
                <svg class="w-3 h-3 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white">Resumen</span>
            </div>

            <!-- Panel de informes -->
            <div class="mx-auto max-w-6xl transform transition-all duration-300 ease-in-out fade-in">
                
                <!-- Tarjeta de bienvenida -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-xl overflow-hidden mb-8">
                    <div class="relative p-8">
                        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 rounded-full bg-white/10"></div>
                        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-60 h-60 rounded-full bg-white/5"></div>
                        
                        <div class="relative">
                            <h1 class="text-3xl font-bold text-white mb-2">Panel de Administración</h1>
                            <p class="text-blue-100 mb-6 max-w-2xl">Administra todos los aspectos del sistema de orientación vocacional desde este panel centralizado.</p>
                            
                            <div class="inline-flex space-x-3">
                                <a href="{{ route('admin.estadisticas.index') }}" class="bg-white text-blue-600 px-5 py-2.5 rounded-lg font-medium shadow-md hover:bg-blue-50 transition">
                                    Ver Estadísticas
                                </a>
                                <a href="{{ route('admin.informes-avanzados.index') }}" class="bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-md hover:bg-indigo-800 transition">
                                    Informes Avanzados
                                </a>
                                <a href="{{ route('admin.usuarios.index') }}" class="bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-md hover:bg-blue-800 transition">
                                    Administrar Usuarios
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen de recursos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Tarjeta de usuarios -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                        <div class="px-6 pt-6">
                            <div class="flex items-center">
                                <div class="bg-blue-500/10 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-bold text-gray-800">Usuarios</h3>
                                    <p class="text-gray-500">Gestiona cuentas y permisos</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pt-4 pb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-blue-600">{{ $totalUsuarios }}</span>
                                <a href="{{ route('admin.usuarios.index') }}" class="text-blue-500 group-hover:text-blue-700 hover:underline font-medium flex items-center">
                                    <span>Administrar</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de carreras -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                        <div class="px-6 pt-6">
                            <div class="flex items-center">
                                <div class="bg-green-500/10 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-bold text-gray-800">Carreras</h3>
                                    <p class="text-gray-500">Administra opciones académicas</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pt-4 pb-6">
                            <div class="flex items-center justify-between">
                                @if(isset($totalCarreras))
                                <span class="text-2xl font-bold text-green-600">{{ $totalCarreras }}</span>
                                @else
                                <span class="text-2xl font-bold text-green-600">--</span>
                                @endif
                                <a href="{{ route('admin.carreras.index') }}" class="text-green-500 group-hover:text-green-700 hover:underline font-medium flex items-center">
                                    <span>Administrar</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de universidades -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                        <div class="px-6 pt-6">
                            <div class="flex items-center">
                                <div class="bg-purple-500/10 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-bold text-gray-800">Universidades</h3>
                                    <p class="text-gray-500">Gestiona instituciones educativas</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pt-4 pb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-purple-600">{{ count($universidadesConCarreras) }}</span>
                                <a href="{{ route('admin.universidades.index') }}" class="text-purple-500 group-hover:text-purple-700 hover:underline font-medium flex items-center">
                                    <span>Administrar</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de tests -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                        <div class="px-6 pt-6">
                            <div class="flex items-center">
                                <div class="bg-amber-500/10 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-bold text-gray-800">Tests</h3>
                                    <p class="text-gray-500">Tests vocacionales completados</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pt-4 pb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-amber-600">{{ $totalTests }}</span>
                                <a href="{{ route('admin.estadisticas.index') }}" class="text-amber-500 group-hover:text-amber-700 hover:underline font-medium flex items-center">
                                    <span>Ver Detalles</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen de actividad -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800">Actividad Reciente</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center p-4 bg-blue-50 rounded-lg mb-4">
                                <div class="bg-blue-100 rounded-full p-2 mr-4">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Tests completados</h4>
                                    <p class="text-sm text-gray-600">Se han completado {{ $testsUltimaSemana }} tests en la última semana</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-4 bg-green-50 rounded-lg">
                                <div class="bg-green-100 rounded-full p-2 mr-4">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Tasa de finalización</h4>
                                    <p class="text-sm text-gray-600">El {{ $tasaConversion }}% de los tests iniciados son completados</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800">Acciones Rápidas</h3>
                        </div>
                        <div class="p-6">
                            <a href="{{ route('admin.tipos-personalidad.index') }}" class="block mb-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <h4 class="font-medium text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Gestionar Tipos de Personalidad
                                </h4>
                            </a>
                            
                            <a href="{{ route('admin.preguntas.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <h4 class="font-medium text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Administrar Preguntas
                                </h4>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('panelApp', () => ({
            opcion: 'informes',
            menuOpen: true
        }))
    })
</script>

<style>
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection