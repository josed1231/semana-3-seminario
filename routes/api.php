<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EstudianteApiController;
use App\Http\Controllers\Api\ProgramaController;
use App\Http\Controllers\Api\RiesgoDesercionController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas de Autenticación
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas Protegidas por Token JWT
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Usuarios
    Route::get('usuarios', [UserController::class, 'index']);
    Route::get('usuarios/{id}', [UserController::class, 'show']);

    // Programas Académicos
    Route::get('programas', [ProgramaController::class, 'index']);
    Route::get('programas/{id}', [ProgramaController::class, 'show']);
    Route::post('programas', [ProgramaController::class, 'store']);

    // Estudiantes
    Route::get('estudiantes', [EstudianteApiController::class, 'index']);
    Route::get('estudiantes/{codigo}', [EstudianteApiController::class, 'show']);
    Route::post('estudiantes', [EstudianteApiController::class, 'store']);

    // Riesgos de Deserción
    Route::get('riesgos', [RiesgoDesercionController::class, 'index']);
    Route::get('riesgos/{id}', [RiesgoDesercionController::class, 'show']);
    Route::post('riesgos', [RiesgoDesercionController::class, 'store']);

    // Tareas (Genera automáticamente index, store, show, update y destroy)
    Route::apiResource('tareas', TaskController::class);
});