<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Muestra el listado de usuarios con soporte de búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $usuarios = User::when($buscar, function ($query, $buscar) {
                return $query->where('name', 'like', "%{$buscar}%")
                            ->orWhere('email', 'like', "%{$buscar}%")
                            ->orWhere('username', 'like', "%{$buscar}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Registra un nuevo usuario en el sistema.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'rol'      => ['required', 'string', 'in:admin,dir_bienestar,dir_unidad,psicologo,docente,user,estudiante'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
        ]);

        $username = $request->username ?? explode('@', $request->email)[0];
        
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $username,
            'rol'      => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Actualiza la información de un usuario.
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'rol'      => ['required', 'string', 'in:admin,dir_bienestar,dir_unidad,psicologo,docente,user,estudiante'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $usuario->name  = $request->name;
        $usuario->email = $request->email;
        $usuario->rol   = $request->rol;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario especifico.
     */
    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}