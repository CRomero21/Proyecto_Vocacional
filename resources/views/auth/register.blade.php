
<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-900 to-indigo-800 relative">
        <!-- Elementos decorativos de fondo -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-20">
            <div class="absolute top-10 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
            <div class="absolute top-0 right-10 w-72 h-72 bg-yellow-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
        </div>

        <div class="w-full sm:max-w-2xl mt-6 px-6 py-6 bg-white/95 backdrop-blur-sm shadow-2xl overflow-hidden sm:rounded-xl border border-white/20 relative z-10">
            <div class="mb-6 text-center">
                <a href="/" class="flex justify-center mb-4">
                    <img src="{{ asset('images/logo_uno_se.png') }}" alt="Logo" class="h-16 transition-transform duration-300 hover:scale-105">
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Crea tu cuenta</h2>
                <p class="text-sm text-gray-600 mt-1">Completa el formulario para comenzar tu orientación vocacional</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ 
                step: 1, 
                showPassword: false, 
                showPasswordConfirm: false,
                loading: false,
                validarPaso1() {
                    let isValid = true;
                    
                    // Validar nombre (solo letras y espacios)
                    if(!document.getElementById('name').value.trim() || 
                    !/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+$/.test(document.getElementById('name').value)) {
                        document.getElementById('name').classList.add('border-red-500');
                        isValid = false;
                        
                        // Encontrar o crear contenedor de error
                        let errorContainer = document.getElementById('name').parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            document.getElementById('name').parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'El nombre solo debe contener letras y espacios';
                        
                    } else {
                        document.getElementById('name').classList.remove('border-red-500');
                        const errorContainer = document.getElementById('name').parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }
                    
                    // Validar edad (solo números y rango)
                    if(!document.getElementById('edad').value || 
                       !/^[0-9]+$/.test(document.getElementById('edad').value) ||
                       parseInt(document.getElementById('edad').value) < 5 || 
                       parseInt(document.getElementById('edad').value) > 120) {
                        document.getElementById('edad').classList.add('border-red-500');
                        isValid = false;
                        
                        // Encontrar o crear contenedor de error
                        let errorContainer = document.getElementById('edad').parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            document.getElementById('edad').parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'La edad debe ser un número entre 5 y 120 años';
                        
                    } else {
                        document.getElementById('edad').classList.remove('border-red-500');
                        const errorContainer = document.getElementById('edad').parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }
                    
                    // Validar sexo (debe estar seleccionado)
                    if(!document.getElementById('sexo').value) {
                        document.getElementById('sexo').classList.add('border-red-500');
                        isValid = false;
                        
                        // Encontrar o crear contenedor de error
                        let errorContainer = document.getElementById('sexo').parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            document.getElementById('sexo').parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'Por favor seleccione su sexo';
                        
                    } else {
                        document.getElementById('sexo').classList.remove('border-red-500');
                        const errorContainer = document.getElementById('sexo').parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }
                    
                    return isValid;
                },
                validarPaso2() {
                    let isValid = true;
                    
                    // Validar departamento (debe estar seleccionado)
                    if(!document.getElementById('departamento').value) {
                        document.getElementById('departamento').classList.add('border-red-500');
                        isValid = false;
                        
                        // Encontrar o crear contenedor de error
                        let errorContainer = document.getElementById('departamento').parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            document.getElementById('departamento').parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'Por favor seleccione un departamento';
                        
                    } else {
                        document.getElementById('departamento').classList.remove('border-red-500');
                        const errorContainer = document.getElementById('departamento').parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }
                    
                    // Validar teléfono (solo números y longitud)
                    if(!document.getElementById('phone').value || 
                       !/^[0-9]+$/.test(document.getElementById('phone').value) ||
                       document.getElementById('phone').value.length < 7 || 
                       document.getElementById('phone').value.length > 8) {
                        document.getElementById('phone').classList.add('border-red-500');
                        isValid = false;
                        
                        // Encontrar o crear contenedor de error
                        let errorContainer = document.getElementById('phone').parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            document.getElementById('phone').parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'El teléfono debe tener entre 7 y 8 dígitos numéricos';
                        
                    } else {
                        document.getElementById('phone').classList.remove('border-red-500');
                        const errorContainer = document.getElementById('phone').parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }
                    
                    // Validar unidad educativa (letras y números)
                    if(!document.getElementById('unidad_educativa').value || 
                       !/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9 ]+$/.test(document.getElementById('unidad_educativa').value)) {
                        document.getElementById('unidad_educativa').classList.add('border-red-500');
                        isValid = false;
                        
                        // Encontrar o crear contenedor de error
                        let errorContainer = document.getElementById('unidad_educativa').parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            document.getElementById('unidad_educativa').parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'Por favor ingrese el nombre de su unidad educativa (letras y números permitidos)';
                        
                    } else {
                        document.getElementById('unidad_educativa').classList.remove('border-red-500');
                        const errorContainer = document.getElementById('unidad_educativa').parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }
                    
                    return isValid;
                }
            }">
                @csrf

                <!-- Indicador de pasos -->
                <div class="flex items-center justify-between px-2 mb-4">
                    <div class="w-full flex items-center">
                        <div class="relative flex items-center justify-center">
                            <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold z-10">1</div>
                            <div x-show="step > 1" class="absolute inset-0 rounded-full bg-indigo-600 z-0 scale-110 transition-transform duration-300 shadow-md"></div>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-2" :class="step >= 2 ? 'bg-indigo-600' : ''"></div>
                    </div>
                    <div class="w-full flex items-center">
                        <div class="relative flex items-center justify-center">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center font-semibold z-10" :class="step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">2</div>
                            <div x-show="step > 2" class="absolute inset-0 rounded-full bg-indigo-600 z-0 scale-110 transition-transform duration-300 shadow-md"></div>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-2" :class="step >= 3 ? 'bg-indigo-600' : ''"></div>
                    </div>
                    <div class="w-full flex items-center">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center font-semibold" :class="step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">3</div>
                    </div>
                </div>

                <!-- Paso 1: Información personal -->
                <div x-show="step === 1" class="space-y-5">
                    <div class="text-sm font-medium text-indigo-600 mb-4">
                        Paso 1: Información personal
                    </div>

                    <!-- Nombre -->
                    <div>
                        <x-input-label for="name" :value="__('Nombre y apellido')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <x-text-input id="name" class="pl-10 block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ingresa tu nombre y apellido" oninput="this.value=this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü ]/g,'')" />                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Edad -->
                        <div>
                            <x-input-label for="edad" :value="__('Edad')" />
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <x-text-input id="edad" class="pl-10 block mt-1 w-full" type="number" name="edad" :value="old('edad')" required min="5" max="120" placeholder="Ej: 18" oninput="this.value=this.value.replace(/[^0-9]/g,'')" />
                            </div>
                            <x-input-error :messages="$errors->get('edad')" class="mt-2" />
                        </div>
                        
                        <!-- Sexo -->
                        <div>
                            <x-input-label for="sexo" :value="__('Sexo')" />
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <select id="sexo" name="sexo" class="pl-10 block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino" {{ old('sexo') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('sexo') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('sexo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('sexo')" class="mt-2" />
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" 
                            @click="if(validarPaso1()) step = 2" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Siguiente
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Paso 2: Ubicación y contacto -->
                <div x-show="step === 2" class="space-y-5" style="display: none;">
                    <div class="text-sm font-medium text-indigo-600 mb-4">
                        Paso 2: Ubicación y contacto
                    </div>

                    <!-- Departamento -->
                    <div>
                        <x-input-label for="departamento" :value="__('Departamento')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <select id="departamento" name="departamento" class="pl-10 block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Seleccione un departamento</option>
                                <option value="Chuquisaca" {{ old('departamento') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                                <option value="La Paz" {{ old('departamento') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                                <option value="Cochabamba" {{ old('departamento') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                                <option value="Oruro" {{ old('departamento') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                                <option value="Potosí" {{ old('departamento') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                                <option value="Tarija" {{ old('departamento') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                                <option value="Santa Cruz" {{ old('departamento') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                                <option value="Beni" {{ old('departamento') == 'Beni' ? 'selected' : '' }}>Beni</option>
                                <option value="Pando" {{ old('departamento') == 'Pando' ? 'selected' : '' }}>Pando</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('departamento')" class="mt-2" />
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <x-input-label for="phone" :value="__('Teléfono')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <x-text-input id="phone" class="pl-10 block mt-1 w-full" type="text" name="phone" :value="old('phone')" required placeholder="Ej: 77123456" oninput="this.value=this.value.replace(/[^0-9]/g,'')" maxlength="8" />
                        </div>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Unidad educativa -->
                    <div>
                        <x-input-label for="unidad_educativa" :value="__('Unidad Educativa')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <x-text-input id="unidad_educativa" class="pl-10 block mt-1 w-full" type="text" name="unidad_educativa" :value="old('unidad_educativa')" required placeholder="Nombre de tu colegio o institución" />
                        </div>
                        <x-input-error :messages="$errors->get('unidad_educativa')" class="mt-2" />
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 1" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Anterior
                        </button>
                        <button type="button" 
                            @click="if(validarPaso2()) step = 3" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Siguiente
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Paso 3: Cuenta y contraseña -->
                <div x-show="step === 3" class="space-y-5" style="display: none;">
                    <div class="text-sm font-medium text-indigo-600 mb-4">
                        Paso 3: Datos de acceso
                    </div>

                    <!-- Correo -->
                    <div>
                        <x-input-label for="email" :value="__('Correo electrónico')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <x-text-input id="email" class="pl-10 block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="tu.correo@gmail.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Solo se aceptan correos de Gmail, Hotmail o Yahoo</p>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <x-input-label for="password" :value="__('Contraseña')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-text-input 
                                id="password" 
                                class="pl-10 pr-10 block mt-1 w-full"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                required 
                                autocomplete="new-password"
                                placeholder="Mínimo 8 caracteres" 
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                                    <svg 
                                        x-show="!showPassword" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        class="h-5 w-5" 
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg 
                                        x-show="showPassword" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        class="h-5 w-5" 
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor"
                                        style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirme Contraseña')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-text-input 
                                id="password_confirmation" 
                                class="pl-10 pr-10 block mt-1 w-full"
                                x-bind:type="showPasswordConfirm ? 'text' : 'password'"
                                name="password_confirmation"
                                required 
                                autocomplete="new-password"
                                placeholder="Repite tu contraseña" 
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button 
                                    type="button" 
                                    @click="showPasswordConfirm = !showPasswordConfirm" 
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                                    <svg 
                                        x-show="!showPasswordConfirm" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        class="h-5 w-5" 
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg 
                                        x-show="showPasswordConfirm" 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        class="h-5 w-5" 
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor"
                                        style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="mt-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" required class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-medium text-gray-700">Acepto los <a href="#" class="text-indigo-600 hover:text-indigo-500">términos y condiciones</a></label>
                                <p class="text-gray-500">Al registrarte, aceptas nuestras políticas de privacidad y uso de datos.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 2" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Anterior
                        </button>
                        <x-primary-button 
                            class="ml-4"
                            @click="loading = true"
                            x-bind:class="{ 'opacity-90 cursor-not-allowed': loading }">
                            <span x-show="!loading">{{ __('Registrar') }}</span>
                            <span x-show="loading" class="flex items-center" style="display: none;">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </x-primary-button>
                    </div>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes una cuenta? 
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Footer simple -->
        <div class="mt-8 text-center text-sm text-white/70">
            <p>&copy; {{ date('Y') }} Orientación Vocacional. Todos los derechos reservados.</p>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</x-guest-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de validación cargado');
    
    // Obtener referencias al formulario y componente Alpine
    const form = document.querySelector('form');
    if (!form) return;
    
    // Función para acceder a datos de Alpine
    function getAlpineData() {
        if (typeof Alpine !== 'undefined') {
            return Alpine.$data(form);
        }
        return null;
    }
    
    // Expresiones regulares para validaciones
    const regexPatterns = {
        onlyLetters: /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+$/,
        onlyNumbers: /^[0-9]+$/,
        email: /^[^\s@]+@(gmail|hotmail|yahoo)\.com$/i
    };
    
    // Obtener referencias a los campos del formulario
    const nameInput = document.getElementById('name');
    const edadInput = document.getElementById('edad');
    const sexoSelect = document.getElementById('sexo');
    const departamentoSelect = document.getElementById('departamento');
    const phoneInput = document.getElementById('phone');
    const unidadEducativaInput = document.getElementById('unidad_educativa');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    // Función para mostrar mensajes de error
    function showError(element, message) {
        // Buscar el contenedor de error (normalmente sigue al input)
        let errorContainer = element.parentNode.parentNode.querySelector('.mt-2');
        
        // Si no existe un contenedor de error, crear uno nuevo
        if (!errorContainer) {
            errorContainer = document.createElement('p');
            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
            element.parentNode.parentNode.appendChild(errorContainer);
        }
        
        // Actualizar el mensaje de error
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
        
        // Resaltar el campo con error
        element.classList.add('border-red-500');
        
        return errorContainer;
    }
    
    // Función para limpiar errores
    function clearError(element) {
        const errorContainer = element.parentNode.parentNode.querySelector('.mt-2');
        if (errorContainer) {
            errorContainer.textContent = '';
            errorContainer.style.display = 'none';
        }
        element.classList.remove('border-red-500');
    }
    
    // Validadores específicos para cada campo
    const validators = {
        name: function(value) {
            if (value.trim().length < 3) {
                return 'El nombre debe tener al menos 3 caracteres';
            }
            if (!regexPatterns.onlyLetters.test(value)) {
                return 'El nombre solo debe contener letras';
            }
            return null;
        },
        
        edad: function(value) {
            if (!regexPatterns.onlyNumbers.test(value)) {
                return 'La edad debe contener solo números';
            }
            if (parseInt(value) < 5 || parseInt(value) > 120) {
                return 'La edad debe estar entre 5 y 120 años';
            }
            return null;
        },
        
        sexo: function(value) {
            if (!value) {
                return 'Por favor seleccione su sexo';
            }
            return null;
        },
        
        departamento: function(value) {
            if (!value) {
                return 'Por favor seleccione un departamento';
            }
            return null;
        },
        
        phone: function(value) {
            if (!regexPatterns.onlyNumbers.test(value)) {
                return 'El teléfono debe contener solo números';
            }
            if (value.length < 7 || value.length > 8) {
                return 'El teléfono debe tener entre 7 y 8 dígitos';
            }
            return null;
        },
        
        unidad_educativa: function(value) {
            if (!value.trim()) {
                return 'El nombre de la unidad educativa es requerido';
            }
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9 ]+$/.test(value)) {
                return 'La unidad educativa solo debe contener letras y números';
            }
            return null;
        },
        
        email: function(value) {
            if (!regexPatterns.email.test(value)) {
                return 'Por favor ingrese un correo válido de Gmail, Hotmail o Yahoo';
            }
            return null;
        },
        
        password: function(value) {
            if (value.length < 8) {
                return 'La contraseña debe tener al menos 8 caracteres';
            }
            return null;
        },
        
        password_confirmation: function(value) {
            if (value !== passwordInput.value) {
                return 'Las contraseñas no coinciden';
            }
            return null;
        }
    };
    
    // Función para validar un campo
    function validateField(field, fieldName) {
        if (!field || !validators[fieldName]) return true;
        
        const error = validators[fieldName](field.value);
        if (error) {
            showError(field, error);
            return false;
        }
        
        clearError(field);
        return true;
    }
    
    
    // Validar nombre cuando pierde el foco
    if (nameInput) {
        nameInput.addEventListener('blur', function() {
            validateField(this, 'name');
        });
    }
    
    // Validar edad
    if (edadInput) {
        edadInput.addEventListener('blur', function() {
            validateField(this, 'edad');
        });
    }
    
    // Validar sexo
    if (sexoSelect) {
        sexoSelect.addEventListener('change', function() {
            validateField(this, 'sexo');
        });
    }
    
    // Validar departamento
    if (departamentoSelect) {
        departamentoSelect.addEventListener('change', function() {
            validateField(this, 'departamento');
        });
    }
    
    // Validar teléfono
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            validateField(this, 'phone');
        });
    }
    
    // Validar unidad educativa
    if (unidadEducativaInput) {
        unidadEducativaInput.addEventListener('blur', function() {
            validateField(this, 'unidad_educativa');
        });
    }
    
    // Validar email
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateField(this, 'email');
        });
    }
    
    // Añadir indicador de fortaleza de contraseña
    if (passwordInput) {
        // Crear contenedor para el indicador
        const strengthContainer = document.createElement('div');
        strengthContainer.classList.add('mt-1');
        strengthContainer.innerHTML = `
            <div class="h-1 w-full bg-gray-200 rounded-full">
                <div id="password-strength" class="h-1 rounded-full bg-gray-200" style="width: 0%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>Débil</span>
                <span>Media</span>
                <span>Fuerte</span>
            </div>
        `;
        
        // Insertar después del campo de contraseña
        passwordInput.parentNode.parentNode.appendChild(strengthContainer);
        
        // Evaluar fortaleza cuando se escribe
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Evaluar criterios de fortaleza
            if (password.length >= 8) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Actualizar barra visual
            const strengthBar = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthBar.style.width = '0%';
                strengthBar.className = 'h-1 rounded-full bg-gray-200';
            } else if (strength < 2) {
                strengthBar.style.width = '33%';
                strengthBar.className = 'h-1 rounded-full bg-red-400';
            } else if (strength < 4) {
                strengthBar.style.width = '66%';
                strengthBar.className = 'h-1 rounded-full bg-yellow-400';
            } else {
                strengthBar.style.width = '100%';
                strengthBar.className = 'h-1 rounded-full bg-green-400';
            }
            
            validateField(this, 'password');
        });
        
        // Validar al perder el foco
        passwordInput.addEventListener('blur', function() {
            validateField(this, 'password');
        });
    }
    
    // Validar confirmación de contraseña
    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('blur', function() {
            validateField(this, 'password_confirmation');
        });
    }
    
    // Validación completa al enviar formulario
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        let errorStep = 0;
        
        // Paso 1: Validar datos personales
        if (!validateField(nameInput, 'name')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 1);
        }
        if (!validateField(edadInput, 'edad')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 1);
        }
        if (!validateField(sexoSelect, 'sexo')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 1);
        }
        
        // Paso 2: Validar ubicación y contacto
        if (!validateField(departamentoSelect, 'departamento')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 2);
        }
        if (!validateField(phoneInput, 'phone')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 2);
        }
        if (!validateField(unidadEducativaInput, 'unidad_educativa')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 2);
        }
        
        // Paso 3: Validar datos de acceso
        if (!validateField(emailInput, 'email')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 3);
        }
        if (!validateField(passwordInput, 'password')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 3);
        }
        if (!validateField(passwordConfirmInput, 'password_confirmation')) {
            hasErrors = true;
            errorStep = Math.max(errorStep, 3);
        }
        
        // Verificar términos y condiciones
        const termsCheckbox = document.getElementById('terms');
        if (termsCheckbox && !termsCheckbox.checked) {
            const termsContainer = termsCheckbox.closest('.mt-4');
            if (termsContainer) {
                const errorMsg = document.createElement('p');
                errorMsg.classList.add('mt-2', 'text-sm', 'text-red-600');
                errorMsg.textContent = 'Debes aceptar los términos y condiciones';
                termsContainer.appendChild(errorMsg);
            }
            hasErrors = true;
            errorStep = Math.max(errorStep, 3);
        }
        
        // Si hay errores, prevenir envío y mostrar el paso con errores
        if (hasErrors) {
            e.preventDefault();
            
            // Restablecer el estado de carga cuando hay errores
            const alpineData = getAlpineData();
            if (alpineData) {
                alpineData.loading = false;
            }
            
            // Reactivar el botón de envío
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = false;
            
            // Cambiar al paso con errores
            if (alpineData && errorStep > 0) {
                alpineData.step = errorStep;
            }
            
            return false;
        }
        
        // Si no hay errores, mostrar estado de carga
        const alpineData = getAlpineData();
        if (alpineData) {
            alpineData.loading = true;
        }
        
        // Prevenir envíos múltiples
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        
        // Permitir que el formulario se envíe
        return true;
    });
});
</script>
@endpush