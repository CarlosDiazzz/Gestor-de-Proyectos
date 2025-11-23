<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Script para evitar parpadeo de tema --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 selection:bg-indigo-500 selection:text-white transition-colors duration-300">
    
    <div class="relative flex items-top justify-center min-h-screen sm:items-center py-4 sm:pt-0">
        
        {{-- NAVEGACIÓN SUPERIOR --}}
        <div class="fixed top-0 right-0 px-6 py-4 z-10 flex items-center gap-4">
            
            {{-- BOTÓN TOGGLE TEMA (Sol/Luna) --}}
            <button id="theme-toggle-welcome" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 transition">
                <svg id="theme-toggle-dark-icon-welcome" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon-welcome" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>

            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 dark:text-gray-300 underline decoration-indigo-500 decoration-2 underline-offset-4 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-300 underline decoration-transparent hover:decoration-indigo-500 underline-offset-4 font-semibold transition">
                        Iniciar Sesión
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm text-gray-700 dark:text-gray-300 underline decoration-transparent hover:decoration-indigo-500 underline-offset-4 font-semibold transition">
                            Registrarse
                        </a>
                    @endif
                @endauth
            @endif
        </div>

        {{-- CONTENIDO PRINCIPAL --}}
        <div class="max-w-7xl mx-auto p-6 lg:p-8 w-full">
            
            <div class="flex justify-center">
                <div class="bg-indigo-50 dark:bg-gray-800 p-4 rounded-2xl mb-8 shadow-inner">
                    <svg class="w-16 h-16 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
            </div>

            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                    <span class="block">Gestión de</span>
                    <span class="block text-indigo-600 dark:text-indigo-400 mt-1">Proyectos Académicos</span>
                </h1>
                <p class="mt-4 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Plataforma integral para la administración de eventos, evaluación de equipos multidisciplinarios y seguimiento de resultados.
                </p>
                <div class="mt-8 max-w-md mx-auto sm:flex sm:justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition shadow-lg">
                            Ir a mi Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition shadow-lg">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="mt-3 sm:mt-0 w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10 transition">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>

            <div class="mt-16 border-t border-gray-200 dark:border-gray-800 pt-10">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    
                    <div class="pt-6">
                        <div class="flow-root bg-white dark:bg-gray-800 rounded-lg px-6 pb-8 shadow-md hover:shadow-lg transition duration-300 h-full">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Equipos Multidisciplinarios</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    Forma equipos con estudiantes de diferentes carreras para potenciar tus proyectos y habilidades.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="flow-root bg-white dark:bg-gray-800 rounded-lg px-6 pb-8 shadow-md hover:shadow-lg transition duration-300 h-full">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                                    </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Evaluación en Tiempo Real</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    Recibe retroalimentación instantánea de los jueces y visualiza tu progreso mediante gráficos dinámicos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="flow-root bg-white dark:bg-gray-800 rounded-lg px-6 pb-8 shadow-md hover:shadow-lg transition duration-300 h-full">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Certificación Digital</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    Genera y descarga constancias de participación y diplomas de logros automáticamente al finalizar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-16 text-sm text-gray-500 dark:text-gray-500">
                &copy; {{ date('Y') }} Sistema de Gestión de Proyectos. Todos los derechos reservados.
            </div>
        </div>
    </div>

    {{-- Script para el Toggle en Welcome (Independiente) --}}
    <script>
        const welcomeToggleBtn = document.getElementById('theme-toggle-welcome');
        const welcomeDarkIcon = document.getElementById('theme-toggle-dark-icon-welcome');
        const welcomeLightIcon = document.getElementById('theme-toggle-light-icon-welcome');

        // Sincronizar iconos al inicio
        if (document.documentElement.classList.contains('dark')) {
            welcomeLightIcon.classList.remove('hidden');
            welcomeDarkIcon.classList.add('hidden');
        } else {
            welcomeLightIcon.classList.add('hidden');
            welcomeDarkIcon.classList.remove('hidden');
        }

        welcomeToggleBtn.addEventListener('click', function() {
            // Alternar clase
            document.documentElement.classList.toggle('dark');
            
            // Guardar y cambiar iconos
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                welcomeLightIcon.classList.remove('hidden');
                welcomeDarkIcon.classList.add('hidden');
            } else {
                localStorage.setItem('theme', 'light');
                welcomeLightIcon.classList.add('hidden');
                welcomeDarkIcon.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>