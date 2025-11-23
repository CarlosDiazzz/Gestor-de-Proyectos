<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel del Participante') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. BARRA DE BIENVENIDA --}}
            <div
                class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 {{ $evento_inscrito ? 'border-indigo-500' : 'border-green-500' }}">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Hola, {{ Auth::user()->name }} 👋
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @if ($evento_inscrito)
                                Participando en: <span
                                    class="font-bold text-indigo-600 dark:text-indigo-400">{{ $evento_inscrito->nombre }}</span>
                            @else
                                ¡Bienvenido! Hay <span class="font-bold text-green-600">{{ $eventos_disponibles_count }}
                                    convocatorias abiertas</span>.
                            @endif
                        </p>
                    </div>
                    @if (!$equipo && $eventos_disponibles_count > 0)
                        <a href="{{ route('participante.equipos.create') }}"
                            class="hidden md:inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                            Iniciar Registro
                        </a>
                    @endif
                </div>
            </div>

            @if ($equipo)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-2 space-y-6">

                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                            <div
                                class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/20">
                                <div>
                                    <h3
                                        class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        {{ $equipo->nombre }}
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 uppercase tracking-wide font-bold">Inscrito</span>
                                    </h3>
                                </div>

                                @php
                                    $mi_participacion = $equipo->participantes->where('user_id', Auth::id())->first();
                                    $soy_lider =
                                        $mi_participacion &&
                                        ($mi_participacion->pivot->perfil_id == 3 ||
                                            $mi_participacion->pivot->es_lider);
                                @endphp

                                <div class="flex gap-2">
                                    @if ($soy_lider)
                                        <a href="{{ route('participante.equipos.edit', $equipo) }}"
                                            class="text-xs bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded transition font-bold flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Gestionar
                                        </a>
                                    @endif
                                    <a href="{{ route('participante.avances.index') }}"
                                        class="text-xs bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 text-indigo-700 dark:text-indigo-300 px-3 py-1.5 rounded transition font-bold flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Avances
                                    </a>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="mb-8">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Proyecto
                                        Registrado</h4>
                                    <div
                                        class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-5 border border-gray-100 dark:border-gray-700">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h2 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                                                    {{ $proyecto->nombre ?? 'Sin definir' }}
                                                </h2>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                                    {{ $proyecto->descripcion ?? 'Agrega una descripción para los jueces.' }}
                                                </p>
                                            </div>
                                            @if ($proyecto && $proyecto->repositorio_url)
                                                <a href="{{ $proyecto->repositorio_url }}" target="_blank"
                                                    class="flex-shrink-0 ml-4 p-2 bg-white dark:bg-gray-800 rounded-full border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-indigo-600 transition"
                                                    title="Ver Repositorio">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>

                                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <h5
                                                class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-3 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                </svg>
                                                Criterios a Evaluar
                                            </h5>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                @if ($evento_inscrito && $evento_inscrito->criterios->count() > 0)
                                                    @foreach ($evento_inscrito->criterios as $criterio)
                                                        <div
                                                            class="bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600 text-center">
                                                            <span
                                                                class="block text-xs font-medium text-gray-600 dark:text-gray-300 truncate"
                                                                title="{{ $criterio->nombre }}">{{ $criterio->nombre }}</span>
                                                            <span
                                                                class="block text-[10px] text-indigo-500 font-bold">{{ $criterio->ponderacion }}%</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-xs text-gray-400 italic col-span-3">Criterios no
                                                        definidos aún.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Integrantes</h4>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($equipo->participantes as $miembro)
                                            <div
                                                class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 px-3 py-1.5 rounded-full border border-gray-100 dark:border-gray-600">
                                                <div
                                                    class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-[10px] font-bold text-indigo-700 dark:text-indigo-300">
                                                    {{ substr($miembro->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ explode(' ', $miembro->user->name)[0] }}</span>
                                                    <span
                                                        class="text-[9px] text-gray-500 dark:text-gray-400">{{ $miembro->pivot->perfil->nombre ?? 'Miembro' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 flex flex-col justify-center min-h-[200px]">
                            <h3
                                class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center uppercase tracking-wider">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Retroalimentación de los Jueces
                            </h3>

                            @if ($proyecto && $proyecto->comentarios->isNotEmpty())
                                <div class="space-y-4">
                                    @foreach ($proyecto->comentarios as $comentario)
                                        <div
                                            class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800/50 relative">
                                            <div class="absolute top-0 left-0 w-1 h-full bg-blue-400 rounded-l-lg">
                                            </div>
                                            <p class="text-xs font-bold text-blue-600 dark:text-blue-300 mb-1">
                                                Juez {{ $loop->iteration }} <span
                                                    class="text-gray-400 font-normal ml-2">•
                                                    {{ $comentario->created_at->diffForHumans() }}</span>
                                            </p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 italic leading-relaxed">
                                                "{{ $comentario->comentario }}"
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- ESTADO VACÍO (Sin comentarios) --}}
                                <div
                                    class="flex flex-col items-center justify-center py-8 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                        </path>
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Sin comentarios aún
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1 text-center max-w-xs">
                                        Los comentarios aparecerán aquí cuando los jueces evalúen tu proyecto.
                                    </p>
                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="lg:col-span-1 space-y-6">

                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-xs font-bold text-gray-400 uppercase mb-4">Resultados Preliminares</h3>
                            <div class="relative h-48 flex items-center justify-center">
                                <canvas id="projectProgressChart"></canvas>
                                <div id="no-grades-msg"
                                    class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                                    <p
                                        class="text-sm text-gray-400 font-medium bg-white dark:bg-gray-800 px-2 py-1 rounded opacity-80">
                                        Esperando evaluación...</p>
                                </div>
                            </div>
                            <div
                                class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <span class="text-xs text-gray-500">Calificación Final:</span>
                                <span
                                    class="font-bold text-lg text-indigo-600 dark:text-indigo-400">{{ number_format($puntajeTotal * 10, 1) }}/100</span>
                            </div>
                        </div>

                        @if ($equipo && $proyecto && $puntajeTotal > 0)
                            <div
                                class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-2 uppercase">
                                    Constancias</h3>
                                <div class="flex flex-col gap-2 mt-4">
                                    <a href="{{ route('participante.constancia.imprimir', 'individual') }}"
                                        target="_blank"
                                        class="flex items-center justify-center w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-500 rounded-md font-bold text-xs uppercase transition">
                                        Personal
                                    </a>
                                    <a href="{{ route('participante.constancia.imprimir', 'equipo') }}"
                                        target="_blank"
                                        class="flex items-center justify-center w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md font-bold text-xs uppercase transition shadow-md">
                                        Del Equipo
                                    </a>
                                </div>
                            </div>
                        @endif





                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-4 uppercase">
                                    Eventos
                                    Próximos</h3>

                                @if ($eventos_proximos->isEmpty())
                                    <div class="text-center py-4">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">No hay eventos en
                                            agenda.</p>
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($eventos_proximos as $evento)
                                            <div
                                                class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-100 dark:border-gray-700">
                                                {{-- FECHA (Caja pequeña) --}}
                                                <div
                                                    class="bg-white dark:bg-gray-800 p-2 rounded text-center border border-gray-200 dark:border-gray-600 min-w-[45px]">
                                                    <span
                                                        class="block text-[10px] text-indigo-500 font-bold uppercase tracking-tighter">
                                                        {{ \Carbon\Carbon::parse($evento->fecha_inicio)->locale('es')->shortMonthName }}
                                                    </span>
                                                    <span
                                                        class="block text-lg font-bold text-gray-800 dark:text-gray-200 leading-none">
                                                        {{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d') }}
                                                    </span>
                                                </div>

                                                {{-- DETALLES --}}
                                                <div class="overflow-hidden">
                                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate"
                                                        title="{{ $evento->nombre }}">
                                                        {{ $evento->nombre }}
                                                    </h4>
                                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                        Cierra:
                                                        {{ \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Create Team Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm hover:shadow-md transition border border-transparent hover:border-green-500 group relative overflow-hidden">
                            {{-- ... (Content from your previous code) ... --}}
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-green-50 dark:bg-green-900/20 rounded-full opacity-50 transition-transform group-hover:scale-150">
                            </div>
                            <div class="relative z-10">
                                <div
                                    class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4 text-green-600 dark:text-green-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Crear Equipo</h3>
                                <p class="text-sm text-gray-500 mb-6 mt-2">Lidera un proyecto. Tú registras la idea y
                                    reclutas a los miembros.</p>
                                <a href="{{ route('participante.equipos.create') }}"
                                    class="inline-flex items-center text-sm font-bold text-green-600 hover:text-green-700">Comenzar
                                    Registro &rarr;</a>
                            </div>
                        </div>

                        {{-- Join Team Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm hover:shadow-md transition border border-transparent hover:border-blue-500 group relative overflow-hidden">
                            {{-- ... (Content from your previous code) ... --}}
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full opacity-50 transition-transform group-hover:scale-150">
                            </div>
                            <div class="relative z-10">
                                <div
                                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4 text-blue-600 dark:text-blue-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Unirme a Equipo</h3>
                                <p class="text-sm text-gray-500 mb-6 mt-2">Busca vacantes en equipos existentes y
                                    postúlate con tu perfil.</p>
                                <a href="{{ route('participante.equipos.join') }}"
                                    class="inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-700">Ver
                                    Lista &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-4 uppercase">Eventos
                                Próximos</h3>

                            @if ($eventos_proximos->isEmpty())
                                <div class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">No hay eventos en agenda.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($eventos_proximos as $evento)
                                        <div
                                            class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-100 dark:border-gray-700">
                                            {{-- FECHA (Caja pequeña) --}}
                                            <div
                                                class="bg-white dark:bg-gray-800 p-2 rounded text-center border border-gray-200 dark:border-gray-600 min-w-[45px]">
                                                <span
                                                    class="block text-[10px] text-indigo-500 font-bold uppercase tracking-tighter">
                                                    {{ \Carbon\Carbon::parse($evento->fecha_inicio)->locale('es')->shortMonthName }}
                                                </span>
                                                <span
                                                    class="block text-lg font-bold text-gray-800 dark:text-gray-200 leading-none">
                                                    {{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d') }}
                                                </span>
                                            </div>

                                            {{-- DETALLES --}}
                                            <div class="overflow-hidden">
                                                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate"
                                                    title="{{ $evento->nombre }}">
                                                    {{ $evento->nombre }}
                                                </h4>
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                    Cierra:
                                                    {{ \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Script Gráfico (Mismo de siempre) --}}
    @if ($equipo && $proyecto)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('projectProgressChart').getContext('2d');
                const labels = @json($chartLabels ?? []);
                const data = @json($chartData ?? []);
                const totalScore = data.reduce((a, b) => a + b, 0);
                if (totalScore > 0) document.getElementById('no-grades-msg').style.display = 'none';
                const textColor = '#cbd5e1';
                const gridColor = 'rgba(148, 163, 184, 0.2)';
                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Puntaje',
                            data: data,
                            backgroundColor: 'rgba(99, 102, 241, 0.2)',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                            pointBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                angleLines: {
                                    color: gridColor
                                },
                                grid: {
                                    color: gridColor
                                },
                                pointLabels: {
                                    color: textColor,
                                    font: {
                                        size: 10,
                                        family: "'Figtree', sans-serif",
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    color: textColor,
                                    backdropColor: 'transparent',
                                    showLabelBackdrop: false
                                },
                                suggestMin: 0,
                                suggestMax: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
        </script>
    @endif
</x-app-layout>
