<?php
namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function index()
    {
        $preguntas = Pregunta::all();
        return view('admin.preguntas.index', compact('preguntas'));
    }

    public function create()
    {
        return view('admin.preguntas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'texto' => 'required',
            'tipo' => 'required'
        ]);
        Pregunta::create($request->only('texto', 'tipo'));
        return redirect()->route('admin.preguntas.index')->with('success', 'Pregunta creada');
    }

    public function edit(Pregunta $pregunta)
    {
        return view('admin.preguntas.edit', compact('pregunta'));
    }

    public function update(Request $request, Pregunta $pregunta)
    {
        $request->validate([
            'texto' => 'required',
            'tipo' => 'required'
        ]);
        $pregunta->update($request->only('texto', 'tipo'));
        return redirect()->route('admin.preguntas.index')->with('success', 'Pregunta actualizada');
    }

    public function destroy(Pregunta $pregunta)
    {
        $pregunta->delete();
        return redirect()->route('admin.preguntas.index')->with('success', 'Pregunta eliminada');
    }
}