<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'edad' => ['required', 'numeric', 'min:5', 'max:120'],
            'sexo' => ['required', 'string'],
            'departamento' => ['required', 'string'],
            'phone' => ['required', 'string', 'min:7', 'max:8'],
            'unidad_educativa' => ['nullable', 'string', 'max:255'], // Cambia 'required' por 'nullable'
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);
        

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone, // nuevo campo
            'unidad_educativa'=> $request->unidad_educativa,//nuevo campo
            'edad'=> $request->edad,//nuevo campo
            'sexo'=> $request->sexo,//nuevo campo
            'departamento' => $request->departamento, // nuevo campo
            'role' => 'estudiante', // asigna automáticamente el rol
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}