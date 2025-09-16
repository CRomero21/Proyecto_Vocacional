<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Pregunta;
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
        $resultados = $this->getResultadosArray($test->resultados);

        $carrerasPrincipales = $resultados['recomendaciones']['afines'] ?? [];
        $carrerasSecundarias = $resultados['recomendaciones']['relacionadas'] ?? [];

        return view('test.resultados', compact(
            'test',
            'tiposPersonalidad',
            'resultados',
            'carrerasPrincipales',
            'carrerasSecundarias'
        ));
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
        $valoresPuntaje = array_values($puntajes);

        $topValues = [];
        for ($i = 0; $i < 3; $i++) {
            $topValues[] = $valoresPuntaje[$i] ?? null;
        }
        $topValues = array_unique($topValues);

        $tiposUsuario = [];
        foreach ($puntajes as $tipo => $valor) {
            if (in_array($valor, $topValues)) {
                $tiposUsuario[] = $tipo;
            }
        }

        $tipoPrimario = $tiposUsuario[0] ?? null;
        $tipoSecundario = $tiposUsuario[1] ?? null;

        $recomendaciones = $this->obtenerRecomendacionesFlexibles($tiposUsuario, $porcentajes);

        if ($recomendaciones === null) {
            $recomendaciones = [];
            Log::warning('No se encontraron recomendaciones para el perfil RIASEC: ' . implode('-', $tiposUsuario));
        }

        $resultados = [
            'puntajes' => $puntajes,
            'promedios' => $promedios,
            'porcentajes' => $porcentajes,
            'recomendaciones' => $recomendaciones,
            'fecha_procesamiento' => now()->toDateTimeString(),
            'retroalimentacion' => null
        ];

        // **CORRECCIÓN: Validaciones para evitar guardar si hay datos null**
        if (empty($resultados['porcentajes']) || 
            !is_array($resultados['porcentajes']) || 
            array_sum($resultados['porcentajes']) === 0 || 
            $tipoPrimario === null || 
            empty($resultados['recomendaciones'])) {
            return redirect()->back()->withErrors([
                'error' => 'No se puede guardar el test porque faltan datos críticos (porcentajes, tipo primario o recomendaciones). Verifica que hayas respondido todas las preguntas correctamente.'
            ])->withInput();
        }

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
            
            $orden = 1;
            foreach (['afines', 'relacionadas'] as $bloque) {
                if (!empty($recomendaciones[$bloque])) {
                    foreach ($recomendaciones[$bloque] as $recomendacion) {
                        DB::table('test_carrera_recomendacion')->insert([
                            'test_id' => $test->id,
                            'carrera_id' => $recomendacion['carrera_id'],
                            'match_porcentaje' => $recomendacion['score'],
                            'orden' => $orden++,
                            'es_primaria' => ($bloque === 'afines'),
                            'area_conocimiento' => $recomendacion['area'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }

        return redirect()->route('test.resultados', $test->id)
            ->with('success', 'Resultados procesados correctamente.');
    }

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

        if (Schema::hasTable('test_carrera_recomendacion')) {
            DB::table('test_carrera_recomendacion')->where('test_id', $test->id)->delete();
        }
        
        $test->delete();

        return redirect()->route('test.historial')
            ->with('success', 'Test eliminado correctamente.');
    }

    /**
     * Matching flexible con score variado, ponderado por porcentajes y universidades asociadas.
     * Siempre incluye la carrera, incluso si no hay universidades.
     * Asegura al menos 1 carrera institucional si no hay ninguna en las recomendaciones.
     */
    private function obtenerRecomendacionesFlexibles($tiposUsuario, $porcentajesUsuario = [])
    {
        $carreras = DB::table('carreras')
            ->join('carrera_tipo', 'carreras.id', '=', 'carrera_tipo.carrera_id')
            ->select(
                'carreras.id',
                'carreras.nombre',
                'carreras.area_conocimiento',
                'carreras.descripcion',
                'carreras.es_institucional',
                'carrera_tipo.tipo_primario',
                'carrera_tipo.tipo_secundario',
                'carrera_tipo.tipo_terciario'
            )
            ->get();

        $recomendaciones = [];

        foreach ($carreras as $carrera) {
            $tiposCarrera = [
                $carrera->tipo_primario,
                $carrera->tipo_secundario,
                $carrera->tipo_terciario
            ];
            $score = 0;

            // Coincidencias exactas y parciales
            foreach ($tiposCarrera as $i => $tipo) {
                if (isset($tiposUsuario[$i]) && $tiposUsuario[$i] === $tipo) {
                    $score += 40;
                } elseif (in_array($tipo, $tiposUsuario)) {
                    $score += 20;
                }
            }

            // Bonus basado en porcentajes del perfil (para mayor precisión)
            $bonusPerfil = 0;
            foreach ($tiposCarrera as $tipo) {
                if (isset($porcentajesUsuario[$tipo])) {
                    $bonusPerfil += $porcentajesUsuario[$tipo] * 0.5;  // Pondera con 50% del porcentaje
                }
            }
            $score += $bonusPerfil;

            $score += rand(0, 9);  // Variación aleatoria

            // Depuración: Ver score raw
            \Log::info("Carrera: " . $carrera->nombre . " - Score raw: " . $score);

            // Normalizar a porcentaje (0-100%)
            $scorePorcentaje = min(100, round(($score / 129) * 100));

            // Depuración: Ver score porcentaje
            \Log::info("Carrera: " . $carrera->nombre . " - Score porcentaje: " . $scorePorcentaje);

            if ($scorePorcentaje > 0) {
                // Universidades asociadas (siempre incluir, con mensaje si no hay)
                $universidades = DB::table('carrera_universidad')
                    ->join('universidades', 'carrera_universidad.universidad_id', '=', 'universidades.id')
                    ->where('carrera_universidad.carrera_id', $carrera->id)
                    ->select(
                        'universidades.id',
                        'universidades.nombre',
                        'universidades.departamento',
                        'universidades.tipo',
                        'universidades.acreditada',
                        'universidades.sitio_web'
                    )
                    ->get()
                    ->toArray();

                // Si no hay universidades, mostrar mensaje claro
                if (empty($universidades)) {
                    $universidades = [
                        [
                            'id' => null,
                            'nombre' => 'No hay universidades registradas para esta carrera.',
                            'departamento' => 'Consulta con instituciones locales para más opciones.',
                            'tipo' => null,
                            'acreditada' => false,
                            'sitio_web' => null
                        ]
                    ];
                }

                $recomendaciones[] = [
                    'carrera_id' => $carrera->id,
                    'nombre' => $carrera->nombre,
                    'area' => $carrera->area_conocimiento,
                    'descripcion' => $carrera->descripcion,
                    'es_institucional' => $carrera->es_institucional,
                    'score' => $scorePorcentaje,
                    'tipos' => implode('-', $tiposCarrera),
                    'universidades' => $universidades,
                ];
            }
        }

        // Ordenar por score descendente
        usort($recomendaciones, fn($a, $b) => $b['score'] <=> $a['score']);

        // Filtrar para diversificar: Priorizar tipos principales (R, A, E)
        $tiposPrincipales = array_slice($tiposUsuario, 0, 3);  // Top 3 tipos
        $afines = [];
        $relacionadas = [];
        $seleccionadas = [];  // Evitar duplicados

        foreach ($recomendaciones as $rec) {
            if (in_array($rec['carrera_id'], $seleccionadas)) {
                continue;
            }

            $tiposRec = explode('-', $rec['tipos']);
            $coincidePrincipal = array_intersect($tiposRec, $tiposPrincipales);
            if (count($coincidePrincipal) >= 2 && count($afines) < 5) {
                $afines[] = $rec;
                $seleccionadas[] = $rec['carrera_id'];
            } elseif (count($relacionadas) < 5) {
                $relacionadas[] = $rec;
                $seleccionadas[] = $rec['carrera_id'];
            }
        }

        // Si no hay suficientes, llenar con las mejores restantes (sin duplicados)
        if (count($afines) < 5) {
            foreach ($recomendaciones as $rec) {
                if (!in_array($rec['carrera_id'], $seleccionadas) && count($afines) < 5) {
                    $afines[] = $rec;
                    $seleccionadas[] = $rec['carrera_id'];
                }
            }
        }
        if (count($relacionadas) < 5) {
            foreach ($recomendaciones as $rec) {
                if (!in_array($rec['carrera_id'], $seleccionadas) && count($relacionadas) < 5) {
                    $relacionadas[] = $rec;
                    $seleccionadas[] = $rec['carrera_id'];
                }
            }
        }

        // Asegurar al menos 1 carrera institucional si no hay ninguna
        $tieneInstitucional = false;
        foreach (array_merge($afines, $relacionadas) as $rec) {
            if ($rec['es_institucional']) {
                $tieneInstitucional = true;
                break;
            }
        }

        if (!$tieneInstitucional) {
            foreach ($recomendaciones as $rec) {
                if ($rec['es_institucional'] && !in_array($rec['carrera_id'], $seleccionadas)) {
                    if (count($relacionadas) < 5) {
                        $relacionadas[] = $rec;
                        $seleccionadas[] = $rec['carrera_id'];
                    } elseif (count($afines) < 5) {
                        $afines[] = $rec;
                        $seleccionadas[] = $rec['carrera_id'];
                    }
                    break;  // Añadir solo 1
                }
            }
        }

        return [
            'afines' => $afines,
            'relacionadas' => $relacionadas
        ];
    }

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

    public function exportarPDF($id)
    {
        $test = \App\Models\Test::findOrFail($id);
        $resultados = $this->getResultadosArray($test->resultados);
        $tiposPersonalidad = $this->tiposPersonalidad();

        // Calcular tipos dominantes y recomendaciones (manejar empates)
        $recomendacionesAreas = [];
        if (!empty($resultados['porcentajes'])) {
            $porcentajes = $resultados['porcentajes'];
            arsort($porcentajes); // Ordenar de mayor a menor
            
            // Encontrar el porcentaje máximo
            $maxPorcentaje = reset($porcentajes);
            
            // Identificar todos los tipos que tienen el porcentaje máximo (manejar empates)
            $tiposDominantes = [];
            foreach ($porcentajes as $tipo => $porcentaje) {
                if ($porcentaje === $maxPorcentaje) {
                    $tiposDominantes[] = $tipo;
                } else {
                    break; // Como está ordenado, podemos parar cuando encontremos un porcentaje menor
                }
            }
            
            // Mapeo de tipos RIASEC a áreas de estudio
            $mapeoAreas = [
                'R' => ['area' => 'Ingeniería, Tecnología o Ciencias Naturales', 'descripcion' => 'Áreas prácticas y técnicas que involucran trabajo con objetos, máquinas y resolución de problemas concretos, alineadas con tu enfoque realista y orientado a la acción.'],
                'I' => ['area' => 'Ciencias, Matemáticas o Investigación', 'descripcion' => 'Áreas analíticas y científicas que requieren pensamiento lógico, observación y resolución de problemas complejos, ideales para tu curiosidad intelectual.'],
                'A' => ['area' => 'Artes, Humanidades o Diseño', 'descripcion' => 'Áreas creativas y expresivas que permiten la innovación, auto-expresión y trabajo sin estructuras rígidas, perfectas para tu sensibilidad artística.'],
                'S' => ['area' => 'Ciencias Sociales, Educación o Salud', 'descripcion' => 'Áreas relacionadas con personas, servicio y empatía, donde puedes ayudar, enseñar y trabajar en entornos colaborativos, aprovechando tu amabilidad social.'],
                'E' => ['area' => 'Administración, Economía o Negocios', 'descripcion' => 'Áreas de liderazgo, gestión y toma de riesgos, donde puedes convencer a otros y lograr objetivos ambiciosos, reflejando tu personalidad emprendedora.'],
                'C' => ['area' => 'Contabilidad, Finanzas o Administración', 'descripcion' => 'Áreas organizadas y detalladas que involucran procedimientos establecidos, datos y precisión, ideales para tu enfoque convencional y meticuloso.']
            ];
            
            // Generar recomendaciones para cada tipo dominante
            foreach ($tiposDominantes as $tipo) {
                $recomendacion = $mapeoAreas[$tipo] ?? ['area' => 'Áreas generales de estudio', 'descripcion' => 'Consulta con un orientador para recomendaciones personalizadas.'];
                $recomendacionesAreas[] = [
                    'tipo' => $tipo,
                    'porcentaje' => $maxPorcentaje,
                    'area' => $recomendacion['area'],
                    'descripcion' => $recomendacion['descripcion']
                ];
            }
        }

        $titulo = 'Resultados de Test Vocacional';
        return \PDF::loadView('test.resultados_pdf', compact(
            'test',
            'resultados',
            'tiposPersonalidad',
            'titulo',
            'recomendacionesAreas'
        ))
        ->download('resultados_test_'.$test->id.'.pdf');
    } 
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
                ->limit(7)
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
}