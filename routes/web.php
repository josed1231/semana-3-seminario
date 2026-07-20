<?php

use App\Http\Controllers\{ProfileController, TaskController, EstudianteController, CuestionarioController, PsicologoController};
use Illuminate\Support\Facades\Route;

// Redirección inicial
Route::get('/', function() {
    if (auth()->check()) {
        $user = auth()->user();
        return (in_array($user->rol, ['user', 'estudiante'])) ? redirect()->route('welcome') : redirect()->route('welcome.admin');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'prevent-back'])->group(function () {

    // BIENVENIDAS
    Route::get('/welcome', fn() => view('welcome'))->name('welcome');
    Route::get('/welcome-admin', function () {
        if (!in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad'])) abort(403);
        return view('welcome_admin');
    })->name('welcome.admin');

    // DASHBOARD (CORREGIDO: Desvía a los estudiantes antes de chocar con el middleware de gestión)
    Route::get('/dashboard', function() {
        $user = auth()->user();
        if (in_array($user->rol, ['user', 'estudiante'])) {
            return redirect()->route('welcome');
        }
        return redirect()->route('estudiantes.index_gestion');
    })->name('dashboard');
    
    // CUESTIONARIO
    Route::controller(CuestionarioController::class)->group(function () {
        Route::get('/cuestionario', 'create')->name('cuestionario.create');
        Route::post('/cuestionario', 'store')->name('cuestionario.store');
        Route::get('/cuestionario/finalizado', fn() => view('cuestionario_success'))->name('cuestionario.success');
    });

    // PERFIL
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // GESTIÓN ESTUDIANTES (Acceso abierto a roles autorizados, restringido mediante el Controlador)
    Route::resource('estudiantes', EstudianteController::class)->except(['destroy']);
    
    // RESULTADOS Y GESTIÓN (Middleware compartido)
    Route::middleware(['verificar.rol:admin,psicologo,dir_bienestar,dir_unidad'])->group(function () {
        Route::get('/resultados-cuestionario', [PsicologoController::class, 'index'])->name('resultados.index');
        Route::get('/resultados-cuestionario/buscar', [PsicologoController::class, 'buscar'])->name('resultados.buscar');
        Route::get('/gestion-estudiantes', [EstudianteController::class, 'index'])->name('estudiantes.index_gestion');
        Route::get('/psicologo/estudiante/{codigo}/editar', [PsicologoController::class, 'edit'])->name('psicologo.edit');
        Route::post('/psicologo/estudiante/{codigo}/actualizar', [PsicologoController::class, 'update'])->name('psicologo.update');
    });

    // ACCIONES EXCLUSIVAS DE ADMIN
    Route::middleware(['verificar.rol:admin'])->group(function () {
        Route::delete('/estudiantes/{estudiante}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');
        Route::controller(TaskController::class)->group(function () {
            Route::get('/tasks/create', 'create')->name('tasks.create');
            Route::post('/tasks', 'store')->name('tasks.store');
        });
    });

    Route::resource('tasks', TaskController::class)->except(['create', 'store']);
});

require __DIR__.'/auth.php';