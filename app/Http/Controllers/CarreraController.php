<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrera;
use App\Models\TipoPersonalidad;

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
                    ->orWhere('area', 'like', "%{$search}%");
            })
            ->when(request('area'), function($query, $area) {
                return $query->where('area', $area);
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
        $areas = Carrera::distinct('area')->pluck('area');

        return view('admin.carreras.index', compact('carreras', 'areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposPersonalidad = TipoPersonalidad::all();
        return view('admin.carreras.create', compact('tiposPersonalidad'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras',
            'area' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'duracion' => 'nullable|string|max:100',
            'perfil_ingreso' => 'nullable|string|max:1000',
            'perfil_egreso' => 'nullable|string|max:1000',
            'tipo_personalidad_id' => 'required|exists:tipos_personalidad,id',
        ]);

        Carrera::create($validated);

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrera $carrera)
    {
        // Cargar relaciones con universidades y tipo de personalidad
        $carrera->load(['universidades', 'tipoPersonalidad']);
        
        return view('admin.carreras.show', compact('carrera'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrera $carrera)
    {
        $tiposPersonalidad = TipoPersonalidad::all();
        return view('admin.carreras.edit', compact('carrera', 'tiposPersonalidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrera $carrera)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre,'.$carrera->id,
            'area' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'duracion' => 'nullable|string|max:100',
            'perfil_ingreso' => 'nullable|string|max:1000',
            'perfil_egreso' => 'nullable|string|max:1000',
            'tipo_personalidad_id' => 'required|exists:tipos_personalidad,id',
        ]);

        $carrera->update($validated);

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

        $carrera->delete();

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera eliminada exitosamente');
    }
}