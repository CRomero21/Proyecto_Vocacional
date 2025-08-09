
@extends('layouts.app')

@section('title', 'Gestión de Preguntas')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{ 
    showDeleteModal: false, 
    questionToDelete: null,
    searchTerm: '',
    activeFilter: 'todos',
    applyFilters() {
        this.filterQuestions();
    },
    filterQuestions() {
        const rows = document.querySelectorAll('#questions-table tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const tipo = row.querySelector('td:nth-child(3) span').textContent.trim();
            
            const matchesSearch = this.searchTerm === '' || text.includes(this.searchTerm.toLowerCase());
            const matchesFilter = this.activeFilter === 'todos' || tipo === this.activeFilter;
            
            row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
        });
    }
}">
    <!-- Encabezado de la página -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Gestión de Preguntas RIASEC</h1>
                    <p class="text-blue-100 mt-1">Administra las preguntas del test vocacional</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('admin.preguntas.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nueva Pregunta
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tarjetas de estadísticas -->
        <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-gray-500 uppercase">Total</div>
                    <div class="mt-1 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $preguntas->count() }}</div>
                        <div class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Preguntas</div>
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-red-500 uppercase">R</div>
                    <div class="mt-1 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $preguntas->where('tipo', 'R')->count() }}</div>
                        <div class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Realista</div>
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-yellow-500 uppercase">I</div>
                    <div class="mt-1 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $preguntas->where('tipo', 'I')->count() }}</div>
                        <div class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Investigador</div>
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-green-500 uppercase">A</div>
                    <div class="mt-1 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $preguntas->where('tipo', 'A')->count() }}</div>
                        <div class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Artístico</div>
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-blue-500 uppercase">S</div>
                    <div class="mt-1 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $preguntas->where('tipo', 'S')->count() }}</div>
                        <div class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Social</div>
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs font-medium text-purple-500 uppercase">E/C</div>
                    <div class="mt-1 flex items-baseline justify-between">
                        <div class="text-2xl font-semibold text-gray-900">{{ $preguntas->whereIn('tipo', ['E', 'C'])->count() }}</div>
                        <div class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Emp/Conv</div>
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
    
    <!-- Panel de filtrado y búsqueda -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="searchTerm" @input="applyFilters()" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Buscar pregunta...">
                    </div>
                </div>
                
                <div class="flex-1">
                    <label for="filter" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por tipo</label>
                    <select x-model="activeFilter" @change="applyFilters()" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="todos">Todos los tipos</option>
                        <option value="R">R - Realista</option>
                        <option value="I">I - Investigador</option>
                        <option value="A">A - Artístico</option>
                        <option value="S">S - Social</option>
                        <option value="E">E - Emprendedor</option>
                        <option value="C">C - Convencional</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabla de preguntas -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="questions-table">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pregunta</th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($preguntas as $pregunta)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $pregunta->id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $pregunta->texto }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($pregunta->tipo == 'R') bg-red-100 text-red-800
                                @elseif($pregunta->tipo == 'I') bg-yellow-100 text-yellow-800
                                @elseif($pregunta->tipo == 'A') bg-green-100 text-green-800
                                @elseif($pregunta->tipo == 'S') bg-blue-100 text-blue-800
                                @elseif($pregunta->tipo == 'E') bg-purple-100 text-purple-800
                                @elseif($pregunta->tipo == 'C') bg-gray-100 text-gray-800
                                @endif">
                                {{ $pregunta->tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.preguntas.edit', $pregunta) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </a>
                                <button @click="questionToDelete = {{ $pregunta->id }}; showDeleteModal = true" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
                                Eliminar pregunta
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que deseas eliminar esta pregunta? Esta acción no se puede deshacer.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        x-show="questionToDelete"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                        @click="document.getElementById('delete-form-' + questionToDelete).submit()">
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
    @foreach($preguntas as $pregunta)
        <form id="delete-form-{{ $pregunta->id }}" action="{{ route('admin.preguntas.destroy', $pregunta) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
@endsection