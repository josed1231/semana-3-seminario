<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl p-6 shadow-sm" style="background-color: #004d2e;">
            <h2 class="font-bold text-2xl leading-tight m-0" style="color: #ffffff;">
                {{ __('Gestión de Usuarios y Roles - Cotecnova') }}
            </h2>
        </div>
    </x-slot>

    <div x-data="{ 
        openEditModal: false, 
        editUser: { id: '', name: '', username: '', email: '', rol: '' },
        search: '{{ request('buscar') }}'
    }" class="py-12 min-h-screen" style="background-color: #f4f6f8;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

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
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Cédula</label>
                            <input type="text" name="username" value="{{ old('username') }}" required placeholder="Ej: 1088123456"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm border border-slate-300 text-black bg-white focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none">
                        </div>

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
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" style="background-color: #f17a28; color: #ffffff;" class="py-2.5 px-6 rounded-xl text-sm font-bold shadow-sm cursor-pointer border-none flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                            Crear Usuario
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6" style="border: 1px solid #e2e8f0;">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 m-0">Usuarios Registrados</h3>
                    
                    <div class="w-full sm:w-80">
                        <form method="GET" action="{{ route('usuarios.index') }}" class="flex gap-2 w-full">
                            <div class="relative flex-1 flex items-center">
                                <input 
                                    type="text" 
                                    name="buscar" 
                                    x-model="search"
                                    placeholder="Buscar cédula, nombre o correo..." 
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
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-black">Cédula</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-black">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-black">Correo</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-black">Rol Asignado</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-black">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($usuarios as $user)
                                <tr 
                                    x-show="search === '' || '{{ strtolower($user->username) }}'.includes(search.toLowerCase()) || '{{ strtolower($user->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(search.toLowerCase())"
                                    class="hover:bg-slate-50 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-black">{{ $user->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-medium">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border bg-slate-100 text-slate-800">
                                            {{ strtoupper(str_replace('_', ' ', $user->rol)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                    @click="editUser = { id: '{{ $user->id }}', username: '{{ $user->username }}', name: '{{ $user->name }}', email: '{{ $user->email }}', rol: '{{ $user->rol }}' }; openEditModal = true;" 
                                                    class="inline-flex items-center justify-center p-2 rounded-xl bg-[#dcece4] hover:bg-[#004d2e] text-[#005a36] hover:text-white shadow-sm transition-all duration-200 group cursor-pointer border-none" 
                                                    title="Editar Usuario">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 scale-100 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');" class="inline m-0">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center justify-center p-2 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border-none shadow-sm cursor-pointer transition-all duration-200 group" 
                                                            title="Eliminar Usuario">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 scale-100 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1 v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center font-bold text-slate-500">No hay usuarios registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 custom-pagination-wrapper">
                    {{ $usuarios->links() }}
                </div>
            </div>

        </div>

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
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Cédula</label>
                        <input type="text" name="username" x-model="editUser.username" required class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none focus:border-[#005a36]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Nombre Completo</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none focus:border-[#005a36]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Correo Electrónico</label>
                        <input type="email" name="email" x-model="editUser.email" required class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none focus:border-[#005a36]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Rol del Usuario</label>
                        <select name="rol" x-model="editUser.rol" required class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none focus:border-[#005a36]">
                            <option value="admin">Administrador</option>
                            <option value="dir_bienestar">Director de Bienestar</option>
                            <option value="dir_unidad">Director de Unidad</option>
                            <option value="psicologo">Psicólogo(a)</option>
                            <option value="docente">Docente</option>
                            <option value="user">Usuario / Estudiante</option>
                        </select>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-3">
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Nueva Contraseña (Opcional)</label>
                            <input type="password" name="password" placeholder="Dejar en blanco para no cambiar" class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none focus:border-[#005a36]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-slate-700">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" placeholder="Repite la nueva contraseña" class="w-full rounded-xl px-4 py-2 text-sm border border-slate-300 text-black bg-white outline-none focus:border-[#005a36]">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 rounded-xl text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer transition-colors">Cancelar</button>
                        <button type="submit" style="background-color: #f17a28; color: #ffffff;" class="px-5 py-2 rounded-xl text-sm font-bold cursor-pointer hover:opacity-90 transition-opacity">Guardar Cambios</button>
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