<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $usuarios = User::when($buscar, function ($query, $buscar) {
                return $query->where('name', 'like', "%{$buscar}%")
                            ->orWhere('email', 'like', "%{$buscar}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString(); // Esto mantiene la palabra buscada al cambiar de página

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'rol' => ['required', 'string', 'in:admin,dir_bienestar,dir_unidad,psicologo,docente,user'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Extraer el username del correo (o ajustarlo según prefieras)
        $username = explode('@', $request->email)[0];
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $username,
            'rol' => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'rol' => ['required', 'string', 'in:admin,dir_bienestar,dir_unidad,psicologo,docente,user'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->rol = $request->rol;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        // Evitar que el usuario elimine su propia cuenta activa
        if (auth()->id() === $usuario->id) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}