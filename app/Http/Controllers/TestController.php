<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Pregunta;
use App\Models\Respuesta;

class TestController extends Controller
{
    public function iniciar()
    {
        $test = Test::create([
            'user_id' => auth()->id(),
            'fecha' => now(),
        ]);
        $preguntas = Pregunta::all();
        return view('test.realizar', [
            'preguntas' => $preguntas,
            'test_id' => $test->id,
        ]);
    }

    public function guardar(Request $request)
    {
        foreach ($request->respuestas as $pregunta_id => $valor) {
            Respuesta::create([
                'test_id' => $request->test_id,
                'pregunta_id' => $pregunta_id,
                'valor' => (int)$valor,
                'user_id' => auth()->id(),
            ]);
        }
        return redirect()->route('dashboard')->with('success', 'Test guardado correctamente');
    }
   public function mostrar(Request $request)
    {
        $preguntas = null;
        $test_id = null;
        if ($request->has('test')) {
            $preguntas = \App\Models\Pregunta::all();
            $test_id = $request->get('test_id');
        }
        return view('dashboard', compact('preguntas', 'test_id'));
    }
}