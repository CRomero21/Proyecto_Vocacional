<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Carrera;
use App\Models\Universidad;
use App\Models\TipoPersonalidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function iniciar()
    {
        $testPendiente = Test::where('user_id', auth()->id())
            ->where('completado', false)
            ->first();

        if ($testPendiente) {
            return redirect()->route('test.continuar', $testPendiente->id)
                ->with('info', 'Tienes un test pendiente. Puedes continuarlo o iniciar uno nuevo.');
        }

        $test = Test::create([
            'user_id' => auth()->id(),
            'fecha' => now(),
        ]);

        // Preguntas en orden aleatorio
        $preguntas = Pregunta::inRandomOrder()->get();

        return view('test.realizar', [
            'preguntas' => $preguntas,
            'test_id' => $test->id,
        ]);
    }

    public function continuar(Test $test)
    {
        if ($test->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $respuestasExistentes = $test->respuestas()->pluck('valor', 'pregunta_id')->toArray();
        $preguntas = Pregunta::inRandomOrder()->get();

        return view('test.continuar', [
            'preguntas' => $preguntas,
            'test_id' => $test->id,
            'respuestas_existentes' => $respuestasExistentes
        ]);
    }

    public function mostrar(Request $request)
    {
        $preguntas = null;
        $test_id = null;

        if ($request->has('test')) {
            $preguntas = Pregunta::all();
            $test_id = $request->get('test_id');
        }

        return view('dashboard', compact('preguntas', 'test_id'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        $tests = Test::where('user_id', $user->id)
            ->where('completado', true)
            ->withCount('respuestas')
            ->orderByDesc('fecha_completado')
            ->get();

        $perfil_riasec = [];
        if ($tests->count()) {
            $ultimo = $tests->first();
            $resultados = $ultimo->resultados;
            if (is_string($resultados)) {
                $resultados = json_decode($resultados, true);
            }
            $perfil_riasec = $resultados['porcentajes'] ?? [];
        }

        $carreras_sugeridas = [];
        if ($tests->count()) {
            $ultimo = $tests->first();
            $resultados = $ultimo->resultados;
            if (is_string($resultados)) {
                $resultados = json_decode($resultados, true);
            }
            $carreras_sugeridas = $resultados['recomendaciones'] ?? [];
        }

        return view('dashboard', [
            'tests' => $tests,
            'perfil_riasec' => $perfil_riasec,
            'carreras_sugeridas' => $carreras_sugeridas,
        ]);
    }

    public function procesarResultados(Test $test)
    {
        $respuestas = $test->respuestas()->with('pregunta')->get();

        if ($respuestas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay respuestas para procesar.');
        }

        $puntajes = [
            'R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0
        ];
        $contadores = [
            'R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0
        ];

        foreach ($respuestas as $respuesta) {
            $tipo = $respuesta->pregunta->tipo;
            if (isset($puntajes[$tipo])) {
                $puntajes[$tipo] += $respuesta->valor;
                $contadores[$tipo]++;
            }
        }

        $promedios = [];
        $porcentajes = [];
        $total_puntos = array_sum($puntajes);

        foreach ($puntajes as $tipo => $puntaje) {
            if ($contadores[$tipo] > 0) {
                $promedios[$tipo] = round($puntaje / $contadores[$tipo], 2);
                $porcentajes[$tipo] = $total_puntos > 0 ? round(($puntaje / $total_puntos) * 100) : 0;
            } else {
                $promedios[$tipo] = 0;
                $porcentajes[$tipo] = 0;
            }
        }

        arsort($puntajes);
        $tiposDominantes = array_keys($puntajes);
        $tipoPrimario = $tiposDominantes[0] ?? null;
        $tipoSecundario = $tiposDominantes[1] ?? null;

        $valoresPuntaje = array_values($puntajes);
        if (count($valoresPuntaje) >= 2 && $valoresPuntaje[0] === $valoresPuntaje[1]) {
            $tiposEmpatados = [];
            $valorEmpatado = $valoresPuntaje[0];
            foreach ($puntajes as $tipo => $valor) {
                if ($valor === $valorEmpatado) {
                    $tiposEmpatados[$tipo] = $promedios[$tipo];
                }
            }
            arsort($tiposEmpatados);
            $tiposOrdenados = array_keys($tiposEmpatados);
            $tipoPrimario = $tiposOrdenados[0] ?? null;
            $tipoSecundario = $tiposOrdenados[1] ?? null;
        }

        $recomendaciones = $this->obtenerRecomendacionesCarreras($tipoPrimario, $tipoSecundario, $porcentajes);

        $resultados = [
            'puntajes' => $puntajes,
            'promedios' => $promedios,
            'porcentajes' => $porcentajes,
            'recomendaciones' => $recomendaciones,
            'fecha_procesamiento' => now()->toDateTimeString()
        ];

        $test->update([
            'tipo_primario' => $tipoPrimario,
            'tipo_secundario' => $tipoSecundario,
            'resultados' => $resultados,
            'completado' => true,
            'fecha_completado' => now()
        ]);

        return redirect()->route('test.resultados', $test->id)
            ->with('success', 'Resultados procesados correctamente.');
    }

    // --- Lógica personalizada de match según porcentajes RIASEC ---
    private function calcularMatchPersonalizado($carrera, $porcentajesUsuario)
    {
        $tiposCarrera = [];
        if (isset($carrera->tipo_primario)) $tiposCarrera[] = $carrera->tipo_primario;
        if (isset($carrera->tipo_secundario)) $tiposCarrera[] = $carrera->tipo_secundario;

        $match = 0;
        foreach ($tiposCarrera as $tipo) {
            $match += $porcentajesUsuario[$tipo] ?? 0;
        }

        if (count($tiposCarrera) > 0) {
            $match = intval($match / count($tiposCarrera));
        }

        return $match;
    }

    
    private function obtenerRecomendacionesCarreras($tipoPrimario, $tipoSecundario, $porcentajesUsuario = [])
    {
        if (!$tipoPrimario) {
            return [];
        }

        // 1. Coincidencia exacta (primario y secundario)
        $exactas = DB::table('carrera_tipo')
            ->join('carreras', 'carrera_tipo.carrera_id', '=', 'carreras.id')
            ->where('carrera_tipo.tipo_primario', $tipoPrimario)
            ->where('carrera_tipo.tipo_secundario', $tipoSecundario)
            ->select('carreras.id', 'carreras.nombre', 'carreras.area_conocimiento', 
                    'carreras.descripcion', 'carreras.es_institucional',
                    'carrera_tipo.tipo_primario', 'carrera_tipo.tipo_secundario')
            ->get();

        $idsExactas = $exactas->pluck('id')->toArray();

        // 2. Coincidencia parcial (solo primario o secundario, pero no ambos)
        $parciales = DB::table('carrera_tipo')
            ->join('carreras', 'carrera_tipo.carrera_id', '=', 'carreras.id')
            ->where(function($q) use ($tipoPrimario, $tipoSecundario) {
                $q->where('carrera_tipo.tipo_primario', $tipoPrimario)
                ->orWhere('carrera_tipo.tipo_secundario', $tipoSecundario);
            })
            ->where(function($q) use ($tipoPrimario, $tipoSecundario) {
               $q->where('carrera_tipo.tipo_primario', '!=', $tipoPrimario)
                ->orWhere('carrera_tipo.tipo_secundario', '!=', $tipoSecundario);
            })
            ->whereNotIn('carreras.id', $idsExactas)
            ->select('carreras.id', 'carreras.nombre', 'carreras.area_conocimiento', 
                    'carreras.descripcion', 'carreras.es_institucional',
                    'carrera_tipo.tipo_primario', 'carrera_tipo.tipo_secundario')
            ->get();

        $idsParciales = $parciales->pluck('id')->toArray();

        // 3. Si faltan, rellena con otras carreras (sin coincidencia)
        $idsYaIncluidos = array_merge($idsExactas, $idsParciales);
        $faltantes = 10 - ($exactas->count() + $parciales->count());
        $otras = collect();
        if ($faltantes > 0) {
            $otras = DB::table('carreras')
                ->whereNotIn('id', $idsYaIncluidos)
                ->select('id', 'nombre', 'area_conocimiento', 'descripcion', 'es_institucional')
                ->limit($faltantes)
                ->get();
        }

        // Unir todas las carreras recomendadas
        $todas = $exactas->concat($parciales)->concat($otras)->take(10);

        // Armar recomendaciones con match personalizado
        $recomendaciones = [];
        foreach ($todas as $carrera) {
            $match = $this->calcularMatchPersonalizado($carrera, $porcentajesUsuario);

            $recomendacion = [
                'carrera_id' => $carrera->id,
                'nombre' => $carrera->nombre,
                'area' => $carrera->area_conocimiento,
                'descripcion' => $carrera->descripcion,
                'match' => $match,
                'es_institucional' => $carrera->es_institucional,
                'universidades' => []
            ];

            // Universidades asociadas
            try {
                if (Schema::hasTable('carrera_universidad') && Schema::hasTable('universidades')) {
                    $universidades = DB::table('carrera_universidad')
                        ->join('universidades', 'carrera_universidad.universidad_id', '=', 'universidades.id')
                        ->where('carrera_id', $carrera->id)
                        ->where('disponible', true)
                        ->select(
                            'universidades.id',
                            'universidades.nombre',
                            'universidades.departamento',
                            'universidades.tipo',
                            'universidades.sitio_web',
                            'universidades.acreditada',
                            'carrera_universidad.modalidad',
                            'carrera_universidad.duracion',
                            'carrera_universidad.costo_semestre'
                        )
                        ->get();

                    $recomendacion['universidades'] = $universidades;
                }
            } catch (\Exception $e) {
                Log::error('Error al obtener universidades: ' . $e->getMessage());
            }

            $recomendaciones[] = $recomendacion;
        }

        // Ordenar por match descendente
        usort($recomendaciones, function($a, $b) {
            return $b['match'] <=> $a['match'];
        });

        return $recomendaciones;
    }

    private function obtenerRecomendacionesAlternativas($tipoPrimario, $tipoSecundario = [])
    {
        // 1. Carreras con al menos una universidad asociada (disponible)
        $carrerasConUniversidad = Carrera::whereHas('universidades', function($q) {
                $q->where('disponible', true);
            })
            ->with(['universidades' => function($q) {
                $q->where('disponible', true);
            }])
            ->limit(10)
            ->get();

        // 2. Si faltan, completar con carreras sin universidad asociada
        $faltantes = 10 - $carrerasConUniversidad->count();
        $carrerasSinUniversidad = collect();
        if ($faltantes > 0) {
            $carrerasSinUniversidad = Carrera::whereDoesntHave('universidades', function($q) {
                    $q->where('disponible', true);
                })
                ->limit($faltantes)
                ->get();
        }

        // 3. Unir ambas colecciones
        $carreras = $carrerasConUniversidad->concat($carrerasSinUniversidad);

        // 4. Formatear resultados
        $recomendaciones = [];
        foreach ($carreras as $carrera) {
            // Calcula el match real usando los porcentajes del usuario
            $match = $this->calcularMatchPersonalizado($carrera, $porcentajesUsuario ?? []);
            $recomendaciones[] = [
                'carrera_id' => $carrera->id,
                'nombre' => $carrera->nombre,
                'area' => $carrera->area_conocimiento,
                'descripcion' => $carrera->descripcion,
                'match' => $match,
                'universidades' => $carrera->universidades ?? []
            ];
        }

        return $recomendaciones;
    }

    // Esta función ya no se usa para el match principal, pero la dejo por si la necesitas para otros cálculos
    private function calcularPorcentajeMatch($userPrimario, $userSecundario, $carreraPrimario, $carreraSecundario, $esInstitucional = false)
    {
        $match = 0;

        if ($userPrimario == $carreraPrimario && $userSecundario == $carreraSecundario) {
            $match = 95;
        } else if ($userPrimario == $carreraSecundario && $userSecundario == $carreraPrimario) {
            $match = 90;
        } else if ($userPrimario == $carreraPrimario) {
            $match = 85;
        } else if ($userPrimario == $carreraSecundario) {
            $match = 80;
        } else if ($userSecundario == $carreraPrimario) {
            $match = 75;
        } else {
            $match = 65;
        }

        if ($esInstitucional) {
            $match += 5;
        }

        return min(round($match), 100);
    }

    public function resultados(Test $test)
    {
        if ($test->user_id !== auth()->id() && 
            !in_array(auth()->user()->role, ['superadmin', 'coordinador'])) {
            abort(403, 'No autorizado');
        }

        if (!$test->completado) {
            return $this->procesarResultados($test);
        }

        $tiposPersonalidad = TipoPersonalidad::pluck('descripcion', 'codigo')->toArray();

        if (empty($tiposPersonalidad)) {
            $tiposPersonalidad = [
                'R' => [
                    'nombre' => 'Realista',
                    'descripcion' => 'Personas prácticas y orientadas a la acción. Prefieren trabajar con objetos, máquinas, herramientas, plantas o animales.',
                    'color' => '#e74c3c',
                ],
                'I' => [
                    'nombre' => 'Investigativo',
                    'descripcion' => 'Personas analíticas, intelectuales y curiosas. Prefieren actividades que impliquen pensar, observar, investigar y resolver problemas.',
                    'color' => '#3498db',
                ],
                'A' => [
                    'nombre' => 'Artístico',
                    'descripcion' => 'Personas creativas, intuitivas y sensibles. Disfrutan de la auto-expresión, la innovación y actividades sin una estructura clara.',
                    'color' => '#9b59b6',
                ],
                'S' => [
                    'nombre' => 'Social',
                    'descripcion' => 'Personas amigables, colaborativas y empáticas. Disfrutan trabajando con otras personas, ayudando, enseñando o brindando asistencia.',
                    'color' => '#2ecc71',
                ],
                'E' => [
                    'nombre' => 'Emprendedor',
                    'descripcion' => 'Personas persuasivas, ambiciosas y seguras. Prefieren liderar, convencer a otros y tomar riesgos para lograr objetivos.',
                    'color' => '#f39c12',
                ],
                'C' => [
                    'nombre' => 'Convencional',
                    'descripcion' => 'Personas organizadas, detallistas y precisas. Prefieren seguir procedimientos establecidos y trabajar con datos de manera ordenada.',
                    'color' => '#1abc9c',
                ],
            ];
        }

        return view('test.resultados', compact('test', 'tiposPersonalidad'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'respuestas' => 'required|array',
            'respuestas.*' => 'required|integer|min:0|max:2',
        ]);

        $test = Test::findOrFail($request->test_id);

        if ($test->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        Respuesta::where('test_id', $test->id)->delete();

        foreach ($request->respuestas as $pregunta_id => $valor) {
            Respuesta::create([
                'test_id' => $test->id,
                'pregunta_id' => $pregunta_id,
                'valor' => (int)$valor,
                'user_id' => auth()->id(),
            ]);
        }

        return $this->procesarResultados($test);
    }

    public function historial()
    {
        $tests = Test::where('user_id', auth()->id())
            ->where('completado', true)
            ->orderBy('fecha_completado', 'desc')
            ->paginate(10);

        return view('test.historial', compact('tests'));
    }

    public function eliminar(Test $test)
    {
        if ($test->user_id !== auth()->id() && auth()->user()->role !== 'superadmin') {
            abort(403, 'No autorizado');
        }

        Respuesta::where('test_id', $test->id)->delete();
        $test->delete();

        return redirect()->route('test.historial')
            ->with('success', 'Test eliminado correctamente.');
    }
}