<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Solicitar Unión a Equipo
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $equipo->nombre }}</p>
            </div>
            <a href="{{ route('participante.equipos.join') }}" 
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                
                {{-- Decoración Superior --}}
                <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                <div class="p-8">
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Enviar Solicitud de Unión</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">El líder del equipo revisará tu solicitud y decidirá si te acepta.</p>
                    </div>

                    {{-- Información del Equipo --}}
                    <div class="mb-8 p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $equipo->nombre }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    Proyecto: <span class="font-semibold">{{ $equipo->proyecto->nombre ?? 'Sin proyecto' }}</span>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    Integrantes: <span class="font-semibold">{{ $equipo->participantes->count() }}/5</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('participante.solicitudes.crear', $equipo) }}" class="space-y-6">
                        @csrf

                        {{-- Información del Solicitante --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Tu Información</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-1">Nombre</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $participante->user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-1">Email</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $participante->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-1">No. Control</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $participante->no_control ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-1">Carrera</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $participante->carrera->nombre ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Selección de Rol --}}
                        <div>
                            <x-input-label for="perfil_solicitado_id" value="Rol que deseas ocupar" class="mb-2 font-bold" />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">Selecciona el rol en el que quieres participar en el equipo</p>
                            
                            @php
                                $rolesDisponibles = $equipo->getRolesDisponibles();
                            @endphp

                            @if(count($rolesDisponibles) > 0)
                                <div class="space-y-2">
                                    @foreach($rolesDisponibles as $rol)
                                        <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-500 transition-all group {{ old('perfil_solicitado_id') == $rol['id'] ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-700/30' }}">
                                            <input type="radio" name="perfil_solicitado_id" value="{{ $rol['id'] }}" 
                                                   class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                                                   {{ old('perfil_solicitado_id') == $rol['id'] ? 'checked' : '' }}
                                                   required>
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-gray-900 dark:text-white">
                                                        @if($rol['id'] == 1)
                                                            👨‍💻 {{ $rol['nombre'] }}
                                                        @elseif($rol['id'] == 2)
                                                            🎨 {{ $rol['nombre'] }}
                                                        @elseif($rol['id'] == 4)
                                                            🧪 {{ $rol['nombre'] }}
                                                        @else
                                                            {{ $rol['nombre'] }}
                                                        @endif
                                                    </span>
                                                    <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $rol['disponibles'] == 1 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                                        {{ $rol['disponibles'] }} {{ $rol['disponibles'] == 1 ? 'vacante' : 'vacantes' }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $rol['disponibles'] }}/{{ $rol['total'] }} disponibles
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                                    <p class="text-sm text-red-700 dark:text-red-400">
                                        ⚠️ Este equipo no tiene vacantes disponibles en este momento.
                                    </p>
                                </div>
                            @endif
                            
                            <x-input-error :messages="$errors->get('perfil_solicitado_id')" class="mt-2" />
                        </div>

                        {{-- Mensaje de Solicitud --}}
                        <div>
                            <x-input-label for="mensaje" value="Mensaje (Opcional)" class="mb-2 font-bold" />
                            <textarea id="mensaje" name="mensaje" rows="5" 
                                class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm p-4 text-sm leading-relaxed placeholder-gray-400 transition-all"
                                placeholder="Cuéntale al líder por qué quieres unirte a su equipo. Puedes mencionar tus habilidades, experiencia, o motivación...">{{ old('mensaje') }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Máximo 500 caracteres</p>
                            <x-input-error :messages="$errors->get('mensaje')" class="mt-2" />
                        </div>

                        {{-- Info Box --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 flex gap-3 items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                Después de enviar tu solicitud, el líder del equipo la revisará. Recibirás una notificación cuando responda.
                            </p>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('participante.equipos.join') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Enviar Solicitud
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
