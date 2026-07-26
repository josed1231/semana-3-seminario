<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController, 
    TaskController, 
    EstudianteController, 
    CuestionarioController, 
    PsicologoController, 
    AlertasController, 
    UserController,
    ProgramController,        
    DirectorUnidadController,
    GeneroController,
    OtpVerificationController
};

/*
|--------------------------------------------------------------------------
| Ruta Raíz / Redirección Inicial
|--------------------------------------------------------------------------
*/
Route::get('/', function() {
    if (auth()->check()) {
        $user = auth()->user();
        return in_array($user->rol, ['user', 'estudiante']) 
            ? redirect()->route('welcome') 
            : redirect()->route('welcome.admin');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rutas Autenticadas (Manejo de Sesión Activa)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back'])->group(function () {

    // ----------------------------------------------------------------------
    // 1. BIENVENIDAS Y DASHBOARD GENERAL
    // ----------------------------------------------------------------------
    Route::get('/welcome', fn() => view('welcome'))->name('welcome');
    
    Route::get('/welcome-admin', function () {
        if (!in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad', 'docente'])) {
            abort(403, 'Acceso no autorizado.');
        }
        return view('welcome_admin');
    })->name('welcome.admin');

    Route::get('/dashboard', function() {
        $user = auth()->user();
        if (in_array($user->rol, ['user', 'estudiante'])) {
            return redirect()->route('welcome');
        }
        return app(AlertasController::class)->dashboard();
    })->name('dashboard');

    // ----------------------------------------------------------------------
    // 2. MÓDULO DE CUESTIONARIOS
    // ----------------------------------------------------------------------
    Route::controller(CuestionarioController::class)->group(function () {
        Route::get('/cuestionario', 'create')->name('cuestionario.create');
        Route::post('/cuestionario', 'store')->name('cuestionario.store');
        
        // Verifica si la vista existe con guion bajo o guion medio para evitar pantallas en blanco o errores 500
        Route::get('/cuestionario/finalizado', function () {
            if (view()->exists('cuestionario_success')) {
                return view('cuestionario_success');
            }
            return view('cuestionario-success');
        })->name('cuestionario.success');
    });

    // ----------------------------------------------------------------------
    // 3. PERFIL DE USUARIO
    // ----------------------------------------------------------------------
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // ----------------------------------------------------------------------
    // 4. GESTIÓN DE MONITOREO Y ESTUDIANTES (Rutas Explícitas por codigo_estudiante)
    // ----------------------------------------------------------------------
    Route::middleware(['verificar.rol:admin,psicologo,dir_bienestar,dir_unidad,docente'])->group(function () {
        Route::get('/monitoreo-alertas', [AlertasController::class, 'index'])->name('alertas.monitoreo');
        Route::get('/monitoreo-alertas/export-pdf', [AlertasController::class, 'exportPdf'])->name('alertas.export-pdf');
        
        // Rutas directas para el CRUD de Estudiantes sin conflictos de ID
        Route::get('/estudiantes', [EstudianteController::class, 'index'])->name('estudiantes.index');
        Route::get('/estudiantes/create', [EstudianteController::class, 'create'])->name('estudiantes.create');
        Route::post('/estudiantes', [EstudianteController::class, 'store'])->name('estudiantes.store');
        Route::get('/estudiantes/{codigo_estudiante}/edit', [EstudianteController::class, 'edit'])->name('estudiantes.edit');
        Route::match(['put', 'patch'], '/estudiantes/{codigo_estudiante}', [EstudianteController::class, 'update'])->name('estudiantes.update');
    });

    // ----------------------------------------------------------------------
    // 5. RESULTADOS DE CUESTIONARIOS Y SEGUIMIENTO PSICOLÓGICO
    // ----------------------------------------------------------------------
    Route::middleware(['verificar.rol:admin,psicologo,dir_bienestar,dir_unidad,docente'])->group(function () {
        Route::get('/resultados-cuestionario', [PsicologoController::class, 'index'])->name('resultados.index');
        Route::get('/resultados-cuestionario/buscar', [PsicologoController::class, 'buscar'])->name('resultados.buscar');
        
        // Seguimiento específico de Psicología
        Route::get('/psicologo/estudiante/{codigo}/editar', [PsicologoController::class, 'edit'])->name('psicologo.edit');
        Route::match(['post', 'put'], '/psicologo/estudiante/{codigo}/actualizar', [PsicologoController::class, 'update'])->name('psicologo.update');
    });

    // ----------------------------------------------------------------------
    // 6. MÓDULOS EXCLUSIVOS DE ADMINISTRADOR
    // ----------------------------------------------------------------------
    Route::middleware(['verificar.rol:admin'])->group(function () {
        // Eliminación de estudiantes por código
        Route::delete('/estudiantes/{codigo_estudiante}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');
        
        // Gestión de Entidades del Sistema
        Route::resource('usuarios', UserController::class)->except(['show', 'create', 'edit']);
        Route::resource('programas', ProgramController::class)->except(['create', 'show', 'edit']);
        Route::resource('directores', DirectorUnidadController::class)->except(['create', 'show', 'edit']);
        Route::resource('generos', GeneroController::class)->except(['create', 'show', 'edit']);
        
        // Creación de Tareas Administrativas
        Route::controller(TaskController::class)->group(function () {
            Route::get('/tasks/create', 'create')->name('tasks.create');
            Route::post('/tasks', 'store')->name('tasks.store');
        });
    });

    // ----------------------------------------------------------------------
    // 7. TAREAS GENERALES
    // ----------------------------------------------------------------------
    Route::resource('tasks', TaskController::class)->except(['create', 'store']);

    // ----------------------------------------------------------------------
    // 8. PRUEBAS / DEBUGGING DE NOTIFICACIONES
    // ----------------------------------------------------------------------
    Route::get('/probar-correo/{codigo}', function ($codigo) {
        $estudiante = App\Models\Estudiante::where('codigo_estudiante', $codigo)->firstOrFail();
        
        // Disparar evento para notificación por correo
        event(new App\Events\EstudianteActualizado($estudiante));

        return "¡Evento de correo disparado correctamente para el estudiante: {$estudiante->nombre_estudiante}!";
    });
});

/*
|--------------------------------------------------------------------------
| Rutas de Verificación OTP (2FA - Sin requerir sesión completa previa)
|--------------------------------------------------------------------------
*/
Route::get('/otp-verify', [OtpVerificationController::class, 'show'])->name('otp.verify');
Route::post('/otp-verify', [OtpVerificationController::class, 'verify'])->name('otp.verify.post');
Route::post('/otp-resend', [OtpVerificationController::class, 'resend'])->name('otp.resend');

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación Básica (Breeze / Fortify / Auth)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';