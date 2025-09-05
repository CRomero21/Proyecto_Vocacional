<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\User;
use App\Models\Carrera;
use App\Models\Universidad;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EstadisticasController extends Controller
{
    public function index()
    {
        try {
            // Datos básicos para las métricas principales
            $totalTests = Test::where('completado', true)->count();
            $totalUsuarios = User::where('role', 'estudiante')->count();
            $testsUltimaSemana = Test::where('completado', true)
                ->where('updated_at', '>=', now()->subDays(7))
                ->count();
            
            $testsIniciados = Test::count();
            $testsCompletados = $totalTests;
            $tasaConversion = $testsIniciados > 0 
                ? round(($testsCompletados / $testsIniciados) * 100, 1) 
                : 0;
            
            // Distribución de estudiantes por departamento
            $estudiantesPorDepartamento = User::where('role', 'estudiante')
                ->selectRaw('departamento, COUNT(*) as total')
                ->whereNotNull('departamento')
                ->where('departamento', '<>', '')
                ->groupBy('departamento')
                ->orderByDesc('total')
                ->get();
            
            // Universidades con carreras institucionales
            $universidadesConCarreras = Universidad::select('universidades.id', 'universidades.nombre')
                ->selectRaw('COUNT(DISTINCT carrera_universidad.carrera_id) as total_carreras')
                ->selectRaw('SUM(CASE WHEN carreras.es_institucional = 1 THEN 1 ELSE 0 END) as carreras_institucionales')
                ->leftJoin('carrera_universidad', 'universidades.id', '=', 'carrera_universidad.universidad_id')
                ->leftJoin('carreras', 'carrera_universidad.carrera_id', '=', 'carreras.id')
                ->groupBy('universidades.id', 'universidades.nombre')
                ->orderBy('carreras_institucionales', 'desc')
                ->get();
            
            // Tendencia de tests por mes
            $testsPorMes = DB::table('tests')
                ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as mes, COUNT(*) as total")
                ->where('completado', true)
                ->whereYear('created_at', date('Y'))
                ->groupBy('mes')
                ->orderBy(DB::raw('MIN(created_at)'))
                ->get();
            
            // Tipos de personalidad
            $porTipoPersonalidad = DB::table('tests')
                ->select('tipo_primario', DB::raw('COUNT(*) as total'))
                ->whereNotNull('tipo_primario')
                ->where('completado', true)
                ->groupBy('tipo_primario')
                ->orderByDesc('total')
                ->get();
            
            // Carreras más recomendadas
            $carrerasMasRecomendadas = DB::table('test_carrera_recomendacion')
                ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                ->selectRaw('carreras.nombre, COUNT(*) as total, AVG(match_porcentaje) as match_promedio')
                ->where('es_primaria', true)
                ->groupBy('carreras.id', 'carreras.nombre')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
            
            // Áreas de conocimiento
            $porAreaConocimiento = DB::table('test_carrera_recomendacion')
                ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                ->selectRaw('carreras.area_conocimiento, COUNT(*) as total')
                ->whereNotNull('carreras.area_conocimiento')
                ->where('carreras.area_conocimiento', '<>', '')
                ->groupBy('carreras.area_conocimiento')
                ->orderByDesc('total')
                ->get();
            
            // CONSULTAS CORREGIDAS PARA LA RETROALIMENTACIÓN (usando JSON)
            
            // Contar retroalimentaciones
            $totalRetroalimentaciones = Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion') IS NOT NULL")->count();
            
            // Promedio de satisfacción
            $promedioSatisfaccion = Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') IS NOT NULL")
                ->selectRaw("AVG(JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad')) as promedio")
                ->value('promedio') ?? 0;
            
            // Distribución por estrellas
            $satisfaccionPorEstrellas = [
                Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') = 1")->count(),
                Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') = 2")->count(),
                Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') = 3")->count(),
                Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') = 4")->count(),
                Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') = 5")->count()
            ];
            
            // Carreras sugeridas por los usuarios en la retroalimentación
            $carrerasSugeridas = DB::table('tests')
                ->join('carreras', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tests.resultados, '$.retroalimentacion.carrera_seleccionada'))"), '=', 'carreras.id')
                ->selectRaw('carreras.nombre, COUNT(*) as total')
                ->whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.carrera_seleccionada') IS NOT NULL")
                ->groupBy('carreras.id', 'carreras.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
            
            // Total de carreras sugeridas
            $totalCarrerasSugeridas = Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.carrera_seleccionada') IS NOT NULL")->count();
            
            // Pasar todas las variables a la vista
            return view('admin.estadisticas.index', compact(
                'totalTests',
                'totalUsuarios',
                'testsUltimaSemana',
                'testsIniciados',
                'testsCompletados',
                'tasaConversion',
                'estudiantesPorDepartamento',
                'universidadesConCarreras',
                'testsPorMes',
                'porTipoPersonalidad',
                'carrerasMasRecomendadas',
                'porAreaConocimiento',
                'totalRetroalimentaciones',
                'promedioSatisfaccion',
                'satisfaccionPorEstrellas',
                'carrerasSugeridas',
                'totalCarrerasSugeridas'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error en EstadisticasController: ' . $e->getMessage());
            return view('admin.estadisticas.index', [
                'error' => 'Ha ocurrido un error al cargar las estadísticas: ' . $e->getMessage()
            ]);
        }
    }

    public function iframe()
    {
        return $this->index();
    }
}