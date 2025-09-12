<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Pregunta;
use App\Models\Carrera;
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

    // Iniciar un nuevo test
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

        $preguntas = Pregunta::inRandomOrder()->get();

        return view('test.realizar', [
            'preguntas' => $preguntas,
            'test_id' => $test->id,
        ]);
    }

    // Continuar un test pendiente
    public function continuar(Test $test)
    {
        if ($test->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        if ($test->completado) {
            return redirect()->route('test.resultados', $test->id);
        }

        $preguntas = Pregunta::inRandomOrder()->get();
        
        return view('test.realizar', [
            'preguntas' => $preguntas,
            'test_id' => $test->id,
        ]);
    }

    // Mostrar dashboard (no se usa para resultados individuales)
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

    // Dashboard para el usuario autenticado
    public function dashboard()
    {
        $user = Auth::user();

        $tests = Test::where('user_id', $user->id)
            ->where('completado', true)
            ->orderByDesc('fecha_completado')
            ->get();

        $perfil_riasec = [];
        $carreras_sugeridas = [];
        
        if ($tests->count()) {
            $ultimo = $tests->first();
            $resultados = $this->getResultadosArray($ultimo->resultados);
            $perfil_riasec = $resultados['porcentajes'] ?? [];
            $carreras_sugeridas = $resultados['recomendaciones'] ?? [];
        }

        return view('dashboard', [
            'tests' => $tests,
            'perfil_riasec' => $perfil_riasec,
            'carreras_sugeridas' => $carreras_sugeridas,
        ]);
    }

    // Mostrar resultados de un test específico
    public function resultados(Test $test)
    {
        $user = auth()->user();
        if ($test->user_id !== $user->id && 
            !in_array($user->role ?? '', ['superadmin', 'coordinador'])) {
            abort(403, 'No autorizado');
        }

        if (!$test->completado) {
            return redirect()->route('test.continuar', $test->id)
                ->with('info', 'Debes completar el test primero.');
        }

        $tiposPersonalidad = $this->tiposPersonalidad();

        return view('test.resultados', compact('test', 'tiposPersonalidad'));
    }

    // Guardar respuestas y procesar resultados
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

        // Calcular puntajes y porcentajes por tipo RIASEC
        $puntajes = [
            'R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0
        ];
        $contadores = [
            'R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0
        ];

        $preguntas = Pregunta::whereIn('id', array_keys($request->respuestas))->get()->keyBy('id');

        foreach ($request->respuestas as $pregunta_id => $valor) {
            $pregunta = $preguntas[$pregunta_id] ?? null;
            if ($pregunta) {
                $tipo = $pregunta->tipo;
                if (isset($puntajes[$tipo])) {
                    $puntajes[$tipo] += (int)$valor;
                    $contadores[$tipo]++;
                }
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

        // Si hay empate, elegir por promedio
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

        // Obtener recomendaciones de carreras con el algoritmo mejorado
        $recomendaciones = $this->obtenerRecomendacionesFlexibles($tipoPrimario, $tipoSecundario, $porcentajes);

        if ($recomendaciones === null) {
            $recomendaciones = [];
            Log::warning('No se encontraron recomendaciones para el perfil RIASEC: ' . $tipoPrimario . '-' . $tipoSecundario);
        }

        $resultados = [
            'puntajes' => $puntajes,
            'promedios' => $promedios,
            'porcentajes' => $porcentajes,
            'recomendaciones' => $recomendaciones,
            'fecha_procesamiento' => now()->toDateTimeString(),
            'retroalimentacion' => null
        ];

        $test->update([
            'tipo_primario' => $tipoPrimario,
            'tipo_secundario' => $tipoSecundario,
            'resultados' => $resultados,
            'completado' => true,
            'fecha_completado' => now()
        ]);

        // Guardar recomendaciones en tabla separada si existe
        if (Schema::hasTable('test_carrera_recomendacion') && is_array($recomendaciones) && !empty($recomendaciones)) {
            DB::table('test_carrera_recomendacion')->where('test_id', $test->id)->delete();
            
            foreach ($recomendaciones as $index => $recomendacion) {
                DB::table('test_carrera_recomendacion')->insert([
                    'test_id' => $test->id,
                    'carrera_id' => $recomendacion['carrera_id'],
                    'match_porcentaje' => $recomendacion['match'],
                    'orden' => $index + 1,
                    'es_primaria' => $recomendacion['es_primaria'] ?? true,
                    'area_conocimiento' => $recomendacion['area'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('test.resultados', $test->id)
            ->with('success', 'Resultados procesados correctamente.');
    }

    // Guardar retroalimentación del usuario
    public function guardarRetroalimentacion(Request $request, Test $test)
    {
        $request->validate([
            'utilidad' => 'required|integer|min:1|max:5',
            'precision' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
            'carrera_seleccionada' => 'nullable|exists:carreras,id'
        ]);
        
        $resultados = $this->getResultadosArray($test->resultados);
        
        $resultados['retroalimentacion'] = [
            'utilidad' => $request->utilidad,
            'precision' => $request->precision,
            'comentario' => $request->comentario,
            'carrera_seleccionada' => $request->carrera_seleccionada,
            'fecha' => now()->toDateTimeString()
        ];
        
        $test->update([
            'resultados' => $resultados
        ]);
        
        return back()->with('success', '¡Gracias por tu retroalimentación! Nos ayudará a mejorar.');
    }

    // Historial de tests del usuario
    public function historial()
    {
        $tests = Test::where('user_id', auth()->id())
            ->where('completado', true)
            ->orderBy('fecha_completado', 'desc')
            ->paginate(10);

        return view('test.historial', compact('tests'));
    }

    // Eliminar un test y sus recomendaciones asociadas
    public function eliminar(Test $test)
    {
        if ($test->user_id !== auth()->id() && auth()->user()->role !== 'superadmin') {
            abort(403, 'No autorizado');
        }

        if (Schema::hasTable('test_carrera_recomendacion')) {
            DB::table('test_carrera_recomendacion')->where('test_id', $test->id)->delete();
        }
        
        $test->delete();

        return redirect()->route('test.historial')
            ->with('success', 'Test eliminado correctamente.');
    }
    
    /**
     * Recomendaciones flexibles y realistas de carreras
     * Siempre marca como principales las 3 carreras con mayor match.
     */
    private function obtenerRecomendacionesFlexibles($tipoPrimario, $tipoSecundario, $porcentajesUsuario = [])
    {
        try {
            if (!$tipoPrimario) {
                arsort($porcentajesUsuario);
                $tiposClaves = array_keys($porcentajesUsuario);
                $tipoPrimario = $tiposClaves[0] ?? 'R';
                $tipoSecundario = $tiposClaves[1] ?? 'I';
            }

            $todasCarreras = DB::table('carreras')
                ->leftJoin('carrera_tipo', 'carreras.id', '=', 'carrera_tipo.carrera_id')
                ->select(
                    'carreras.id', 
                    'carreras.nombre', 
                    'carreras.area_conocimiento', 
                    'carreras.descripcion', 
                    'carreras.es_institucional',
                    'carrera_tipo.tipo_primario', 
                    'carrera_tipo.tipo_secundario'
                )
                ->get();

            if ($todasCarreras->isEmpty()) {
                return [
                    'afines' => [],
                    'relacionadas' => []
                ];
            }

            $carrerasConMatch = [];
            foreach ($todasCarreras as $carrera) {
                if (empty($carrera->tipo_primario)) $carrera->tipo_primario = 'R';
                if (empty($carrera->tipo_secundario)) $carrera->tipo_secundario = 'I';

                $match = $this->calcularMatchPersonalizado($carrera, $porcentajesUsuario);

                if (!isset($carrerasConMatch[$carrera->id]) || $match > $carrerasConMatch[$carrera->id]['match']) {
                    $carrerasConMatch[$carrera->id] = [
                        'carrera_id' => $carrera->id,
                        'nombre' => $carrera->nombre,
                        'area' => $carrera->area_conocimiento,
                        'descripcion' => $carrera->descripcion,
                        'match' => $match,
                        'es_institucional' => $carrera->es_institucional,
                        'tipo_primario' => $carrera->tipo_primario,
                    ];
                }
            }

            // Ordenar por match descendente
            $carrerasConMatch = array_values($carrerasConMatch);
            usort($carrerasConMatch, function($a, $b) {
                return $b['match'] <=> $a['match'];
            });

            // Separar afines y relacionadas
            $afines = [];
            $relacionadas = [];
            foreach ($carrerasConMatch as $carrera) {
                if ($carrera['tipo_primario'] === $tipoPrimario) {
                    $afines[] = $carrera;
                } else {
                    $relacionadas[] = $carrera;
                }
            }

            // Limita la cantidad si quieres
            $afines = array_slice($afines, 0, 5);
            $relacionadas = array_slice($relacionadas, 0, 5);

            return [
                'afines' => $afines,
                'relacionadas' => $relacionadas
            ];

        } catch (\Exception $e) {
            return [
                'afines' => [],
                'relacionadas' => []
            ];
        }
    }
    /**
     * Calcula el match personalizado y realista entre un perfil RIASEC y una carrera
     * Ajuste: multiplica el resultado final por 1.5 para que los matches sean más altos.
     */
    private function calcularMatchPersonalizado($carrera, $porcentajesUsuario)
    {
        $compatibilidadRIASEC = [
            'R' => ['R' => 1.0, 'I' => 0.15, 'A' => 0.05, 'S' => 0.01, 'E' => 0.1, 'C' => 0.1],
            'I' => ['R' => 0.15, 'I' => 1.0, 'A' => 0.2, 'S' => 0.01, 'E' => 0.05, 'C' => 0.05],
            'A' => ['R' => 0.05, 'I' => 0.2, 'A' => 1.0, 'S' => 0.15, 'E' => 0.1, 'C' => 0.01],
            'S' => ['R' => 0.01, 'I' => 0.01, 'A' => 0.15, 'S' => 1.0, 'E' => 0.2, 'C' => 0.05],
            'E' => ['R' => 0.1, 'I' => 0.05, 'A' => 0.1, 'S' => 0.2, 'E' => 1.0, 'C' => 0.15],
            'C' => ['R' => 0.1, 'I' => 0.05, 'A' => 0.01, 'S' => 0.05, 'E' => 0.15, 'C' => 1.0]
        ];

        arsort($porcentajesUsuario);
        $tiposOrdenados = array_keys($porcentajesUsuario);
        $tipoPrimarioUsuario = $tiposOrdenados[0] ?? null;
        $tipoSecundarioUsuario = $tiposOrdenados[1] ?? null;
        $tipoTerciarioUsuario = $tiposOrdenados[2] ?? null;

        $pesoPrimario = 0.95;
        $pesoSecundario = 0.04;
        $pesoTerciario = 0.01;

        $match = 0;
        $pesoTotal = 0;

        if (isset($carrera->tipo_primario)) {
            foreach ($porcentajesUsuario as $tipoUsuario => $porcentaje) {
                $factor = $compatibilidadRIASEC[$carrera->tipo_primario][$tipoUsuario] ?? 0;
                $peso = 0;
                if ($tipoUsuario == $tipoPrimarioUsuario) $peso = $pesoPrimario;
                elseif ($tipoUsuario == $tipoSecundarioUsuario) $peso = $pesoSecundario;
                elseif ($tipoUsuario == $tipoTerciarioUsuario) $peso = $pesoTerciario;
                else continue;
                $match += ($porcentaje * $factor * $peso / 100);
                $pesoTotal += $factor * $peso;
            }
        }

        if (isset($carrera->tipo_secundario)) {
            foreach ($porcentajesUsuario as $tipoUsuario => $porcentaje) {
                $factor = $compatibilidadRIASEC[$carrera->tipo_secundario][$tipoUsuario] ?? 0;
                $peso = 0;
                if ($tipoUsuario == $tipoPrimarioUsuario) $peso = $pesoSecundario;
                elseif ($tipoUsuario == $tipoSecundarioUsuario) $peso = $pesoTerciario;
                elseif ($tipoUsuario == $tipoTerciarioUsuario) $peso = 0.005;
                else continue;
                $match += ($porcentaje * $factor * $peso / 100);
                $pesoTotal += $factor * $peso;
            }
        }

        $matchFinal = $pesoTotal > 0 ? ($match / $pesoTotal) * 100 : 0;

        if ($carrera->tipo_primario == $tipoPrimarioUsuario) {
            $matchFinal += 20;
        }

        $matchFinal = min(round($matchFinal), 100);

        return $matchFinal;
    }
    // Exportar resultados a PDF
    public function exportarPDF($id)
    {
        $test = \App\Models\Test::findOrFail($id);
        $resultados = $this->getResultadosArray($test->resultados);
        $tiposPersonalidad = $this->tiposPersonalidad();

        $titulo = 'Resultados de Test Vocacional';
        return \PDF::loadView('test.resultados_pdf', compact('test', 'resultados', 'tiposPersonalidad', 'titulo'))
            ->download('resultados_test_'.$test->id.'.pdf');
    }

    // Informe avanzado (estadísticas generales)
    public function informes()
    {
        $porTipoPersonalidad = DB::table('test')
            ->where('completado', true)
            ->selectRaw('tipo_primario, COUNT(*) as total')
            ->groupBy('tipo_primario')
            ->orderByDesc('total')
            ->get();
            
        $carrerasMasRecomendadas = [];
        if (Schema::hasTable('test_carrera_recomendacion')) {
            $carrerasMasRecomendadas = DB::table('test_carrera_recomendacion')
                ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                ->selectRaw('carreras.nombre, COUNT(*) as total, AVG(match_porcentaje) as match_promedio')
                ->where('es_primaria', true)
                ->groupBy('carreras.id', 'carreras.nombre')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        }
        
        $porAreaConocimiento = DB::table('test_carrera_recomendacion')
            ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
            ->selectRaw('carreras.area_conocimiento, COUNT(*) as total')
            ->whereNotNull('carreras.area_conocimiento')
            ->groupBy('carreras.area_conocimiento')
            ->orderByDesc('total')
            ->get();
        
        return view('informes.index', compact(
            'porTipoPersonalidad',
            'carrerasMasRecomendadas',
            'porAreaConocimiento'
        ));
    }
    
    // Actualizar preguntas del test RIASEC
    public function actualizarPreguntasRIASEC()
    {
        DB::table('preguntas')->truncate();
        
        $preguntas = [
            // ... (preguntas por tipo RIASEC, igual que antes) ...
        ];
        
        foreach ($preguntas as $pregunta) {
            DB::table('preguntas')->insert([
                'texto' => $pregunta['texto'],
                'tipo' => $pregunta['tipo'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return "Se actualizaron " . count($preguntas) . " preguntas para el test RIASEC.";
    }

    // Métodos auxiliares para limpieza y reutilización

    private function getResultadosArray($resultados)
    {
        return is_string($resultados) ? json_decode($resultados, true) : $resultados;
    }

    private function tiposPersonalidad()
    {
        return [
            'R' => 'Personas prácticas y orientadas a la acción. Prefieren trabajar con objetos, máquinas, herramientas, plantas o animales.',
            'I' => 'Personas analíticas, intelectuales y curiosas. Prefieren actividades que impliquen pensar, observar, investigar y resolver problemas.',
            'A' => 'Personas creativas, intuitivas y sensibles. Disfrutan de la auto-expresión, la innovación y actividades sin una estructura clara.',
            'S' => 'Personas amigables, colaborativas y empáticas. Disfrutan trabajando con otras personas, ayudando, enseñando o brindando asistencia.',
            'E' => 'Personas persuasivas, ambiciosas y seguras. Prefieren liderar, convencer a otros y tomar riesgos para lograr objetivos.',
            'C' => 'Personas organizadas, detallistas y precisas. Prefieren seguir procedimientos establecidos y trabajar con datos de manera ordenada.',
        ];
    }
}