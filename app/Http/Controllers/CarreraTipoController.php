<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CarreraTipo;
use Illuminate\Http\Request;

class CarreraTipoController extends Controller
{
    // Constantes para describir los tipos RIASEC
    private $tiposDescripcion = [
        'R' => 'Realista',
        'I' => 'Investigador',
        'A' => 'Artístico',
        'S' => 'Social',
        'E' => 'Emprendedor',
        'C' => 'Convencional'
    ];

    /**
     * Mostrar el formulario para editar tipos RIASEC
     */
    public function edit(Carrera $carrera)
    {
        return view('admin.carreras.tipos', [
            'carrera' => $carrera->load('carreraTipos'),
            'tiposDescripcion' => $this->tiposDescripcion
        ]);
    }

    /**
     * Almacenar un nuevo tipo RIASEC
     */
    public function store(Request $request, Carrera $carrera)
    {
        $request->validate([
            'tipo_primario' => 'required|string|size:1',
            'tipo_secundario' => 'nullable|string|size:1',
            'tipo_terciario' => 'nullable|string|size:1',
        ]);

        $carrera->carreraTipos()->create([
            'tipo_primario' => $request->tipo_primario,
            'tipo_secundario' => $request->tipo_secundario,
            'tipo_terciario' => $request->tipo_terciario,
        ]);

        return redirect()->route('admin.carreras.tipos.edit', $carrera)
            ->with('success', 'Combinación RIASEC agregada correctamente');
    }

    /**
     * Eliminar un tipo RIASEC
     */
    public function destroy(Carrera $carrera, CarreraTipo $tipo)
    {
        $tipo->delete();
        
        return redirect()->route('admin.carreras.tipos.edit', $carrera)
            ->with('success', 'Combinación RIASEC eliminada correctamente');
    }
}