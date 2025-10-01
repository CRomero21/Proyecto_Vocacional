<x-guest-layout>
    <!-- status de sesion -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <h2 class="text-center text-white text-2xl font-bold mb-6">Iniciar sesión</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo -->
        <div>
            <x-input-label for="email" :value="__('Correo')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- contraseña -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Contraseña') }}</label>
            <div class="relative">
                <input id="password" name="password" type="password" autocomplete="current-password"
                    required
                    class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-10 py-2 px-3 text-base text-gray-900 placeholder-gray-400"
                    placeholder="Ingresa tu contraseña">
                <button type="button" onclick="togglePassword()" class="absolute right-0 top-1/2 -translate-y-1/2 px-3 flex items-center" tabindex="-1">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268-2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('password'))
                <p class="mt-2 text-sm text-red-600">{{ $errors->first('password') }}</p>
            @endif
        </div>
        <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.add('text-indigo-600');
            } else {
                input.type = 'password';
                icon.classList.remove('text-indigo-600');
            }
        }
        </script>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex flex-col items-start">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-black hover:text-indigo-700 rounded-md mb-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                            {{ __('¿olvidaste tu contraseña?') }}
                        </a>
                @endif
                <a class="underline text-sm text-black hover:text-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('¿quieres registrarte?') }}
                </a>
            </div>
            <x-primary-button class="ms-3">
                {{ __('Ingresar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
