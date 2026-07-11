<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('tasks.index');
    }
    return redirect()->route('login');
});

    Route::middleware(['verificar.rol:admin'])->group(function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    });

    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('tasks.index');
        }
        return redirect()->route('login');
    });

// Grupo protegido con autenticación y evitar retorno atrás
Route::middleware(['auth', 'prevent-back'])->group(function () {

    // Rutas del Dashboard y Perfil
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- RUTAS DE EDICIÓN Y CONSULTA (ACCESIBLES POR TODOS) ---
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // --- RUTAS DE CREACIÓN (SOLO ADMINISTRADORES) ---
    Route::middleware(['verificar.rol:admin'])->group(function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });

});

// Cambia esto si estaba diferente al final de tu archivo web.php
require __DIR__.'/auth.php';