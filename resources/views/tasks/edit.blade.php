<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Tarea') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-300 dark:border-gray-700">
                
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Modificar los datos de la Tarea</h3>

                <form action="{{ route('tasks.update', $tarea->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-2">Título *</label>
                        <input type="text" name="titulo" value="{{ old('titulo', $tarea->titulo) }}" required
                               class="w-full text-sm rounded-md border-gray-300 bg-white text-black dark:bg-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm py-2 px-3">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-2">Descripción</label>
                        <textarea name="descripcion" rows="4"
                                  class="w-full text-sm rounded-md border-gray-300 bg-white text-black dark:bg-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm py-2 px-3">{{ old('descripcion', $tarea->descripcion) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-2">Categoría *</label>
                        <select name="category_id" required
                                class="w-full text-sm rounded-md border-gray-300 bg-white text-black dark:bg-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm py-2 px-3">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $tarea->category_id == $category->id ? 'selected' : '' }} class="text-black dark:text-gray-100">
                                    {{ $category->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-2">Estado</label>
                        <select name="estado" class="w-full text-sm rounded-md border-gray-300 bg-white text-black dark:bg-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm py-2 px-3">
                            <option value="pendiente" {{ $tarea->estado == 'pendiente' ? 'selected' : '' }} class="text-black dark:text-gray-100">Pendiente</option>
                            <option value="en_progreso" {{ $tarea->estado == 'en_progreso' ? 'selected' : '' }} class="text-black dark:text-gray-100">En Progreso</option>
                            <option value="completado" {{ $tarea->estado == 'completado' ? 'selected' : '' }} class="text-black dark:text-gray-100">Completado</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-2">Fecha Límite</label>
                        <input type="date" name="fecha_limite" value="{{ old('fecha_limite', $tarea->fecha_limite) }}"
                               class="w-full text-sm rounded-md border-gray-300 bg-white text-black dark:bg-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm py-2 px-3">
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 font-bold text-xs uppercase tracking-wider rounded transition">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded transition shadow-sm">
                            Actualizar Tarea
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>