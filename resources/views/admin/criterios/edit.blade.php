<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Criterio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Editando criterio para el evento: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $criterio->evento->nombre }}</span>
                </div>

                <form method="POST" action="{{ route('admin.criterios.update', $criterio) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="nombre" :value="__('Nombre del Criterio')" />
                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $criterio->nombre)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="ponderacion" :value="__('Ponderación (%)')" />
                        <x-text-input id="ponderacion" class="block mt-1 w-full" type="number" name="ponderacion" :value="old('ponderacion', $criterio->ponderacion)" min="1" max="100" required />
                        <x-input-error :messages="$errors->get('ponderacion')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ url()->previous() }}" class="text-gray-600 dark:text-gray-400 underline text-sm hover:text-gray-900 dark:hover:text-gray-100">Cancelar</a>
                        
                        <x-primary-button>
                            {{ __('Actualizar') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>