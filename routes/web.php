<?php



use App\Http\Controllers\ProfileController;

use App\Http\Controllers\TaskController;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\EstudianteController;

use Illuminate\Support\Facades\Route;

use App\Models\User;



// Redirección de la raíz (Solo una vez)

Route::get('/', function () {

    if (auth()->check()) {

        return redirect()->route('tasks.index');

    }

    return redirect()->route('login');

});



// Grupo protegido con autenticación y evitar retorno atrás

Route::middleware(['auth', 'prevent-back'])->group(function () {



    // Rutas del Dashboard y Perfil

    Route::get('/dashboard', [EstudianteController::class, 'index'])->name('dashboard');


    // Rutas para la gestión de Estudiantes (Ya incluye index, create, store, show, edit, update, destroy)

    Route::resource('estudiantes', EstudianteController::class);

   

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // --- RUTAS DE EDICIÓN Y CONSULTA DE TAREAS (ACCESIBLES POR TODOS) ---

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');

    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');

    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');



    // --- RUTAS DE CREACIÓN DE TAREAS (SOLO ADMINISTRADORES) ---

    Route::middleware(['verificar.rol:admin'])->group(function () {

        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');

        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

    });



});



require __DIR__.'/auth.php';