<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Encabezado --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Catálogo de Carreras</h2>
                
                <a href="{{ route('admin.carreras.create') }}" 
                   class="inline-flex items-center justify-center rounded-lg bg-indigo-600 py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-10 gap-2 transition-all shadow-md hover:shadow-lg">
                   <span>+</span> Nueva Carrera
                </a>
            </div>

            {{-- Alertas --}}
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tabla --}}
            <div class="rounded-3xl border border-gray-200 bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-gray-700 dark:bg-gray-800 sm:px-7.5 xl:pb-1 ">
                
                {{-- Buscador --}}
                <div class="mb-6">
                    <form action="{{ route('admin.carreras.index') }}" method="GET" class="relative w-full sm:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Buscar por nombre o clave..." 
                            class="w-full rounded-lg border-[1.5px] border-gray-300 bg-transparent py-2 pl-10 pr-4 font-medium outline-none transition focus:border-indigo-600 active:border-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-600">
                    </form>
                </div>

                <div class="max-w-full overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50 text-left dark:bg-gray-700/50">
                                <th class="min-w-[100px] py-4 px-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider xl:pl-11">Clave</th>
                                <th class="min-w-[220px] py-4 px-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">Nombre de la Carrera</th>
                                <th class="min-w-[120px] py-4 px-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">Alumnos</th>
                                <th class="py-4 px-4 font-bold text-gray-500 dark:text-gray-400 text-right uppercase text-xs tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($carreras as $carrera)
                                <tr class="border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-5 px-4 pl-9 xl:pl-11">
                                        <span class="inline-block rounded bg-gray-100 px-2.5 py-0.5 text-sm font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                                            {{ $carrera->clave }}
                                        </span>
                                    </td>
                                    <td class="py-5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-indigo-50 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg overflow-hidden shrink-0">
                                                {{ strtoupper(substr($carrera->nombre, 0, 1)) }}
                                            </div>
                                            <p class="text-gray-800 dark:text-white font-medium">{{ $carrera->nombre }}</p>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            {{ $carrera->participantes()->count() }} inscritos
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-right">
                                        <div class="flex items-center justify-end space-x-3.5">
                                            <a href="{{ route('admin.carreras.edit', $carrera) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 text-gray-500 transition">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                            <form action="{{ route('admin.carreras.destroy', $carrera) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="hover:text-red-600 dark:hover:text-red-400 text-gray-500 transition" onclick="return confirm('¿Eliminar esta carrera?')">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="py-6 px-4">
                    {{ $carreras->links('components.pagination') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>