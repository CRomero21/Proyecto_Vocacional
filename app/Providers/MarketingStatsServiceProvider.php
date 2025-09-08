<?php
// filepath: c:\Users\USUARIO\Desktop\laravel\Proyecto_Vocacional\app\Providers\MarketingStatsServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Test;
use App\Models\Carrera;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketingStatsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar la clase de estadísticas como un singleton en el contenedor
        $this->app->singleton('marketing.stats', function ($app) {
            return new class {
                
                public function getMarketingDashboardStats($periodo = 30)
                {
                    $fechaInicio = Carbon::now()->subDays($periodo);
                    
                    return [
                        'conversion' => $this->getConversionMetrics($fechaInicio),
                        'segmentacion' => $this->getSegmentationMetrics(),
                        'recomendaciones' => $this->getRecommendationMetrics(),
                        'tendencias' => $this->getTrendMetrics(),
                        'satisfaccion' => $this->getSatisfactionMetrics(),
                    ];
                }
                
                private function getConversionMetrics($fechaInicio)
                {
                    // Conteos principales
                    $totalUsuarios = User::where('role', 'estudiante')->count();
                    $totalTestsIniciados = Test::count();
                    $totalTestsCompletados = Test::where('completado', 1)->count();
                    $nuevosTestsCompletados = Test::where('completado', 1)
                        ->where('updated_at', '>=', $fechaInicio)
                        ->count();
                        
                    // Calcular tasas
                    $tasaInicio = $totalUsuarios > 0 ? ($totalTestsIniciados / $totalUsuarios) * 100 : 0;
                    $tasaComplecion = $totalTestsIniciados > 0 ? ($totalTestsCompletados / $totalTestsIniciados) * 100 : 0;
                    $tasaConversion = $totalUsuarios > 0 ? ($totalTestsCompletados / $totalUsuarios) * 100 : 0;
                    
                    // Cálculo del costo por adquisición (simulado con datos reales)
                    $costoMarketingMensual = 1000; // Simulación - ajustar según datos reales
                    $costoAdquisicion = $nuevosTestsCompletados > 0 ? 
                        $costoMarketingMensual / $nuevosTestsCompletados : 0;
                        
                    // Cálculo del valor del ciclo de vida del cliente (LTV)
                    $valorPromedioConversion = 85; // Valor estimado - ajustar según modelo de negocio
                    $tasaRetencion = 0.72; // Tasa estimada - ajustar según datos reales
                    $ltv = $valorPromedioConversion / (1 - $tasaRetencion);
                    
                    return [
                        'total_usuarios' => $totalUsuarios,
                        'tests_iniciados' => $totalTestsIniciados,
                        'tests_completados' => $totalTestsCompletados,
                        'tests_recientes' => $nuevosTestsCompletados,
                        'tasa_inicio' => round($tasaInicio, 1),
                        'tasa_complecion' => round($tasaComplecion, 1),
                        'tasa_conversion' => round($tasaConversion, 1),
                        'costo_adquisicion' => round($costoAdquisicion, 2),
                        'ltv' => round($ltv, 2),
                        'tasa_retencion' => $tasaRetencion * 100,
                    ];
                }
                
                private function getSegmentationMetrics()
                {
                    // Distribución geográfica - departamentos
                    $departamentos = User::where('role', 'estudiante')
                        ->whereNotNull('departamento')
                        ->where('departamento', '<>', '')
                        ->groupBy('departamento')
                        ->select('departamento', DB::raw('COUNT(*) as total'))
                        ->orderBy('total', 'desc')
                        ->get();
                        
                    // Análisis de personalidad
                    $tiposPersonalidad = Test::where('completado', 1)
                        ->whereNotNull('tipo_primario')
                        ->groupBy('tipo_primario')
                        ->select('tipo_primario', DB::raw('COUNT(*) as total'))
                        ->orderBy('total', 'desc')
                        ->get();
                        
                    // Obtener tipo dominante para insights
                    $tipoDominante = $tiposPersonalidad->first();
                    $totalTests = Test::where('completado', 1)->count();
                    $porcentajeDominante = $totalTests > 0 && $tipoDominante ? 
                        round(($tipoDominante->total / $totalTests) * 100, 1) : 0;
                        
                    // Métricas de conversión por tipo de personalidad
                    $conversionPorTipo = [];
                    if (!$tiposPersonalidad->isEmpty()) {
                        // Personas introvertidas (I en el tipo MBTI)
                        $testIntrovertidos = Test::where('completado', 1)
                            ->where('tipo_primario', 'like', 'I%')
                            ->count();
                            
                        $totalIntrovertidos = Test::where('tipo_primario', 'like', 'I%')->count();
                        $tasaIntrovertidos = $totalIntrovertidos > 0 ? 
                            ($testIntrovertidos / $totalIntrovertidos) * 100 : 0;
                            
                        $conversionPorTipo = [
                            'introvertidos' => round($tasaIntrovertidos, 1),
                        ];
                    }
                    
                    return [
                        'departamentos' => $departamentos,
                        'tipos_personalidad' => $tiposPersonalidad,
                        'tipo_dominante' => $tipoDominante ? $tipoDominante->tipo_primario : null,
                        'porcentaje_dominante' => $porcentajeDominante,
                        'conversion_por_tipo' => $conversionPorTipo,
                    ];
                }
                
                private function getRecommendationMetrics()
                {
                    // Carreras más recomendadas
                    $carrerasRecomendadas = DB::table('test_carrera_recomendacion')
                        ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                        ->where('es_primaria', 1)
                        ->groupBy('carreras.id', 'carreras.nombre')
                        ->select(
                            'carreras.nombre', 
                            DB::raw('COUNT(*) as total'), 
                            DB::raw('AVG(match_porcentaje) as match_promedio')
                        )
                        ->orderBy('total', 'desc')
                        ->limit(10)
                        ->get();
                        
                    // Áreas de conocimiento
                    $areasConocimiento = DB::table('test_carrera_recomendacion')
                        ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                        ->whereNotNull('carreras.area_conocimiento')
                        ->where('carreras.area_conocimiento', '<>', '')
                        ->groupBy('carreras.area_conocimiento')
                        ->select('carreras.area_conocimiento', DB::raw('COUNT(*) as total'))
                        ->orderBy('total', 'desc')
                        ->get();
                        
                    // Identificar la carrera y área más popular
                    $carreraTop = $carrerasRecomendadas->first();
                    $areaTop = $areasConocimiento->first();
                    
                    return [
                        'carreras_recomendadas' => $carrerasRecomendadas,
                        'areas_conocimiento' => $areasConocimiento,
                        'carrera_top' => $carreraTop ? $carreraTop->nombre : null,
                        'match_promedio_top' => $carreraTop ? round($carreraTop->match_promedio, 1) : null,
                        'area_principal' => $areaTop ? $areaTop->area_conocimiento : null,
                        'crecimiento_area' => 15, // Simulado - sustituir por cálculo real cuando haya datos históricos
                    ];
                }
                
                private function getTrendMetrics()
                {
                    $añoActual = date('Y');
                    
                    // Tests completados por mes
                    $testsPorMes = Test::where('completado', 1)
                        ->whereYear('created_at', $añoActual)
                        ->select(
                            DB::raw("DATE_FORMAT(created_at, '%b %Y') as mes"), 
                            DB::raw('COUNT(*) as total')
                        )
                        ->groupBy('mes')
                        ->orderByRaw('MIN(created_at)')
                        ->get();
                        
                    // Calcular crecimiento mensual
                    $mesesData = [];
                    $mesAnteriorTotal = 0;
                    
                    foreach ($testsPorMes as $index => $mes) {
                        $crecimiento = 0;
                        if ($index > 0 && $mesAnteriorTotal > 0) {
                            $crecimiento = round((($mes->total - $mesAnteriorTotal) / $mesAnteriorTotal) * 100, 1);
                        }
                        
                        $mesesData[] = [
                            'mes' => $mes->mes,
                            'total' => $mes->total,
                            'crecimiento' => $crecimiento
                        ];
                        
                        $mesAnteriorTotal = $mes->total;
                    }
                    
                    return [
                        'tests_por_mes' => $testsPorMes,
                        'tendencia_mensual' => $mesesData,
                        'crecimiento_promedio' => !empty($mesesData) ? 
                            array_sum(array_column($mesesData, 'crecimiento')) / count($mesesData) : 0,
                    ];
                }
                
                private function getSatisfactionMetrics()
                {
                    // Retroalimentación promedio
                    $promedioUtilidad = Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') IS NOT NULL")
                        ->select(DB::raw("AVG(JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad')) as promedio"))
                        ->first();
                        
                    // Distribución de calificaciones
                    $distribucionCalificaciones = [];
                    $totalCalificaciones = 0;
                    
                    for ($i = 1; $i <= 5; $i++) {
                        $count = Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.utilidad') = ?", [$i])->count();
                        $distribucionCalificaciones[$i] = $count;
                        $totalCalificaciones += $count;
                    }
                    
                    // Calcular porcentajes
                    $porcentajes = [];
                    if ($totalCalificaciones > 0) {
                        foreach ($distribucionCalificaciones as $valor => $cantidad) {
                            $porcentajes[$valor] = round(($cantidad / $totalCalificaciones) * 100, 1);
                        }
                    }
                    
                    // Carreras realmente seleccionadas
                    $carrerasSeleccionadas = DB::table('tests')
                        ->join('carreras', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(tests.resultados, '$.retroalimentacion.carrera_seleccionada'))"), '=', 'carreras.id')
                        ->whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.carrera_seleccionada') IS NOT NULL")
                        ->groupBy('carreras.id', 'carreras.nombre')
                        ->select('carreras.nombre', DB::raw('COUNT(*) as total'))
                        ->orderBy('total', 'desc')
                        ->limit(5)
                        ->get();
                        
                    $totalSelecciones = Test::whereRaw("JSON_EXTRACT(resultados, '$.retroalimentacion.carrera_seleccionada') IS NOT NULL")->count();
                    
                    return [
                        'promedio_utilidad' => $promedioUtilidad ? round($promedioUtilidad->promedio, 1) : 0,
                        'distribucion_calificaciones' => $distribucionCalificaciones,
                        'porcentajes_calificacion' => $porcentajes,
                        'carreras_seleccionadas' => $carrerasSeleccionadas,
                        'total_selecciones' => $totalSelecciones,
                        'porcentaje_feedback' => $totalCalificaciones > 0 ? 
                            round(($totalCalificaciones / Test::where('completado', 1)->count()) * 100, 1) : 0,
                    ];
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}