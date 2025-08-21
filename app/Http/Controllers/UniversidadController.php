<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Universidad;

class UniversidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $universidades = Universidad::query()
            ->when(request('search'), function($query, $search) {
                return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('ubicacion', 'like', "%{$search}%");
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
            ->paginate(10)
            ->withQueryString();

        return view('admin.universidades.index', compact('universidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.universidades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:universidades',
            'departamento' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'municipio' => 'required|string|max:255', // Validación para el nuevo campo
            'tipo' => 'required|string|in:Pública,Privada',
            'telefono' => 'nullable|string|max:20',
            'sitio_web' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'acreditada' => 'nullable|boolean',
        ]);

        // Convertir el checkbox en un valor booleano
        $validated['acreditada'] = $request->has('acreditada') ? 1 : 0;

        // Manejar la carga del logo si existe
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        Universidad::create($validated);

        return redirect()->route('admin.universidades.index')
            ->with('success', 'Universidad creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Universidad $universidad)
    {
        // Cargar relación con carreras
        $universidad->load('carreras');
        
        return view('admin.universidades.show', compact('universidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Universidad $universidad)
    {
        return view('admin.universidades.edit', compact('universidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Universidad $universidad)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:universidades,nombre,'.$universidad->id,
            'tipo' => 'required|string|in:Pública,Privada',
            'ubicacion' => 'required|string|max:255',
            'sitio_web' => 'nullable|url|max:255',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        $universidad->update($validated);

        return redirect()->route('admin.universidades.show', $universidad)
            ->with('success', 'Universidad actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Universidad $universidad)
    {
        // Verificar si tiene carreras asociadas
        if ($universidad->carreras()->exists()) {
            return back()->with('error', 'No se puede eliminar la universidad porque tiene carreras asociadas');
        }

        $universidad->delete();

        return redirect()->route('admin.universidades.index')
            ->with('success', 'Universidad eliminada exitosamente');
    }
}