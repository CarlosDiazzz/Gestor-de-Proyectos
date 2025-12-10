<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles del Evento') }}
            </h2>
            <a href="{{ route('admin.eventos.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ================================================= --}}
                {{-- COLUMNA IZQUIERDA: INFORMACIÓN (1/3)              --}}
                {{-- ================================================= --}}
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-8">

                        {{-- Header de la Tarjeta --}}
                        <div
                            class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $evento->nombre }}
                                </h3>
                                {{-- Badge de Estado --}}
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $estado = 'Finalizado';
                                    $color = 'gray';

                                    if ($now->between($evento->fecha_inicio, $evento->fecha_fin)) {
                                        $estado = 'En Curso';
                                        $color = 'green';
                                    } elseif ($now->lt($evento->fecha_inicio)) {
                                        $estado = 'Próximo';
                                        $color = 'indigo';
                                    }
                                @endphp
                                <span
                                    class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900/50 dark:text-{{ $color }}-300 border border-{{ $color }}-200 dark:border-{{ $color }}-800">
                                    {{ $estado }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">ID: #{{ $evento->id }}</p>
                        </div>

                        {{-- Cuerpo --}}
                        <div class="p-6 space-y-6">

                            <div>
                                <label
                                    class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Descripción</label>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    {{ $evento->descripcion ?? 'Sin descripción detallada.' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-gray-50 dark:bg-gray-700/30 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <span class="block text-xs text-indigo-500 font-bold uppercase mb-1">Inicia</span>
                                    <span class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d M, Y') }}
                                    </span>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-700/30 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <span class="block text-xs text-red-500 font-bold uppercase mb-1">Termina</span>
                                    <span class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($evento->fecha_fin)->format('d M, Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2">
                                <a href="{{ route('admin.eventos.edit', $evento) }}"
                                    class="flex items-center justify-center w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Editar Información
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================================================= --}}
                {{-- COLUMNA DERECHA: CRITERIOS (2/3)                  --}}
                {{-- ================================================= --}}
                <div class="lg:col-span-2 space-y-6" x-data="{ openForm: false }">

                    {{-- Tarjeta Principal de Criterios --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

                        <div
                            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/20">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Criterios de Evaluación</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Define la rúbrica para los jueces.
                                </p>
                            </div>

                            <button @click="openForm = !openForm"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase rounded-lg transition shadow-md hover:shadow-lg">
                                <span x-show="!openForm">+ Agregar Criterio</span>
                                <span x-show="openForm">Cancelar</span>
                            </button>
                        </div>

                        <div class="p-6">

                            {{-- Cálculo de Porcentajes --}}
                            @php
                                $sumaTotal = $evento->criterios->sum('ponderacion');
                                $disponible = 100 - $sumaTotal;
                            @endphp

                            {{-- Formulario Desplegable --}}
                            <div x-show="openForm" x-transition
                                class="mb-8 bg-indigo-50 dark:bg-indigo-900/20 p-5 rounded-2xl border border-indigo-100 dark:border-indigo-800 relative">
                                <div class="absolute top-0 right-0 -mt-2 -mr-2">
                                    <span class="flex h-3 w-3">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                                    </span>
                                </div>

                                <h4
                                    class="text-sm font-bold text-indigo-800 dark:text-indigo-300 mb-4 uppercase tracking-wide">
                                    Nuevo Criterio</h4>

                                <form action="{{ route('admin.eventos.criterios.store', $evento) }}" method="POST"
                                    class="flex flex-col md:flex-row gap-4 items-end">
                                    @csrf
                                    <div class="flex-1 w-full">
                                        <label
                                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nombre</label>
                                        <input type="text" name="nombre" placeholder="Ej. Innovación, Diseño..."
                                            required
                                            class="w-full rounded-xl border-gray-300 dark:bg-gray-800 dark:border-gray-600 text-sm focus:ring-indigo-500">
                                    </div>
                                    <div class="w-full md:w-32">
                                        <label
                                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Peso
                                            (%)</label>
                                        <input type="number" name="ponderacion" min="1"
                                            max="{{ $disponible }}" placeholder="Máx {{ $disponible }}" required
                                            class="w-full rounded-xl border-gray-300 dark:bg-gray-800 dark:border-gray-600 text-sm focus:ring-indigo-500">
                                    </div>
                                    <button type="submit"
                                        class="w-full md:w-auto px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition shadow-md">
                                        Guardar
                                    </button>
                                </form>
                            </div>

                            {{-- Resumen Visual (Donut Chart y Stats) --}}
                            <div
                                class="flex flex-col md:flex-row gap-8 mb-8 items-center justify-center p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <div class="relative w-24 h-24 flex-shrink-0">
                                    <svg class="w-full h-full transform -rotate-90">
                                        <circle cx="48" cy="48" r="40" stroke="currentColor"
                                            stroke-width="8" fill="transparent"
                                            class="text-gray-200 dark:text-gray-700" />
                                        <circle cx="48" cy="48" r="40" stroke="currentColor"
                                            stroke-width="8" fill="transparent" stroke-dasharray="251.2"
                                            stroke-dashoffset="{{ 251.2 - (251.2 * $sumaTotal) / 100 }}"
                                            class="{{ $disponible == 0 ? 'text-green-500' : 'text-indigo-500' }} transition-all duration-1000" />
                                    </svg>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $sumaTotal }}%
                                    </div>
                                </div>

                                <div class="text-center md:text-left">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Estado de la Rúbrica
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xs">
                                        @if ($disponible == 0)
                                            <span
                                                class="text-green-600 dark:text-green-400 font-bold">¡Completa!</span>
                                            La suma de criterios es 100%. Todo listo para evaluar.
                                        @else
                                            <span
                                                class="text-yellow-600 dark:text-yellow-400 font-bold">Incompleta.</span>
                                            Tienes un <span class="font-bold">{{ $disponible }}% disponible</span>
                                            para asignar.
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Lista de Criterios --}}
                            @if ($evento->criterios->isEmpty())
                                <div
                                    class="text-center py-12 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl">
                                    <div
                                        class="bg-gray-100 dark:bg-gray-800 p-3 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-gray-900 dark:text-white font-bold text-sm">Sin Criterios</h3>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Este evento aún no tiene
                                        reglas de evaluación.</p>
                                </div>
                            @else
                                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($evento->criterios as $criterio)
                                        <div
                                            class="group p-4 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors flex items-center justify-between">

                                            {{-- Info Izquierda --}}
                                            <div class="flex items-center gap-4 w-full">
                                                <div
                                                    class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-800 dark:text-white font-black text-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                                    {{ $criterio->ponderacion }}<span
                                                        class="text-[10px] align-top">%</span>
                                                </div>

                                                <div class="flex-1">
                                                    <div class="flex justify-between items-center h-8">
                                                        {{-- Altura fija para alinear botones --}}
                                                        <h4 class="font-bold text-gray-900 dark:text-gray-200">
                                                            {{ $criterio->nombre }}</h4>

                                                        {{-- ACCIONES (Editar + Eliminar) --}}
                                                        <div
                                                            class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">

                                                            {{-- Botón EDITAR --}}
                                                            <a href="{{ route('admin.criterios.edit', $criterio) }}" class="p-2 text-gray-400 dark:hover:bg-900/20 rounded-lg transition-colors">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                                    </path>
                                                                </svg>
                                                            </a>
                                                                

                                                            {{-- Botón ELIMINAR --}}
                                                            <form
                                                                action="{{ route('admin.criterios.destroy', $criterio->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('¿Eliminar este criterio?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                                    title="Eliminar Criterio">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    {{-- Barra Visual --}}
                                                    <div
                                                        class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mt-2 overflow-hidden">
                                                        <div class="h-full bg-indigo-500 rounded-full"
                                                            style="width: {{ $criterio->ponderacion }}%"></div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
