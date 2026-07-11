<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): JsonResponse
    {
        // Trae los usuarios (puedes usar paginate() si son muchos)
        $usuarios = User::all(); 
        
        return response()->json($usuarios, 200);
    }

    public function show($id): JsonResponse
    {
        // Busca el usuario o lanza un error 404 si no existe
        $usuario = User::findOrFail($id); 
        
        return response()->json($usuario, 200);
    }

}