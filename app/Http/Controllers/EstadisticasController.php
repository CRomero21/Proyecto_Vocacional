<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Test;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Tablas y filtros
            $tables = DB::select('SHOW TABLES');
            Log::info('Tablas disponibles en la base de datos:', array_map(function($table) {
                return get_object_vars($table)[key(get_object_vars($table))];
            }, $tables));

            $periodo = $request->input('periodo', 30);
            $fechaDesde = Carbon::now()->subDays($periodo);
            $departamentoFiltro = $request->input('departamento');

            // Resumen general
            $totalUsuarios = User::where('created_at', '>=', $fechaDesde)->count();
            $testsIniciados = Test::where('created_at', '>=', $fechaDesde)->count();
            $testsCompletados = Test::where('created_at', '>=', $fechaDesde)->where('completado', 1)->count();

            // Listas básicas
            $departamentos = \App\Models\Departamento::whereIn('id', User::pluck('departamento_id')->filter()->unique())
                ->pluck('nombre')->toArray();
            $ciudades = \App\Models\Ciudad::whereIn('id', User::pluck('ciudad_id')->filter()->unique())
                ->pluck('nombre')->toArray();
            $unidadesEducativas = \App\Models\UnidadEducativa::whereIn('id', User::pluck('unidad_educativa_id')->filter()->unique())
                ->pluck('nombre')->toArray();

            // Género
            $generosFijos = ['Femenino', 'Masculino', 'No especificado'];
            $distribucionPorGenero = array_map(function($g){ return (object)['genero'=>$g,'total'=>0]; }, $generosFijos);
            $generosDB = User::where('created_at', '>=', $fechaDesde)
                ->select('sexo', DB::raw('count(*) as total'))
                ->groupBy('sexo')->get();
            foreach ($generosDB as $item) {
                $g = strtolower($item->sexo);
                $norm = ($g==='f' || $g==='femenino') ? 'Femenino' : (($g==='m'||$g==='masculino') ? 'Masculino' : 'No especificado');
                foreach ($distribucionPorGenero as $k => $obj) {
                    if ($obj->genero === $norm) { $distribucionPorGenero[$k]->total = $item->total; break; }
                }
            }

            // Edad (rango ajustado: 16-19, 20-22, 23-25, 26-30, 31+)
            $distribucionPorEdad = [
                ['rango' => '16-19', 'total' => 0],
                ['rango' => '20-22', 'total' => 0],
                ['rango' => '23-25', 'total' => 0],
                ['rango' => '26-30', 'total' => 0],
                ['rango' => '31+', 'total' => 0],
            ];
            $usuarios = User::where('created_at', '>=', $fechaDesde)
                ->whereNotNull('fecha_nacimiento')
                ->where('fecha_nacimiento', '!=', '')
                ->where('fecha_nacimiento', '>=', '1930-01-01')
                ->where('fecha_nacimiento', '<=', now())
                ->select('id', 'fecha_nacimiento')->get();
            foreach ($usuarios as $u) {
                try {
                    $edad = Carbon::parse($u->fecha_nacimiento)->age;
                    if ($edad >= 16 && $edad <= 19) $distribucionPorEdad[0]['total']++;
                    elseif ($edad >= 20 && $edad <= 22) $distribucionPorEdad[1]['total']++;
                    elseif ($edad >= 23 && $edad <= 25) $distribucionPorEdad[2]['total']++;
                    elseif ($edad >= 26 && $edad <= 30) $distribucionPorEdad[3]['total']++;
                    elseif ($edad > 30) $distribucionPorEdad[4]['total']++;
                } catch (\Exception $e) { Log::error('Fecha inválida: '.$u->fecha_nacimiento); }
            }

            // Departamentos
            $estudiantesPorDepartamento = \App\Models\Departamento::select('departamentos.nombre as departamento', DB::raw('COUNT(users.id) as total'))
                ->join('users', 'departamentos.id', '=', 'users.departamento_id')
                ->where('users.created_at', '>=', $fechaDesde)
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) { $q->where('departamentos.nombre', $departamentoFiltro); })
                ->groupBy('departamentos.id', 'departamentos.nombre')
                ->orderByDesc('total')->get();

            // Instituciones top 5
            $topInstituciones = \App\Models\UnidadEducativa::select('unidades_educativas.nombre as nombre', DB::raw('COUNT(users.id) as usuarios'))
                ->join('users', 'unidades_educativas.id', '=', 'users.unidad_educativa_id')
                ->groupBy('unidades_educativas.id', 'unidades_educativas.nombre')
                ->orderByDesc('usuarios')->limit(5)->get();
            $topTotalUsuarios = $topInstituciones->sum('usuarios');
            if ($topTotalUsuarios > 0) foreach ($topInstituciones as $inst) { $inst->porcentaje = round(($inst->usuarios / $topTotalUsuarios) * 100, 1); }

            // Personalidad
            $porTipoPersonalidad = Test::where('tests.created_at', '>=', $fechaDesde)
                ->where('completado', 1)->whereNotNull('tipo_primario')
                ->groupBy('tipo_primario')
                ->select('tipo_primario', DB::raw('count(*) as total'))
                ->orderByDesc('total')->get();

            // Carreras más solicitadas (desde retroalimentaciones)
            $carrerasMasRecomendadas = [];
            try {
                $retro = \App\Models\Retroalimentacion::query()
                    ->whereNotNull('carrera_id')
                    ->where('retroalimentaciones.created_at', '>=', $fechaDesde)
                    ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                        $q->join('users', 'retroalimentaciones.user_id', '=', 'users.id')
                          ->join('departamentos', 'users.departamento_id', '=', 'departamentos.id')
                          ->where('departamentos.nombre', $departamentoFiltro)
                          ->select('retroalimentaciones.*');
                    })
                    ->select('carrera_id', DB::raw('COUNT(*) as total'))
                    ->groupBy('carrera_id')
                    ->orderByDesc('total')
                    ->with('carrera:id,nombre')
                    ->limit(10)->get();
                $carrerasMasRecomendadas = $retro->map(function($i){
                    return ['id'=>$i->carrera_id,'nombre'=>$i->carrera->nombre ?? 'Sin nombre','total'=>(int)$i->total,'match_promedio'=>0];
                })->values()->toArray();
            } catch (\Exception $e) { Log::error('Carreras retro error: '.$e->getMessage()); }

            // Valoraciones (tabla retroalimentaciones)
            $utilidadPromedio = 0; $precisionPromedio = 0; $distribucionValoraciones = [1=>0,2=>0,3=>0,4=>0,5=>0]; $totalValoraciones = 0;
            try {
                $queryRetro = \App\Models\Retroalimentacion::whereNotNull('utilidad');
                $totalValoraciones = $queryRetro->count();
                $utilidadPromedio = $totalValoraciones > 0 ? round($queryRetro->avg('utilidad'), 2) : 0;
                $precisionPromedio = $totalValoraciones > 0 ? round($queryRetro->avg('precision'), 2) : 0;
                $valores = $queryRetro->select('utilidad', DB::raw('count(*) as total'))->groupBy('utilidad')->get();
                foreach ($valores as $it) { $v=(int)$it->utilidad; if ($v>=1 && $v<=5) $distribucionValoraciones[$v]=$it->total; }
            } catch (\Exception $e) { Log::error('Retro valoraciones error: '.$e->getMessage()); }

            // Comentarios recientes
            $comentariosRecientes = \App\Models\Retroalimentacion::whereNotNull('comentario')
                ->orderByDesc('created_at')->with('user:id,name')->limit(30)->get()
                ->map(function($item){ return (object)['usuario'=>$item->user->name ?? 'Usuario','valoracion'=>$item->utilidad ?? 0,'texto'=>$item->comentario,'fecha'=>Carbon::parse($item->created_at)->format('d/m/Y')]; });

            // Insights
            $tipoPersonalidadDominante = null; $porcentajeDominante = 0; $carreraTop = null; $porcentajeTopCarreras = 0;
            if ($porTipoPersonalidad instanceof \Illuminate\Support\Collection && $porTipoPersonalidad->count() > 0) {
                $tipoPersonalidadDominante = $porTipoPersonalidad->first()->tipo_primario;
                $totalPersonalidades = $porTipoPersonalidad->sum('total');
                $porcentajeDominante = round(($porTipoPersonalidad->first()->total / $totalPersonalidades) * 100, 1);
            }
            if (!empty($carrerasMasRecomendadas)) {
                $primer = $carrerasMasRecomendadas[0];
                $carreraTop = $primer['nombre'];
                $totalRecs = array_sum(array_map(function($c){ return $c['total']; }, $carrerasMasRecomendadas));
                $top5 = array_sum(array_map(function($c){ return $c['total']; }, array_slice($carrerasMasRecomendadas, 0, 5)));
                $porcentajeTopCarreras = $totalRecs > 0 ? round(($top5 / $totalRecs) * 100, 1) : 0;
            }

            // Carreras seleccionadas top (retro)
            $carrerasSeleccionadasTop = \App\Models\Retroalimentacion::select('carrera_id', DB::raw('COUNT(*) as total'))
                ->whereNotNull('carrera_id')->groupBy('carrera_id')->orderByDesc('total')
                ->with('carrera:id,nombre')->limit(10)->get()->map(function($it){ return ['nombre'=>$it->carrera->nombre ?? 'Sin nombre','total'=>$it->total]; })->toArray();
            if (empty($carrerasSeleccionadasTop) || !is_array($carrerasSeleccionadasTop)) { $carrerasSeleccionadasTop = []; }

            return view('admin.estadisticas.index', compact(
                'totalUsuarios','testsIniciados','testsCompletados','departamentos',
                'distribucionPorGenero','distribucionPorEdad','estudiantesPorDepartamento',
                'topInstituciones','porTipoPersonalidad','carrerasMasRecomendadas',
                'utilidadPromedio','precisionPromedio','distribucionValoraciones','totalValoraciones','comentariosRecientes',
                'tipoPersonalidadDominante','porcentajeDominante','carreraTop','porcentajeTopCarreras',
                'carrerasSeleccionadasTop','departamentoFiltro'
            ));

        } catch (\Exception $e) {
            Log::error('Error en estadísticas: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return view('admin.estadisticas.index', [
                'error' => 'Error al cargar las estadísticas: ' . $e->getMessage(),
                'totalUsuarios' => 0, 'testsIniciados' => 0, 'testsCompletados' => 0,
                'departamentos' => [], 'departamentoFiltro' => null,
                'distribucionPorGenero' => [], 'distribucionPorEdad' => [], 'estudiantesPorDepartamento' => [],
                'topInstituciones' => [], 'porTipoPersonalidad' => [], 'carrerasMasRecomendadas' => [],
                'valoracionPromedio' => 0, 'distribucionValoraciones' => [], 'totalValoraciones' => 0,
                'comentariosRecientes' => [], 'tipoPersonalidadDominante' => null, 'porcentajeDominante' => 0,
                'carreraTop' => null, 'porcentajeTopCarreras' => 0, 'carrerasSeleccionadasTop' => []
            ]);
        }
    }

    public function exportarExcel(Request $request)
    {
        try {
            $periodo = (int) $request->input('periodo', 30);
            $departamentoFiltro = $request->input('departamento');
            $fechaDesde = Carbon::now()->subDays($periodo);

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="estadisticas_vocacional_' . Carbon::now()->format('Ymd_His') . '.csv"',
                'Pragma' => 'no-cache','Cache-Control' => 'must-revalidate, post-check=0, pre-check=0','Expires' => '0'
            ];

            $callback = function() use ($fechaDesde, $periodo, $departamentoFiltro) {
                $out = fopen('php://output', 'w');
                // BOM UTF-8 para compatibilidad Excel en Windows
                fwrite($out, "\xEF\xBB\xBF");
                // Helper para escribir con ; como separador
                $csv = function(array $row) use ($out) { fputcsv($out, $row, ';'); };

                // Resumen general
                $totalUsuarios = User::where('created_at', '>=', $fechaDesde)->count();
                $testsIniciados = Test::where('created_at', '>=', $fechaDesde)->count();
                $testsCompletados = Test::where('created_at', '>=', $fechaDesde)->where('completado', 1)->count();

                $csv(['ESTADÍSTICAS DEL SISTEMA VOCACIONAL']);
                $csv(['Generado el', Carbon::now()->format('d/m/Y H:i')]);
                $csv(['Periodo', 'Últimos ' . $periodo . ' días']);
                $csv(['Departamento', $departamentoFiltro ? $departamentoFiltro : 'Todos']);
                $csv([]);
                $csv(['RESUMEN GENERAL']);
                $csv(['Total Usuarios', $totalUsuarios]);
                $csv(['Tests Iniciados', $testsIniciados]);
                $csv(['Tests Completados', $testsCompletados]);
                $csv(['Tasa de Completitud', $testsIniciados > 0 ? round(($testsCompletados / $testsIniciados) * 100, 1) . '%"' : '0%']);
                $csv([]);

                // Edad (rangos: 16-19, 20-22, 23-25, 26-30, 31+)
                $csv(['DISTRIBUCIÓN POR EDAD']);
                $csv(['Rango','Total','Porcentaje']);
                $edades = ['16-19'=>0,'20-22'=>0,'23-25'=>0,'26-30'=>0,'31+'=>0];
                $usuarios = User::where('created_at','>=',$fechaDesde)
                    ->whereNotNull('fecha_nacimiento')
                    ->where('fecha_nacimiento','!=','')
                    ->where('fecha_nacimiento','>=','1930-01-01')
                    ->where('fecha_nacimiento','<=',now())
                    ->select('id','fecha_nacimiento')->get();
                foreach($usuarios as $u){
                    try{ $edad=Carbon::parse($u->fecha_nacimiento)->age;
                        if($edad>=16&&$edad<=19)$edades['16-19']++;
                        elseif($edad>=20&&$edad<=22)$edades['20-22']++;
                        elseif($edad>=23&&$edad<=25)$edades['23-25']++;
                        elseif($edad>=26&&$edad<=30)$edades['26-30']++;
                        elseif($edad>30)$edades['31+']++; }catch(\Exception $e){}
                }
                $sumEd = array_sum($edades);
                foreach($edades as $r=>$t){ $p=$sumEd>0?round(($t/$sumEd)*100,1).'%' : '0%'; $csv([$r,$t,$p]); }
                $csv([]);

                // Género
                $csv(['DISTRIBUCIÓN POR GÉNERO']);
                $csv(['Género','Total','Porcentaje']);
                $gen = ['Femenino'=>0,'Masculino'=>0,'No especificado'=>0];
                $gdb = User::where('created_at','>=',$fechaDesde)->select('sexo', DB::raw('count(*) as total'))->groupBy('sexo')->get();
                foreach($gdb as $it){ $s=strtolower($it->sexo); $n = ($s==='f'||$s==='femenino')?'Femenino':(($s==='m'||$s==='masculino')?'Masculino':'No especificado'); $gen[$n]+=$it->total; }
                $sumG=array_sum($gen);
                foreach($gen as $k=>$t){ $p=$sumG>0?round(($t/$sumG)*100,1).'%' : '0%'; $csv([$k,$t,$p]); }
                $csv([]);

                // Personalidad
                $csv(['DISTRIBUCIÓN POR TIPO DE PERSONALIDAD']);
                $csv(['Tipo','Total','Porcentaje']);
                $per = Test::where('tests.created_at', '>=', $fechaDesde)
                    ->where('completado', 1)
                    ->whereNotNull('tipo_primario')
                    ->groupBy('tipo_primario')
                    ->select('tipo_primario', DB::raw('count(*) as total'))
                    ->orderByDesc('total')->get();
                $sumP = $per->sum('total');
                foreach($per as $it){ $csv([$it->tipo_primario, (int)$it->total, $sumP>0?round(($it->total/$sumP)*100,1).'%' : '0%']); }
                $csv([]);

                // Departamentos (aplicando filtro si existe)
                $csv(['DISTRIBUCIÓN POR DEPARTAMENTO']);
                $csv(['Departamento','Total']);
                $deptos = \App\Models\Departamento::select('departamentos.nombre as departamento', DB::raw('COUNT(users.id) as total'))
                    ->join('users', 'departamentos.id', '=', 'users.departamento_id')
                    ->where('users.created_at', '>=', $fechaDesde)
                    ->when($departamentoFiltro, function($q) use ($departamentoFiltro) { $q->where('departamentos.nombre', $departamentoFiltro); })
                    ->groupBy('departamentos.id', 'departamentos.nombre')
                    ->orderByDesc('total')->get();
                foreach($deptos as $d){ $csv([$d->departamento, (int)$d->total]); }
                $csv([]);

                // Carreras más solicitadas (retro)
                $csv(['CARRERAS MÁS SOLICITADAS (RETROALIMENTACIONES)']);
                $csv(['Carrera','Total Selecciones']);
                try {
                    $retro = \App\Models\Retroalimentacion::query()
                        ->whereNotNull('carrera_id')
                        ->where('retroalimentaciones.created_at', '>=', $fechaDesde)
                        ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                            $q->join('users', 'retroalimentaciones.user_id', '=', 'users.id')
                              ->join('departamentos', 'users.departamento_id', '=', 'departamentos.id')
                              ->where('departamentos.nombre', $departamentoFiltro)
                              ->select('retroalimentaciones.*');
                        })
                        ->select('carrera_id', DB::raw('COUNT(*) as total'))
                        ->groupBy('carrera_id')
                        ->orderByDesc('total')
                        ->with('carrera:id,nombre')
                        ->limit(10)->get();
                    foreach($retro as $it){ $csv([$it->carrera->nombre ?? 'Sin nombre', (int)$it->total]); }
                } catch (\Exception $e) {
                    $csv(['Error', $e->getMessage()]);
                }

                fclose($out);
            };

            return new StreamedResponse($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error al exportar a Excel: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('admin.estadisticas.index')
                ->with('error', 'Error al exportar a Excel: ' . $e->getMessage());
        }
    }

    public function exportarPdf()
    {
        return redirect()->route('admin.estadisticas.index')
            ->with('success', 'La descarga del PDF comenzará automáticamente');
    }
}