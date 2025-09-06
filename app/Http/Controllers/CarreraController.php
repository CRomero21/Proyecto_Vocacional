<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrera;
use App\Models\CarreraTipo;
use App\Models\TipoPersonalidad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Carrera::with('carreraTipos');
        
        // Filtrar por nombre (búsqueda)
        if ($request->filled('search')) {
            $query->where('nombre', 'LIKE', '%' . $request->search . '%');
        }
        
        // Filtrar por área de conocimiento
        if ($request->filled('area_conocimiento')) {
            $query->where('area_conocimiento', $request->area_conocimiento);
        }
        
        // Filtrar por es_institucional
        if ($request->filled('es_institucional')) {
            $query->where('es_institucional', $request->es_institucional);
        }
        
        // Obtener áreas únicas para el filtro
        $areas_conocimiento = Carrera::select('area_conocimiento')
                        ->distinct()
                        ->whereNotNull('area_conocimiento')
                        ->orderBy('area_conocimiento')
                        ->pluck('area_conocimiento');
        
        // Ordenar y paginar
        $carreras = $query->orderBy('nombre', 'asc')
                          ->paginate(10)
                          ->appends($request->except('page'));
        
        return view('admin.carreras.index', compact('carreras', 'areas_conocimiento'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposPersonalidad = TipoPersonalidad::all();
        $areas = Carrera::distinct('area_conocimiento')
            ->whereNotNull('area_conocimiento')
            ->orderBy('area_conocimiento')
            ->pluck('area_conocimiento')
            ->filter()
            ->values();
            
        $tiposRIASEC = [
            'R' => 'Realista',
            'I' => 'Investigador',
            'A' => 'Artístico',
            'S' => 'Social',
            'E' => 'Emprendedor',
            'C' => 'Convencional'
        ];
        
        return view('admin.carreras.create', compact('tiposPersonalidad', 'areas', 'tiposRIASEC'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras',
            'descripcion' => 'required|string|max:1000',
            'duracion' => 'nullable|string|max:100',
            'perfil_ingreso' => 'nullable|string|max:1000',
            'perfil_egreso' => 'nullable|string|max:1000',
            'combinaciones' => 'required|array|min:1',
            'combinaciones.*.tipo_primario' => 'required|string|size:1',
            'combinaciones.*.tipo_secundario' => 'nullable|string|size:1',
            'combinaciones.*.tipo_terciario' => 'nullable|string|size:1',
            'imagen' => 'nullable|image|max:2048',
            'nueva_area' => 'nullable|string|max:255',
        ]);

        // Prioriza nueva_area si viene, si no, usa area_conocimiento del select
        $area = $request->filled('nueva_area') ? $request->input('nueva_area') : $request->input('area_conocimiento');

        // Si no hay área, error manual
        if (!$area) {
            return back()->withInput()->withErrors(['area_conocimiento' => 'Debes seleccionar o escribir un área de conocimiento.']);
        }

        // Manejo de imagen
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('carreras', 'public');
        }

        // Guardar carrera
        $carrera = new Carrera();
        $carrera->nombre = $validated['nombre'];
        $carrera->area_conocimiento = $area;
        $carrera->descripcion = $validated['descripcion'];
        $carrera->duracion = $validated['duracion'] ?? null;
        $carrera->perfil_ingreso = $validated['perfil_ingreso'] ?? null;
        $carrera->perfil_egreso = $validated['perfil_egreso'] ?? null;
        $carrera->es_institucional = $request->has('es_institucional');
        $carrera->imagen = $imagenPath;
        $carrera->save();

        // Guardar todas las combinaciones RIASEC
        foreach ($request->combinaciones as $combinacion) {
            $carrera->carreraTipos()->create([
                'tipo_primario' => $combinacion['tipo_primario'] ?? null,
                'tipo_secundario' => $combinacion['tipo_secundario'] ?? null,
                'tipo_terciario' => $combinacion['tipo_terciario'] ?? null,
            ]);
        }

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrera $carrera)
    {
        // Cargar relaciones con universidades y tipos RIASEC
        $carrera->load(['universidades', 'carreraTipos']);
        
        return view('admin.carreras.show', compact('carrera'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrera $carrera)
    {
        $tiposPersonalidad = TipoPersonalidad::all();
        $areas = Carrera::distinct('area_conocimiento')
            ->whereNotNull('area_conocimiento')
            ->orderBy('area_conocimiento')
            ->pluck('area_conocimiento')
            ->filter()
            ->values();
            
        $tiposRIASEC = [
            'R' => 'Realista',
            'I' => 'Investigador',
            'A' => 'Artístico',
            'S' => 'Social',
            'E' => 'Emprendedor',
            'C' => 'Convencional'
        ];
        
        return view('admin.carreras.edit', compact('carrera', 'tiposPersonalidad', 'areas', 'tiposRIASEC'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrera $carrera)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre,'.$carrera->id,
            'descripcion' => 'required|string|max:1000',
            'duracion' => 'nullable|string|max:100',
            'perfil_ingreso' => 'nullable|string|max:1000',
            'perfil_egreso' => 'nullable|string|max:1000',
            'combinaciones' => 'required|array|min:1',
            'combinaciones.*.tipo_primario' => 'required|string|size:1',
            'combinaciones.*.tipo_secundario' => 'nullable|string|size:1',
            'combinaciones.*.tipo_terciario' => 'nullable|string|size:1',
            'imagen' => 'nullable|image|max:2048',
            'nueva_area' => 'nullable|string|max:255',
        ]);

        $area = $request->filled('nueva_area') ? $request->input('nueva_area') : $request->input('area_conocimiento');
        if (!$area) {
            return back()->withInput()->withErrors(['area_conocimiento' => 'Debes seleccionar o escribir un área de conocimiento.']);
        }

        // Manejo de imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($carrera->imagen) {
                Storage::disk('public')->delete($carrera->imagen);
            }
            
            $imagenPath = $request->file('imagen')->store('carreras', 'public');
            $carrera->imagen = $imagenPath;
        }

        $carrera->nombre = $validated['nombre'];
        $carrera->area_conocimiento = $area;
        $carrera->descripcion = $validated['descripcion'];
        $carrera->duracion = $validated['duracion'] ?? null;
        $carrera->perfil_ingreso = $validated['perfil_ingreso'] ?? null;
        $carrera->perfil_egreso = $validated['perfil_egreso'] ?? null;
        $carrera->es_institucional = $request->has('es_institucional');
        $carrera->save();

        // Obtener IDs existentes para identificar los eliminados
        $idsExistentes = $carrera->carreraTipos()->pluck('id')->toArray();
        $idsEnviados = collect($request->combinaciones)
                   ->filter(function($item) {
                       return !empty($item['id']);
                   })
                   ->pluck('id')
                   ->toArray();
    
        // Eliminar combinaciones que ya no existen
        $idsAEliminar = array_diff($idsExistentes, $idsEnviados);
        if (!empty($idsAEliminar)) {
            CarreraTipo::whereIn('id', $idsAEliminar)->delete();
        }
    
        // Actualizar o crear combinaciones
        foreach ($request->combinaciones as $combinacion) {
            if (!empty($combinacion['id'])) {
                // Actualizar combinación existente
                CarreraTipo::find($combinacion['id'])->update([
                    'tipo_primario' => $combinacion['tipo_primario'],
                    'tipo_secundario' => $combinacion['tipo_secundario'] ?? null,
                    'tipo_terciario' => $combinacion['tipo_terciario'] ?? null,
                ]);
            } else {
                // Crear nueva combinación
                $carrera->carreraTipos()->create([
                    'tipo_primario' => $combinacion['tipo_primario'],
                    'tipo_secundario' => $combinacion['tipo_secundario'] ?? null,
                    'tipo_terciario' => $combinacion['tipo_terciario'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.carreras.show', $carrera)
            ->with('success', 'Carrera actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrera $carrera)
    {
        // Verificar si tiene universidades asociadas
        if ($carrera->universidades()->exists()) {
            return back()->with('error', 'No se puede eliminar la carrera porque está asociada a universidades');
        }

        // Eliminar las combinaciones RIASEC de la carrera
        $carrera->carreraTipos()->delete();
        
        // Eliminar imagen si existe
        if ($carrera->imagen) {
            Storage::disk('public')->delete($carrera->imagen);
        }

        $carrera->delete();

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera eliminada exitosamente');
    }
}