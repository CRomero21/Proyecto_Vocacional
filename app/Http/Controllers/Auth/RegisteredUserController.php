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
            'name' => ['required', 'string', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+$/', 'min:3', 'max:255'],
            'edad' => ['required', 'integer', 'min:5', 'max:120'],
            'sexo' => ['required', 'string', 'in:Masculino,Femenino,Otro'],
            'departamento' => ['required', 'string', 'in:Chuquisaca,La Paz,Cochabamba,Oruro,Potosí,Tarija,Santa Cruz,Beni,Pando'],
            'phone' => ['required', 'regex:/^[0-9]{7,8}$/', 'min:7', 'max:8'],
            'unidad_educativa' => ['required', 'string', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9 ]+$/', 'max:255'],
            'email' => ['required', 'string', 'email', 'regex:/^[^\s@]+@(gmail|hotmail|yahoo)\.com$/i', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ], [
            // Mensajes personalizados
            'name.regex' => 'El nombre solo debe contener letras y espacios',
            'edad.min' => 'La edad debe ser mayor o igual a 5 años',
            'edad.max' => 'La edad debe ser menor o igual a 120 años',
            'phone.regex' => 'El teléfono debe tener entre 7 y 8 dígitos numéricos',
            'unidad_educativa.required' => 'El nombre de la unidad educativa es requerido',
            'unidad_educativa.regex' => 'La unidad educativa solo debe contener letras y números',
            'email.regex' => 'Solo se aceptan correos de Gmail, Hotmail o Yahoo',
            'terms.accepted' => 'Debes aceptar los términos y condiciones',
        ]);
        
        // El resto del código permanece igual...
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'unidad_educativa'=> $request->unidad_educativa,
            'edad'=> $request->edad,
            'sexo'=> $request->sexo,
            'departamento' => $request->departamento,
            'role' => 'estudiante',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}