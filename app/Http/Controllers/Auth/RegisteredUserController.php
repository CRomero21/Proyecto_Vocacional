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
            'name' => [
                'required',
                'string',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+$/',
                'min:3',
                'max:255'
            ],
            'fecha_nacimiento' => ['required', 'date'],
            'sexo' => ['required', 'string', 'in:Masculino,Femenino,Otro'],
            'departamento' => ['required', 'string', 'in:Chuquisaca,La Paz,Cochabamba,Oruro,Potosí,Tarija,Santa Cruz,Beni,Pando'],
            'ciudad' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^[0-9]{7,8}$/', 'min:7', 'max:8'],
            // Permite letras, números, espacios, puntos y guiones en unidad educativa
            'unidad_educativa' => [
                'required',
                'string',
                'regex:/^[\pL\pN .\-]+$/u',
                'max:255'
            ],
            // Permite puntos, guiones y mayúsculas en el correo, y dominios gmail, hotmail, yahoo, outlook
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^[A-Za-z0-9._%+-]+@(gmail|hotmail|yahoo|outlook)\.com$/i',
                'max:255',
                'unique:users'
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ], [
            'name.regex' => 'El nombre solo debe contener letras y espacios',
            'phone.regex' => 'El teléfono debe tener entre 7 y 8 dígitos numéricos',
            'unidad_educativa.required' => 'El nombre de la unidad educativa es requerido',
            'unidad_educativa.regex' => 'La unidad educativa solo debe contener letras, números, puntos y guiones',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es requerida',
            'ciudad.required' => 'La ciudad es requerida',
            'email.regex' => 'Solo se aceptan correos de Gmail, Hotmail, Yahoo u Outlook',
            'terms.accepted' => 'Debes aceptar los términos y condiciones',
        ]);
         $edad = \Carbon\Carbon::parse($request->fecha_nacimiento)->age;

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'unidad_educativa'=> $request->unidad_educativa,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'ciudad' => $request->ciudad,
            'sexo'=> $request->sexo,
            'departamento' => $request->departamento,
            'role' => 'estudiante',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}