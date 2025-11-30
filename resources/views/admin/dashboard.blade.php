<x-app-layout>
    {{-- El header ya está en el layout principal, así que no usamos x-slot header --}}

    {{-- CDN de Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Contenedor Principal --}}
    <div class="space-y-6">

        {{-- SECCIÓN 1: Tarjetas de Resumen (Stats Cards) --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            
            {{-- TARJETA 1: USUARIOS --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Usuarios</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $total_jueces + $total_participantes }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="flex items-center gap-1 text-sm font-medium text-green-600 dark:text-green-400">
                        {{ $total_jueces }} Jueces
                    </span>
                    <span class="text-sm text-gray-400">|</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $total_participantes }} Alumnos</span>
                </div>
            </div>

            {{-- TARJETA 2: EQUIPOS --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipos Activos</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $total_equipos }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2 py-1 rounded-md">
                        Registrados
                    </span>
                </div>
            </div>

            {{-- TARJETA 3: PROYECTOS --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyectos</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $total_proyectos }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $proyectosEvaluados }} Evaluados</span>
                    <span class="text-gray-400">{{ $proyectosPendientes }} Pendientes</span>
                </div>
            </div>

            {{-- TARJETA 4: EVENTOS --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Eventos Activos</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $eventos_activos->count() }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30 px-2 py-1 rounded-md">
                        En curso
                    </span>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: Paneles Grandes --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Gráficos (2 columnas) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Panel Gráfico 1 --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Progreso de Evaluación</h3>
                    <div class="relative h-72 w-full">
                        <canvas id="chartEvaluacion"></canvas>
                    </div>
                </div>

                {{-- Panel Gráfico 2 --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Participación por Carrera</h3>
                    <div class="relative h-64 w-full flex justify-center">
                        <canvas id="chartCarreras"></canvas>
                    </div>
                </div>
            </div>

            {{-- Calendario / Agenda (1 columna) --}}
            <div class="lg:col-span-1 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 h-fit">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Próximos Eventos</h3>
                    <a href="{{ route('admin.eventos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Ver todo</a>
                </div>

                @if($eventos_activos->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No hay eventos programados.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($eventos_activos as $evento)
                            <div class="group flex items-start gap-4 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">
                                {{-- Fecha Badge --}}
                                <div class="flex flex-col items-center justify-center h-14 w-14 rounded-lg bg-indigo-50 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-gray-600">
                                    <span class="text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->locale('es')->shortMonthName }}</span>
                                    <span class="text-xl font-bold">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d') }}</span>
                                </div>
                                
                                {{-- Info --}}
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $evento->nombre }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $evento->descripcion }}</p>
                                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Fin: {{ \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <button class="mt-6 w-full py-2.5 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 text-sm text-gray-500 dark:text-gray-400 hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition flex items-center justify-center gap-2"
                        onclick="window.location.href='{{ route('admin.eventos.create') }}'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Evento
                </button>
            </div>

        </div>
    </div>

    {{-- Script de Gráficos (Mismo de antes, solo verificando colores) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dataCarreras = @json($participantesPorCarrera);
            const evaluados = {{ $proyectosEvaluados }};
            const pendientes = {{ $proyectosPendientes }};

            // Función para color de texto dinámico
            const getTextColor = () => document.documentElement.classList.contains('dark') ? '#9ca3af' : '#64748b';
            const getGridColor = () => document.documentElement.classList.contains('dark') ? '#374151' : '#f1f5f9';

            // Gráfico 1: Barras
            new Chart(document.getElementById('chartEvaluacion'), {
                type: 'bar',
                data: {
                    labels: ['Evaluados', 'Pendientes'],
                    datasets: [{
                        label: 'Proyectos',
                        data: [evaluados, pendientes],
                        backgroundColor: ['#4f46e5', '#94a3b8'], // Indigo-600 y Slate-400
                        borderRadius: 6,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: getGridColor() },
                            ticks: { color: getTextColor() }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: getTextColor() }
                        }
                    }
                }
            });

            // Gráfico 2: Dona
            new Chart(document.getElementById('chartCarreras'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(dataCarreras),
                    datasets: [{
                        data: Object.values(dataCarreras),
                        backgroundColor: ['#6366f1', '#ec4899', '#10b981', '#f59e0b', '#3b82f6'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                color: getTextColor()
                            }
                        }
                    },
                    cutout: '75%'
                }
            });
        });
    </script>
</x-app-layout>