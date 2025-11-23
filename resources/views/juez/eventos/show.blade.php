<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestión del Evento: {{ $evento->nombre }}
            </h2>
            <a href="{{ route('juez.dashboard') }}"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                &larr; Volver al Panel
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'equipos' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button @click="tab = 'equipos'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'equipos', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': tab !== 'equipos' }"
                    class="py-4 px-6 block hover:text-indigo-500 focus:outline-none border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                    Equipos y Proyectos
                </button>
                <button @click="tab = 'criterios'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'criterios', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': tab !== 'criterios' }"
                    class="py-4 px-6 block hover:text-indigo-500 focus:outline-none border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                    Configuración de Rúbrica
                </button>
            </div>

            {{-- SECCIÓN 1: LISTA DE PROYECTOS (CORREGIDA) --}}
            <div x-show="tab === 'equipos'" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Proyectos Registrados</h3>

                    {{-- CAMBIO 1: Validamos si hay PROYECTOS, no equipos --}}
                    @if ($evento->proyectos->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay proyectos registrados en este
                                evento aún.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Equipo</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Proyecto</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Estado Evaluación</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    {{-- CAMBIO 2: Iteramos sobre PROYECTOS --}}
                                    @foreach ($evento->proyectos as $proyecto)
                                        @php
                                            // Obtenemos el equipo a través del proyecto
                                            $equipo = $proyecto->equipo;
                                            // Verificamos si ya tiene calificaciones (filtrado por el controlador)
                                            $yaCalificado = $proyecto->calificaciones->isNotEmpty();
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $equipo->nombre ?? 'Sin Equipo' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $equipo ? $equipo->participantes->count() : 0 }} Integrantes
                                                </div>
                                            </td>

                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100 font-bold">
                                                    {{ $proyecto->nombre }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                    {{ $proyecto->descripcion }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($yaCalificado)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Completado
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        Pendiente
                                                    </span>
                                                @endif
                                            </td>

                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end items-center gap-3">

                                                {{-- Enlace de Evaluación (Ya existía) --}}
                                                @if ($proyecto)
                                                    <a href="{{ route('juez.evaluaciones.edit', $proyecto) }}"
                                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-bold hover:underline">
                                                        {{ $yaCalificado ? 'Editar Nota' : 'Evaluar' }}
                                                    </a>
                                                @endif

                                                {{-- NUEVO: Botón de Gestión --}}
                                                <a href="{{ route('juez.equipos.edit', $equipo) }}"
                                                    class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                                                    title="Gestionar Equipo">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Editar Equipo
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SECCIÓN 2: CRITERIOS (RÚBRICA) --}}
            <div x-show="tab === 'criterios'" class="space-y-6" style="display: none;" x-data="{ editing: null, editForm: { id: null, nombre: '', ponderacion: '' } }">

                {{-- ALERTAS --}}
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <strong class="font-bold">¡Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="md:col-span-1 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 h-fit sticky top-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Nuevo Criterio</h3>

                        @php
                            $sumaTotal = $evento->criterios->sum('ponderacion');
                            $disponible = 100 - $sumaTotal;
                        @endphp

                        <div
                            class="mb-6 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-600">
                            Disponible para asignar:
                            <span
                                class="block text-2xl font-black {{ $disponible == 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $disponible }}%
                            </span>
                        </div>

                        <form action="{{ route('juez.criterios.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                            <div class="mb-4">
                                <x-input-label for="nombre" :value="__('Nombre del Criterio')" />
                                <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre"
                                    placeholder="Ej: Innovación" required />
                            </div>

                            <div class="mb-6">
                                <x-input-label for="ponderacion" :value="__('Ponderación (%)')" />
                                <div class="flex items-center">
                                    <x-text-input id="ponderacion" class="block mt-1 w-full" type="number"
                                        name="ponderacion" min="1" max="{{ $disponible }}" required />
                                    <span class="ml-2 text-gray-500 font-bold">%</span>
                                </div>
                            </div>

                            <x-primary-button class="w-full justify-center" :disabled="$disponible <= 0">
                                {{ $disponible <= 0 ? 'Rúbrica Completa (100%)' : 'Agregar Criterio' }}
                            </x-primary-button>
                        </form>
                    </div>

                    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Criterios Definidos</h3>
                            <div
                                class="text-sm font-bold px-3 py-1 rounded {{ $sumaTotal == 100 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                Total: {{ $sumaTotal }}%
                            </div>
                        </div>

                        @if ($evento->criterios->isEmpty())
                            <div
                                class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                <p class="text-gray-500 dark:text-gray-400">No hay criterios definidos.</p>
                                <p class="text-sm text-gray-400 mt-1">Agrega el primero desde el panel izquierdo.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($evento->criterios as $criterio)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-indigo-300 transition">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                                                {{ $criterio->ponderacion }}%
                                            </div>
                                            <div>
                                                <span
                                                    class="block font-bold text-gray-800 dark:text-gray-200 text-lg">{{ $criterio->nombre }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button
                                                @click="editing = true; editForm = { id: {{ $criterio->id }}, nombre: '{{ $criterio->nombre }}', ponderacion: {{ $criterio->ponderacion }} }"
                                                class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <form action="{{ route('juez.criterios.destroy', $criterio->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Seguro? Esto borrará las calificaciones asociadas.');">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition"
                                                    title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div x-show="editing" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-900 opacity-75" @click="editing = false"></div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-md w-full p-6 relative z-10">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Editar Criterio</h3>

                            <form :action="'/juez/criterios/' + editForm.id" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <x-input-label :value="__('Nombre')" />
                                    <x-text-input class="block mt-1 w-full" type="text" name="nombre"
                                        x-model="editForm.nombre" required />
                                </div>

                                <div class="mb-6">
                                    <x-input-label :value="__('Ponderación %')" />
                                    <x-text-input class="block mt-1 w-full" type="number" name="ponderacion"
                                        x-model="editForm.ponderacion" min="1" max="100" required />
                                    <p class="text-xs text-gray-500 mt-1">Al reducir este valor, liberarás espacio para
                                        otros criterios.</p>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button type="button" @click="editing = false"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300 font-bold">
                                        Cancelar
                                    </button>
                                    <x-primary-button>
                                        Guardar Cambios
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
