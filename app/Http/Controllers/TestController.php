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
    /**
     * Constructor para aplicar middlewares
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Inicia un nuevo test para el usuario autenticado
     */
    public function iniciar()
    {
        // Verificar si el usuario ya tiene un test en progreso
        $testPendiente = Test::where('user_id', auth()->id())
                            ->where('completado', false)
                            ->first();
        
        if ($testPendiente) {
            return redirect()->route('test.continuar', $testPendiente->id)
                            ->with('info', 'Tienes un test pendiente. Puedes continuarlo o iniciar uno nuevo.');
        }
        
        // Crear nuevo test
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
    
    /**
     * Muestra la vista para continuar un test pendiente
     */
    public function continuar(Test $test)
    {
        // Verificar que el test pertenece al usuario
        if ($test->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }
        
        // Obtener preguntas ya respondidas
        $respuestasExistentes = $test->respuestas()->pluck('valor', 'pregunta_id')->toArray();
        
        $preguntas = Pregunta::all();
        
        return view('test.continuar', [
            'preguntas' => $preguntas,
            'test_id' => $test->id,
            'respuestas_existentes' => $respuestasExistentes
        ]);
    }
    
    /**
     * Muestra las preguntas del test en el dashboard
     */
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
    
    /**
     * Procesa las respuestas de un test para calcular el perfil RIASEC
     */
    public function procesarResultados(Test $test)
    {
        // Obtener todas las respuestas del test
        $respuestas = $test->respuestas()->with('pregunta')->get();
        
        if ($respuestas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay respuestas para procesar.');
        }
        
        // Inicializar contadores para cada tipo RIASEC
        $puntajes = [
            'R' => 0, // Realista
            'I' => 0, // Investigativo
            'A' => 0, // Artístico
            'S' => 0, // Social
            'E' => 0, // Emprendedor
            'C' => 0  // Convencional
        ];
        
        // Contar respuestas por tipo
        $contadores = [
            'R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0
        ];
        
        // Sumar puntos según respuestas
        foreach ($respuestas as $respuesta) {
            $tipo = $respuesta->pregunta->tipo;
            
            if (isset($puntajes[$tipo])) {
                $puntajes[$tipo] += $respuesta->valor;
                $contadores[$tipo]++;
            }
        }
        
        // Calcular promedios y porcentajes normalizados
        $promedios = [];
        $porcentajes = [];
        $total_puntos = array_sum($puntajes);
        
        foreach ($puntajes as $tipo => $puntaje) {
            if ($contadores[$tipo] > 0) {
                $promedios[$tipo] = round($puntaje / $contadores[$tipo], 2);
                // Normalizar a porcentaje del total
                $porcentajes[$tipo] = $total_puntos > 0 ? 
                                      round(($puntaje / $total_puntos) * 100) : 0;
            } else {
                $promedios[$tipo] = 0;
                $porcentajes[$tipo] = 0;
            }
        }
        
        // Ordenar puntajes de mayor a menor
        arsort($puntajes);
        
        // Obtener los dos tipos con mayor puntaje
        $tiposDominantes = array_keys($puntajes);
        $tipoPrimario = $tiposDominantes[0] ?? null;
        $tipoSecundario = $tiposDominantes[1] ?? null;
        
        // Verificar si hay empate en el primer lugar
        $valoresPuntaje = array_values($puntajes);
        if (count($valoresPuntaje) >= 2 && $valoresPuntaje[0] === $valoresPuntaje[1]) {
            // Desempatar usando otros criterios (ejemplo: mayor promedio)
            $tiposEmpatados = [];
            $valorEmpatado = $valoresPuntaje[0];
            
            foreach ($puntajes as $tipo => $valor) {
                if ($valor === $valorEmpatado) {
                    $tiposEmpatados[$tipo] = $promedios[$tipo];
                }
            }
            
            // Ordenar por promedio
            arsort($tiposEmpatados);
            $tiposOrdenados = array_keys($tiposEmpatados);
            
            $tipoPrimario = $tiposOrdenados[0] ?? null;
            $tipoSecundario = $tiposOrdenados[1] ?? null;
        }
        
        // Obtener recomendaciones basadas en el perfil
        $recomendaciones = $this->obtenerRecomendacionesCarreras($tipoPrimario, $tipoSecundario);
        
        // Guardar resultados en formato JSON
        $resultados = [
            'puntajes' => $puntajes,
            'promedios' => $promedios,
            'porcentajes' => $porcentajes,
            'recomendaciones' => $recomendaciones,
            'fecha_procesamiento' => now()->toDateTimeString()
        ];
        
        // Actualizar el test con los resultados
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
    
    /**
     * Obtiene recomendaciones de carreras y universidades basadas en el perfil RIASEC
     */
    private function obtenerRecomendacionesCarreras($tipoPrimario, $tipoSecundario)
    {
        if (!$tipoPrimario) {
            return [];
        }
        
        try {
            // Verificar si existen las tablas necesarias
            if (!Schema::hasTable('carrera_tipo')) {
                throw new \Exception("La tabla carrera_tipo no existe");
            }
            
            // Buscar carreras que coinciden con el perfil RIASEC
            $carrerasCoincidentes = DB::table('carrera_tipo')
                ->join('carreras', 'carrera_tipo.carrera_id', '=', 'carreras.id')
                ->where(function($query) use ($tipoPrimario, $tipoSecundario) {
                    // Coincidencia exacta (mismo orden)
                    $query->where(function($q) use ($tipoPrimario, $tipoSecundario) {
                        $q->where('tipo_primario', $tipoPrimario)
                          ->where('tipo_secundario', $tipoSecundario);
                    })
                    // Coincidencia exacta (orden inverso)
                    ->orWhere(function($q) use ($tipoPrimario, $tipoSecundario) {
                        $q->where('tipo_primario', $tipoSecundario)
                          ->where('tipo_secundario', $tipoPrimario);
                    })
                    // Coincidencia parcial (solo primer tipo)
                    ->orWhere('tipo_primario', $tipoPrimario)
                    // Coincidencia parcial (segundo tipo como primario)
                    ->orWhere('tipo_primario', $tipoSecundario);
                })
                ->select('carreras.id', 'carreras.nombre', 'carreras.area_conocimiento', 
                         'carreras.descripcion', 'carreras.es_institucional',
                         'carrera_tipo.tipo_primario', 'carrera_tipo.tipo_secundario')
                ->get();
            
            if ($carrerasCoincidentes->isEmpty()) {
                return $this->obtenerRecomendacionesAlternativas($tipoPrimario, $tipoSecundario);
            }
            
            // Calcular porcentaje de match para cada carrera
            $recomendaciones = [];
            
            foreach ($carrerasCoincidentes as $carrera) {
                $match = $this->calcularPorcentajeMatch(
                    $tipoPrimario, 
                    $tipoSecundario,
                    $carrera->tipo_primario, 
                    $carrera->tipo_secundario,
                    $carrera->es_institucional ?? false
                );
                
                $recomendacion = [
                    'carrera_id' => $carrera->id,
                    'nombre' => $carrera->nombre,
                    'area' => $carrera->area_conocimiento,
                    'descripcion' => $carrera->descripcion,
                    'match' => $match,
                    'universidades' => []
                ];
                
                // Intentar obtener universidades si la tabla existe
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
                    // Si hay error al obtener universidades, continuar sin ellas
                    Log::error('Error al obtener universidades: ' . $e->getMessage());
                }
                
                $recomendaciones[] = $recomendacion;
            }
            
            // Ordenar por porcentaje de match (descendente)
            usort($recomendaciones, function($a, $b) {
                return $b['match'] <=> $a['match'];
            });
            
            // Limitar a las 10 mejores recomendaciones
            return array_slice($recomendaciones, 0, 10);
            
        } catch (\Exception $e) {
            Log::error('Error en obtenerRecomendacionesCarreras: ' . $e->getMessage());
            return $this->obtenerRecomendacionesAlternativas($tipoPrimario, $tipoSecundario);
        }
    }
    
    /**
     * Implementación alternativa para recomendaciones si no existe la tabla carrera_tipo
     */
    private function obtenerRecomendacionesAlternativas($tipoPrimario, $tipoSecundario)
    {
        // Carreras recomendadas por tipo RIASEC primario
        $recomendacionesPorTipo = [
            'R' => [
                ['nombre' => 'Ingeniería Civil', 'area' => 'Ingeniería y Tecnología', 'match' => 95, 'descripcion' => 'Diseño y construcción de estructuras e infraestructuras.'],
                ['nombre' => 'Ingeniería Mecánica', 'area' => 'Ingeniería y Tecnología', 'match' => 92, 'descripcion' => 'Diseño y mantenimiento de sistemas mecánicos y maquinaria.'],
                ['nombre' => 'Arquitectura', 'area' => 'Arquitectura y Diseño', 'match' => 90, 'descripcion' => 'Diseño de espacios y edificaciones funcionales y estéticas.'],
                ['nombre' => 'Agronomía', 'area' => 'Ciencias Agrícolas', 'match' => 85, 'descripcion' => 'Estudio y mejora de técnicas de producción agrícola.'],
                ['nombre' => 'Tecnología en Electrónica', 'area' => 'Ingeniería y Tecnología', 'match' => 80, 'descripcion' => 'Diseño y mantenimiento de sistemas electrónicos.'],
            ],
            'I' => [
                ['nombre' => 'Medicina', 'area' => 'Ciencias de la Salud', 'match' => 95, 'descripcion' => 'Diagnóstico, tratamiento y prevención de enfermedades.'],
                ['nombre' => 'Física', 'area' => 'Ciencias Básicas', 'match' => 92, 'descripcion' => 'Estudio de las leyes fundamentales que rigen el universo.'],
                ['nombre' => 'Biología', 'area' => 'Ciencias Básicas', 'match' => 90, 'descripcion' => 'Estudio de los seres vivos y sus procesos vitales.'],
                ['nombre' => 'Química', 'area' => 'Ciencias Básicas', 'match' => 85, 'descripcion' => 'Estudio de la composición y propiedades de la materia.'],
                ['nombre' => 'Matemáticas', 'area' => 'Ciencias Básicas', 'match' => 80, 'descripcion' => 'Estudio de números, estructuras y patrones abstractos.'],
            ],
            'A' => [
                ['nombre' => 'Diseño Gráfico', 'area' => 'Arquitectura y Diseño', 'match' => 95, 'descripcion' => 'Creación visual de mensajes, identidades y experiencias.'],
                ['nombre' => 'Música', 'area' => 'Humanidades y Artes', 'match' => 92, 'descripcion' => 'Estudio y creación de composiciones musicales.'],
                ['nombre' => 'Literatura', 'area' => 'Humanidades y Artes', 'match' => 90, 'descripcion' => 'Estudio y creación de obras literarias.'],
                ['nombre' => 'Teatro', 'area' => 'Humanidades y Artes', 'match' => 85, 'descripcion' => 'Interpretación de personajes y puesta en escena.'],
                ['nombre' => 'Cine y Televisión', 'area' => 'Humanidades y Artes', 'match' => 80, 'descripcion' => 'Creación de productos audiovisuales.'],
            ],
            'S' => [
                ['nombre' => 'Psicología', 'area' => 'Ciencias Sociales', 'match' => 95, 'descripcion' => 'Estudio del comportamiento y procesos mentales.'],
                ['nombre' => 'Trabajo Social', 'area' => 'Ciencias Sociales', 'match' => 92, 'descripcion' => 'Intervención para mejorar el bienestar social e individual.'],
                ['nombre' => 'Enfermería', 'area' => 'Ciencias de la Salud', 'match' => 90, 'descripcion' => 'Cuidado y atención integral a pacientes.'],
                ['nombre' => 'Educación', 'area' => 'Educación', 'match' => 85, 'descripcion' => 'Formación y acompañamiento de procesos de aprendizaje.'],
                ['nombre' => 'Terapia Ocupacional', 'area' => 'Ciencias de la Salud', 'match' => 80, 'descripcion' => 'Rehabilitación y adaptación a través de actividades.'],
            ],
            'E' => [
                ['nombre' => 'Administración de Empresas', 'area' => 'Economía y Negocios', 'match' => 95, 'descripcion' => 'Gestión eficiente de recursos empresariales.'],
                ['nombre' => 'Marketing', 'area' => 'Economía y Negocios', 'match' => 92, 'descripcion' => 'Desarrollo de estrategias para posicionar productos y servicios.'],
                ['nombre' => 'Derecho', 'area' => 'Derecho', 'match' => 90, 'descripcion' => 'Interpretación y aplicación de normas jurídicas.'],
                ['nombre' => 'Relaciones Internacionales', 'area' => 'Ciencias Sociales', 'match' => 85, 'descripcion' => 'Análisis de la interacción entre estados y organismos internacionales.'],
                ['nombre' => 'Comunicación Social', 'area' => 'Ciencias Sociales', 'match' => 80, 'descripcion' => 'Gestión de la comunicación en diversas plataformas y contextos.'],
            ],
            'C' => [
                ['nombre' => 'Contaduría Pública', 'area' => 'Economía y Negocios', 'match' => 95, 'descripcion' => 'Registro, análisis e interpretación de información financiera.'],
                ['nombre' => 'Ingeniería de Sistemas', 'area' => 'Ingeniería y Tecnología', 'match' => 92, 'descripcion' => 'Diseño y desarrollo de soluciones tecnológicas.'],
                ['nombre' => 'Estadística', 'area' => 'Ciencias Básicas', 'match' => 90, 'descripcion' => 'Recolección, análisis e interpretación de datos.'],
                ['nombre' => 'Economía', 'area' => 'Economía y Negocios', 'match' => 85, 'descripcion' => 'Estudio de la producción, distribución y consumo de bienes y servicios.'],
                ['nombre' => 'Bibliotecología', 'area' => 'Humanidades y Artes', 'match' => 80, 'descripcion' => 'Gestión de información y recursos bibliográficos.'],
            ],
        ];
        
        // Combinar recomendaciones de tipo primario y secundario
        $recomendaciones = [];
        
        if (isset($recomendacionesPorTipo[$tipoPrimario])) {
            foreach ($recomendacionesPorTipo[$tipoPrimario] as $carrera) {
                $recomendaciones[] = [
                    'carrera_id' => null, // No tenemos ID real
                    'nombre' => $carrera['nombre'],
                    'area' => $carrera['area'],
                    'descripcion' => $carrera['descripcion'],
                    'match' => $carrera['match'],
                    'universidades' => []
                ];
            }
        }
        
        if ($tipoSecundario && isset($recomendacionesPorTipo[$tipoSecundario])) {
            foreach ($recomendacionesPorTipo[$tipoSecundario] as $carrera) {
                // Reducir el match para tipos secundarios
                $match = round($carrera['match'] * 0.8);
                
                $recomendaciones[] = [
                    'carrera_id' => null, // No tenemos ID real
                    'nombre' => $carrera['nombre'],
                    'area' => $carrera['area'],
                    'descripcion' => $carrera['descripcion'] . ' (Recomendado por perfil ' . $tipoSecundario . ')',
                    'match' => $match,
                    'universidades' => []
                ];
            }
        }
        
        // Ordenar por porcentaje de match (descendente)
        usort($recomendaciones, function($a, $b) {
            return $b['match'] <=> $a['match'];
        });
        
        // Eliminar duplicados y limitar a 10
        $resultado = [];
        $nombresAgregados = [];
        
        foreach ($recomendaciones as $rec) {
            if (!in_array($rec['nombre'], $nombresAgregados) && count($resultado) < 10) {
                $resultado[] = $rec;
                $nombresAgregados[] = $rec['nombre'];
            }
        }
        
        return $resultado;
    }
    
    /**
     * Calcula el porcentaje de coincidencia entre perfiles RIASEC
     */
    private function calcularPorcentajeMatch($userPrimario, $userSecundario, $carreraPrimario, $carreraSecundario, $esInstitucional = false)
    {
        $match = 0;
        
        // Coincidencia exacta (mismo orden)
        if ($userPrimario == $carreraPrimario && $userSecundario == $carreraSecundario) {
            $match = 95;
        }
        // Coincidencia exacta (orden inverso)
        else if ($userPrimario == $carreraSecundario && $userSecundario == $carreraPrimario) {
            $match = 90;
        }
        // Coincidencia solo con tipo primario
        else if ($userPrimario == $carreraPrimario) {
            $match = 85;
        }
        // Otras coincidencias parciales
        else if ($userPrimario == $carreraSecundario) {
            $match = 80;
        }
        else if ($userSecundario == $carreraPrimario) {
            $match = 75;
        }
        else {
            $match = 65;
        }
        
        // Bonus para carreras institucionales
        if ($esInstitucional) {
            $match += 5;
        }
        
        return min(round($match), 100);
    }

    
    
    /**
     * Muestra los resultados del test con interpretaciones y recomendaciones
     */
    public function resultados(Test $test)
    {
        // Verificar que el test pertenece al usuario o es un superadmin/coordinador
        if ($test->user_id !== auth()->id() && 
            !in_array(auth()->user()->role, ['superadmin', 'coordinador'])) {
            abort(403, 'No autorizado');
        }
        
        // Si el test no está procesado, procesarlo
        if (!$test->completado) {
            return $this->procesarResultados($test);
        }
        
        // Obtener información de los tipos de personalidad
        $tiposPersonalidad = TipoPersonalidad::pluck('descripcion', 'codigo')->toArray();
        
        if (empty($tiposPersonalidad)) {
            // Definición de respaldo si no hay registros en la base de datos
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
    
    /**
     * Guarda las respuestas del test y procesa los resultados
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'respuestas' => 'required|array',
            'respuestas.*' => 'required|integer|min:0|max:2',
        ]);
        
        $test = Test::findOrFail($request->test_id);
        
        // Verificar que el test pertenece al usuario
        if ($test->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }
        
        // Eliminar respuestas anteriores (si estamos actualizando)
        Respuesta::where('test_id', $test->id)->delete();
        
        // Guardar nuevas respuestas
        foreach ($request->respuestas as $pregunta_id => $valor) {
            Respuesta::create([
                'test_id' => $test->id,
                'pregunta_id' => $pregunta_id,
                'valor' => (int)$valor,
                'user_id' => auth()->id(),
            ]);
        }
        
        // Procesar los resultados inmediatamente
        return $this->procesarResultados($test);
    }
    
    /**
     * Lista los tests completados por el usuario actual
     */
    public function historial()
    {
        $tests = Test::where('user_id', auth()->id())
                     ->where('completado', true)
                     ->orderBy('fecha_completado', 'desc')
                     ->paginate(10);
                     
        return view('test.historial', compact('tests'));
    }
    
    /**
     * Elimina un test específico
     */
    public function eliminar(Test $test)
    {
        // Verificar que el test pertenece al usuario o es un superadmin
        if ($test->user_id !== auth()->id() && auth()->user()->role !== 'superadmin') {
            abort(403, 'No autorizado');
        }
        
        // Eliminar respuestas asociadas
        Respuesta::where('test_id', $test->id)->delete();
        
        // Eliminar el test
        $test->delete();
        
        return redirect()->route('test.historial')
                         ->with('success', 'Test eliminado correctamente.');
    }
}