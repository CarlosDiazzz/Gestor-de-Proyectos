<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registro de Equipo y Proyecto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('participante.equipos.store') }}">
                @csrf

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            1. Configuración del Equipo
                        </h3>
                    </div>
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                        <div>
                            <x-input-label for="evento_id" :value="__('Evento al que participan')" />
                            <select id="evento_id" name="evento_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">-- Selecciona el evento --</option>
                                @foreach($eventosDisponibles as $evento)
                                    <option value="{{ $evento->id }}" {{ old('evento_id') == $evento->id ? 'selected' : '' }}>
                                        {{ $evento->nombre }} (Cierra: {{ \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m') }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('evento_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nombre_equipo" :value="__('Nombre del Equipo')" />
                            <x-text-input id="nombre_equipo" class="block mt-1 w-full" type="text" name="nombre_equipo" :value="old('nombre_equipo')" required placeholder="Ej. Alpha Devs" />
                            <x-input-error :messages="$errors->get('nombre_equipo')" class="mt-2" />
                        </div>

                        {{-- Configuración de Vacantes --}}
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-xl border border-gray-200 dark:border-gray-600">
                            <h4 class="text-md font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Configuración de Vacantes del Equipo
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Define cuántos miembros de cada rol necesitas. Tú serás el <strong>Líder</strong> automáticamente.
                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Total máximo: 4 miembros</span> (más tú como Líder = 5 total)
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- Programadores --}}
                                <div>
                                    <label for="max_programadores" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        👨‍💻 Programadores
                                    </label>
                                    <select id="max_programadores" name="max_programadores" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500"
                                            onchange="validarTotalVacantes()">
                                        @for($i = 0; $i <= 4; $i++)
                                            <option value="{{ $i }}" {{ old('max_programadores', 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Diseñadores --}}
                                <div>
                                    <label for="max_disenadores" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        🎨 Diseñadores
                                    </label>
                                    <select id="max_disenadores" name="max_disenadores" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500"
                                            onchange="validarTotalVacantes()">
                                        @for($i = 0; $i <= 4; $i++)
                                            <option value="{{ $i }}" {{ old('max_disenadores', 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Testers --}}
                                <div>
                                    <label for="max_testers" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        🧪 Testers
                                    </label>
                                    <select id="max_testers" name="max_testers" 
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500"
                                            onchange="validarTotalVacantes()">
                                        @for($i = 0; $i <= 4; $i++)
                                            <option value="{{ $i }}" {{ old('max_testers', 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Contador Total --}}
                            <div class="mt-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total de vacantes:</span>
                                    <span id="total-vacantes" class="text-lg font-bold text-indigo-600 dark:text-indigo-400">0</span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Máximo permitido: 4 (más tú como Líder = 5 total)
                                </div>
                                <div id="error-vacantes" class="text-sm text-red-600 dark:text-red-400 mt-2 hidden">
                                    ⚠️ El total no puede exceder 4 vacantes
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('max_programadores')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4 border-indigo-500">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            2. Definición del Proyecto
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 ml-7">Estos datos pueden editarse más adelante.</p>
                    </div>
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                        
                        <div>
                            <x-input-label for="nombre_proyecto" :value="__('Título del Proyecto')" />
                            <x-text-input id="nombre_proyecto" class="block mt-1 w-full" type="text" name="nombre_proyecto" :value="old('nombre_proyecto')" required placeholder="Ej. Sistema de Riego IoT" />
                            <x-input-error :messages="$errors->get('nombre_proyecto')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="descripcion_proyecto" :value="__('Descripción Breve')" />
                            <textarea id="descripcion_proyecto" name="descripcion_proyecto" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required placeholder="Describe el problema y tu solución...">{{ old('descripcion_proyecto') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion_proyecto')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="repositorio_url" :value="__('Enlace al Repositorio (GitHub/GitLab)')" />
                            <x-text-input id="repositorio_url" class="block mt-1 w-full" type="url" name="repositorio_url" :value="old('repositorio_url')" placeholder="https://github.com/usuario/repo" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Opcional. Puedes agregarlo más tarde.</p>
                            <x-input-error :messages="$errors->get('repositorio_url')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('participante.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline mr-4">Cancelar</a>
                    <x-primary-button class="ml-3">
                        {{ __('Registrar Todo') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function validarTotalVacantes() {
        const programadores = parseInt(document.getElementById('max_programadores')?.value) || 0;
        const disenadores = parseInt(document.getElementById('max_disenadores')?.value) || 0;
        const testers = parseInt(document.getElementById('max_testers')?.value) || 0;
        
        const total = programadores + disenadores + testers;
        const totalElement = document.getElementById('total-vacantes');
        if (totalElement) {
            totalElement.textContent = total;
        }
        
        const errorDiv = document.getElementById('error-vacantes');
        const submitBtn = document.querySelector('button[type="submit"], .ml-3');
        
        if (total > 4) {
            errorDiv?.classList.remove('hidden');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        } else {
            errorDiv?.classList.add('hidden');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', validarTotalVacantes);
    </script>
    @endpush
</x-app-layout>