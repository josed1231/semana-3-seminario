<?php

use App\Http\Controllers\{ProfileController, TaskController, EstudianteController, CuestionarioController, PsicologoController};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware(['auth', 'prevent-back'])->group(function () {

    // DASHBOARD: Redirección automática
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (in_array($user->rol, ['user', 'estudiante'])) {
            return redirect()->route('cuestionario.create');
        }
        if (!in_array($user->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad'])) {
            abort(403);
        }
        return app(EstudianteController::class)->index(request());
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

    // GESTIÓN ESTUDIANTES (Admin)
    Route::middleware(['verificar.rol:admin'])->group(function () {
        Route::resource('estudiantes', EstudianteController::class);
        Route::controller(TaskController::class)->group(function () {
            Route::get('/tasks/create', 'create')->name('tasks.create');
            Route::post('/tasks', 'store')->name('tasks.store');
        });
    });
    
    Route::get('/estudiantes/{estudiante}/edit', [EstudianteController::class, 'edit'])->name('estudiantes.edit');
    Route::put('/estudiantes/{estudiante}', [EstudianteController::class, 'update'])->name('estudiantes.update');

    // RESULTADOS CUESTIONARIO (Acceso para todos menos 'user' y 'estudiante')
    Route::middleware(['verificar.rol:admin,psicologo,dir_bienestar,dir_unidad'])->group(function () {
        Route::get('/resultados-cuestionario', [PsicologoController::class, 'index'])->name('resultados.index');
        Route::get('/resultados-cuestionario/buscar', [PsicologoController::class, 'buscar'])->name('resultados.buscar');
        
        // Mantenemos estas rutas por si necesitas editar desde el mismo módulo
        Route::get('/psicologo/estudiante/{codigo}/editar', [PsicologoController::class, 'edit'])->name('psicologo.edit');
        Route::post('/psicologo/estudiante/{codigo}/actualizar', [PsicologoController::class, 'update'])->name('psicologo.update');
    });

    Route::resource('tasks', TaskController::class)->except(['create', 'store']);
});

require __DIR__.'/auth.php';