<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Universidad;
use App\Models\Carrera;
use App\Models\CarreraUniversidad;

class CarreraUniversidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\CarreraUniversidad::with(['carrera', 'universidad']);

        if ($request->filled('universidad_id')) {
            $query->where('universidad_id', $request->universidad_id);
        }
        if ($request->filled('q')) {
            $query->whereHas('carrera', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->q . '%');
            });
        }

        $carrerasUniversidades = $query->orderBy('id', 'desc')->get();
        $universidades = \App\Models\Universidad::orderBy('nombre')->get();

        return view('admin.carrera-universidad.index', compact('carrerasUniversidades', 'universidades'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $universidades = Universidad::orderBy('nombre')->get();
        $carreras = Carrera::orderBy('nombre')->get();
        return view('admin.carrera-universidad.create', compact('universidades', 'carreras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'universidad_id' => 'required|exists:universidades,id',
            'modalidad' => 'required|string|max:50',
            'duracion' => 'nullable|string|max:100',
            'costo_semestre' => 'nullable|numeric',
            'requisitos' => 'nullable|string|max:500',
            'disponible' => 'nullable|boolean',
        ]);

        CarreraUniversidad::create([
            'carrera_id' => $validated['carrera_id'],
            'universidad_id' => $validated['universidad_id'],
            'modalidad' => $validated['modalidad'],
            'duracion' => $validated['duracion'] ?? null,
            'costo_semestre' => $validated['costo_semestre'] ?? null,
            'requisitos' => $validated['requisitos'] ?? null,
            'disponible' => $request->has('disponible'),
        ]);

        return redirect()->route('admin.carrera-universidad.index')
            ->with('success', 'Carrera asignada correctamente a la universidad.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $carreraUniversidad = CarreraUniversidad::with(['carrera', 'universidad'])->findOrFail($id);
        return view('admin.carrera-universidad.edit', compact('carreraUniversidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $carreraUniversidad = CarreraUniversidad::findOrFail($id);

        $validated = $request->validate([
            'modalidad' => 'required|string|max:50',
            'duracion' => 'nullable|string|max:100',
            'costo_semestre' => 'nullable|numeric',
            'requisitos' => 'nullable|string|max:500',
            'disponible' => 'nullable|boolean',
        ]);

        $carreraUniversidad->update([
            'modalidad' => $validated['modalidad'],
            'duracion' => $validated['duracion'] ?? null,
            'costo_semestre' => $validated['costo_semestre'] ?? null,
            'requisitos' => $validated['requisitos'] ?? null,
            'disponible' => $request->has('disponible'),
        ]);

        return redirect()->route('admin.carrera-universidad.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rel = CarreraUniversidad::findOrFail($id);
        $rel->delete();

        return back()->with('success', 'Carrera desvinculada exitosamente');
    }
}