
<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white relative">        <!-- Elementos decorativos de fondo -->
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
                    
                    // Validar fecha de nacimiento
                    const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
                    if(!fechaNacimiento) {
                        document.getElementById('fecha_nacimiento').classList.add('border-red-500');
                        isValid = false;
                        let errorContainer = document.getElementById('fecha_nacimiento').parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            document.getElementById('fecha_nacimiento').parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'La fecha de nacimiento es requerida';
                    } else {
                        const fecha = new Date(fechaNacimiento);
                        const hoy = new Date();
                        if (fecha > hoy) {
                            document.getElementById('fecha_nacimiento').classList.add('border-red-500');
                            isValid = false;
                            let errorContainer = document.getElementById('fecha_nacimiento').parentNode.parentNode.querySelector('.text-red-600');
                            if (!errorContainer) {
                                errorContainer = document.createElement('p');
                                errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                                document.getElementById('fecha_nacimiento').parentNode.parentNode.appendChild(errorContainer);
                            }
                            errorContainer.textContent = 'La fecha de nacimiento no puede ser en el futuro';
                        } else if (fecha.getFullYear() < 1900) {
                            document.getElementById('fecha_nacimiento').classList.add('border-red-500');
                            isValid = false;
                            let errorContainer = document.getElementById('fecha_nacimiento').parentNode.parentNode.querySelector('.text-red-600');
                            if (!errorContainer) {
                                errorContainer = document.createElement('p');
                                errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                                document.getElementById('fecha_nacimiento').parentNode.parentNode.appendChild(errorContainer);
                            }
                            errorContainer.textContent = 'La fecha de nacimiento no puede ser menor a 1900';
                        } else {
                            document.getElementById('fecha_nacimiento').classList.remove('border-red-500');
                            const errorContainer = document.getElementById('fecha_nacimiento').parentNode.parentNode.querySelector('.text-red-600');
                            if (errorContainer) errorContainer.textContent = '';
                        }
                    }
                    
                    // Validar sexo (debe estar seleccionado)
                    if(!document.getElementById('sexo').value) {
                        document.getElementById('sexo').classList.add('border-red-500');
                        isValid = false;
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
                    validarPaso3() {
                        let isValid = true;

                        // Validar correo
                        const email = document.getElementById('email');
                        const emailValue = email.value.trim();
                        const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|hotmail\.com|yahoo\.com)$/i;
                        if (!emailValue || !emailRegex.test(emailValue)) {
                            email.classList.add('border-red-500');
                            isValid = false;
                            let errorContainer = email.parentNode.parentNode.querySelector('.text-red-600');
                            if (!errorContainer) {
                                errorContainer = document.createElement('p');
                                errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                                email.parentNode.parentNode.appendChild(errorContainer);
                            }
                            errorContainer.textContent = 'Ingrese un correo válido (Gmail, Hotmail o Yahoo)';
                        } else {
                            email.classList.remove('border-red-500');
                            const errorContainer = email.parentNode.parentNode.querySelector('.text-red-600');
                            if (errorContainer) errorContainer.textContent = '';
                        }

                        // Validar contraseña
                        const password = document.getElementById('password');
                        if (!password.value || password.value.length < 8) {
                            password.classList.add('border-red-500');
                            isValid = false;
                            let errorContainer = password.parentNode.parentNode.querySelector('.text-red-600');
                            if (!errorContainer) {
                                errorContainer = document.createElement('p');
                                errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                                password.parentNode.parentNode.appendChild(errorContainer);
                            }
                            errorContainer.textContent = 'La contraseña debe tener al menos 8 caracteres';
                        } else {
                            password.classList.remove('border-red-500');
                            const errorContainer = password.parentNode.parentNode.querySelector('.text-red-600');
                            if (errorContainer) errorContainer.textContent = '';
                        }

                        // Validar confirmación de contraseña
                        const passwordConfirm = document.getElementById('password_confirmation');
                        if (passwordConfirm.value !== password.value) {
                            passwordConfirm.classList.add('border-red-500');
                            isValid = false;
                            let errorContainer = passwordConfirm.parentNode.parentNode.querySelector('.text-red-600');
                            if (!errorContainer) {
                                errorContainer = document.createElement('p');
                                errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                                passwordConfirm.parentNode.parentNode.appendChild(errorContainer);
                            }
                            errorContainer.textContent = 'Las contraseñas no coinciden';
                        } else {
                            passwordConfirm.classList.remove('border-red-500');
                            const errorContainer = passwordConfirm.parentNode.parentNode.querySelector('.text-red-600');
                            if (errorContainer) errorContainer.textContent = '';
                        }

                        // Validar términos y condiciones
                        const terms = document.getElementById('terms');
                        if (!terms.checked) {
                            isValid = false;
                            let errorContainer = terms.parentNode.parentNode.parentNode.querySelector('.text-red-600');
                            if (!errorContainer) {
                                errorContainer = document.createElement('p');
                                errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                                terms.parentNode.parentNode.parentNode.appendChild(errorContainer);
                            }
                            errorContainer.textContent = 'Debe aceptar los términos y condiciones';
                        } else {
                            const errorContainer = terms.parentNode.parentNode.parentNode.querySelector('.text-red-600');
                            if (errorContainer) errorContainer.textContent = '';
                        }

                        return isValid;
                    },
                validarPaso2() {
                    let isValid = true;
                    // Validar departamento (debe estar seleccionado y no vacío)
                    const departamento = document.getElementById('departamento_id');
                    if(!departamento.value) {
                        departamento.classList.add('border-red-500');
                        isValid = false;
                        let errorContainer = departamento.parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            departamento.parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'Por favor seleccione un departamento válido';
                    } else {
                        departamento.classList.remove('border-red-500');
                        const errorContainer = departamento.parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }

                    // Validar ciudad (debe estar seleccionada y no vacía)
                    const ciudad = document.getElementById('ciudad_id');
                    if(!ciudad.value) {
                        ciudad.classList.add('border-red-500');
                        isValid = false;
                        let errorContainer = ciudad.parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            ciudad.parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'Por favor seleccione una ciudad válida';
                    } else {
                        ciudad.classList.remove('border-red-500');
                        const errorContainer = ciudad.parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }

                    // Validar unidad educativa (debe estar seleccionada y no vacía)
                    const unidad = document.getElementById('unidad_educativa_id');
                    if(!unidad.value) {
                        unidad.classList.add('border-red-500');
                        isValid = false;
                        let errorContainer = unidad.parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            unidad.parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'Por favor seleccione una unidad educativa válida';
                    } else {
                        unidad.classList.remove('border-red-500');
                        const errorContainer = unidad.parentNode.parentNode.querySelector('.text-red-600');
                        if (errorContainer) errorContainer.textContent = '';
                    }

                    // Validar teléfono (solo números y longitud)
                    const phone = document.getElementById('phone');
                    if(!phone.value || !/^[0-9]+$/.test(phone.value) || phone.value.length < 7 || phone.value.length > 8) {
                        phone.classList.add('border-red-500');
                        isValid = false;
                        let errorContainer = phone.parentNode.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.classList.add('mt-2', 'text-sm', 'text-red-600');
                            phone.parentNode.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = 'El teléfono debe tener entre 7 y 8 dígitos numéricos';
                    } else {
                        phone.classList.remove('border-red-500');
                        const errorContainer = phone.parentNode.parentNode.querySelector('.text-red-600');
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
                        <x-input-label for="name" :value="__('Nombre y apellido')" class="text-black" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <x-text-input id="name" class="pl-10 block mt-1 w-full bg-white text-black border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ingresa tu nombre y apellido" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Fecha de nacimiento -->
                        <div>
                            <x-input-label for="fecha_nacimiento" :value="__('Fecha de nacimiento')" />
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <!-- Quitado el icono/logo de la izquierda -->
                                <x-text-input 
                                    id="fecha_nacimiento" 
                                    class="block mt-1 w-full bg-white" 
                                    type="date" 
                                    name="fecha_nacimiento" 
                                    :value="old('fecha_nacimiento')" 
                                    required 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
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
                                <select id="sexo" name="sexo" class="pl-10 block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white" required>
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

                    <!-- Departamento (select dependiente) -->
                    <div>
                        <x-input-label for="departamento_id" :value="__('Departamento')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <select id="departamento_id" name="departamento_id" class="pl-10 block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white" required>
                                <option value="">Seleccione un departamento</option>
                                @foreach($departamentos as $departamento)
                                    <option value="{{ $departamento->id }}" {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>{{ $departamento->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('departamento_id')" class="mt-2" />
                    </div>

                    <!-- Ciudad (select dependiente) -->
                    <div>
                        <x-input-label for="ciudad_id" :value="__('Ciudad')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                            </div>
                            <select id="ciudad_id" name="ciudad_id" class="pl-10 block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white" required>
                                <option value="">Seleccione una ciudad</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('ciudad_id')" class="mt-2" />
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
                            <x-text-input id="phone" class="pl-10 block mt-1 w-full bg-white" type="text" name="phone" :value="old('phone')" required placeholder="Ej: 77123456" maxlength="8" />
                        </div>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Unidad educativa -->
                    <!-- Unidad educativa (select dependiente) -->
                    <div>
                        <x-input-label for="unidad_educativa_id" :value="__('Unidad Educativa')" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <select id="unidad_educativa_id" name="unidad_educativa_id" class="pl-10 block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white" required>
                                <option value="">Seleccione una unidad educativa</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('unidad_educativa_id')" class="mt-2" />
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
                            <x-text-input id="email" class="pl-10 block mt-1 w-full bg-white" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="tu.correo@gmail.com" />
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
                                class="pl-10 pr-10 block mt-1 w-full bg-white"
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
                                class="pl-10 pr-10 block mt-1 w-full bg-white"
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
                            type="submit"
                            @click.prevent="if(validarPaso3()) { loading = true; $el.form.submit(); }"
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
            <!-- Script para selects dependientes -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Datos desde backend (Blade)
                    const ciudades = @json($ciudades);
                    const unidades = @json($unidadesEducativas);

                    const oldCiudadId = "{{ old('ciudad_id') }}";
                    const oldUnidadId = "{{ old('unidad_educativa_id') }}";

                    const departamentoSelect = document.getElementById('departamento_id');
                    const ciudadSelect = document.getElementById('ciudad_id');
                    const unidadSelect = document.getElementById('unidad_educativa_id');

                    // Actualiza ciudades según departamento
                    departamentoSelect.addEventListener('change', function() {
                        const deptId = this.value;
                        ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
                        unidadSelect.innerHTML = '<option value="">Seleccione una unidad educativa</option>';
                        if (!deptId) return;
                        const filteredCiudades = ciudades.filter(c => c.departamento_id == deptId);
                        filteredCiudades.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = c.nombre;
                            if (oldCiudadId && c.id == oldCiudadId) opt.selected = true;
                            ciudadSelect.appendChild(opt);
                        });
                    });

                    // Actualiza unidades según ciudad
                    ciudadSelect.addEventListener('change', function() {
                        const ciudadId = this.value;
                        unidadSelect.innerHTML = '<option value="">Seleccione una unidad educativa</option>';
                        if (!ciudadId) return;
                        const filteredUnidades = unidades.filter(u => u.ciudad_id == ciudadId);
                        filteredUnidades.forEach(u => {
                            const opt = document.createElement('option');
                            opt.value = u.id;
                            opt.textContent = u.nombre;
                            if (oldUnidadId && u.id == oldUnidadId) opt.selected = true;
                            unidadSelect.appendChild(opt);
                        });
                    });

                    // Inicialización si hay old values
                    if (departamentoSelect.value) {
                        departamentoSelect.dispatchEvent(new Event('change'));
                        if (oldCiudadId) {
                            ciudadSelect.value = oldCiudadId;
                            ciudadSelect.dispatchEvent(new Event('change'));
                        }
                        if (oldUnidadId) {
                            unidadSelect.value = oldUnidadId;
                        }
                    }
                });
            </script>

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