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

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('participante.dashboard') }}" class="flex justify-center rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 px-6 font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancelar
                    </a>
                    <button type="submit" class="flex justify-center rounded bg-indigo-600 py-2 px-6 font-medium text-white hover:bg-opacity-90 hover:bg-indigo-700 transition">
                        {{ __('Registrar Todo') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>