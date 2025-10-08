<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\User;
use App\Models\Carrera;
use App\Models\Universidad;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
    public function index()
    {
        // Verificación de roles
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        
        // Datos básicos para las estadísticas
        $totalTests = Test::where('completado', true)->count();
        $totalUsuarios = User::count(); // Contar todos los usuarios en lugar de solo 'user'
        $totalCarreras = Carrera::count();
        $testsUltimaSemana = Test::where('completado', true)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        
        $testsIniciados = Test::count();
        $testsCompletados = $totalTests;
        $tasaConversion = $testsIniciados > 0 
            ? round(($testsCompletados / $testsIniciados) * 100, 1) 
            : 0;
        
        // Consulta para obtener universidades con sus carreras
        $universidadesConCarreras = Universidad::select('universidades.id', 'universidades.nombre')
            ->selectRaw('COUNT(DISTINCT carrera_universidad.carrera_id) as total_carreras')
            ->selectRaw('SUM(CASE WHEN carreras.es_institucional = 1 THEN 1 ELSE 0 END) as carreras_institucionales')
            ->leftJoin('carrera_universidad', 'universidades.id', '=', 'carrera_universidad.universidad_id')
            ->leftJoin('carreras', 'carrera_universidad.carrera_id', '=', 'carreras.id')
            ->groupBy('universidades.id', 'universidades.nombre')
            ->orderBy('carreras_institucionales', 'desc')
            ->get();
        
        // Consultas para análisis de personalidad y carreras
        // CORREGIDO: tipo_personalidad_primario → tipo_primario
        $porTipoPersonalidad = DB::table('tests')
            ->select('tipo_primario', DB::raw('COUNT(*) as total'))
            ->whereNotNull('tipo_primario')
            ->groupBy('tipo_primario')
            ->orderByDesc('total')
            ->get();
            
        $carrerasMasRecomendadas = DB::table('test_carrera_recomendacion')
            ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
            ->selectRaw('carreras.nombre, COUNT(*) as total, AVG(match_porcentaje) as match_promedio')
            ->where('es_primaria', true)
            ->groupBy('carreras.id', 'carreras.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
            
        $porAreaConocimiento = DB::table('test_carrera_recomendacion')
            ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
            ->selectRaw('carreras.area_conocimiento, COUNT(*) as total')
            ->whereNotNull('carreras.area_conocimiento')
            ->groupBy('carreras.area_conocimiento')
            ->orderByDesc('total')
            ->get();
        
        return view('informes.index', compact(
            'totalTests',
            'totalUsuarios',
            'totalCarreras',
            'testsIniciados',
            'testsCompletados',
            'tasaConversion',
            'testsUltimaSemana',
            'universidadesConCarreras',
            'porTipoPersonalidad',
            'carrerasMasRecomendadas',
            'porAreaConocimiento'
        ));
    }
}