<?php

use App\Http\Controllers\{
    ProfileController, 
    TaskController, 
    EstudianteController, 
    CuestionarioController, 
    PsicologoController, 
    AlertasController, 
    UserController
};
use Illuminate\Support\Facades\Route;

// Redirección inicial según el rol
Route::get('/', function() {
    if (auth()->check()) {
        $user = auth()->user();
        return in_array($user->rol, ['user', 'estudiante']) 
            ? redirect()->route('welcome') 
            : redirect()->route('welcome.admin');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'prevent-back'])->group(function () {

    // BIENVENIDAS
    Route::get('/welcome', fn() => view('welcome'))->name('welcome');
    
    Route::get('/welcome-admin', function () {
        if (!in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad'])) {
            abort(403, 'Acceso no autorizado.');
        }
        return view('welcome_admin');
    })->name('welcome.admin');

    // DASHBOARD ADMINISTRATIVO (Accesible para admin, psicologo, dir_bienestar, dir_unidad)
    Route::get('/dashboard', function() {
        $user = auth()->user();
        if (in_array($user->rol, ['user', 'estudiante'])) {
            return redirect()->route('welcome');
        }
        return app(AlertasController::class)->dashboard();
    })->name('dashboard');
    
    // CUESTIONARIO (Accesible para todos los usuarios autenticados)
    Route::controller(CuestionarioController::class)->group(function () {
        Route::get('/cuestionario', 'create')->name('cuestionario.create');
        Route::post('/cuestionario', 'store')->name('cuestionario.store');
        Route::get('/cuestionario/finalizado', fn() => view('cuestionario_success'))->name('cuestionario.success');
    });

    // PERFIL DE USUARIO
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // --------------------------------------------------------------------------
    // GESTIÓN DE ESTUDIANTES Y MONITOREO (ADMIN, PSICÓLOGO, DIR_BIENESTAR, DIR_UNIDAD)
    // Permite que el Psicólogo consulte y edite estudiantes en el Monitoreo
    // --------------------------------------------------------------------------
    Route::middleware(['verificar.rol:admin,psicologo,dir_bienestar,dir_unidad'])->group(function () {
        // Monitoreo de Alertas
        Route::get('/monitoreo-alertas', [AlertasController::class, 'index'])->name('alertas.monitoreo');

        // Recursos de estudiantes (index, show, create, store, edit, update)
        Route::resource('estudiantes', EstudianteController::class)->except(['destroy']);
    });

    // --------------------------------------------------------------------------
    // RUTAS PARA VER RESULTADOS DE CUESTIONARIOS Y SEGUIMIENTO PSICOLÓGICO
    // --------------------------------------------------------------------------
    Route::middleware(['verificar.rol:admin,psicologo,dir_bienestar'])->group(function () {
        Route::get('/resultados-cuestionario', [PsicologoController::class, 'index'])->name('resultados.index');
        Route::get('/resultados-cuestionario/buscar', [PsicologoController::class, 'buscar'])->name('resultados.buscar');
        
        // Seguimiento específico del psicólogo
        Route::get('/psicologo/estudiante/{codigo}/editar', [PsicologoController::class, 'edit'])->name('psicologo.edit');
        Route::post('/psicologo/estudiante/{codigo}/actualizar', [PsicologoController::class, 'update'])->name('psicologo.update');
    });

    // --------------------------------------------------------------------------
    // ACCIONES EXCLUSIVAS DEL ADMINISTRADOR
    // --------------------------------------------------------------------------
    Route::middleware(['verificar.rol:admin'])->group(function () {
        Route::delete('/estudiantes/{estudiante}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');
        
        // Módulo de Gestión de Usuarios
        Route::resource('usuarios', UserController::class)->except(['show', 'create', 'edit']);
        
        // Tareas Administrativas
        Route::controller(TaskController::class)->group(function () {
            Route::get('/tasks/create', 'create')->name('tasks.create');
            Route::post('/tasks', 'store')->name('tasks.store');
        });
    });

    // TAREAS GENERALES
    Route::resource('tasks', TaskController::class)->except(['create', 'store']);
});

require __DIR__.'/auth.php';