<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl p-6 shadow-sm bg-[#004d2e] flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl leading-tight text-white m-0">
                    {{ __('Perfil de Usuario - Cotecnova') }}
                </h2>
                <p class="text-xs text-emerald-100 mt-1 m-0">
                    Administra tu información personal, credenciales de acceso y seguridad de la cuenta.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 min-h-screen bg-[#f4f6f8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Card 1: Información del Perfil -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6 sm:p-8 border border-slate-200">
                <div class="flex items-center space-x-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="p-2.5 rounded-xl bg-emerald-50 text-[#004d2e]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 m-0">Información del Perfil</h3>
                        <p class="text-xs text-slate-500 m-0">Actualiza la información general de tu usuario y correo electrónico.</p>
                    </div>
                </div>

                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Card 2: Actualizar Contraseña -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6 sm:p-8 border border-slate-200">
                <div class="flex items-center space-x-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="p-2.5 rounded-xl bg-orange-50 text-[#f17a28]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 m-0">Seguridad y Contraseña</h3>
                        <p class="text-xs text-slate-500 m-0">Asegúrate de utilizar una contraseña extensa y aleatoria para proteger tu cuenta.</p>
                    </div>
                </div>

                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Card 3: Zona de Peligro (Eliminar Cuenta) -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6 sm:p-8 border border-red-200">
                <div class="flex items-center space-x-3 mb-6 border-b border-red-100 pb-4">
                    <div class="p-2.5 rounded-xl bg-red-50 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-red-900 m-0">Eliminar Cuenta</h3>
                        <p class="text-xs text-slate-500 m-0">Proceso permanente e irreversible sobre tu información y recursos asociados.</p>
                    </div>
                </div>

                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>