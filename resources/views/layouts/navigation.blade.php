
<nav x-data="{ open: false }" class="bg-gradient-to-r from-[#051a9a] via-[#0b3be9] to-[#0079f4] border-b border-[#131e58] shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition-transform hover:scale-105">
                        <img src="{{ asset('images/logo_uno_blanco.png') }}" alt="Logo" class="block h-10 w-auto drop-shadow-lg" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(auth()->user()->role === 'superadmin')
                            <x-nav-link :href="url('/informes')" :active="request()->is('informes')" class="group">
                                <div class="flex items-center px-4 py-2 rounded-md transition-all {{ request()->is('informes') ? 'bg-[#131e58] text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="font-semibold">Informes</span>
                                </div>
                            </x-nav-link>
                            
                        @elseif(auth()->user()->role === 'coordinador')
                            <x-nav-link :href="route('coordinador.dashboard')" :active="request()->routeIs('coordinador.dashboard')" class="group">
                                <div class="flex items-center px-4 py-2 rounded-md transition-all {{ request()->routeIs('coordinador.dashboard') ? 'bg-[#131e58] text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span class="font-semibold">Dashboard</span>
                                </div>
                            </x-nav-link>
                            
                            <x-nav-link :href="route('admin.informes-avanzados.index')" :active="request()->routeIs('admin.informes-avanzados.*')" class="group">
                                <div class="flex items-center px-4 py-2 rounded-md transition-all {{ request()->routeIs('admin.informes-avanzados.*') ? 'bg-[#131e58] text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="font-semibold">Informes</span>
                                </div>
                            </x-nav-link>
                            
                            <x-nav-link :href="route('admin.estadisticas.index')" :active="request()->routeIs('admin.estadisticas.*')" class="group">
                                <div class="flex items-center px-4 py-2 rounded-md transition-all {{ request()->routeIs('admin.estadisticas.*') ? 'bg-[#131e58] text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                    </svg>
                                    <span class="font-semibold">Estadísticas</span>
                                </div>
                            </x-nav-link>
                            
                        @else
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="group">
                                <div class="flex items-center px-4 py-2 rounded-md transition-all {{ request()->routeIs('dashboard') ? 'bg-[#131e58] text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span class="font-semibold">Inicio</span>
                                </div>
                            </x-nav-link>
                            
                            <x-nav-link :href="route('test.iniciar')" :active="request()->routeIs('test.*')" class="group">
                                <div class="flex items-center px-4 py-2 rounded-md transition-all {{ request()->routeIs('test.*') ? 'bg-[#131e58] text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="font-semibold">Realizar Test</span>
                                </div>
                            </x-nav-link>
                            
                            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="group">
                                <div class="flex items-center px-4 py-2 rounded-md transition-all {{ request()->routeIs('profile.edit') ? 'bg-[#131e58] text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="font-semibold">Mi Perfil</span>
                                </div>
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-[#131e58]/20 text-sm leading-4 font-medium rounded-md text-white bg-[#131e58] hover:bg-[#051a9a] focus:outline-none transition ease-in-out duration-150 shadow">
                            <div class="flex items-center">
                                <div class="h-7 w-7 rounded-full bg-[#0079f4] flex items-center justify-center mr-2 text-white shadow text-xs font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    
                    <x-slot name="content">
                        <div class="border-b border-gray-200 pb-2 pt-1 px-4">
                            <p class="text-xs text-gray-500">Conectado como</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-[#0b3be9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('Editar Perfil') }}
                        </x-dropdown-link>
                        
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" class="flex items-center text-red-600 hover:text-red-800 hover:bg-red-50"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-[#131e58] focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#051a9a]">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->role === 'superadmin')
                    <x-responsive-nav-link :href="url('/informes')" :active="request()->is('informes')">
                        <div class="flex items-center {{ request()->is('informes') ? 'text-white bg-[#131e58]' : 'text-white' }} rounded-md px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Informes
                        </div>
                    </x-responsive-nav-link>
                    
                @elseif(auth()->user()->role === 'coordinador')
                    <x-responsive-nav-link :href="route('coordinador.dashboard')" :active="request()->routeIs('coordinador.dashboard')">
                        <div class="flex items-center {{ request()->routeIs('coordinador.dashboard') ? 'text-white bg-[#131e58]' : 'text-white' }} rounded-md px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </div>
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('coordinador.informes')" :active="request()->routeIs('coordinador.informes')">
                        <div class="flex items-center {{ request()->routeIs('coordinador.informes') ? 'text-white bg-[#131e58]' : 'text-white' }} rounded-md px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Informes
                        </div>
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('coordinador.estadisticas')" :active="request()->routeIs('coordinador.estadisticas')">
                        <div class="flex items-center {{ request()->routeIs('coordinador.estadisticas') ? 'text-white bg-[#131e58]' : 'text-white' }} rounded-md px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            Estadísticas
                        </div>
                    </x-responsive-nav-link>
                    
                @else
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <div class="flex items-center {{ request()->routeIs('dashboard') ? 'text-white bg-[#131e58]' : 'text-white' }} rounded-md px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Inicio
                        </div>
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('test.iniciar')" :active="request()->routeIs('test.*')">
                        <div class="flex items-center {{ request()->routeIs('test.*') ? 'text-white bg-[#131e58]' : 'text-white' }} rounded-md px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Realizar Test
                        </div>
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>
        
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-[#0b3be9]">
            <div class="px-4 flex items-center">
                <div class="h-9 w-9 rounded-full bg-[#0079f4] flex items-center justify-center mr-3 text-white shadow text-sm font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-[#00aeff]">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 border-t border-[#0b3be9]/30 pt-2">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <div class="flex items-center text-white rounded-md px-3 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Editar Perfil') }}
                    </div>
                </x-responsive-nav-link>
                
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <div class="flex items-center text-white bg-[#131e58]/50 rounded-md px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Cerrar sesión') }}
                        </div>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>