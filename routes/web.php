<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\PsicologoController;
use Illuminate\Support\Facades\Route;

// Redirección de la raíz
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'prevent-back'])->group(function () {

    // --- PROTECCIÓN DEL DASHBOARD ---
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Si es usuario normal o estudiante, redirigir al cuestionario
        if (in_array($user->rol, ['user', 'estudiante'])) {
            return redirect()->route('cuestionario.show');
        }

        // Roles permitidos para ver el Dashboard administrativo
        $rolesPermitidos = ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad'];
        if (!in_array($user->rol, $rolesPermitidos)) {
            abort(403); 
        }

        return app(EstudianteController::class)->index(request());
    })->name('dashboard');

    // --- RUTAS PROTEGIDAS POR ROL ---
    // El admin entra por defecto en todas gracias a la lógica del middleware actualizado
    Route::middleware(['verificar.rol:admin'])->group(function () {
        Route::resource('estudiantes', EstudianteController::class);
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });

    Route::middleware(['verificar.rol:psicologo'])->group(function () {
        Route::get('/psicologo', [PsicologoController::class, 'index'])->name('psicologo.index');
        Route::get('/psicologo/estudiante/{codigo}/editar', [PsicologoController::class, 'edit'])->name('psicologo.edit');
        Route::post('/psicologo/estudiante/{codigo}/actualizar', [PsicologoController::class, 'update'])->name('psicologo.update');
    });

    // --- CUESTIONARIO ---
    Route::get('/cuestionario', [CuestionarioController::class, 'show'])->name('cuestionario.show');
    Route::post('/cuestionario', [CuestionarioController::class, 'store'])->name('cuestionario.store');
    Route::get('/cuestionario/finalizado', fn() => view('cuestionario_success'))->name('cuestionario.success');

    // Perfil y Tareas generales
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('tasks', TaskController::class)->except(['create', 'store']);
});

require __DIR__.'/auth.php';