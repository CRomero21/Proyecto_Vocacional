<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\UnidadEducativa;
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
        $departamentos = Departamento::all();
        $ciudades = Ciudad::all();
        $unidadesEducativas = UnidadEducativa::all(); // <-- nombre igual que en la vista

        return view('auth.register', compact('departamentos', 'ciudades', 'unidadesEducativas'));
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
            'departamento_id' => ['required', 'exists:departamentos,id'],
            'ciudad_id' => ['required', 'exists:ciudades,id'],
            'phone' => ['required', 'regex:/^[0-9]{7,8}$/', 'min:7', 'max:8'],
            'unidad_educativa_id' => ['required', 'exists:unidades_educativas,id'],
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
            'fecha_nacimiento.required' => 'La fecha de nacimiento es requerida',
            'departamento_id.required' => 'El departamento es requerido',
            'departamento_id.exists' => 'El departamento seleccionado no es válido',
            'ciudad_id.required' => 'La ciudad es requerida',
            'ciudad_id.exists' => 'La ciudad seleccionada no es válida',
            'unidad_educativa_id.required' => 'La unidad educativa es requerida',
            'unidad_educativa_id.exists' => 'La unidad educativa seleccionada no es válida',
            'email.regex' => 'Solo se aceptan correos de Gmail, Hotmail, Yahoo u Outlook',
            'terms.accepted' => 'Debes aceptar los términos y condiciones',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'departamento_id' => $request->departamento_id,
            'ciudad_id' => $request->ciudad_id,
            'unidad_educativa_id' => $request->unidad_educativa_id,
            'role' => 'estudiante',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}