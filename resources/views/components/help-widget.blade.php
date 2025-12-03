<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    <!-- FAQ Menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2" @click.away="open = false"
        class="mb-4 w-80 rounded-lg border border-gray-200 bg-white p-4 shadow-xl dark:border-gray-700 dark:bg-gray-800"
        style="display: none;">
        <div class="mb-3 flex items-center justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Ayuda y FAQ</h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="space-y-2 overflow-y-auto max-h-96">
            <!-- FAQ Item 1 -->
            <div x-data="{ expanded: false }" class="rounded-md border border-gray-100 dark:border-gray-700">
                <button @click="expanded = !expanded"
                    class="flex w-full items-center justify-between bg-gray-50 px-3 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    <span>¿Cómo registrarse a un equipo?</span>
                    <svg :class="{'rotate-180': expanded}" class="h-4 w-4 transform transition-transform duration-200"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300">
                    Para unirte o crear un equipo, ve a la sección "Equipos" en el menú lateral. Si eres líder,
                    selecciona "Crear Equipo". Si deseas unirte, busca el equipo por su código o nombre y solicita
                    unirte.
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div x-data="{ expanded: false }" class="rounded-md border border-gray-100 dark:border-gray-700">
                <button @click="expanded = !expanded"
                    class="flex w-full items-center justify-between bg-gray-50 px-3 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    <span>¿Dónde ver mis resultados?</span>
                    <svg :class="{'rotate-180': expanded}" class="h-4 w-4 transform transition-transform duration-200"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300">
                    Tus resultados estarán disponibles en la sección "Resultados" una vez que los jueces hayan
                    completado la evaluación de tu proyecto. Recibirás una notificación cuando estén listos.
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div x-data="{ expanded: false }" class="rounded-md border border-gray-100 dark:border-gray-700">
                <button @click="expanded = !expanded"
                    class="flex w-full items-center justify-between bg-gray-50 px-3 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    <span>¿Cómo editar mi perfil?</span>
                    <svg :class="{'rotate-180': expanded}" class="h-4 w-4 transform transition-transform duration-200"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300">
                    Dirígete a la esquina superior derecha, haz clic en tu nombre y selecciona "Perfil". Allí podrás
                    actualizar tu información personal y contraseña.
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div x-data="{ expanded: false }" class="rounded-md border border-gray-100 dark:border-gray-700">
                <button @click="expanded = !expanded"
                    class="flex w-full items-center justify-between bg-gray-50 px-3 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    <span>¿Cómo subir mi proyecto?</span>
                    <svg :class="{'rotate-180': expanded}" class="h-4 w-4 transform transition-transform duration-200"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300">
                    Una vez que tengas un equipo registrado, la opción "Mi Proyecto" se habilitará en el menú. Allí
                    podrás subir la documentación y detalles de tu proyecto.
                </div>
            </div>
        </div>
    </div>

    <!-- Help Button -->
    <button @click="open = !open"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-green-400 text-white shadow-lg transition-all hover:bg-green-500 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-300 active:scale-95 dark:focus:ring-green-800"
        aria-label="Ayuda">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>
</div>