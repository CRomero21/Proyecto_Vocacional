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

        if ($test->completado) {
            return redirect()->route('test.resultados', $test->id);
        }

        // Preguntas en orden aleatorio para realizar el test
        $preguntas = Pregunta::inRandomOrder()->get();
        
        return view('test.realizar', [
            'preguntas' => $preguntas,
            'test_id' => $test->id,
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
            ->orderByDesc('fecha_completado')
            ->get();

        $perfil_riasec = [];
        $carreras_sugeridas = [];
        
        if ($tests->count()) {
            $ultimo = $tests->first();
            $resultados = $ultimo->resultados;
            if (is_string($resultados)) {
                $resultados = json_decode($resultados, true);
            }
            $perfil_riasec = $resultados['porcentajes'] ?? [];
            $carreras_sugeridas = $resultados['recomendaciones'] ?? [];
        }

        return view('dashboard', [
            'tests' => $tests,
            'perfil_riasec' => $perfil_riasec,
            'carreras_sugeridas' => $carreras_sugeridas,
        ]);
    }

    public function resultados(Test $test)
    {
        if ($test->user_id !== auth()->id() && 
            !in_array(auth()->user()->role ?? '', ['superadmin', 'coordinador'])) {
            abort(403, 'No autorizado');
        }

        if (!$test->completado) {
            return redirect()->route('test.continuar', $test->id)
                ->with('info', 'Debes completar el test primero.');
        }

        $tiposPersonalidad = TipoPersonalidad::pluck('descripcion', 'codigo')->toArray();

        if (empty($tiposPersonalidad)) {
            $tiposPersonalidad = [
                'R' => 'Personas prácticas y orientadas a la acción. Prefieren trabajar con objetos, máquinas, herramientas, plantas o animales.',
                'I' => 'Personas analíticas, intelectuales y curiosas. Prefieren actividades que impliquen pensar, observar, investigar y resolver problemas.',
                'A' => 'Personas creativas, intuitivas y sensibles. Disfrutan de la auto-expresión, la innovación y actividades sin una estructura clara.',
                'S' => 'Personas amigables, colaborativas y empáticas. Disfrutan trabajando con otras personas, ayudando, enseñando o brindando asistencia.',
                'E' => 'Personas persuasivas, ambiciosas y seguras. Prefieren liderar, convencer a otros y tomar riesgos para lograr objetivos.',
                'C' => 'Personas organizadas, detallistas y precisas. Prefieren seguir procedimientos establecidos y trabajar con datos de manera ordenada.',
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

        // Procesar resultados directamente desde las respuestas del formulario
        $puntajes = [
            'R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0
        ];
        $contadores = [
            'R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0
        ];

        // Obtener las preguntas una sola vez para optimizar
        $preguntas = Pregunta::whereIn('id', array_keys($request->respuestas))->get()->keyBy('id');

        // Procesar respuestas directamente del request
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

        // Obtener recomendaciones de carreras
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

        // Guardar recomendaciones en tabla separada
        if (Schema::hasTable('test_carrera_recomendacion')) {
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

        // Eliminar recomendaciones de carreras
        if (Schema::hasTable('test_carrera_recomendacion')) {
            DB::table('test_carrera_recomendacion')->where('test_id', $test->id)->delete();
        }
        
        // Ya no necesitamos eliminar respuestas porque no las guardamos
        $test->delete();

        return redirect()->route('test.historial')
            ->with('success', 'Test eliminado correctamente.');
    }
    
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

        // Dar preferencia a carreras institucionales
        if (isset($carrera->es_institucional) && $carrera->es_institucional) {
            $match += 10; // Bonus de 10% para carreras institucionales
        }

        return min($match, 100); // No superar el 100%
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
        $faltantes = 5 - ($exactas->count() + $parciales->count()); // Reducimos a 5 para dejar espacio a recomendaciones por área
        $otras = collect();
        if ($faltantes > 0) {
            $otras = DB::table('carreras')
                ->whereNotIn('id', $idsYaIncluidos)
                ->select('id', 'nombre', 'area_conocimiento', 'descripcion', 'es_institucional')
                ->limit($faltantes)
                ->get();
        }

        // Unir todas las carreras recomendadas principales
        $todas = $exactas->concat($parciales)->concat($otras);
        
        // Armar recomendaciones con match personalizado
        $recomendacionesPrincipales = [];
        $areasConocimiento = [];
        
        foreach ($todas as $carrera) {
            $match = $this->calcularMatchPersonalizado($carrera, $porcentajesUsuario);

            $recomendacion = [
                'carrera_id' => $carrera->id,
                'nombre' => $carrera->nombre,
                'area' => $carrera->area_conocimiento,
                'descripcion' => $carrera->descripcion,
                'match' => $match,
                'es_institucional' => $carrera->es_institucional,
                'es_primaria' => true,
                'universidades' => []
            ];
            
            // Guardar áreas de conocimiento para buscar carreras relacionadas
            if (!empty($carrera->area_conocimiento) && !in_array($carrera->area_conocimiento, $areasConocimiento)) {
                $areasConocimiento[] = $carrera->area_conocimiento;
            }

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
                        ->get()
                        ->toArray(); // Convertir a array para evitar el error de objeto vs array

                    $recomendacion['universidades'] = $universidades;
                }
            } catch (\Exception $e) {
                Log::error('Error al obtener universidades: ' . $e->getMessage());
            }

            $recomendacionesPrincipales[] = $recomendacion;
        }
        
        // 4. Obtener carreras relacionadas por área de conocimiento
        $idsCarrerasYaIncluidas = array_column($recomendacionesPrincipales, 'carrera_id');
        
        if (!empty($areasConocimiento)) {
            $carrerasRelacionadas = DB::table('carreras')
                ->whereIn('area_conocimiento', $areasConocimiento)
                ->whereNotIn('id', $idsCarrerasYaIncluidas)
                ->select('id', 'nombre', 'area_conocimiento', 'descripcion', 'es_institucional')
                ->limit(5)
                ->get();
                
            foreach ($carrerasRelacionadas as $carrera) {
                $match = $this->calcularMatchPersonalizado($carrera, $porcentajesUsuario);
                
                $recomendacion = [
                    'carrera_id' => $carrera->id,
                    'nombre' => $carrera->nombre,
                    'area' => $carrera->area_conocimiento,
                    'descripcion' => $carrera->descripcion,
                    'match' => $match,
                    'es_institucional' => $carrera->es_institucional,
                    'es_primaria' => false,
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
                            ->get()
                            ->toArray(); // Convertir a array para evitar el error de objeto vs array

                        $recomendacion['universidades'] = $universidades;
                    }
                } catch (\Exception $e) {
                    Log::error('Error al obtener universidades: ' . $e->getMessage());
                }
                
                $recomendacionesPrincipales[] = $recomendacion;
            }
        }
        
        // Ordenar el conjunto completo nuevamente por match e institucional
        usort($recomendacionesPrincipales, function($a, $b) {
            // Si uno es institucional y el otro no, priorizar el institucional
            if ($a['es_institucional'] && !$b['es_institucional']) return -1;
            if (!$a['es_institucional'] && $b['es_institucional']) return 1;
            
            // Si uno es recomendación primaria y el otro no, priorizar la primaria
            if ($a['es_primaria'] && !$b['es_primaria']) return -1;
            if (!$a['es_primaria'] && $b['es_primaria']) return 1;
            
            // Finalmente ordenar por porcentaje de match
            return $b['match'] <=> $a['match'];
        });
        
        // Limitar a 10 resultados finales
        return array_slice($recomendacionesPrincipales, 0, 10);
    }
    
    /**
     * Genera informes de análisis basados en los tests RIASEC y recomendaciones de carreras
     */
    public function informes()
    {
        // Tests completados por tipo de personalidad
        $porTipoPersonalidad = DB::table('test')
            ->where('completado', true)
            ->selectRaw('tipo_primario, COUNT(*) as total')
            ->groupBy('tipo_primario')
            ->orderByDesc('total')
            ->get();
            
        // Carreras más recomendadas
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
        
        // Distribución por áreas de conocimiento
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
}