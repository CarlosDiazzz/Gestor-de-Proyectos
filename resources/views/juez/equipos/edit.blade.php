<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Equipo: {{ $equipo->nombre }}
            </h2>
            <a href="{{ route('juez.evento.show', $equipo->evento_id ?? $equipo->proyecto->evento_id) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                &larr; Volver al Evento
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="teamManager({{ $candidatos }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 border-b dark:border-gray-700 pb-2">Datos Generales</h3>
                
                <form action="{{ route('juez.equipos.update', $equipo) }}" method="POST" class="flex gap-4 items-end">
                    @csrf @method('PUT')
                    
                    <div class="w-full md:w-1/2">
                        <x-input-label for="nombre" :value="__('Nombre del Equipo')" />
                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="$equipo->nombre" required />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <x-primary-button>Actualizar Nombre</x-primary-button>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 h-fit">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 uppercase">Agregar Integrante</h3>
                        
                        @if($equipo->participantes->count() < 5)
                            <div class="relative" x-data="{ open: false }">
                                <label class="text-xs text-gray-500 mb-1 block uppercase font-bold">Buscar Alumno</label>
                                <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                       placeholder="Escribe nombre..." 
                                       class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-white text-sm py-2 pl-3">
                                
                                <div x-show="search.length > 0 && filteredParticipants.length > 0" 
                                     class="absolute z-50 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-xl max-h-48 overflow-y-auto mt-2"
                                     style="display: none;">
                                    <template x-for="p in filteredParticipants" :key="p.id">
                                        <div @click="selectParticipant(p); open = false" class="p-2 hover:bg-indigo-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200" x-text="p.name"></p>
                                                <p class="text-[10px] text-gray-500" x-text="p.carrera"></p>
                                            </div>
                                            <span class="text-[10px] bg-green-100 text-green-800 px-1 rounded">Libre</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('juez.equipos.addMember', $equipo) }}" x-show="selectedId !== null" class="mt-4 bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded border border-indigo-100 dark:border-indigo-800">
                                @csrf
                                <input type="hidden" name="participante_id" x-model="selectedId">
                                
                                <div class="mb-3">
                                    <p class="text-xs text-indigo-500 font-bold uppercase">Seleccionado:</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="selectedName"></p>
                                    <button type="button" @click="resetSelection()" class="text-[10px] text-red-500 underline">Cambiar</button>
                                </div>

                                <div class="mb-3">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Asignar Rol</label>
                                    <select name="perfil_id" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-xs py-1.5">
                                        @foreach($perfiles as $perfil)
                                            <option value="{{ $perfil->id }}">{{ $perfil->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <x-primary-button class="w-full justify-center text-xs">Confirmar</x-primary-button>
                            </form>
                        @else
                            <div class="bg-yellow-50 p-3 rounded text-center text-xs text-yellow-700">Equipo lleno (5/5).</div>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 uppercase border-b dark:border-gray-700 pb-2">Integrantes Actuales</h3>
                        
                        <div class="space-y-3">
                            @foreach($equipo->participantes as $miembro)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                                            {{ substr($miembro->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                                {{ $miembro->user->name }}
                                                @if($miembro->pivot->perfil_id == 3 || $miembro->pivot->es_lider)
                                                    <span class="ml-2 text-[10px] bg-green-100 text-green-800 px-1.5 py-0.5 rounded-full">Líder</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $miembro->carrera->nombre ?? 'N/A' }} • 
                                                <span class="text-indigo-500 font-medium">
                                                    {{ \App\Models\Perfil::find($miembro->pivot->perfil_id)->nombre ?? 'Rol' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <form action="{{ route('juez.equipos.removeMember', [$equipo, $miembro->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar este miembro?');">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition" title="Eliminar del equipo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script Alpine --}}
    <script>
        function teamManager(participantsData) {
            return {
                search: '',
                participants: participantsData,
                selectedId: null,
                selectedName: '',

                get filteredParticipants() {
                    if (this.search === '') return [];
                    const query = this.search.toLowerCase();
                    return this.participants.filter(p => p.name.toLowerCase().includes(query) || p.no_control.toLowerCase().includes(query));
                },
                selectParticipant(p) {
                    this.selectedId = p.id;
                    this.selectedName = p.name;
                    this.search = '';
                },
                resetSelection() {
                    this.selectedId = null;
                    this.selectedName = '';
                }
            }
        }
    </script>
</x-app-layout>