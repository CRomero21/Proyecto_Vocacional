<?php
namespace App\Http\Controllers;

use App\Models\TipoPersonalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoPersonalidadController extends Controller
{
    /**
     * Muestra la lista de tipos de personalidad con el conteo de carreras asociadas.
     */
    public function index()
    {
        $tipos = TipoPersonalidad::all();

        // Para cada tipo, contar carreras donde su código aparece como primario/secundario/terciario
        foreach ($tipos as $tipo) {
            $tipo->carreras_count = DB::table('carrera_tipo')
                ->where('tipo_primario', $tipo->codigo)
                ->orWhere('tipo_secundario', $tipo->codigo)
                ->orWhere('tipo_terciario', $tipo->codigo)
                ->count();
        }

        return view('admin.tipos-personalidad.index', compact('tipos'));
    }

    /**
     * Muestra el formulario para crear un nuevo tipo de personalidad.
     */
    public function create()
    {
        return view('admin.tipos-personalidad.create');
    }

    /**
     * Almacena un nuevo tipo de personalidad en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:10|unique:tipos_personalidad,codigo',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'caracteristicas' => 'nullable|string',
            'color_hex' => 'nullable|string|max:7',
        ]);

        TipoPersonalidad::create($request->all());

        return redirect()->route('admin.tipos-personalidad.index')
            ->with('success', 'Tipo de personalidad creado correctamente.');
    }

    /**
     * Muestra un tipo de personalidad específico.
     */
    public function show($id)
    {
        $tipoPersonalidad = TipoPersonalidad::findOrFail($id);
        return view('admin.tipos-personalidad.show', compact('tipoPersonalidad'));
    }

    /**
     * Muestra el formulario para editar un tipo de personalidad.
     */
    public function edit($id)
    {
        $tipoPersonalidad = TipoPersonalidad::findOrFail($id);
        return view('admin.tipos-personalidad.edit', compact('tipoPersonalidad'));
    }

    /**
     * Actualiza un tipo de personalidad en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $tipoPersonalidad = TipoPersonalidad::findOrFail($id);

        $request->validate([
            'codigo' => 'required|string|max:10|unique:tipos_personalidad,codigo,' . $tipoPersonalidad->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'caracteristicas' => 'nullable|string',
            'color_hex' => 'nullable|string|max:7',
        ]);

        $tipoPersonalidad->update($request->all());

        return redirect()->route('admin.tipos-personalidad.index')
            ->with('success', 'Tipo de personalidad actualizado correctamente.');
    }

    /**
     * Elimina un tipo de personalidad de la base de datos.
     */
    public function destroy($id)
    {
        $tipoPersonalidad = TipoPersonalidad::findOrFail($id);
        $tipoPersonalidad->delete();

        return redirect()->route('admin.tipos-personalidad.index')
            ->with('success', 'Tipo de personalidad eliminado correctamente.');
    }
}