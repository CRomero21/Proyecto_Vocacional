<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoPersonalidad;

class TipoPersonalidadController extends Controller
{
    public function index()
    {
        $tiposPersonalidad = TipoPersonalidad::withCount([
            'carrerasPrimario',
            'carrerasSecundario',
            'carrerasTerciario'
        ])->get();

        return view('admin.tipos-personalidad.index', compact('tiposPersonalidad'));
    }

    public function create()
    {
        return view('admin.tipos-personalidad.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:10|unique:tipos_personalidad',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'caracteristicas' => 'required|string',
            'color_hex' => 'required|string|max:7',
        ]);

        TipoPersonalidad::create($validated);

        return redirect()->route('admin.tipos-personalidad.index')
            ->with('success', 'Perfil RIASEC creado exitosamente');
    }

    public function show(TipoPersonalidad $tipos_personalidad)
    {
        $tipos_personalidad->load('carrerasPrimario', 'carrerasSecundario', 'carrerasTerciario');
        return view('admin.tipos-personalidad.show', ['tipoPersonalidad' => $tipos_personalidad]);
    }

    public function edit(TipoPersonalidad $tipos_personalidad)
    {
        return view('admin.tipos-personalidad.edit', ['tipoPersonalidad' => $tipos_personalidad]);
    }

    public function update(Request $request, TipoPersonalidad $tipos_personalidad)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:10|unique:tipos_personalidad,codigo,'.$tipos_personalidad->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'caracteristicas' => 'required|string',
            'color_hex' => 'required|string|max:7',
        ]);

        $tipos_personalidad->update($validated);

        return redirect()->route('admin.tipos-personalidad.show', ['tipos_personalidad' => $tipos_personalidad->id])
            ->with('success', 'Perfil RIASEC actualizado exitosamente');
    }

    public function destroy(TipoPersonalidad $tipos_personalidad)
    {
        if (
            $tipos_personalidad->carrerasPrimario()->exists() ||
            $tipos_personalidad->carrerasSecundario()->exists() ||
            $tipos_personalidad->carrerasTerciario()->exists()
        ) {
            return back()->with('error', 'No se puede eliminar este perfil RIASEC porque tiene carreras asociadas');
        }

        $tipos_personalidad->delete();

        return redirect()->route('admin.tipos-personalidad.index')
            ->with('success', 'Perfil RIASEC eliminado exitosamente');
    }
}