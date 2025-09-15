use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\User;
use App\Models\TestRetroalimentacion;
use App\Models\Universidad;
use App\Models\Carrera;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstadisticasExport;

<?php

namespace App\Http\Controllers;


class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Filtros
            $periodo = $request->input('periodo', 30);
            $departamentoFiltro = $request->input('departamento');
            $fechaDesde = Carbon::now()->subDays($periodo);
            
            // Consulta base para tests
            $testsQuery = Test::query();
            $usersQuery = User::where('role', 'estudiante');
            
            // Aplicar filtro de fecha
            $testsQuery->where('created_at', '>=', $fechaDesde);
            
            // Aplicar filtro de departamento si existe
            if ($departamentoFiltro) {
                $usersQuery->where('departamento', $departamentoFiltro);
                $testsQuery->whereHas('user', function($q) use ($departamentoFiltro) {
                    $q->where('departamento', $departamentoFiltro);
                });
            }
            
            // Datos básicos
            $totalUsuarios = $usersQuery->count();
            $testsIniciados = $testsQuery->count();
            $testsCompletados = $testsQuery->where('completado', true)->count();
            
            // Listado de departamentos para filtro
            $departamentos = User::where('role', 'estudiante')
                ->whereNotNull('departamento')
                ->where('departamento', '!=', '')
                ->distinct()
                ->pluck('departamento')
                ->toArray();
            
            // Distribución por género
            $distribucionPorGenero = User::where('role', 'estudiante')
                ->selectRaw('sexo as genero, COUNT(*) as total')
                ->groupBy('sexo')
                ->orderBy('total', 'desc')
                ->get();
            
            // Distribución por edad
            $distribucionPorEdad = [
                ['rango' => '16-18', 'total' => User::where('role', 'estudiante')->whereBetween('edad', [16, 18])->count()],
                ['rango' => '19-21', 'total' => User::where('role', 'estudiante')->whereBetween('edad', [19, 21])->count()],
                ['rango' => '22-25', 'total' => User::where('role', 'estudiante')->whereBetween('edad', [22, 25])->count()],
                ['rango' => '26-30', 'total' => User::where('role', 'estudiante')->whereBetween('edad', [26, 30])->count()],
                ['rango' => '31+', 'total' => User::where('role', 'estudiante')->where('edad', '>', 30)->count()]
            ];
            
            // Estudiantes por departamento
            $estudiantesPorDepartamento = User::where('role', 'estudiante')
                ->whereNotNull('departamento')
                ->where('departamento', '!=', '')
                ->selectRaw('departamento, COUNT(*) as total')
                ->groupBy('departamento')
                ->orderByDesc('total')
                ->get();
            
            // Top instituciones educativas
            $topInstituciones = User::where('role', 'estudiante')
                ->whereNotNull('unidad_educativa')
                ->where('unidad_educativa', '!=', '')
                ->selectRaw('unidad_educativa as nombre, COUNT(*) as usuarios')
                ->groupBy('unidad_educativa')
                ->orderByDesc('usuarios')
                ->limit(5)
                ->get();
                
            // Calcular el porcentaje para cada institución
            $totalConInstitucion = User::where('role', 'estudiante')
                ->whereNotNull('unidad_educativa')
                ->where('unidad_educativa', '!=', '')
                ->count();
                
            foreach ($topInstituciones as $institucion) {
                $institucion->porcentaje = $totalConInstitucion > 0 ? 
                    round(($institucion->usuarios / $totalConInstitucion) * 100, 1) : 0;
            }
            
            // Tipos de personalidad
            $porTipoPersonalidad = Test::where('completado', true)
                ->whereNotNull('tipo_primario')
                ->selectRaw('tipo_primario, COUNT(*) as total')
                ->groupBy('tipo_primario')
                ->orderByDesc('total')
                ->get();
                
            // Tipo de personalidad dominante
            $tipoPersonalidadDominante = $porTipoPersonalidad->first()->tipo_primario ?? 'No hay datos';
            $totalPersonalidades = $porTipoPersonalidad->sum('total');
            $porcentajeDominante = $totalPersonalidades > 0 ? 
                round(($porTipoPersonalidad->first()->total / $totalPersonalidades) * 100, 1) : 0;
            
            // Carreras más recomendadas
            $carrerasMasRecomendadas = DB::table('test_carrera_recomendacion')
                ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                ->selectRaw('carreras.nombre, carreras.id, COUNT(*) as total, AVG(match_porcentaje) as match_promedio')
                ->where('es_primaria', true)
                ->groupBy('carreras.id', 'carreras.nombre')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
                
            $carreraTop = $carrerasMasRecomendadas->first()->nombre ?? 'No hay datos';
            
            // Retroalimentación y satisfacción
            $valoracionPromedio = TestRetroalimentacion::avg('utilidad') ?? 0;
            $totalValoraciones = TestRetroalimentacion::count();
            
            // Distribución de valoraciones
            $distribucionValoraciones = [
                1 => TestRetroalimentacion::where('utilidad', 1)->count(),
                2 => TestRetroalimentacion::where('utilidad', 2)->count(),
                3 => TestRetroalimentacion::where('utilidad', 3)->count(),
                4 => TestRetroalimentacion::where('utilidad', 4)->count(),
                5 => TestRetroalimentacion::where('utilidad', 5)->count(),
            ];
            
            // Comentarios recientes
            $comentariosRecientes = TestRetroalimentacion::join('users', 'test_retroalimentacion.user_id', '=', 'users.id')
                ->select(
                    'test_retroalimentacion.id',
                    'test_retroalimentacion.comentarios as texto',
                    'test_retroalimentacion.utilidad as valoracion',
                    'users.name as usuario',
                    'test_retroalimentacion.created_at as fecha_raw'
                )
                ->whereNotNull('test_retroalimentacion.comentarios')
                ->where('test_retroalimentacion.comentarios', '!=', '')
                ->orderByDesc('test_retroalimentacion.created_at')
                ->limit(5)
                ->get();
                
            // Formatear fechas para mejor visualización
            foreach ($comentariosRecientes as $comentario) {
                $comentario->fecha = Carbon::parse($comentario->fecha_raw)->format('d/m/Y H:i');
            }

            // Retornar datos a la vista
            return view('admin.estadisticas.index', compact(
                'totalUsuarios',
                'testsIniciados',
                'testsCompletados',
                'departamentos',
                'departamentoFiltro',
                'distribucionPorGenero',
                'distribucionPorEdad',
                'estudiantesPorDepartamento',
                'topInstituciones',
                'porTipoPersonalidad',
                'tipoPersonalidadDominante',
                'porcentajeDominante',
                'carrerasMasRecomendadas',
                'carreraTop',
                'valoracionPromedio',
                'totalValoraciones',
                'distribucionValoraciones',
                'comentariosRecientes'
            ));
        } catch (\Exception $e) {
            // En caso de error, retornar la vista con valores por defecto y mensaje de error
            return view('admin.estadisticas.index', [
                'error' => $e->getMessage(),
                'totalUsuarios' => 0,
                'testsIniciados' => 0,
                'testsCompletados' => 0,
                'departamentos' => [],
                'departamentoFiltro' => '',
                'distribucionPorGenero' => [],
                'distribucionPorEdad' => [],
                'estudiantesPorDepartamento' => [],
                'topInstituciones' => [],
                'porTipoPersonalidad' => [],
                'tipoPersonalidadDominante' => 'No hay datos',
                'porcentajeDominante' => 0,
                'carrerasMasRecomendadas' => [],
                'carreraTop' => 'No hay datos',
                'valoracionPromedio' => 0,
                'totalValoraciones' => 0,
                'distribucionValoraciones' => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0],
                'comentariosRecientes' => []
            ]);
        }
    }

    /**
     * Exportar datos a Excel
     */
    public function excel(Request $request)
    {
        // Reutilizar la lógica del método index para obtener los mismos datos
        $data = $this->obtenerDatosEstadisticas($request);
        
        return Excel::download(new EstadisticasExport($data), 'estadisticas-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Obtener todos los datos para estadísticas y exportación
     */
    private function obtenerDatosEstadisticas(Request $request)
    {
        // Filtros
        $periodo = $request->input('periodo', 30);
        $departamentoFiltro = $request->input('departamento');
        $fechaDesde = Carbon::now()->subDays($periodo);
        
        // Consultas base
        $testsQuery = Test::query();
        $usersQuery = User::where('role', 'estudiante');
        
        // Aplicar filtros
        $testsQuery->where('created_at', '>=', $fechaDesde);
        if ($departamentoFiltro) {
            $usersQuery->where('departamento', $departamentoFiltro);
            $testsQuery->whereHas('user', function($q) use ($departamentoFiltro) {
                $q->where('departamento', $departamentoFiltro);
            });
        }
        
        // Consultas para datos generales
        $totalUsuarios = $usersQuery->count();
        $testsIniciados = $testsQuery->count();
        $testsCompletados = $testsQuery->where('completado', true)->count();
        
        // Obtener datos detallados
        $usuariosPorGenero = $this->obtenerDistribucionPorGenero($departamentoFiltro);
        $usuariosPorEdad = $this->obtenerDistribucionPorEdad($departamentoFiltro);
        $usuariosPorDepartamento = $this->obtenerUsuariosPorDepartamento();
        $carrerasMasRecomendadas = $this->obtenerCarrerasMasRecomendadas();
        $retroalimentacion = $this->obtenerDatosRetroalimentacion();
        $tiposPersonalidad = $this->obtenerTiposPersonalidad();
        
        // Datos de universidades y carreras
        $universidadesConCarreras = Universidad::select('universidades.id', 'universidades.nombre')
            ->selectRaw('COUNT(DISTINCT carrera_universidad.carrera_id) as total_carreras')
            ->selectRaw('SUM(CASE WHEN carreras.es_institucional = 1 THEN 1 ELSE 0 END) as carreras_institucionales')
            ->leftJoin('carrera_universidad', 'universidades.id', '=', 'carrera_universidad.universidad_id')
            ->leftJoin('carreras', 'carrera_universidad.carrera_id', '=', 'carreras.id')
            ->groupBy('universidades.id', 'universidades.nombre')
            ->orderBy('carreras_institucionales', 'desc')
            ->get();
            
        // Retornar todos los datos organizados
        return [
            'resumen_general' => [
                'total_usuarios' => $totalUsuarios,
                'tests_iniciados' => $testsIniciados,
                'tests_completados' => $testsCompletados,
                'tasa_completitud' => $testsIniciados > 0 ? round(($testsCompletados / $testsIniciados) * 100, 1) : 0
            ],
            'demograficos' => [
                'por_genero' => $usuariosPorGenero,
                'por_edad' => $usuariosPorEdad,
                'por_departamento' => $usuariosPorDepartamento
            ],
            'personalidad' => $tiposPersonalidad,
            'carreras' => $carrerasMasRecomendadas,
            'universidades' => $universidadesConCarreras,
            'satisfaccion' => $retroalimentacion
        ];
    }
    
    /**
     * Obtener distribución de usuarios por género
     */
    private function obtenerDistribucionPorGenero($departamento = null)
    {
        $query = User::where('role', 'estudiante');
        
        if ($departamento) {
            $query->where('departamento', $departamento);
        }
        
        return $query->selectRaw('sexo as genero, COUNT(*) as total')
            ->groupBy('sexo')
            ->orderBy('total', 'desc')
            ->get();
    }
    
    /**
     * Obtener distribución de usuarios por edad
     */
    private function obtenerDistribucionPorEdad($departamento = null)
    {
        $query = User::where('role', 'estudiante');
        
        if ($departamento) {
            $query->where('departamento', $departamento);
        }
        
        // Crear rangos de edad
        $rangos = [
            ['rango' => '16-18', 'total' => $query->clone()->whereBetween('edad', [16, 18])->count()],
            ['rango' => '19-21', 'total' => $query->clone()->whereBetween('edad', [19, 21])->count()],
            ['rango' => '22-25', 'total' => $query->clone()->whereBetween('edad', [22, 25])->count()],
            ['rango' => '26-30', 'total' => $query->clone()->whereBetween('edad', [26, 30])->count()],
            ['rango' => '31+', 'total' => $query->clone()->where('edad', '>', 30)->count()]
        ];
        
        return $rangos;
    }
    
    /**
     * Obtener usuarios por departamento
     */
    private function obtenerUsuariosPorDepartamento()
    {
        return User::where('role', 'estudiante')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->selectRaw('departamento, COUNT(*) as total')
            ->groupBy('departamento')
            ->orderByDesc('total')
            ->get();
    }
    
    /**
     * Obtener las carreras más recomendadas
     */
    private function obtenerCarrerasMasRecomendadas()
    {
        return DB::table('test_carrera_recomendacion')
            ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
            ->leftJoin('carrera_universidad', 'carreras.id', '=', 'carrera_universidad.carrera_id')
            ->leftJoin('universidades', 'carrera_universidad.universidad_id', '=', 'universidades.id')
            ->selectRaw('carreras.nombre, carreras.id, COUNT(*) as total, AVG(match_porcentaje) as match_promedio')
            ->selectRaw('GROUP_CONCAT(DISTINCT universidades.nombre) as universidades')
            ->where('es_primaria', true)
            ->groupBy('carreras.id', 'carreras.nombre')
            ->orderByDesc('total')
            ->limit(20)
            ->get();
    }
    
    /**
     * Obtener datos de retroalimentación
     */
    private function obtenerDatosRetroalimentacion()
    {
        $valoracionPromedio = TestRetroalimentacion::avg('utilidad') ?? 0;
        $totalValoraciones = TestRetroalimentacion::count();
        
        $distribucionValoraciones = [
            1 => TestRetroalimentacion::where('utilidad', 1)->count(),
            2 => TestRetroalimentacion::where('utilidad', 2)->count(),
            3 => TestRetroalimentacion::where('utilidad', 3)->count(),
            4 => TestRetroalimentacion::where('utilidad', 4)->count(),
            5 => TestRetroalimentacion::where('utilidad', 5)->count(),
        ];
        
        $comentariosRecientes = TestRetroalimentacion::join('users', 'test_retroalimentacion.user_id', '=', 'users.id')
            ->select(
                'test_retroalimentacion.id',
                'test_retroalimentacion.comentarios',
                'test_retroalimentacion.utilidad as valoracion',
                'users.name as usuario',
                'test_retroalimentacion.created_at'
            )
            ->whereNotNull('test_retroalimentacion.comentarios')
            ->where('test_retroalimentacion.comentarios', '!=', '')
            ->orderByDesc('test_retroalimentacion.created_at')
            ->limit(10)
            ->get();
            
        return [
            'promedio' => $valoracionPromedio,
            'total' => $totalValoraciones,
            'distribucion' => $distribucionValoraciones,
            'comentarios' => $comentariosRecientes
        ];
    }
    
    /**
     * Obtener distribución por tipos de personalidad
     */
    private function obtenerTiposPersonalidad()
    {
        $porTipoPersonalidad = Test::where('completado', true)
            ->whereNotNull('tipo_primario')
            ->selectRaw('tipo_primario, COUNT(*) as total')
            ->groupBy('tipo_primario')
            ->orderByDesc('total')
            ->get();
            
        return $porTipoPersonalidad;
    }
}