<x-guest-layout>
    <h2 class="text-center text-white text-2xl font-bold mb-6">Registrarse</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nombre -->
        <div>
            <x-input-label for="name" :value="__('Nombre y apellido')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        
        <div class="mt-4 flex gap-4">
            <!-- Edad -->
            <div class="w-1/2">
                <x-input-label for="edad" :value="__('Edad')" />
                <x-text-input id="edad" class="block mt-1 w-full" type="number" name="edad" :value="old('edad')" required min="1" max="120"/>
                <x-input-error :messages="$errors->get('edad')" class="mt-2" />
            </div>
            <!-- Sexo -->
            <div class="w-1/2">
                <x-input-label for="sexo" :value="__('Sexo')" />
                <select id="sexo" name="sexo" class="block mt-1 w-full bg-gray-800 text-white" required>
                    <option value="" disabled selected>Seleccione...</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
                <x-input-error :messages="$errors->get('sexo')" class="mt-2" />
            </div>
        </div>
        
        <!-- Departamento -->
        <div class="mt-4">
            <label for="departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
            <select id="departamento" name="departamento" required class="block mt-1 w-full bg-gray-800 text-white">
                <option value="" disabled selected>Seleccione un departamento</option>
                <option value="Chuquisaca">Chuquisaca</option>
                <option value="La Paz">La Paz</option>
                <option value="Cochabamba">Cochabamba</option>
                <option value="Oruro">Oruro</option>
                <option value="Potosí">Potosí</option>
                <option value="Tarija">Tarija</option>
                <option value="Santa Cruz">Santa Cruz</option>
                <option value="Beni">Beni</option>
                <option value="Pando">Pando</option>
            </select>
            @error('departamento')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <!-- numero de telefono -->
        <div class="mt-4">
            <x-input-label  for="phone" :value="__('Telefono')"/>
            <x-text-input id="phone" name="phone" type="text" required autofocus />
        </div>

        <!-- unidad educativa-->
        <div class="mt-4">
            <x-input-label for="unidad_educativa" :value="__('Unidad Educativa')"/>
            <x-text-input id="unidad_educativa" class="block mt-1 2-full" type="text" name="unidad_educativa" :value="old('unidad_educativa')" required />
            <x-input-error :messages ="$errors->get('unidad_educativa')" class="mt.2"/>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

         <!-- Password -->
        <div class="mt-4 relative">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full pr-10"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <button type="button" onclick="togglePassword('password')" class="absolute right-2 top-8 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7-8-3.134-8-7z" />
                </svg>
            </button>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4 relative">
            <x-input-label for="password_confirmation" :value="__('Confirme Contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-2 top-8 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7-8-3.134-8-7z" />
                </svg>
            </button>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
        </script>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('¿Ya estas Registrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
