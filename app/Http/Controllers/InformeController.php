<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class InformeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        $query = $request->input('buscar');
        $usuarios = User::when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })->get();

        return view('informes.index', compact('usuarios', 'query'));
    }
}