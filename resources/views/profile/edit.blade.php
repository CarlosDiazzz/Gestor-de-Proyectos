<x-app-layout>
    <div class="mx-auto max-w-270">

        {{-- Encabezado con Breadcrumb --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black dark:text-white text-2xl">
                Mi Perfil
            </h2>
            <nav>
                <ol class="flex items-center gap-2">
                    <li><a class="font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600" href="{{ route(Auth::user()->getDashboardRouteName()) }}">Dashboard /</a></li>
                    <li class="font-medium text-indigo-600">Perfil</li>
                </ol>
            </nav>
        </div>

        {{-- TARJETA DE RESUMEN (HEADER PROFILE) --}}
        <div class="mb-10 rounded-sm border border-gray-200 bg-white shadow-default dark:border-gray-700 dark:bg-gray-800 sm:p-5 rounded-3xl">
            {{-- Imagen de fondo opcional o barra de color --}}
            
            <div class="px-6 pb-6 lg:pb-8 xl:pb-10">
                <div class="relative z-30 mx-auto -mt-16 h-32 w-32 rounded-full bg-white/20 p-1 backdrop-blur sm:h-40 sm:w-40 sm:p-2">
                    <div class="relative flex h-full w-full items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="h-full w-full object-cover">
                        @else
                            <span class="text-4xl font-bold text-gray-500 dark:text-gray-300">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Ícono de Edición (más grande) -->
                    <button onclick="document.getElementById('profileAvatarInput').click()" 
                            class="absolute bottom-2 right-2 h-10 w-10 rounded-full bg-indigo-600 dark:bg-indigo-500 border-4 border-white dark:border-gray-800 flex items-center justify-center hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all shadow-lg cursor-pointer group hover:scale-110"
                            title="Cambiar foto de perfil">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                </div>
                

                <div class="mt-4 text-center">
                    <h3 class="mb-1.5 text-2xl font-semibold text-black dark:text-white">
                        {{ $user->name }}
                    </h3>
                    <p class="font-medium text-gray-500 dark:text-gray-400 mb-4">{{ $user->email }}</p>

                    {{-- Badges de Roles --}}
                    <div class="flex items-center justify-center gap-3">
                        @foreach($user->roles as $rol)
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-900/30 dark:text-indigo-400 dark:ring-indigo-400/30">
                                {{ $rol->nombre }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Estadísticas o Info Extra (Opcional, se ve bien en el diseño) --}}
                    @if(isset($esParticipante) && $esParticipante)
                    <div class="mx-auto mt-6 mb-2 grid max-w-94 grid-cols-2 rounded-md border border-gray-200 dark:border-gray-700 py-2.5 shadow-1 dark:bg-[#37404F]">
                        <div class="flex flex-col items-center justify-center gap-1 border-r border-gray-200 px-4 dark:border-gray-700 xsm:flex-row">
                            <span class="font-semibold text-black dark:text-white">No. Control:</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $user->participante->no_control ?? 'S/N' }}</span>
                        </div>
                        <div class="flex flex-col items-center justify-center gap-1 px-4 xsm:flex-row">
                            <span class="font-semibold text-black dark:text-white">Carrera:</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-[100px]" title="{{ $user->participante->carrera->nombre ?? 'N/A' }}">
                                {{ $user->participante->carrera->clave ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Formulario oculto para subir avatar -->
        <form id="profileAvatarForm" action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            <input type="file" 
                   id="profileAvatarInput" 
                   name="avatar" 
                   accept="image/*" 
                   onchange="document.getElementById('profileAvatarForm').submit()"
                   class="hidden">
        </form>

        {{-- GRID DE FORMULARIOS --}}
        <div class="grid grid-cols-1 gap-8">
            
            {{-- 1. INFORMACIÓN PERSONAL --}}
            <div class="rounded-sm border border-gray-200 bg-white shadow-default dark:border-gray-700 dark:bg-gray-800 sm:p-5 rounded-3xl">
                <div class="border-b border-gray-200 py-4 px-6.5 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">
                        Información Personal
                    </h3>
                </div>
                <div class="p-6.5">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. SEGURIDAD --}}
            <div class="rounded-sm border border-gray-200 bg-white shadow-default dark:border-gray-700 dark:bg-gray-800 sm:p-5 rounded-3xl">
                <div class="border-b border-gray-200 py-4 px-6.5 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">
                        Actualizar Contraseña
                    </h3>
                </div>
                <div class="p-6.5">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 3. ZONA DE PELIGRO --}}
            <div class="rounded-sm border border-red-100 bg-white shadow-default dark:border-red-900/30 dark:bg-gray-800 sm:p-5 rounded-3xl">
                <div class="border-b border-red-100 py-4 px-6.5 dark:border-red-900/30">
                    <h3 class="font-bold text-red-600 dark:text-red-400 text-lg">
                        Eliminar Cuenta
                    </h3>
                </div>
                <div class="p-6.5">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>