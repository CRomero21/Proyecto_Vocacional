<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrera;
use App\Models\TipoPersonalidad;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carreras = Carrera::query()
            ->when(request('search'), function($query, $search) {
                return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('area_conocimiento', 'like', "%{$search}%");
            })
            ->when(request('area_conocimiento'), function($query, $area_conocimiento) {
                return $query->where('area_conocimiento', $area_conocimiento);
            })
            ->when(request('orden'), function($query, $orden) {
                return match($orden) {
                    'nombre_asc' => $query->orderBy('nombre', 'asc'),
                    'nombre_desc' => $query->orderBy('nombre', 'desc'),
                    'recientes' => $query->orderBy('created_at', 'desc'),
                    'antiguos' => $query->orderBy('created_at', 'asc'),
                    default => $query->orderBy('nombre', 'asc')
                };
            }, function($query) {
                return $query->orderBy('nombre', 'asc');
            })
            ->withCount('universidades')
            ->paginate(10)
            ->withQueryString();

        // Obtener áreas disponibles para filtrado
        $areas_conocimiento = Carrera::distinct('area_conocimiento')->pluck('area_conocimiento');

        return view('admin.carreras.index', compact('carreras', 'areas_conocimiento'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposPersonalidad = TipoPersonalidad::all();
        $areas = Carrera::distinct('area_conocimiento')->pluck('area_conocimiento')->filter()->values();
        return view('admin.carreras.create', compact('tiposPersonalidad', 'areas'));
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
            'tipo_primario' => 'required|string|max:10',
            'tipo_secundario' => 'nullable|string|max:10',
            'tipo_terciario' => 'nullable|string|max:10',
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

        // Guardar en carrera_tipo
        DB::table('carrera_tipo')->insert([
            'carrera_id' => $carrera->id,
            'tipo_primario' => $validated['tipo_primario'] ?? null,
            'tipo_secundario' => $validated['tipo_secundario'] ?? null,
            'tipo_terciario' => $validated['tipo_terciario'] ?? null,
        ]);

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrera $carrera)
    {
        // Cargar relaciones con universidades y tipo de personalidad
        $carrera->load(['universidades']);
        
        return view('admin.carreras.show', compact('carrera'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrera $carrera)
    {
        $tiposPersonalidad = TipoPersonalidad::all();
        $areas = Carrera::distinct('area_conocimiento')->pluck('area_conocimiento')->filter()->values();
        return view('admin.carreras.edit', compact('carrera', 'tiposPersonalidad', 'areas'));
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
            'tipo_primario' => 'required|string|max:10',
            'tipo_secundario' => 'nullable|string|max:10',
            'tipo_terciario' => 'nullable|string|max:10',
            'imagen' => 'nullable|image|max:2048',
            'nueva_area' => 'nullable|string|max:255',
        ]);

        $area = $request->filled('nueva_area') ? $request->input('nueva_area') : $request->input('area_conocimiento');
        if (!$area) {
            return back()->withInput()->withErrors(['area_conocimiento' => 'Debes seleccionar o escribir un área de conocimiento.']);
        }

        // Manejo de imagen
        if ($request->hasFile('imagen')) {
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

        // Actualizar o crear en carrera_tipo
        DB::table('carrera_tipo')->updateOrInsert(
            ['carrera_id' => $carrera->id],
            [
                'tipo_primario' => $validated['tipo_primario'] ?? null,
                'tipo_secundario' => $validated['tipo_secundario'] ?? null,
                'tipo_terciario' => $validated['tipo_terciario'] ?? null,
            ]
        );

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

        // Eliminar también de carrera_tipo
        DB::table('carrera_tipo')->where('carrera_id', $carrera->id)->delete();

        $carrera->delete();

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera eliminada exitosamente');
    }
}