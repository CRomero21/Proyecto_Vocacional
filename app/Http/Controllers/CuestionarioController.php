<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Test;
use Illuminate\Support\Facades\Auth;

class CuestionarioController extends Controller
{
    // Mostrar el dashboard con historial de tests
    public function mostrar(Request $request)
    {
        // Historial de tests del usuario autenticado
        $tests = Auth::user()->tests()->withCount('respuestas')->orderByDesc('fecha')->get();

        $perfil_riasec = [];
        $carreras_sugeridas = [];

        if ($tests->count()) {
            // Tomar el test más reciente
            $ultimoTest = $tests->first();
            $resultados = is_string($ultimoTest->resultados) ? json_decode($ultimoTest->resultados, true) : $ultimoTest->resultados;

            // Extraer perfil RIASEC (porcentajes)
            if (!empty($resultados['porcentajes'])) {
                $perfil_riasec = $resultados['porcentajes'];
                arsort($perfil_riasec); // Ordenar de mayor a menor
            }

            // Extraer carreras sugeridas (ordenar por match)
            if (!empty($resultados['recomendaciones'])) {
                $carreras_sugeridas = collect($resultados['recomendaciones'])
                    ->sortByDesc('match')
                    ->take(5)
                    ->map(function($carrera) {
                        return [
                            'nombre' => $carrera['nombre'] ?? '',
                            'match' => $carrera['match'] ?? 0
                        ];
                    })->values()->toArray();
            }
        }

        return view('dashboard', [
            'tests' => $tests,
            'perfil_riasec' => $perfil_riasec,
            'carreras_sugeridas' => $carreras_sugeridas
        ]);
    }

    // Guardar las respuestas del usuario (no se usa si usas TestController para guardar tests)
    public function guardar(Request $request)
    {
        $data = $request->validate([
            'respuestas' => 'required|array',
            'test_id' => 'required|exists:tests,id',
        ]);

        foreach ($data['respuestas'] as $pregunta_id => $valor) {
            Respuesta::create([
                'test_id' => $data['test_id'],
                'pregunta_id' => $pregunta_id,
                'valor' => (int)$valor,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Respuestas guardadas correctamente.');
    }
}