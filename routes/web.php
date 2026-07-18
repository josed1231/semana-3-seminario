<?php

use App\Http\Controllers\{ProfileController, TaskController, EstudianteController, CuestionarioController, PsicologoController};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// 1. Redirección raíz
Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware(['auth', 'prevent-back'])->group(function () {

    // 2. RUTA PARA CARGA DINÁMICA DE DOCENTES (Asignación automática)
    Route::get('/get-docentes/{id_programa}', function ($id_programa) {
        return response()->json(DB::table('docentes')->where('id_programa', $id_programa)->get());
    });

    // 3. DASHBOARD
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (in_array($user->rol, ['user', 'estudiante'])) {
            return redirect()->route('cuestionario.show');
        }
        if (!in_array($user->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad'])) {
            abort(403);
        }
        return app(EstudianteController::class)->index(request());
    })->name('dashboard');

    // 4. CUESTIONARIO
    Route::controller(CuestionarioController::class)->group(function () {
        Route::get('/cuestionario', 'show')->name('cuestionario.show');
        Route::post('/cuestionario', 'store')->name('cuestionario.store');
        Route::get('/cuestionario/finalizado', fn() => view('cuestionario_success'))->name('cuestionario.success');
    });

    // 5. MÓDULO ADMINISTRATIVO
    Route::middleware(['verificar.rol:admin'])->group(function () {
        Route::resource('estudiantes', EstudianteController::class);
        Route::controller(TaskController::class)->group(function () {
            Route::get('/tasks/create', 'create')->name('tasks.create');
            Route::post('/tasks', 'store')->name('tasks.store');
        });
    });

    // 6. MÓDULO PSICOLOGÍA
    Route::middleware(['verificar.rol:psicologo'])->group(function () {
        Route::get('/psicologo', [PsicologoController::class, 'index'])->name('psicologo.index');
        Route::get('/psicologo/estudiante/{codigo}/editar', [PsicologoController::class, 'edit'])->name('psicologo.edit');
        Route::post('/psicologo/estudiante/{codigo}/actualizar', [PsicologoController::class, 'update'])->name('psicologo.update');
    });

    // 7. PERFIL Y TAREAS GENERALES
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
    
    Route::resource('tasks', TaskController::class)->except(['create', 'store']);
});

require __DIR__.'/auth.php';