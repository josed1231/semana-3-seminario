<nav x-data="{ open: false }" class="bg-[#004d2e] sticky top-0 z-50 shadow-md">
    <!-- Estilos de la barra de desplazamiento -->
    <style>
        .nav-scroll::-webkit-scrollbar {
            height: 4px;
        }
        .nav-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        .nav-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.35);
            border-radius: 10px;
        }
        .nav-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.6);
        }
    </style>

    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 gap-4">
            
            <!-- LOGO INSTITUCIONAL -->
            <div class="flex items-center shrink-0">
                <a href="{{ in_array(auth()->user()->rol, ['user', 'estudiante']) ? route('welcome') : route('dashboard') }}"> 
                    <x-application-logo class="block h-9 w-auto fill-current text-white" />
                </a>
            </div>

            <!-- CONTENEDOR CON SCROLL FLUIDO POR RUEDA DE RATÓN -->
            <div class="hidden sm:flex sm:items-center flex-1 overflow-x-auto nav-scroll min-w-0 px-2 scroll-smooth"
                 @wheel.prevent="$el.scrollBy({ left: $event.deltaY * 1.2, behavior: 'smooth' })">
                <div class="flex space-x-6 whitespace-nowrap items-center h-full">
                    
                    @if(!in_array(auth()->user()->rol, ['user', 'estudiante']))
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:text-gray-200 transition duration-150 ease-in-out">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('alertas.monitoreo')" :active="request()->routeIs('alertas.monitoreo')" class="text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:text-gray-200 transition duration-150 ease-in-out">
                            {{ __('Monitoreo Alertas') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('cuestionario.create')" :active="request()->routeIs('cuestionario.create')" class="text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:text-gray-200 transition duration-150 ease-in-out">
                        {{ __('Cuestionario') }}
                    </x-nav-link>

                    @if(in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar']))
                        <x-nav-link :href="route('resultados.index')" :active="request()->routeIs('resultados.index')" class="text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:text-gray-200 transition duration-150 ease-in-out">
                            {{ __('Resultados Cuestionario') }}
                        </x-nav-link>
                    @endif

                    <!-- Módulos Exclusivos para Administrador -->
                    @if(auth()->user()->rol === 'admin')
                        <x-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" class="text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:text-gray-200 transition duration-150 ease-in-out">
                            {{ __('Gestión de Usuarios') }}
                        </x-nav-link>

                        <x-nav-link :href="route('programas.index')" :active="request()->routeIs('programas.*')" class="text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:text-gray-200 transition duration-150 ease-in-out">
                            {{ __('Gestión de Programas') }}
                        </x-nav-link>

                        <x-nav-link :href="route('directores.index')" :active="request()->routeIs('directores.*')" class="text-white inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:text-gray-200 transition duration-150 ease-in-out">
                            {{ __('Directores de Unidad') }}
                        </x-nav-link>
                    @endif

                </div>
            </div>

            <!-- MENU PERFIL DE USUARIO -->
            <div class="hidden sm:flex sm:items-center shrink-0 sm:ms-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-[#004d2e] hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- BOTÓN HAMBURGUESA MÓVIL -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-[#003e1c] focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- MENÚ RESPONSIVE MÓVIL -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#004d2e]">
        <div class="pt-2 pb-3 space-y-1">
            @if(!in_array(auth()->user()->rol, ['user', 'estudiante']))
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('alertas.monitoreo')" :active="request()->routeIs('alertas.monitoreo')" class="text-white">
                    {{ __('Monitoreo Alertas') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('cuestionario.create')" :active="request()->routeIs('cuestionario.create')" class="text-white">
                {{ __('Cuestionario') }}
            </x-responsive-nav-link>

            @if(in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar']))
                <x-responsive-nav-link :href="route('resultados.index')" :active="request()->routeIs('resultados.index')" class="text-white">
                    {{ __('Resultados Cuestionario') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->rol === 'admin')
                <x-responsive-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" class="text-white">
                    {{ __('Gestión de Usuarios') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('programas.index')" :active="request()->routeIs('programas.*')" class="text-white">
                    {{ __('Gestión de Programas') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('directores.index')" :active="request()->routeIs('directores.*')" class="text-white">
                    {{ __('Directores de Unidad') }}
                </x-responsive-nav-link>
            @endif
        </div>
        
        <div class="pt-4 pb-1 border-t border-[#003e1c]">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-white">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>