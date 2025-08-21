<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Universidad;
use App\Models\Carrera;

class CarreraUniversidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $universidades = Universidad::withCount('carreras')
            ->when(request('search'), function($query, $search) {
                return $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('admin.carrera-universidad.index', compact('universidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $universidades = Universidad::orderBy('nombre')->get();
        $carreras = Carrera::orderBy('nombre')->get();
        
        // Preseleccionar universidad si viene en la URL
        $universidadSeleccionada = null;
        if ($request->has('universidad_id')) {
            $universidadSeleccionada = Universidad::find($request->universidad_id);
        }

        return view('admin.carrera-universidad.create', compact('universidades', 'carreras', 'universidadSeleccionada'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'universidad_id' => 'required|exists:universidades,id',
            'carreras' => 'required|array',
            'carreras.*' => 'exists:carreras,id'
        ]);

        $universidad = Universidad::find($validated['universidad_id']);
        
        // Sincronizar carreras sin desvincular las existentes
        $universidad->carreras()->syncWithoutDetaching($validated['carreras']);

        return redirect()->route('admin.universidades.show', $universidad)
            ->with('success', 'Carreras asociadas exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrera $carrera, Universidad $universidad)
    {
        $universidad->carreras()->detach($carrera->id);
        
        return back()->with('success', 'Carrera desvinculada exitosamente');
    }
}