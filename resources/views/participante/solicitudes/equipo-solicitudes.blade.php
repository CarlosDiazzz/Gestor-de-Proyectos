<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $equipo->nombre }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Solicitudes de Unión Pendientes</p>
            </div>
            <a href="{{ route('participante.dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 text-green-700 dark:text-green-300 text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 text-red-700 dark:text-red-300 text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($solicitudes->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7-4a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No hay solicitudes pendientes</h3>
                    <p class="text-gray-500 dark:text-gray-400">Todos los candidatos han sido procesados o no hay nuevas solicitudes.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($solicitudes as $solicitud)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $solicitud->participante->user->name }}
                                        </h3>
                                        <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                            <p><span class="font-semibold">Email:</span> {{ $solicitud->participante->user->email }}</p>
                                            <p><span class="font-semibold">No. Control:</span> {{ $solicitud->participante->no_control ?? 'N/A' }}</p>
                                            <p><span class="font-semibold">Carrera:</span> {{ $solicitud->participante->carrera->nombre ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mensaje de la Solicitud --}}
                                @if($solicitud->mensaje)
                                    <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <p class="text-sm text-blue-800 dark:text-blue-300">
                                            <span class="font-semibold">Mensaje:</span><br>
                                            {{ $solicitud->mensaje }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Rol Solicitado --}}
                                @if($solicitud->perfil_solicitado_id)
                                    <div class="mb-4 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                        <p class="text-sm text-purple-800 dark:text-purple-300">
                                            <span class="font-semibold">Rol Solicitado:</span> {{ $solicitud->perfilSugerido->nombre ?? 'N/A' }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Acciones con opción de cambiar rol --}}
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-4">
                                    <form method="POST" action="{{ route('participante.solicitudes.aceptar', $solicitud) }}" class="space-y-3">
                                        @csrf
                                        
                                        {{-- Selector de rol para el líder (opcional) --}}
                                        <div>
                                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 block">Rol a Asignar (opcional - si no especificas, se usará el solicitado)</label>
                                            <select name="perfil_id" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-white text-sm p-2 focus:ring-indigo-500">
                                                <option value="">-- Usar rol solicitado --</option>
                                                @php
                                                    $rolesEquipo = $solicitud->equipo->getRolesDisponibles();
                                                @endphp
                                                @foreach($rolesEquipo as $rol)
                                                    <option value="{{ $rol['id'] }}">
                                                        {{ $rol['nombre'] }} ({{ $rol['disponibles'] }}/{{ $rol['total'] }} vacantes)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button type="submit" class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-lg shadow-md hover:shadow-lg transition-all inline-flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Aceptar
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('participante.solicitudes.rechazar', $solicitud) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-lg shadow-md hover:shadow-lg transition-all inline-flex items-center justify-center gap-2" onclick="return confirm('¿Estás seguro de rechazar esta solicitud?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Paginación --}}
                    <div class="mt-6">
                        {{ $solicitudes->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
