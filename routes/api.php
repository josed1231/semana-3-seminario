<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EstudianteController;
use App\Http\Controllers\Api\ProgramaController;
use App\Http\Controllers\Api\RiesgoDesercionController;
use Illuminate\Support\Facades\Route;

// Públicas
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// Protegidas
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me',      [AuthController::class, 'me']);

    // Usuarios
    Route::get('usuarios',     [UserController::class, 'index']);
    Route::get('usuarios/{id}',[UserController::class, 'show']);

    // Programas
    Route::get('programas',      [ProgramaController::class, 'index']);
    Route::get('programas/{id}', [ProgramaController::class, 'show']);
    Route::post('programas',     [ProgramaController::class, 'store']);

    // Estudiantes
    Route::get('estudiantes',          [EstudianteController::class, 'index']);
    Route::get('estudiantes/{codigo}', [EstudianteController::class, 'show']);
    Route::post('estudiantes',         [EstudianteController::class, 'store']);

    // Riesgos
    Route::get('riesgos',      [RiesgoDesercionController::class, 'index']);
    Route::get('riesgos/{id}', [RiesgoDesercionController::class, 'show']);
    Route::post('riesgos',     [RiesgoDesercionController::class, 'store']);

    // Tareas
    Route::apiResource('tareas', TaskController::class);
});