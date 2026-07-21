<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl p-6 shadow-sm" style="background-color: #004d2e;">
            <h2 class="font-bold text-2xl leading-tight m-0" style="color: #ffffff;">
                {{ __('Gestión de Usuarios y Roles - Cotecnova') }}
            </h2>
        </div>
    </x-slot>

    <!-- Contenedor Principal con Alpine.js para Modal de Edición y Búsqueda en Tiempo Real -->
    <div x-data="{ 
        openEditModal: false, 
        editUser: { id: '', name: '', email: '', rol: '' },
        search: '{{ request('buscar') }}'
    }" class="py-12 min-h-screen" style="background-color: #f4f6f8;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Mensajes de Éxito -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- BLOQUE DE ERRORES DE VALIDACIÓN (Crucial para saber qué falla) -->
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-bold space-y-1">
                    <p class="font-black text-base mb-1">⚠️ Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SECCIÓN 1: Formulario para Crear Usuario -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6" style="border: 1px solid #e2e8f0;">
                <div class="flex items-center space-x-3 mb-6 border-b border-gray-100 pb-4">
                    <div class="p-2 rounded-xl bg-orange-50 text-[#f17a28]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#004d2e] m-0">Registrar Nuevo Usuario</h3>
                </div>

                <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Nombre Completo</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ej: Juan Pérez"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm border border-slate-300 text-black bg-white focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="usuario@cotecnova.edu.co"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm border border-slate-300 text-black bg-white focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Rol del Usuario</label>
                            <select name="rol" required class="w-full rounded-xl px-4 py-2.5 text-sm border border-slate-300 text-black bg-white focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none">
                                <option value="" disabled {{ old('rol') ? '' : 'selected' }}>Seleccione un rol...</option>
                                <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="dir_bienestar" {{ old('rol') == 'dir_bienestar' ? 'selected' : '' }}>Director de Bienestar</option>
                                <option value="dir_unidad" {{ old('rol') == 'dir_unidad' ? 'selected' : '' }}>Director de Unidad</option>
                                <option value="psicologo" {{ old('rol') == 'psicologo' ? 'selected' : '' }}>Psicólogo(a)</option>
                                <option value="docente" {{ old('rol') == 'docente' ? 'selected' : '' }}>Docente</option>
                                <option value="user" {{ old('rol') == 'user' ? 'selected' : '' }}>Usuario / Estudiante</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Contraseña</label>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-xl px-4 py-2.5 text-sm border border-slate-300 text-black bg-white focus:border-[#005a36] outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full rounded-xl px-4 py-2.5 text-sm border border-slate-300 text-black bg-white focus:border-[#005a36] outline-none">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" style="background-color: #f17a28; color: #ffffff;" class="w-full py-2.5 px-5 rounded-xl text-sm font-bold shadow-sm cursor-pointer border-none flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                                Crear Usuario
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- SECCIÓN 2: Tabla de Usuarios -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6" style="border: 1px solid #e2e8f0;">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 m-0">Usuarios Registrados</h3>
                    
                    <!-- Buscador Avanzado con filtro en tiempo real -->
                    <div class="w-full sm:w-80">
                        <form method="GET" action="{{ route('usuarios.index') }}" class="flex gap-2 w-full">
                            <div class="relative flex-1 flex items-center">
                                <input 
                                    type="text" 
                                    name="buscar" 
                                    x-model="search"
                                    placeholder="Buscar nombre o correo..." 
                                    class="rounded-xl pl-4 pr-10 py-2 text-sm w-full text-slate-800 bg-white border border-slate-300 outline-none focus:border-[#004d2e] focus:ring-1 focus:ring-[#004d2e]"
                                    autocomplete="off"
                                >
                                <button 
                                    x-show="search.length > 0" 
                                    @click="search = ''; window.location.href='{{ route('usuarios.index') }}'" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer"
                                    type="button"
                                >
                                    ✕
                                </button>
                            </div>
                            <button type="submit" style="background-color: #004d2e; color: #ffffff;" class="px-4 py-2 rounded-xl text-sm font-bold hover:opacity-90 transition-opacity">Buscar</button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-100">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-black">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-black">Correo</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-black">Rol Asignado</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-black">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($usuarios as $user)
                                <tr 
                                    x-show="search === '' || '{{ strtolower($user->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(search.toLowerCase())"
                                    class="hover:bg-slate-50 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-black">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border bg-slate-100 text-slate-800">
                                            {{ strtoupper(str_replace('_', ' ', $user->rol)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center space-x-3">
                                        <button type="button" @click="editUser = { id: '{{ $user->id }}', name: '{{ $user->name }}', email: '{{ $user->email }}', rol: '{{ $user->rol }}' }; openEditModal = true;" 
                                                class="text-blue-600 hover:text-blue-800 font-bold cursor-pointer">
                                            Editar
                                        </button>

                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold cursor-pointer">Eliminar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center font-bold text-slate-500">No hay usuarios.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <div class="mt-4 custom-pagination-wrapper">
                    {{ $usuarios->links() }}
                </div>
            </div>

        </div>

        <!-- MODAL DE EDICIÓN FLOTANTE (Alpine.js) -->
        <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="openEditModal = false"></div>

            <div class="bg-white rounded-3xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6 z-10 border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                    <h3 class="text-xl font-bold text-[#004d2e]">Editar Usuario</h3>
                    <button type="button" @click="openEditModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
                </div>

                <form :action="'/usuarios/' + editUser.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Nombre Completo</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Correo Electrónico</label>
                        <input type="email" name="email" x-model="editUser.email" required class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Rol del Usuario</label>
                        <select name="rol" x-model="editUser.rol" required class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none">
                            <option value="admin">Administrador</option>
                            <option value="dir_bienestar">Director de Bienestar</option>
                            <option value="dir_unidad">Director de Unidad</option>
                            <option value="psicologo">Psicólogo(a)</option>
                            <option value="docente">Docente</option>
                            <option value="user">Usuario / Estudiante</option>
                        </select>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="password" placeholder="Dejar en blanco para no cambiar" class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 rounded-xl text-sm font-bold bg-slate-100 text-slate-700">Cancelar</button>
                        <button type="submit" style="background-color: #f17a28; color: #ffffff;" class="px-5 py-2 rounded-xl text-sm font-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .custom-pagination-wrapper nav svg { width: 20px; height: 20px; display: inline; }
        .custom-pagination-wrapper nav span, 
        .custom-pagination-wrapper nav a { color: #334155 !important; background-color: #ffffff !important; border-color: #cbd5e1 !important; }
        .custom-pagination-wrapper nav span[aria-current="page"] span { background-color: #f17a28 !important; color: #ffffff !important; border-color: #f17a28 !important; }
        .custom-pagination-wrapper nav a:hover { background-color: #f8fafc !important; color: #f17a28 !important; }
    </style>
</x-app-layout>