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

        // Obtener recomendaciones de carreras con el algoritmo mejorado
        $recomendaciones = $this->obtenerRecomendacionesFlexibles($tipoPrimario, $tipoSecundario, $porcentajes);

        // Asegurarse de que $recomendaciones nunca sea null
        if ($recomendaciones === null) {
            $recomendaciones = []; // Convertir a array vacío si es null
            Log::warning('No se encontraron recomendaciones para el perfil RIASEC: ' . $tipoPrimario . '-' . $tipoSecundario);
        }

        $resultados = [
            'puntajes' => $puntajes,
            'promedios' => $promedios,
            'porcentajes' => $porcentajes,
            'recomendaciones' => $recomendaciones,
            'fecha_procesamiento' => now()->toDateTimeString(),
            'retroalimentacion' => null // Campo para guardar retroalimentación del usuario
        ];

        $test->update([
            'tipo_primario' => $tipoPrimario,
            'tipo_secundario' => $tipoSecundario,
            'resultados' => $resultados,
            'completado' => true,
            'fecha_completado' => now()
        ]);

        // Guardar recomendaciones en tabla separada (protegido contra null)
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

    public function guardarRetroalimentacion(Request $request, Test $test)
    {
        $request->validate([
            'utilidad' => 'required|integer|min:1|max:5',
            'precision' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
            'carrera_seleccionada' => 'nullable|exists:carreras,id'
        ]);
        
        // Obtener resultados actuales
        $resultados = $test->resultados;
        if (is_string($resultados)) {
            $resultados = json_decode($resultados, true);
        }
        
        // Añadir retroalimentación
        $resultados['retroalimentacion'] = [
            'utilidad' => $request->utilidad,
            'precision' => $request->precision,
            'comentario' => $request->comentario,
            'carrera_seleccionada' => $request->carrera_seleccionada,
            'fecha' => now()->toDateTimeString()
        ];
        
        // Actualizar el test
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

        // Eliminar recomendaciones de carreras
        if (Schema::hasTable('test_carrera_recomendacion')) {
            DB::table('test_carrera_recomendacion')->where('test_id', $test->id)->delete();
        }
        
        // Ya no necesitamos eliminar respuestas porque no las guardamos
        $test->delete();

        return redirect()->route('test.historial')
            ->with('success', 'Test eliminado correctamente.');
    }
    
    /**
     * Obtiene recomendaciones de carreras de manera flexible
     * Siempre retorna un array (nunca null) y muestra carreras aproximadas
     */
    private function obtenerRecomendacionesFlexibles($tipoPrimario, $tipoSecundario, $porcentajesUsuario = [])
    {
        try {
            // Incluso si no hay tipo primario, intentaremos encontrar recomendaciones
            if (!$tipoPrimario) {
                // Tomar los dos tipos con mayor porcentaje
                arsort($porcentajesUsuario);
                $tiposClaves = array_keys($porcentajesUsuario);
                $tipoPrimario = $tiposClaves[0] ?? 'R'; // Valor por defecto
                $tipoSecundario = $tiposClaves[1] ?? 'I'; // Valor por defecto
                
                Log::info("No hay tipo primario, usando los de mayor porcentaje: $tipoPrimario y $tipoSecundario");
            }

            // 1. Obtener TODAS las carreras de la base de datos
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
            
            // Si no hay carreras, crear un array vacío
            if ($todasCarreras->isEmpty()) {
                Log::warning('No hay carreras en la base de datos');
                return [];
            }
            
            // 2. Calcular el match para CADA carrera
            $carrerasConMatch = [];
            
            foreach ($todasCarreras as $carrera) {
                // Si la carrera no tiene tipos RIASEC asignados, usar valores predeterminados
                if (empty($carrera->tipo_primario)) {
                    $carrera->tipo_primario = 'R';
                }
                if (empty($carrera->tipo_secundario)) {
                    $carrera->tipo_secundario = 'I';
                }
                
                // Calcular match con matriz de compatibilidad
                $match = $this->calcularMatchPersonalizado($carrera, $porcentajesUsuario);
                
                // Determinar si es recomendación primaria o secundaria
                $esPrimaria = ($match >= 50);
                
                // IMPORTANTE: Incluir TODAS las carreras, no solo las que tienen alto match
                $carrerasConMatch[] = [
                    'carrera' => $carrera,
                    'match' => $match,
                    'es_primaria' => $esPrimaria
                ];
            }
            
            // 3. Ordenar carreras por porcentaje de match (de mayor a menor)
            usort($carrerasConMatch, function($a, $b) {
                return $b['match'] <=> $a['match'];
            });
            
            // 4. Limitar a 10 resultados y formatear para el resultado final
            $carrerasConMatch = array_slice($carrerasConMatch, 0, 10);
            
            $recomendacionesFinal = [];
            
            foreach ($carrerasConMatch as $item) {
                $carrera = $item['carrera'];
                $match = $item['match'];
                $esPrimaria = $item['es_primaria'];
                
                // Obtener información adicional
                $campoLaboral = $this->obtenerCampoLaboral($carrera->id);
                $habilidades = $this->obtenerHabilidadesCarrera($carrera->id);
                
                $recomendacion = [
                    'carrera_id' => $carrera->id,
                    'nombre' => $carrera->nombre,
                    'area' => $carrera->area_conocimiento,
                    'descripcion' => $carrera->descripcion,
                    'match' => $match,
                    'es_institucional' => $carrera->es_institucional,
                    'es_primaria' => $esPrimaria,
                    'universidades' => [],
                    'campo_laboral' => $campoLaboral,
                    'habilidades' => $habilidades
                ];
                
                // Obtener universidades asociadas
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
                            ->toArray();

                        $recomendacion['universidades'] = $universidades;
                    }
                } catch (\Exception $e) {
                    Log::error('Error al obtener universidades: ' . $e->getMessage());
                    $recomendacion['universidades'] = [];
                }
                
                $recomendacionesFinal[] = $recomendacion;
            }
            
            // Asegurarnos de siempre devolver un array, nunca null
            return $recomendacionesFinal;
            
        } catch (\Exception $e) {
            // Registrar el error para diagnóstico
            Log::error('Error en obtenerRecomendacionesFlexibles: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Devolver un array vacío en caso de error
            return [];
        }
    }
    
    /**
     * Calcula el match personalizado entre un perfil RIASEC y una carrera
     */
    private function calcularMatchPersonalizado($carrera, $porcentajesUsuario)
    {
        // Consideramos todos los tipos RIASEC con una ponderación más precisa
        $match = 0;
        $pesoTotal = 0;
        
        // Matriz de compatibilidad entre tipos RIASEC
        // Los valores representan qué tan compatibles son los tipos entre sí
        $compatibilidadRIASEC = [
            'R' => ['R' => 1.0, 'I' => 0.7, 'A' => 0.3, 'S' => 0.2, 'E' => 0.3, 'C' => 0.6],
            'I' => ['R' => 0.7, 'I' => 1.0, 'A' => 0.6, 'S' => 0.4, 'E' => 0.3, 'C' => 0.4],
            'A' => ['R' => 0.3, 'I' => 0.6, 'A' => 1.0, 'S' => 0.7, 'E' => 0.5, 'C' => 0.2],
            'S' => ['R' => 0.2, 'I' => 0.4, 'A' => 0.7, 'S' => 1.0, 'E' => 0.8, 'C' => 0.3],
            'E' => ['R' => 0.3, 'I' => 0.3, 'A' => 0.5, 'S' => 0.8, 'E' => 1.0, 'C' => 0.7],
            'C' => ['R' => 0.6, 'I' => 0.4, 'A' => 0.2, 'S' => 0.3, 'E' => 0.7, 'C' => 1.0]
        ];
        
        // Calcular match para el tipo primario y secundario de la carrera
        if (isset($carrera->tipo_primario)) {
            $tipoPrimarioCarrera = $carrera->tipo_primario;
            
            // Para cada tipo del usuario, calcular su contribución al match
            foreach ($porcentajesUsuario as $tipoUsuario => $porcentaje) {
                // Obtener el factor de compatibilidad entre este tipo del usuario y el tipo primario de la carrera
                $factorCompatibilidad = $compatibilidadRIASEC[$tipoPrimarioCarrera][$tipoUsuario] ?? 0;
                
                // El peso es mayor para el tipo primario de la carrera
                $peso = $factorCompatibilidad * 0.6; // 60% de peso para el tipo primario
                $match += ($porcentaje * $peso / 100);
                $pesoTotal += $peso;
            }
        }
        
        if (isset($carrera->tipo_secundario)) {
            $tipoSecundarioCarrera = $carrera->tipo_secundario;
            
            // Para cada tipo del usuario, calcular su contribución al match
            foreach ($porcentajesUsuario as $tipoUsuario => $porcentaje) {
                // Obtener el factor de compatibilidad entre este tipo del usuario y el tipo secundario de la carrera
                $factorCompatibilidad = $compatibilidadRIASEC[$tipoSecundarioCarrera][$tipoUsuario] ?? 0;
                
                // El peso es menor para el tipo secundario de la carrera
                $peso = $factorCompatibilidad * 0.4; // 40% de peso para el tipo secundario
                $match += ($porcentaje * $peso / 100);
                $pesoTotal += $peso;
            }
        }
        
        // Normalizar el match a un valor entre 0 y 100
        $matchFinal = $pesoTotal > 0 ? ($match / $pesoTotal) * 100 : 0;
        
        // Redondear y limitar a 100
        return min(round($matchFinal), 100);
    }
    
    // Métodos para obtener información adicional (datos dummy si no existen tablas)
    private function obtenerCampoLaboral($carreraId)
    {
        $camposLaborales = [
            // Datos para carreras populares
            1 => "Hospitales, clínicas, centros de salud, investigación médica, docencia",
            2 => "Desarrollo de software, ciberseguridad, análisis de datos, inteligencia artificial",
            3 => "Empresas públicas y privadas, consultorías, auditorías, emprendimientos",
            4 => "Bufetes de abogados, fiscalía, juzgados, asesorías jurídicas, notarías",
            5 => "Hospitales, clínicas dentales privadas, centros de salud, docencia",
            6 => "Laboratorios clínicos, industria farmacéutica, investigación, desarrollo de medicamentos",
            7 => "Instituciones educativas, centros de investigación pedagógica, editoriales educativas",
            8 => "Agencias de publicidad, medios de comunicación, departamentos de marketing",
            9 => "Constructoras, consultoras, sector público, empresas de infraestructura",
            10 => "Hospitales, clínicas, centros deportivos, rehabilitación, gimnasios"
        ];
        
        // Si existe el campo laboral en la base de datos, retornarlo
        try {
            if (Schema::hasTable('carrera_info')) {
                $info = DB::table('carrera_info')
                    ->where('carrera_id', $carreraId)
                    ->value('campo_laboral');
                
                if ($info) {
                    return $info;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener campo laboral: ' . $e->getMessage());
        }
        
        // Si no existe, retornar un valor predeterminado o el de la lista dummy
        return $camposLaborales[$carreraId] ?? 
               "Empresas públicas y privadas, consultoría, emprendimientos, docencia e investigación.";
    }
    
    private function obtenerHabilidadesCarrera($carreraId)
    {
        $habilidadesCarreras = [
            // Datos para carreras populares
            1 => ["Pensamiento analítico", "Empatía", "Toma de decisiones", "Comunicación efectiva", "Trabajo bajo presión"],
            2 => ["Lógica", "Resolución de problemas", "Creatividad", "Trabajo en equipo", "Aprendizaje continuo"],
            3 => ["Análisis numérico", "Toma de decisiones", "Liderazgo", "Comunicación", "Negociación"],
            4 => ["Argumentación", "Análisis crítico", "Comunicación oral y escrita", "Negociación", "Investigación"],
            5 => ["Precisión", "Habilidades manuales", "Comunicación", "Empatía", "Trabajo en equipo"],
            6 => ["Precisión", "Investigación", "Análisis", "Atención al detalle", "Metodología científica"],
            7 => ["Comunicación", "Empatía", "Creatividad", "Adaptabilidad", "Planificación"],
            8 => ["Creatividad", "Comunicación", "Análisis de mercado", "Pensamiento estratégico", "Trabajo en equipo"],
            9 => ["Cálculo", "Visión espacial", "Resolución de problemas", "Trabajo en equipo", "Liderazgo"],
            10 => ["Conocimiento anatómico", "Empatía", "Comunicación", "Trabajo en equipo", "Precisión"]
        ];
        
        // Si existen habilidades en la base de datos, retornarlas
        try {
            if (Schema::hasTable('carrera_info')) {
                $info = DB::table('carrera_info')
                    ->where('carrera_id', $carreraId)
                    ->value('habilidades');
                
                if ($info) {
                    return json_decode($info, true);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener habilidades: ' . $e->getMessage());
        }
        
        // Si no existen, retornar un valor predeterminado o de la lista dummy
        return $habilidadesCarreras[$carreraId] ?? 
               ["Comunicación", "Trabajo en equipo", "Análisis", "Resolución de problemas", "Adaptabilidad"];
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
    
    /**
     * Método para actualizar las preguntas RIASEC con preguntas mejoradas
     */
    public function actualizarPreguntasRIASEC()
    {
        // Eliminar preguntas existentes
        DB::table('preguntas')->truncate();
        
        $preguntas = [
            // Preguntas tipo Realista (R)
            ['texto' => 'Me gusta trabajar con herramientas, maquinaria o equipos técnicos', 'tipo' => 'R'],
            ['texto' => 'Disfruto reparando o construyendo cosas con mis manos', 'tipo' => 'R'],
            ['texto' => 'Prefiero actividades que me permitan ver resultados tangibles y concretos', 'tipo' => 'R'],
            ['texto' => 'Me interesa entender cómo funcionan los motores, aparatos o sistemas mecánicos', 'tipo' => 'R'],
            ['texto' => 'Disfruto las actividades al aire libre o que impliquen esfuerzo físico', 'tipo' => 'R'],
            ['texto' => 'Prefiero resolver problemas prácticos en lugar de teóricos', 'tipo' => 'R'],
            ['texto' => 'Me gusta desarmar cosas para ver cómo funcionan por dentro', 'tipo' => 'R'],
            ['texto' => 'Prefiero trabajar con objetos en lugar de con personas o ideas abstractas', 'tipo' => 'R'],
            ['texto' => 'Me considero una persona práctica y orientada a la acción', 'tipo' => 'R'],
            ['texto' => 'Disfruto actividades como la carpintería, mecánica, electricidad o jardinería', 'tipo' => 'R'],
            
            // Preguntas tipo Investigativo (I)
            ['texto' => 'Me gusta resolver problemas complejos mediante el análisis lógico', 'tipo' => 'I'],
            ['texto' => 'Disfruto investigando y descubriendo nuevos conocimientos', 'tipo' => 'I'],
            ['texto' => 'Me interesa entender fenómenos o teorías científicas', 'tipo' => 'I'],
            ['texto' => 'Prefiero actividades que impliquen pensamiento abstracto', 'tipo' => 'I'],
            ['texto' => 'Me gusta diseñar experimentos para probar hipótesis', 'tipo' => 'I'],
            ['texto' => 'Disfruto analizando datos y encontrando patrones o tendencias', 'tipo' => 'I'],
            ['texto' => 'Me interesa leer sobre avances científicos o tecnológicos', 'tipo' => 'I'],
            ['texto' => 'Prefiero trabajar en problemas que requieran precisión y atención al detalle', 'tipo' => 'I'],
            ['texto' => 'Me considero una persona curiosa y analítica', 'tipo' => 'I'],
            ['texto' => 'Disfruto resolviendo acertijos, rompecabezas o problemas matemáticos', 'tipo' => 'I'],
            
            // Preguntas tipo Artístico (A)
            ['texto' => 'Me gusta expresarme de manera creativa o artística', 'tipo' => 'A'],
            ['texto' => 'Disfruto actividades que me permitan usar mi imaginación', 'tipo' => 'A'],
            ['texto' => 'Me interesa crear o interpretar música, arte, literatura o diseño', 'tipo' => 'A'],
            ['texto' => 'Prefiero entornos de trabajo donde pueda innovar y ser original', 'tipo' => 'A'],
            ['texto' => 'Me gusta pensar en nuevas formas de hacer las cosas', 'tipo' => 'A'],
            ['texto' => 'Disfruto actividades que no tengan instrucciones o reglas estrictas', 'tipo' => 'A'],
            ['texto' => 'Me considero una persona creativa e intuitiva', 'tipo' => 'A'],
            ['texto' => 'Prefiero proyectos que me permitan expresar mi individualidad', 'tipo' => 'A'],
            ['texto' => 'Me interesa el significado emocional o estético de las cosas', 'tipo' => 'A'],
            ['texto' => 'Disfruto apreciando el arte, la música, el cine o la literatura', 'tipo' => 'A'],
            
            // Preguntas tipo Social (S)
            ['texto' => 'Me gusta ayudar a otras personas con sus problemas', 'tipo' => 'S'],
            ['texto' => 'Disfruto enseñando o compartiendo conocimientos con los demás', 'tipo' => 'S'],
            ['texto' => 'Me interesa entender las motivaciones y comportamientos de las personas', 'tipo' => 'S'],
            ['texto' => 'Prefiero trabajar en equipo en lugar de individualmente', 'tipo' => 'S'],
            ['texto' => 'Me gusta facilitar la comunicación entre personas o grupos', 'tipo' => 'S'],
            ['texto' => 'Disfruto escuchando y ofreciendo apoyo emocional a los demás', 'tipo' => 'S'],
            ['texto' => 'Me considero una persona empática y comprensiva', 'tipo' => 'S'],
            ['texto' => 'Prefiero trabajar para mejorar el bienestar de las personas', 'tipo' => 'S'],
            ['texto' => 'Me interesa participar en actividades comunitarias o de voluntariado', 'tipo' => 'S'],
            ['texto' => 'Disfruto organizando eventos sociales o grupales', 'tipo' => 'S'],
            
            // Preguntas tipo Emprendedor (E)
            ['texto' => 'Me gusta liderar grupos o proyectos', 'tipo' => 'E'],
            ['texto' => 'Disfruto persuadiendo o influyendo en otras personas', 'tipo' => 'E'],
            ['texto' => 'Me interesa iniciar y desarrollar mis propios proyectos o negocios', 'tipo' => 'E'],
            ['texto' => 'Prefiero tomar decisiones y asumir responsabilidades', 'tipo' => 'E'],
            ['texto' => 'Me gusta competir y superar desafíos para alcanzar objetivos', 'tipo' => 'E'],
            ['texto' => 'Disfruto negociando y llegando a acuerdos ventajosos', 'tipo' => 'E'],
            ['texto' => 'Me considero una persona ambiciosa y orientada al logro', 'tipo' => 'E'],
            ['texto' => 'Prefiero roles donde pueda motivar y dirigir a otros', 'tipo' => 'E'],
            ['texto' => 'Me interesa la estrategia y la planificación para obtener resultados', 'tipo' => 'E'],
            ['texto' => 'Disfruto hablando en público o presentando ideas', 'tipo' => 'E'],
            
            // Preguntas tipo Convencional (C)
            ['texto' => 'Me gusta trabajar con datos, números o registros de manera ordenada', 'tipo' => 'C'],
            ['texto' => 'Disfruto siguiendo procedimientos claros y bien definidos', 'tipo' => 'C'],
            ['texto' => 'Me interesa organizar información y mantener sistemas eficientes', 'tipo' => 'C'],
            ['texto' => 'Prefiero entornos de trabajo estructurados y predecibles', 'tipo' => 'C'],
            ['texto' => 'Me gusta prestar atención a los detalles y la precisión', 'tipo' => 'C'],
            ['texto' => 'Disfruto completando tareas que requieren meticulosidad', 'tipo' => 'C'],
            ['texto' => 'Me considero una persona responsable y confiable', 'tipo' => 'C'],
            ['texto' => 'Prefiero seguir reglas establecidas en lugar de improvisar', 'tipo' => 'C'],
            ['texto' => 'Me interesa mantener registros exactos y actualizados', 'tipo' => 'C'],
            ['texto' => 'Disfruto clasificando y organizando información o materiales', 'tipo' => 'C'],
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
}