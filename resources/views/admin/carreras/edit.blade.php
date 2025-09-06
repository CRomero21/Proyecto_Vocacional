
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.carreras.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Editar Carrera: {{ $carrera->nombre }}</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.carreras.update', $carrera) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre de la carrera -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Carrera*</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $carrera->nombre) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Área de conocimiento -->
                <div>
                    <label for="area_conocimiento" class="block text-sm font-medium text-gray-700 mb-1">Área de Conocimiento*</label>
                    <div class="flex">
                        <select name="area_conocimiento" id="area_conocimiento"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                onchange="document.getElementById('nueva_area').value='';" >
                            <option value="">Seleccionar área...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ (old('area_conocimiento', $carrera->area_conocimiento) == $area) ? 'selected' : '' }}>
                                    {{ $area }}
                                </option>
                            @endforeach
                        </select>
                        <span class="mx-2 text-gray-400 self-center">o</span>
                        <input type="text" name="nueva_area" id="nueva_area" placeholder="Nueva área"
                               value="{{ old('nueva_area') }}"
                               class="w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                               oninput="if(this.value.length){document.getElementById('area_conocimiento').selectedIndex=0;}">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Selecciona un área existente o escribe una nueva.</p>
                    @error('area_conocimiento')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('nueva_area')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción*</label>
                    <textarea name="descripcion" id="descripcion" rows="3" required
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('descripcion', $carrera->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duración -->
                <div>
                    <label for="duracion" class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                    <input type="text" name="duracion" id="duracion" value="{{ old('duracion', $carrera->duracion) }}"
                           placeholder="Ej: 5 años, 10 semestres"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    @error('duracion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Imagen -->
                <div>
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">Imagen de la Carrera</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*"
                           class="w-full text-gray-700 px-3 py-2 border rounded-md">
                    @if($carrera->imagen)
                        <div class="mt-2 flex items-center">
                            <img src="{{ Storage::url($carrera->imagen) }}" alt="{{ $carrera->nombre }}" class="h-16 w-auto object-cover rounded">
                            <p class="ml-2 text-sm text-gray-600">Imagen actual</p>
                        </div>
                    @endif
                    <p class="text-gray-500 text-xs mt-1">Imagen representativa (opcional, max: 2MB)</p>
                    @error('imagen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Perfil de ingreso -->
                <div>
                    <label for="perfil_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Perfil de Ingreso</label>
                    <textarea name="perfil_ingreso" id="perfil_ingreso" rows="2"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('perfil_ingreso', $carrera->perfil_ingreso) }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">¿Qué características debe tener el estudiante que ingresa?</p>
                    @error('perfil_ingreso')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Perfil de egreso -->
                <div>
                    <label for="perfil_egreso" class="block text-sm font-medium text-gray-700 mb-1">Perfil de Egreso</label>
                    <textarea name="perfil_egreso" id="perfil_egreso" rows="2"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('perfil_egreso', $carrera->perfil_egreso) }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">¿Qué competencias tendrá el egresado?</p>
                    @error('perfil_egreso')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Es institucional -->
                <div class="flex items-center mt-4">
                    <input type="checkbox" name="es_institucional" id="es_institucional" value="1" 
                           {{ old('es_institucional', $carrera->es_institucional) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <label for="es_institucional" class="ml-2 block text-sm text-gray-700">
                        Carrera institucional (priorizada en resultados)
                    </label>
                </div>
            </div>

            <!-- SECCIÓN MEJORADA: Perfiles RIASEC existentes -->
            <div class="mt-6 border-t border-gray-200 pt-4">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Perfiles RIASEC de la Carrera</h3>
                        <p class="text-sm text-gray-500">Gestiona las combinaciones de tipos de personalidad asociadas.</p>
                    </div>
                    <button type="button" onclick="agregarCombinacionRIASEC()" 
                            class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Agregar Combinación
                    </button>
                </div>
                
                <!-- Contenedor para combinaciones RIASEC -->
                <div id="riasec-container">
                    @forelse($carrera->carreraTipos as $index => $tipo)
                        <div class="riasec-combination mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-800">Combinación #{{ $index + 1 }}</h4>
                                <button type="button" onclick="eliminarCombinacion(this)" class="text-red-500 hover:text-red-700" 
                                        {{ $carrera->carreraTipos->count() <= 1 ? 'style=display:none;' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="combinaciones[{{ $index }}][id]" value="{{ $tipo->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Primario*</label>
                                    <select name="combinaciones[{{ $index }}][tipo_primario]" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Seleccionar tipo...</option>
                                        @foreach($tiposRIASEC as $codigo => $nombre)
                                            <option value="{{ $codigo }}" {{ $tipo->tipo_primario == $codigo ? 'selected' : '' }}>
                                                {{ $codigo }} - {{ $nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Secundario</label>
                                    <select name="combinaciones[{{ $index }}][tipo_secundario]"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Ninguno (opcional)</option>
                                        @foreach($tiposRIASEC as $codigo => $nombre)
                                            <option value="{{ $codigo }}" {{ $tipo->tipo_secundario == $codigo ? 'selected' : '' }}>
                                                {{ $codigo }} - {{ $nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Terciario</label>
                                    <select name="combinaciones[{{ $index }}][tipo_terciario]"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Ninguno (opcional)</option>
                                        @foreach($tiposRIASEC as $codigo => $nombre)
                                            <option value="{{ $codigo }}" {{ $tipo->tipo_terciario == $codigo ? 'selected' : '' }}>
                                                {{ $codigo }} - {{ $nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Si no hay combinaciones, mostrar una vacía -->
                        <div class="riasec-combination mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-800">Combinación #1</h4>
                                <button type="button" onclick="eliminarCombinacion(this)" class="text-red-500 hover:text-red-700" style="display:none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Primario*</label>
                                    <select name="combinaciones[0][tipo_primario]" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Seleccionar tipo...</option>
                                        @foreach($tiposRIASEC as $codigo => $nombre)
                                            <option value="{{ $codigo }}">{{ $codigo }} - {{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Secundario</label>
                                    <select name="combinaciones[0][tipo_secundario]"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Ninguno (opcional)</option>
                                        @foreach($tiposRIASEC as $codigo => $nombre)
                                            <option value="{{ $codigo }}">{{ $codigo }} - {{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Terciario</label>
                                    <select name="combinaciones[0][tipo_terciario]"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                        <option value="">Ninguno (opcional)</option>
                                        @foreach($tiposRIASEC as $codigo => $nombre)
                                            <option value="{{ $codigo }}">{{ $codigo }} - {{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('admin.carreras.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Carrera
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let combinacionCount = {{ $carrera->carreraTipos->count() > 0 ? $carrera->carreraTipos->count() : 1 }};
    
    function agregarCombinacionRIASEC() {
        combinacionCount++;
        
        const container = document.getElementById('riasec-container');
        const template = `
            <div class="riasec-combination mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium text-gray-800">Combinación #${combinacionCount}</h4>
                    <button type="button" onclick="eliminarCombinacion(this)" class="text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Primario*</label>
                        <select name="combinaciones[${combinacionCount-1}][tipo_primario]" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Seleccionar tipo...</option>
                            @foreach($tiposRIASEC as $codigo => $nombre)
                                <option value="{{ $codigo }}">{{ $codigo }} - {{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Secundario</label>
                        <select name="combinaciones[${combinacionCount-1}][tipo_secundario]"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Ninguno (opcional)</option>
                            @foreach($tiposRIASEC as $codigo => $nombre)
                                <option value="{{ $codigo }}">{{ $codigo }} - {{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Terciario</label>
                        <select name="combinaciones[${combinacionCount-1}][tipo_terciario]"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Ninguno (opcional)</option>
                            @foreach($tiposRIASEC as $codigo => $nombre)
                                <option value="{{ $codigo }}">{{ $codigo }} - {{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        `;
        
        // Añadir nueva combinación al contenedor
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template;
        container.appendChild(wrapper.firstElementChild);
        
        // Mostrar botones de eliminar si hay más de una combinación
        if (combinacionCount > 1) {
            document.querySelectorAll('.riasec-combination button').forEach(btn => {
                btn.style.display = 'block';
            });
        }
    }
    
    function eliminarCombinacion(button) {
        const combination = button.closest('.riasec-combination');
        combination.remove();
        
        // Renumerar las combinaciones
        document.querySelectorAll('.riasec-combination').forEach((el, index) => {
            el.querySelector('h4').textContent = `Combinación #${index + 1}`;
            
            // Actualizar los índices de los campos
            el.querySelectorAll('select').forEach(select => {
                const name = select.name;
                const fieldType = name.substring(name.lastIndexOf('[') + 1, name.lastIndexOf(']'));
                select.name = `combinaciones[${index}][${fieldType}]`;
            });
            
            // Actualizar el campo id si existe
            const idField = el.querySelector('input[name$="[id]"]');
            if (idField) {
                idField.name = `combinaciones[${index}][id]`;
            }
        });
        
        combinacionCount--;
        
        // Ocultar los botones de eliminar si solo queda una combinación
        if (combinacionCount === 1) {
            document.querySelectorAll('.riasec-combination button').forEach(btn => {
                btn.style.display = 'none';
            });
        }
    }
</script>
@endsection