<nav x-data="{ open: false, catalogOpen: false }" class="zeta-nav border-b">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <x-application-logo class="block h-9 w-9 text-slate-700" />
                        <span class="zeta-brand-wordmark text-base">REFRIGERACION ZETA</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('directory.index')" :active="request()->routeIs('directory.index')">
                        {{ __('Directorio') }}
                    </x-nav-link>

                    <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                        {{ __('Cursos') }}
                    </x-nav-link>

                    @if (Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Panel Admin') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->role === 'technician')
                        <x-nav-link :href="route('technician.verification.create')" :active="request()->routeIs('technician.verification.*')">
                            {{ Auth::user()->isTechnicianVerified() ? __('Tecnico Verificado') : __('Verificacion Tecnica') }}
                        </x-nav-link>
                    @endif

                    <div class="relative" @mouseenter="catalogOpen = true" @mouseleave="catalogOpen = false">
                        <button type="button" class="inline-flex items-center h-full text-sm font-medium text-gray-600 hover:text-gray-900">
                            {{ __('Lavadoras') }}
                            <svg class="ms-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="catalogOpen" x-transition class="absolute left-0 mt-2 w-72 rounded-md bg-white border border-gray-200 shadow-lg z-50 p-4" style="display: none;">
                            <p class="text-xs uppercase tracking-wider text-gray-500 mb-3">Marcas y modelos</p>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li><span class="font-semibold">Whirlpool</span>: Xpert System, 8MWTWCO31W</li>
                                <li><span class="font-semibold">Mabe</span>: LMA74215WBAB0, Aqua Saver Green</li>
                                <li><span class="font-semibold">Samsung</span>: WA19CG6745BV, WA13T5260BY</li>
                                <li><span class="font-semibold">LG</span>: WT13DSBP, WT21WT6HKA</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('directory.index')" :active="request()->routeIs('directory.index')">
                {{ __('Directorio') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                {{ __('Cursos') }}
            </x-responsive-nav-link>

            @if (Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Panel Admin') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->role === 'technician')
                <x-responsive-nav-link :href="route('technician.verification.create')" :active="request()->routeIs('technician.verification.*')">
                    {{ Auth::user()->isTechnicianVerified() ? __('Tecnico Verificado') : __('Verificacion Tecnica') }}
                </x-responsive-nav-link>
            @endif

            <div class="px-4 pt-2 pb-1 text-xs uppercase tracking-wider text-gray-500">Lavadoras</div>
            <div class="px-4 pb-3 text-sm text-gray-600 space-y-1">
                <p><span class="font-semibold">Whirlpool:</span> Xpert System, 8MWTWCO31W</p>
                <p><span class="font-semibold">Mabe:</span> LMA74215WBAB0, Aqua Saver Green</p>
                <p><span class="font-semibold">Samsung:</span> WA19CG6745BV, WA13T5260BY</p>
                <p><span class="font-semibold">LG:</span> WT13DSBP, WT21WT6HKA</p>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
