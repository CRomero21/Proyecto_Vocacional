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
                    ->orWhere('departamento', 'like', "%{$search}%")
                    ->orWhere('municipio', 'like', "%{$search}%");
            })
            ->when(request('departamento'), function($query, $departamento) {
                return $query->where('departamento', $departamento);
            })
            ->when(request('tipo'), function($query, $tipo) {
                return $query->where('tipo', $tipo);
            })
            ->when(request('orden'), function($query, $orden) {
                return match($orden) {
                    'nombre_asc' => $query->orderBy('nombre', 'asc'),
                    'nombre_desc' => $query->orderBy('nombre', 'desc'),
                    'recientes' => $query->orderBy('created_at', 'desc'),
                    'antiguos' => $query->orderBy('created_at', 'asc'),
                    'carreras_desc' => $query->withCount('carreras')->orderBy('carreras_count', 'desc'),
                    default => $query->orderBy('nombre', 'asc')
                };
            }, function($query) {
                return $query->orderBy('nombre', 'asc');
            })
            ->withCount('carreras')
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
            'municipio' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'tipo' => 'required|string|in:Pública,Privada',
            'telefono' => 'nullable|string|max:20',
            'sitio_web' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'acreditada' => 'nullable|boolean',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        $validated['acreditada'] = $request->has('acreditada') ? 1 : 0;

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
            'departamento' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'tipo' => 'required|string|in:Pública,Privada',
            'telefono' => 'nullable|string|max:20',
            'sitio_web' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'acreditada' => 'nullable|boolean',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        $validated['acreditada'] = $request->has('acreditada') ? 1 : 0;

        if ($request->hasFile('logo')) {
            // Elimina el logo anterior si existe
            if ($universidad->logo) {
                \Storage::disk('public')->delete($universidad->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $universidad->update($validated);

    return redirect()->route('admin.universidades.index')->with('success', 'Universidad actualizada correctamente');    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Universidad $universidad)
    {
        if ($universidad->carreras()->exists()) {
            return back()->with('error', 'No se puede eliminar la universidad porque tiene carreras asociadas');
        }

        if ($universidad->logo) {
            \Storage::disk('public')->delete($universidad->logo);
        }

        $universidad->delete();

        return redirect()->route('admin.universidades.index')
            ->with('success', 'Universidad eliminada exitosamente');
    }
}