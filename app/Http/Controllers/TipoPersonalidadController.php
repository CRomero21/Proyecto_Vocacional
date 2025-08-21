<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoPersonalidad;

class TipoPersonalidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposPersonalidad = TipoPersonalidad::withCount('carreras')
            ->get();

        return view('admin.tipos-personalidad.index', compact('tiposPersonalidad'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tipos-personalidad.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:10|unique:tipos_personalidad',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'caracteristicas' => 'required|string',
            'profesiones_afines' => 'required|string',
        ]);

        TipoPersonalidad::create($validated);

        return redirect()->route('admin.tipos-personalidad.index')
            ->with('success', 'Perfil RIASEC creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoPersonalidad $tipoPersonalidad)
    {
        // Cargar relación con carreras
        $tipoPersonalidad->load('carreras');
        
        return view('admin.tipos-personalidad.show', compact('tipoPersonalidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoPersonalidad $tipoPersonalidad)
    {
        return view('admin.tipos-personalidad.edit', compact('tipoPersonalidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoPersonalidad $tipoPersonalidad)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:10|unique:tipos_personalidad,codigo,'.$tipoPersonalidad->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'caracteristicas' => 'required|string',
            'profesiones_afines' => 'required|string',
        ]);

        $tipoPersonalidad->update($validated);

        return redirect()->route('admin.tipos-personalidad.show', $tipoPersonalidad)
            ->with('success', 'Perfil RIASEC actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoPersonalidad $tipoPersonalidad)
    {
        // Verificar si tiene carreras asociadas
        if ($tipoPersonalidad->carreras()->exists()) {
            return back()->with('error', 'No se puede eliminar este perfil RIASEC porque tiene carreras asociadas');
        }

        $tipoPersonalidad->delete();

        return redirect()->route('admin.tipos-personalidad.index')
            ->with('success', 'Perfil RIASEC eliminado exitosamente');
    }
}