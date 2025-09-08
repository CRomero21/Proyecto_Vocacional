<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Test;
use App\Models\Carrera;
use App\Models\Institucion;
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
            // Logging para diagnóstico
            $tables = DB::select('SHOW TABLES');
            Log::info('Tablas disponibles en la base de datos:', array_map(function($table) {
                return get_object_vars($table)[key(get_object_vars($table))];
            }, $tables));
            
            // Filtros
            $periodo = $request->input('periodo', 30); // Por defecto últimos 30 días
            $departamentoFiltro = $request->input('departamento');
            
            $fechaDesde = Carbon::now()->subDays($periodo);
            
            // Consulta base para usuarios con el filtro de departamento si existe
            $queryUsuarios = User::where('created_at', '>=', $fechaDesde);
            $queryTests = Test::where('created_at', '>=', $fechaDesde);
            
            if ($departamentoFiltro) {
                $queryUsuarios = $queryUsuarios->where('departamento', $departamentoFiltro);
                $queryTests = $queryTests->whereHas('user', function($q) use ($departamentoFiltro) {
                    $q->where('departamento', $departamentoFiltro);
                });
            }
            
            // 1. Resumen General
            $totalUsuarios = $queryUsuarios->count();
            $testsIniciados = $queryTests->count();
            $testsCompletados = $queryTests->where('completado', 1)->count();
            
            // 2. Lista de departamentos para el filtro
            $departamentos = User::whereNotNull('departamento')
                ->where('departamento', '!=', '')
                ->distinct()
                ->pluck('departamento')
                ->toArray();
            
            // 3. Distribución por género - usando 'sexo' en lugar de 'genero'
            $distribucionPorGenero = User::where('created_at', '>=', $fechaDesde)
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                    return $q->where('departamento', $departamentoFiltro);
                })
                ->select('sexo as genero', DB::raw('count(*) as total'))
                ->groupBy('sexo')
                ->get()
                ->map(function($item) {
                    // Normalizar nombres de género para la visualización
                    if (strtolower($item->genero) == 'f' || strtolower($item->genero) == 'femenino') {
                        $item->genero = 'Femenino';
                    } else if (strtolower($item->genero) == 'm' || strtolower($item->genero) == 'masculino') {
                        $item->genero = 'Masculino';
                    } else {
                        $item->genero = 'No especificado';
                    }
                    return $item;
                });
            
            // 4. Distribución por edad
            $distribucionPorEdad = [
                ['rango' => '16-18', 'total' => 0],
                ['rango' => '19-21', 'total' => 0],
                ['rango' => '22-25', 'total' => 0],
                ['rango' => '26-30', 'total' => 0],
                ['rango' => '31+', 'total' => 0]
            ];
            
            // Calcular edad a partir de fecha_nacimiento
            $usuarios = User::where('created_at', '>=', $fechaDesde)
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                    return $q->where('departamento', $departamentoFiltro);
                })
                ->whereNotNull('fecha_nacimiento')
                ->select('id', 'fecha_nacimiento')
                ->get()
                ->map(function($user) {
                    // Calcular edad a partir de fecha_nacimiento
                    $fechaNacimiento = new Carbon($user->fecha_nacimiento);
                    $user->edad = $fechaNacimiento->age;
                    return $user;
                });
            
            foreach ($usuarios as $usuario) {
                $edad = intval($usuario->edad);
                if ($edad >= 16 && $edad <= 18) {
                    $distribucionPorEdad[0]['total']++;
                } else if ($edad >= 19 && $edad <= 21) {
                    $distribucionPorEdad[1]['total']++;
                } else if ($edad >= 22 && $edad <= 25) {
                    $distribucionPorEdad[2]['total']++;
                } else if ($edad >= 26 && $edad <= 30) {
                    $distribucionPorEdad[3]['total']++;
                } else if ($edad > 30) {
                    $distribucionPorEdad[4]['total']++;
                }
            }
            
            // 5. Distribución geográfica
            $estudiantesPorDepartamento = User::where('created_at', '>=', $fechaDesde)
                ->whereNotNull('departamento')
                ->where('departamento', '!=', '')
                ->groupBy('departamento')
                ->select('departamento', DB::raw('count(*) as total'))
                ->orderByDesc('total')
                ->get();
            
            // 6. Instituciones con mayor participación - usando unidad_educativa
            $topInstituciones = DB::table('users')
                ->where('users.created_at', '>=', $fechaDesde)
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                    return $q->where('users.departamento', $departamentoFiltro);
                })
                ->whereNotNull('unidad_educativa')
                ->where('unidad_educativa', '!=', '')
                ->groupBy('unidad_educativa')
                ->select(
                    'unidad_educativa as nombre',
                    DB::raw('COUNT(*) as usuarios')
                )
                ->orderByDesc('usuarios')
                ->limit(5)
                ->get();
            
            // Calcular porcentaje de usuarios para cada institución
            if ($topInstituciones->count() > 0 && $totalUsuarios > 0) {
                foreach ($topInstituciones as $institucion) {
                    $institucion->porcentaje = round(($institucion->usuarios / $totalUsuarios) * 100, 1);
                }
            }
            
            // 7. Tipos de personalidad
            $porTipoPersonalidad = Test::where('tests.created_at', '>=', $fechaDesde)
                ->where('completado', 1)
                ->whereNotNull('tipo_primario')
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                    return $q->whereHas('user', function($query) use ($departamentoFiltro) {
                        $query->where('departamento', $departamentoFiltro);
                    });
                })
                ->groupBy('tipo_primario')
                ->select('tipo_primario', DB::raw('count(*) as total'))
                ->orderByDesc('total')
                ->get();
            
            // 8. Carreras más recomendadas - MODIFICADO para detectar la tabla correcta
            $carrerasMasRecomendadas = collect(); // Inicializa como colección vacía
            
            // Intenta encontrar la tabla correcta que relaciona tests con carreras
            $tablasParaProbar = [
                'test_carrera_recomendacion', 
                'test_carrera', 
                'test_recomendaciones', 
                'carrera_test',
                'recomendaciones'
            ];
            
            $tablaEncontrada = null;
            foreach ($tablasParaProbar as $tabla) {
                if (Schema::hasTable($tabla)) {
                    Log::info("Tabla encontrada para relación test-carrera: {$tabla}");
                    $tablaEncontrada = $tabla;
                    break;
                }
            }
            
            if ($tablaEncontrada) {
                try {
                    // Determinar qué campos usar según la tabla
                    $testIdField = 'test_id';
                    $carreraIdField = 'carrera_id';
                    $compatibilidadField = 'compatibilidad';
                    
                    // Ajustar nombres de campos según la tabla
                    if ($tablaEncontrada == 'recomendaciones') {
                        $testIdField = 'test_id';
                        $carreraIdField = 'carrera_id';
                        $compatibilidadField = 'porcentaje';
                    }
                    
                    $carrerasMasRecomendadas = DB::table('tests')
                        ->join($tablaEncontrada, 'tests.id', '=', $tablaEncontrada.'.'.$testIdField)
                        ->join('carreras', $tablaEncontrada.'.'.$carreraIdField, '=', 'carreras.id')
                        ->where('tests.created_at', '>=', $fechaDesde)
                        ->where('tests.completado', 1)
                        ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                            return $q->whereExists(function($query) use ($departamentoFiltro) {
                                $query->select(DB::raw(1))
                                    ->from('users')
                                    ->whereRaw('users.id = tests.user_id')
                                    ->where('departamento', $departamentoFiltro);
                            });
                        })
                        ->groupBy('carreras.id', 'carreras.nombre')
                        ->select(
                            'carreras.nombre', 
                            DB::raw('COUNT(*) as total'), 
                            DB::raw('AVG(IFNULL('.$tablaEncontrada.'.'.$compatibilidadField.', 0)) as match_promedio')
                        )
                        ->orderByDesc('total')
                        ->limit(10)
                        ->get();
                } catch (\Exception $e) {
                    Log::error('Error al consultar carreras recomendadas: ' . $e->getMessage());
                }
            } else {
                Log::warning('No se encontró ninguna tabla para la relación test-carrera');
                
                // Alternativa: Extraer recomendaciones del JSON de resultados
                try {
                    $tests = Test::where('created_at', '>=', $fechaDesde)
                        ->where('completado', 1)
                        ->whereNotNull('resultados')
                        ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                            return $q->whereHas('user', function($query) use ($departamentoFiltro) {
                                $query->where('departamento', $departamentoFiltro);
                            });
                        })
                        ->get();
                    
                    $carrerasConteo = [];
                    
                    foreach ($tests as $test) {
                        $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                        
                        if (!empty($resultados['recomendaciones'])) {
                            foreach ($resultados['recomendaciones'] as $recomendacion) {
                                $carreraId = $recomendacion['carrera_id'];
                                $carreraNombre = $recomendacion['nombre'];
                                $match = $recomendacion['match'] ?? 0;
                                
                                if (!isset($carrerasConteo[$carreraId])) {
                                    $carrerasConteo[$carreraId] = [
                                        'nombre' => $carreraNombre,
                                        'total' => 0,
                                        'match_sum' => 0
                                    ];
                                }
                                
                                $carrerasConteo[$carreraId]['total']++;
                                $carrerasConteo[$carreraId]['match_sum'] += $match;
                            }
                        }
                    }
                    
                    // Convertir a colección y calcular promedio
                    foreach ($carrerasConteo as $id => $data) {
                        $carrerasMasRecomendadas->push((object)[
                            'nombre' => $data['nombre'],
                            'total' => $data['total'],
                            'match_promedio' => $data['total'] > 0 ? $data['match_sum'] / $data['total'] : 0
                        ]);
                    }
                    
                    // Ordenar y limitar
                    $carrerasMasRecomendadas = $carrerasMasRecomendadas->sortByDesc('total')->take(10)->values();
                    
                } catch (\Exception $e) {
                    Log::error('Error al extraer recomendaciones del JSON: ' . $e->getMessage());
                }
            }
            
            // 9. Valoración y satisfacción - CORREGIDO: Leer desde JSON en resultados
            $valoracionPromedio = 0;
            $distribucionValoraciones = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            $totalValoraciones = 0;

            try {
                // Obtener los tests completados
                $testsConValoracion = Test::where('created_at', '>=', $fechaDesde)
                    ->where('completado', 1)
                    ->whereNotNull('resultados')
                    ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                        return $q->whereHas('user', function($query) use ($departamentoFiltro) {
                            $query->where('departamento', $departamentoFiltro);
                        });
                    })
                    ->get();
                
                // Variables para calcular promedios
                $sumaUtilidad = 0;
                $numUtilidad = 0;
                
                // Procesar cada test para extraer valoraciones del JSON
                foreach ($testsConValoracion as $test) {
                    $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                    
                    // Si hay retroalimentación en los resultados JSON
                    if (!empty($resultados['retroalimentacion']['utilidad'])) {
                        $valorUtilidad = (int)$resultados['retroalimentacion']['utilidad'];
                        if ($valorUtilidad >= 1 && $valorUtilidad <= 5) {
                            $sumaUtilidad += $valorUtilidad;
                            $numUtilidad++;
                            
                            // Incrementar contador en la distribución
                            if (isset($distribucionValoraciones[$valorUtilidad])) {
                                $distribucionValoraciones[$valorUtilidad]++;
                            }
                        }
                    }
                }
                
                // Calcular promedio si hay datos
                if ($numUtilidad > 0) {
                    $valoracionPromedio = $sumaUtilidad / $numUtilidad;
                    $totalValoraciones = $numUtilidad;
                }
            } catch (\Exception $e) {
                Log::error('Error al procesar valoraciones JSON: ' . $e->getMessage());
            }
            
            // 10. Comentarios recientes - CORREGIDO: Leer desde JSON en resultados
            $comentariosRecientes = collect();

            try {
                $testsConComentarios = Test::where('created_at', '>=', $fechaDesde)
                    ->where('completado', 1)
                    ->whereNotNull('resultados')
                    ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                        return $q->whereHas('user', function($query) use ($departamentoFiltro) {
                            $query->where('departamento', $departamentoFiltro);
                        });
                    })
                    ->with('user:id,name')
                    ->select('id', 'user_id', 'resultados', 'created_at')
                    ->orderByDesc('created_at')
                    ->limit(15)
                    ->get();
                
                foreach ($testsConComentarios as $test) {
                    $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                    
                    if (!empty($resultados['retroalimentacion']['comentario'])) {
                        $comentariosRecientes->push((object)[
                            'usuario' => $test->user->name ?? 'Usuario',
                            'valoracion' => $resultados['retroalimentacion']['utilidad'] ?? 0,
                            'texto' => $resultados['retroalimentacion']['comentario'],
                            'fecha' => $test->created_at->format('d/m/Y')
                        ]);
                    }
                }
                
                // Limitar a 5 comentarios
                $comentariosRecientes = $comentariosRecientes->take(5);
            } catch (\Exception $e) {
                Log::error('Error al procesar comentarios JSON: ' . $e->getMessage());
            }
            
            // 11. Insights adicionales
            // Obtener el tipo de personalidad dominante
            $tipoPersonalidadDominante = null;
            $porcentajeDominante = 0;
            $carreraTop = null;
            $porcentajeTopCarreras = 0;
            
            if ($porTipoPersonalidad->count() > 0) {
                $tipoPersonalidadDominante = $porTipoPersonalidad->first()->tipo_primario;
                $totalPersonalidades = $porTipoPersonalidad->sum('total');
                $porcentajeDominante = round(($porTipoPersonalidad->first()->total / $totalPersonalidades) * 100, 1);
            }
            
            // Obtener la carrera principal
            if (isset($carrerasMasRecomendadas) && $carrerasMasRecomendadas->count() > 0) {
                $carreraTop = $carrerasMasRecomendadas->first()->nombre;
                $totalRecomendaciones = $carrerasMasRecomendadas->sum('total');
                $top5Total = $carrerasMasRecomendadas->take(5)->sum('total');
                $porcentajeTopCarreras = $totalRecomendaciones > 0 ? round(($top5Total / $totalRecomendaciones) * 100, 1) : 0;
            }
            
            return view('admin.estadisticas.index', compact(
                'totalUsuarios', 'testsIniciados', 'testsCompletados', 'departamentos', 'departamentoFiltro',
                'distribucionPorGenero', 'distribucionPorEdad', 'estudiantesPorDepartamento',
                'topInstituciones', 'porTipoPersonalidad', 'carrerasMasRecomendadas',
                'valoracionPromedio', 'distribucionValoraciones', 'totalValoraciones', 'comentariosRecientes',
                'tipoPersonalidadDominante', 'porcentajeDominante', 'carreraTop', 'porcentajeTopCarreras'
            ));
            
        } catch (\Exception $e) {
            // Loguear el error para diagnóstico
            Log::error('Error en estadísticas: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Proporcionar valores por defecto para todas las variables cuando ocurre un error
            return view('admin.estadisticas.index', [
                'error' => 'Error al cargar las estadísticas: ' . $e->getMessage(),
                'totalUsuarios' => 0,
                'testsIniciados' => 0,
                'testsCompletados' => 0,
                'departamentos' => [],
                'departamentoFiltro' => null,
                'distribucionPorGenero' => [],
                'distribucionPorEdad' => [],
                'estudiantesPorDepartamento' => [],
                'topInstituciones' => [],
                'porTipoPersonalidad' => [],
                'carrerasMasRecomendadas' => [],
                'valoracionPromedio' => 0,
                'distribucionValoraciones' => [],
                'totalValoraciones' => 0,
                'comentariosRecientes' => [],
                'tipoPersonalidadDominante' => null,
                'porcentajeDominante' => 0,
                'carreraTop' => null,
                'porcentajeTopCarreras' => 0
            ]);
        }
    }
    
    public function exportarExcel()
    {
        try {
            // Preparar datos
            $periodo = 30; // Por defecto últimos 30 días
            $fechaDesde = Carbon::now()->subDays($periodo);
            
            // Obtener estadísticas básicas
            $totalUsuarios = User::where('created_at', '>=', $fechaDesde)->count();
            $testsIniciados = Test::where('created_at', '>=', $fechaDesde)->count();
            $testsCompletados = Test::where('created_at', '>=', $fechaDesde)->where('completado', 1)->count();
            
            // Crear respuesta CSV (compatible con todas las versiones de PHP)
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="estadisticas_vocacional_' . Carbon::now()->format('d_m_Y') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            $callback = function() use ($totalUsuarios, $testsIniciados, $testsCompletados, $fechaDesde) {
                $file = fopen('php://output', 'w');
                
                // Cabecera
                fputcsv($file, ['ESTADÍSTICAS DEL SISTEMA VOCACIONAL']);
                fputcsv($file, ['Generado el:', Carbon::now()->format('d/m/Y H:i')]);
                fputcsv($file, ['Periodo:', 'Últimos 30 días']);
                fputcsv($file, []);
                
                // Resumen general
                fputcsv($file, ['RESUMEN GENERAL']);
                fputcsv($file, ['Total Usuarios', $totalUsuarios]);
                fputcsv($file, ['Tests Iniciados', $testsIniciados]);
                fputcsv($file, ['Tests Completados', $testsCompletados]);
                fputcsv($file, ['Tasa de Completitud', $testsIniciados > 0 ? round(($testsCompletados / $testsIniciados) * 100, 1) . '%' : '0%']);
                fputcsv($file, []);
                
                // Distribución por edad
                $distribucionPorEdad = [
                    '16-18' => 0,
                    '19-21' => 0,
                    '22-25' => 0,
                    '26-30' => 0,
                    '31+' => 0
                ];
                
                $usuarios = User::where('created_at', '>=', $fechaDesde)
                    ->whereNotNull('fecha_nacimiento')
                    ->select('id', 'fecha_nacimiento')
                    ->get()
                    ->map(function($user) {
                        $fechaNacimiento = new Carbon($user->fecha_nacimiento);
                        $user->edad = $fechaNacimiento->age;
                        return $user;
                    });
                    
                foreach ($usuarios as $usuario) {
                    $edad = intval($usuario->edad);
                    if ($edad >= 16 && $edad <= 18) {
                        $distribucionPorEdad['16-18']++;
                    } else if ($edad >= 19 && $edad <= 21) {
                        $distribucionPorEdad['19-21']++;
                    } else if ($edad >= 22 && $edad <= 25) {
                        $distribucionPorEdad['22-25']++;
                    } else if ($edad >= 26 && $edad <= 30) {
                        $distribucionPorEdad['26-30']++;
                    } else if ($edad > 30) {
                        $distribucionPorEdad['31+']++;
                    }
                }
                
                fputcsv($file, ['DISTRIBUCIÓN POR EDAD']);
                fputcsv($file, ['Rango', 'Total', 'Porcentaje']);
                
                foreach ($distribucionPorEdad as $rango => $total) {
                    $porcentaje = $usuarios->count() > 0 ? round(($total / $usuarios->count()) * 100, 1) . '%' : '0%';
                    fputcsv($file, [$rango, $total, $porcentaje]);
                }
                fputcsv($file, []);
                
                // Distribución por género
                $distribucionPorGenero = User::where('created_at', '>=', $fechaDesde)
                    ->select('sexo as genero', DB::raw('count(*) as total'))
                    ->groupBy('sexo')
                    ->get()
                    ->map(function($item) {
                        if (strtolower($item->genero) == 'f' || strtolower($item->genero) == 'femenino') {
                            $item->genero = 'Femenino';
                        } else if (strtolower($item->genero) == 'm' || strtolower($item->genero) == 'masculino') {
                            $item->genero = 'Masculino';
                        } else {
                            $item->genero = 'No especificado';
                        }
                        return $item;
                    });
                
                fputcsv($file, ['DISTRIBUCIÓN POR GÉNERO']);
                fputcsv($file, ['Género', 'Total', 'Porcentaje']);
                
                $totalUsuariosPorGenero = $distribucionPorGenero->sum('total');
                foreach ($distribucionPorGenero as $item) {
                    $porcentaje = $totalUsuariosPorGenero > 0 ? round(($item->total / $totalUsuariosPorGenero) * 100, 1) . '%' : '0%';
                    fputcsv($file, [$item->genero, $item->total, $porcentaje]);
                }
                fputcsv($file, []);
                
                // Valoraciones - CORREGIDO: Leer desde JSON en resultados
                try {
                    // Obtener los tests completados
                    $testsConValoracion = Test::where('created_at', '>=', $fechaDesde)
                        ->where('completado', 1)
                        ->whereNotNull('resultados')
                        ->get();
                    
                    // Variables para calcular promedios
                    $sumaUtilidad = 0;
                    $numUtilidad = 0;
                    $distribucionValoraciones = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                    
                    // Procesar cada test para extraer valoraciones del JSON
                    foreach ($testsConValoracion as $test) {
                        $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                        
                        // Si hay retroalimentación en los resultados JSON
                        if (!empty($resultados['retroalimentacion']['utilidad'])) {
                            $valorUtilidad = (int)$resultados['retroalimentacion']['utilidad'];
                            if ($valorUtilidad >= 1 && $valorUtilidad <= 5) {
                                $sumaUtilidad += $valorUtilidad;
                                $numUtilidad++;
                                
                                // Incrementar contador en la distribución
                                if (isset($distribucionValoraciones[$valorUtilidad])) {
                                    $distribucionValoraciones[$valorUtilidad]++;
                                }
                            }
                        }
                    }
                    
                    // Calcular promedio si hay datos
                    $valoracionPromedio = $numUtilidad > 0 ? $sumaUtilidad / $numUtilidad : 0;
                    
                    fputcsv($file, ['VALORACIONES']);
                    fputcsv($file, ['Valoración promedio de utilidad', round($valoracionPromedio, 2) . '/5']);
                    fputcsv($file, ['Total valoraciones', $numUtilidad]);
                    fputcsv($file, []);
                    fputcsv($file, ['DISTRIBUCIÓN DE VALORACIONES']);
                    fputcsv($file, ['Valor', 'Cantidad', 'Porcentaje']);
                    
                    foreach ($distribucionValoraciones as $valor => $cantidad) {
                        $porcentaje = $numUtilidad > 0 ? round(($cantidad / $numUtilidad) * 100, 1) . '%' : '0%';
                        fputcsv($file, [$valor, $cantidad, $porcentaje]);
                    }
                } catch (\Exception $e) {
                    fputcsv($file, ['VALORACIONES']);
                    fputcsv($file, ['Error al procesar valoraciones: ' . $e->getMessage()]);
                }
                fputcsv($file, []);
                
                // Carreras más recomendadas - Extraer del JSON en caso necesario
                try {
                    // Intenta encontrar la tabla correcta que relaciona tests con carreras
                    $tablasParaProbar = [
                        'test_carrera_recomendacion', 
                        'test_carrera', 
                        'test_recomendaciones', 
                        'carrera_test',
                        'recomendaciones'
                    ];
                    
                    $tablaEncontrada = null;
                    foreach ($tablasParaProbar as $tabla) {
                        if (Schema::hasTable($tabla)) {
                            $tablaEncontrada = $tabla;
                            break;
                        }
                    }
                    
                    $carrerasMasRecomendadas = collect();
                    
                    if ($tablaEncontrada) {
                        // Determinar qué campos usar según la tabla
                        $testIdField = 'test_id';
                        $carreraIdField = 'carrera_id';
                        $compatibilidadField = 'compatibilidad';
                        
                        // Ajustar nombres de campos según la tabla
                        if ($tablaEncontrada == 'recomendaciones') {
                            $testIdField = 'test_id';
                            $carreraIdField = 'carrera_id';
                            $compatibilidadField = 'porcentaje';
                        }
                        
                        $carrerasMasRecomendadas = DB::table('tests')
                            ->join($tablaEncontrada, 'tests.id', '=', $tablaEncontrada.'.'.$testIdField)
                            ->join('carreras', $tablaEncontrada.'.'.$carreraIdField, '=', 'carreras.id')
                            ->where('tests.created_at', '>=', $fechaDesde)
                            ->where('tests.completado', 1)
                            ->groupBy('carreras.id', 'carreras.nombre')
                            ->select(
                                'carreras.nombre', 
                                DB::raw('COUNT(*) as total'), 
                                DB::raw('AVG(IFNULL('.$tablaEncontrada.'.'.$compatibilidadField.', 0)) as match_promedio')
                            )
                            ->orderByDesc('total')
                            ->limit(10)
                            ->get();
                    } else {
                        // Extraer del JSON
                        $tests = Test::where('created_at', '>=', $fechaDesde)
                            ->where('completado', 1)
                            ->whereNotNull('resultados')
                            ->get();
                        
                        $carrerasConteo = [];
                        
                        foreach ($tests as $test) {
                            $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                            
                            if (!empty($resultados['recomendaciones'])) {
                                foreach ($resultados['recomendaciones'] as $recomendacion) {
                                    $carreraId = $recomendacion['carrera_id'];
                                    $carreraNombre = $recomendacion['nombre'];
                                    $match = $recomendacion['match'] ?? 0;
                                    
                                    if (!isset($carrerasConteo[$carreraId])) {
                                        $carrerasConteo[$carreraId] = [
                                            'nombre' => $carreraNombre,
                                            'total' => 0,
                                            'match_sum' => 0
                                        ];
                                    }
                                    
                                    $carrerasConteo[$carreraId]['total']++;
                                    $carrerasConteo[$carreraId]['match_sum'] += $match;
                                }
                            }
                        }
                        
                        // Convertir a colección y calcular promedio
                        foreach ($carrerasConteo as $id => $data) {
                            $carrerasMasRecomendadas->push((object)[
                                'nombre' => $data['nombre'],
                                'total' => $data['total'],
                                'match_promedio' => $data['total'] > 0 ? $data['match_sum'] / $data['total'] : 0
                            ]);
                        }
                        
                        // Ordenar y limitar
                        $carrerasMasRecomendadas = $carrerasMasRecomendadas->sortByDesc('total')->take(10)->values();
                    }
                    
                    fputcsv($file, ['CARRERAS MÁS RECOMENDADAS']);
                    fputcsv($file, ['Carrera', 'Total Recomendaciones', 'Match Promedio (%)']);
                    
                    foreach ($carrerasMasRecomendadas as $carrera) {
                        fputcsv($file, [$carrera->nombre, $carrera->total, round($carrera->match_promedio, 1) . '%']);
                    }
                } catch (\Exception $e) {
                    fputcsv($file, ['CARRERAS MÁS RECOMENDADAS']);
                    fputcsv($file, ['Error al procesar carreras: ' . $e->getMessage()]);
                }
                
                fclose($file);
            };
            
            return new StreamedResponse($callback, 200, $headers);
            
        } catch (\Exception $e) {
            // Loguear el error para diagnóstico
            Log::error('Error al exportar a Excel: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return redirect()->route('admin.estadisticas.index')
                ->with('error', 'Error al exportar a Excel: ' . $e->getMessage());
        }
    }
    
    public function exportarPdf()
    {
        // Redirigimos al usuario - la exportación PDF se maneja en el frontend con JS
        return redirect()->route('admin.estadisticas.index')
            ->with('success', 'La descarga del PDF comenzará automáticamente');
    }
}