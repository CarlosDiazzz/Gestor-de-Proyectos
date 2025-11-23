<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Evaluación de Proyecto
        </h2>
    </x-slot>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Inicializamos Alpine con los datos del servidor --}}
    <div class="py-12" x-data="evaluador({
        criterios: {{ $proyecto->evento->criterios->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre, 'peso' => $c->ponderacion]) }},
        previas: {{ json_encode($calificacionesPrevias) }}
    })">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- COLUMNA IZQUIERDA: RÚBRICA (SLIDERS) --}}
                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $proyecto->nombre }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $proyecto->descripcion }}</p>
                        @if ($proyecto->repositorio_url)
                            <a href="{{ $proyecto->repositorio_url }}" target="_blank"
                                class="inline-flex items-center mt-4 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                Ver Repositorio
                            </a>
                        @endif
                    </div>

                    <form id="evalForm" method="POST" action="{{ route('juez.evaluaciones.store', $proyecto) }}">
                        @csrf

                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                            <h4
                                class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-6 border-b dark:border-gray-700 pb-2">
                                Rúbrica de Evaluación</h4>

                            <div class="space-y-8">
                                <template x-for="criterio in criterios" :key="criterio.id">
                                    <div class="border-b border-gray-100 dark:border-gray-700 pb-6 last:border-0">

                                        <div class="flex justify-between items-center mb-4">
                                            <div>
                                                <label class="text-lg font-medium text-gray-800 dark:text-gray-200"
                                                    x-text="criterio.nombre"></label>
                                                <span
                                                    class="ml-2 text-xs font-bold bg-indigo-100 text-indigo-700 px-2 py-1 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                                    Peso: <span x-text="criterio.peso"></span>%
                                                </span>
                                            </div>
                                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                                <span x-text="scores[criterio.id]"></span><span
                                                    class="text-sm text-gray-400">/100</span>
                                            </div>
                                        </div>


                                        <input type="range" min="0" max="100"
                                            x-model.number="scores[criterio.id]" @input="updateChart()"
                                            :name="'puntuaciones[' + criterio.id + ']'"
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600 dark:bg-gray-700">

                                        <div class="flex justify-between text-xs text-gray-400 mt-2">
                                            <span>Deficiente (0)</span>
                                            <span>Regular (50)</span>
                                            <span>Excelente (100)</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mt-6">
                            <h4
                                class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 border-b dark:border-gray-700 pb-2">
                                Retroalimentación Cualitativa
                            </h4>

                            <div class="mb-4">
                                <x-input-label for="comentario" :value="__('Comentarios y Recomendaciones para el Equipo')" />
                                <textarea id="comentario" name="comentario" rows="4"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                    placeholder="Escribe aquí tus observaciones sobre fortalezas y áreas de mejora...">{{ old('comentario', $comentarioTexto) }}</textarea>
                                <p class="text-xs text-gray-500 mt-2">Este comentario será visible para los alumnos.</p>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- COLUMNA DERECHA: RESUMEN EN TIEMPO REAL --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 sticky top-6">
                        <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4">Resumen de
                            Calificación</h3>

                        <div class="relative h-48 mb-6">
                            <canvas id="realTimeChart"></canvas>
                        </div>

                        <div class="text-center border-t border-gray-100 dark:border-gray-700 pt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nota Final Ponderada</p>
                            <div class="text-5xl font-black text-indigo-600 dark:text-indigo-400 mt-2"
                                x-text="calculateTotal()"></div>
                            <p class="text-xs text-gray-400 mt-1">sobre 100 puntos</p>
                        </div>

                        <div class="mt-8">
                            <button type="submit" form="evalForm"
                                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-lg transform hover:scale-105 transition duration-150 ease-in-out">
                                Guardar Evaluación
                            </button>
                            <a href="{{ route('juez.dashboard') }}"
                                class="block text-center mt-4 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script de Lógica Alpine + Chart.js --}}
    <script>
        function evaluador(data) {
            return {
                criterios: data.criterios,
                scores: {},
                chart: null,

                init() {
                    // 1. Inicializar puntuaciones (con previos o 0)
                    this.criterios.forEach(c => {
                        this.scores[c.id] = data.previas[c.id] ? parseInt(data.previas[c.id]) : 0;
                    });

                    // 2. Inicializar Gráfico
                    this.initChart();
                },

                // Calcular promedio ponderado en tiempo real
                calculateTotal() {
                    let total = 0;
                    this.criterios.forEach(c => {
                        let puntos = this.scores[c.id] || 0;
                        total += (puntos * c.peso) / 100;
                    });
                    return total.toFixed(1);
                },

                initChart() {
                    const ctx = document.getElementById('realTimeChart').getContext('2d');
                    const labels = this.criterios.map(c => c.nombre);

                    const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    const textColor = isDark ? '#cbd5e1' : '#4b5563';

                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: this.getChartData(),
                                backgroundColor: [
                                    '#6366f1', '#ec4899', '#10b981', '#f59e0b', '#3b82f6'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true
                                }
                            }
                        }
                    });
                },

                getChartData() {
                    return this.criterios.map(c => this.scores[c.id]);
                },

                updateChart() {
                    if (this.chart) {
                        this.chart.data.datasets[0].data = this.getChartData();
                        this.chart.update();
                    }
                }
            }
        }
    </script>
</x-app-layout>
