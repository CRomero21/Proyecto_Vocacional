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
        // Obtener todos los tests del usuario con el conteo de respuestas
        $tests = Auth::user()->tests()->withCount('respuestas')->orderByDesc('fecha')->get();

        $perfil_riasec = [];
        $areas_sugeridas = [];

        if ($tests->count()) {
            $ultimoTest = $tests->first();
            $resultados = is_string($ultimoTest->resultados) ? json_decode($ultimoTest->resultados, true) : $ultimoTest->resultados;

            // Extraer perfil RIASEC del último test
            if (!empty($resultados['porcentajes'])) {
                $perfil_riasec = $resultados['porcentajes'];
                arsort($perfil_riasec); // Ordenar de mayor a menor

                // Tomar los top 3 tipos (primario, secundario, terciario)
                $topTipos = array_slice(array_keys($perfil_riasec), 0, 3);

                // Mapeo de tipos RIASEC a áreas sugeridas
                $mapeoAreas = [
                    'R' => ['area' => 'Ingeniería, Tecnología o Ciencias Naturales', 'descripcion' => 'Áreas prácticas y técnicas que involucran trabajo con objetos, máquinas y resolución de problemas concretos.'],
                    'I' => ['area' => 'Ciencias, Matemáticas o Investigación', 'descripcion' => 'Áreas analíticas y científicas que requieren pensamiento lógico y resolución de problemas complejos.'],
                    'A' => ['area' => 'Artes, Humanidades o Diseño', 'descripcion' => 'Áreas creativas y expresivas que permiten la innovación y auto-expresión.'],
                    'S' => ['area' => 'Ciencias Sociales, Educación o Salud', 'descripcion' => 'Áreas relacionadas con personas, servicio y empatía.'],
                    'E' => ['area' => 'Administración, Economía o Negocios', 'descripcion' => 'Áreas de liderazgo, gestión y toma de riesgos.'],
                    'C' => ['area' => 'Contabilidad, Finanzas o Administración', 'descripcion' => 'Áreas organizadas y detalladas que involucran procedimientos y datos.']
                ];

                // Generar áreas sugeridas para los top 3 tipos
                foreach ($topTipos as $tipo) {
                    $area = $mapeoAreas[$tipo] ?? ['area' => 'Áreas generales', 'descripcion' => 'Consulta con un orientador.'];
                    $areas_sugeridas[] = [
                        'tipo' => $tipo,
                        'area' => $area['area'],
                        'descripcion' => $area['descripcion'],
                        'porcentaje' => $perfil_riasec[$tipo]
                    ];
                }
            }
        }

        // Para cada test, calcular el perfil dominante (tipo con mayor porcentaje)
        foreach ($tests as $test) {
            $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
            if (!empty($resultados['porcentajes'])) {
                $perfil_riasec_test = $resultados['porcentajes'];
                arsort($perfil_riasec_test);
                $test->perfil_dominante = array_key_first($perfil_riasec_test);
            } else {
                $test->perfil_dominante = null;
            }
        }

        return view('dashboard', [
            'tests' => $tests,
            'perfil_riasec' => $perfil_riasec,
            'areas_sugeridas' => $areas_sugeridas
        ]);
    }

    // Guardar las respuestas del usuario
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