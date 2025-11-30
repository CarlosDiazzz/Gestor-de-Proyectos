<header class="sticky top-0 z-40 flex w-full bg-white drop-shadow-sm dark:bg-gray-900 dark:drop-shadow-none border-b border-gray-200 dark:border-gray-700">
    <div class="flex flex-grow items-center justify-between px-4 py-3 shadow-2 md:px-6 2xl:px-11">
        
        <!-- BOTÓN HAMBURGUESA (Solo Móvil) -->
        <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
            <button @click.stop="sidebarOpen = !sidebarOpen" class="z-50 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm dark:border-strokedark dark:bg-boxdark lg:hidden">
                <span class="block h-5.5 w-5.5 cursor-pointer">
                    <svg class="h-6 w-6 text-gray-600 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </span>
            </button>
        </div>

        <!-- BUSCADOR (Decorativo) -->
        <div class="hidden sm:block">
            <div class="relative">
                <button class="absolute left-0 top-1/2 -translate-y-1/2">
                    <svg class="w-5 h-5 text-gray-400 hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
                <input type="text" placeholder="Buscar..." class="w-full bg-transparent pl-9 pr-4 text-black focus:outline-none dark:text-white border-none focus:ring-0 xl:w-125 placeholder-gray-400" />
            </div>
        </div>

        <!-- ÁREA DERECHA (Perfil y Tema) -->
        <div class="flex items-center gap-3 2xsm:gap-7">
            
            <!-- TOGGLE DARK MODE -->
            <button id="theme-toggle" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none rounded-lg text-sm p-2.5 mr-2">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>

            <!-- MENÚ USUARIO -->
            <div class="relative" x-data="{ dropdownOpen: false }">
                <a @click.prevent="dropdownOpen = !dropdownOpen" class="flex items-center gap-4 cursor-pointer" href="#">
                    <span class="hidden text-right lg:block">
                        <span class="block text-sm font-medium text-black dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="block text-xs text-gray-500">{{ Auth::user()->email }}</span>
                    </span>
                    <span class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 overflow-hidden flex items-center justify-center">
                        {{-- Icono de usuario --}}
                        <svg class="h-6 w-6 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </span>
                    <svg :class="dropdownOpen && 'rotate-180'" class="hidden fill-current sm:block w-4 h-4 text-gray-500 duration-200 ease-in-out" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </a>

                <!-- Dropdown Content -->
                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" class="absolute right-0 mt-4 flex w-62.5 flex-col rounded-sm border border-gray-200 bg-white shadow-default dark:border-gray-700 dark:bg-gray-800 z-50">
                    <ul class="flex flex-col gap-5 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white lg:text-base">
                                <svg class="fill-current w-5 h-5" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                Mi Perfil
                            </a>
                        </li>
                    </ul>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-3.5 px-6 py-4 text-sm font-medium duration-300 ease-in-out hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white lg:text-base w-full text-left">
                            <svg class="fill-current w-5 h-5" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Lógica del Toggle Dark Mode
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        var themeToggleBtn = document.getElementById('theme-toggle');

        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleDarkIcon.classList.add('hidden');
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
                themeToggleLightIcon.classList.add('hidden');
            }
        }

        // Inicialización
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateIcons();

        themeToggleBtn.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('color-theme', 'dark');
            } else {
                localStorage.setItem('color-theme', 'light');
            }
            updateIcons();
        });
    </script>
</header>