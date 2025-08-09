<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('buscar');
        $usuarios = User::when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%");
        })->get();

        return view('admin.usuarios.index', compact('usuarios', 'query'));
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$usuario->id,
            'role' => 'required|in:estudiante,superadmin',
        ]);

        $usuario->update($request->only('name', 'email', 'role'));

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}