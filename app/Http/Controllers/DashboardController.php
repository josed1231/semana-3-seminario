<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User; // <-- ASEGÚRATE DE QUE ESTA LÍNEA ESTÉ AQUÍ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- AGREGA ESTA LÍNEA TAMBIÉN

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 🚨 LOGUEO FORZADO TEMPORAL PARA LA PRUEBA 🚨
        // Buscamos al usuario de prueba e iniciamos su sesión en la web tradicional
        $userPrueba = User::where('username', 'prueba_juan')->first();
        if ($userPrueba) {
            Auth::login($userPrueba);
        }

        // 1. Capturamos lo que el usuario escribió en el cuadro de búsqueda
        $searchTerm = $request->input('search');

        // 2. Traemos las tareas aplicando el filtro si existe un término de búsqueda
        $tareasRecientes = Task::with(['category', 'user'])
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('id', $searchTerm)
                             ->orWhere('titulo', 'LIKE', "%{$searchTerm}%")
                             ->orWhereDate('fecha_limite', $searchTerm);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 3. Tus estadísticas actuales
        $stats = [
            'total'                 => Task::count(),
            'completadas'           => Task::where('estado', 'completado')->count(),
            'en_progreso'           => Task::where('estado', 'en_progreso')->count(),
            'pendientes'            => Task::where('estado', 'pendiente')->count(),
            'promedio_por_usuario'  => 10, 
        ];

        // 4. Retornamos la vista con las variables requeridas
        return view('dashboard', compact('tareasRecientes', 'stats'));
    }
}