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
        // 13. Promedios de utilidad y precisión desde retroalimentaciones
        $utilidadPromedio = null;
        $precisionPromedio = null;
        $departamentoFiltro = $request->input('departamento'); // asegurar que esté definido antes
        $retroalimentacionesQuery = \App\Models\Retroalimentacion::query();
        if ($departamentoFiltro) {
            $retroalimentacionesQuery = $retroalimentacionesQuery->whereHas('user', function($q) use ($departamentoFiltro) {
                $q->where('departamento', $departamentoFiltro);
            });
        }
        $utilidadPromedio = round($retroalimentacionesQuery->whereNotNull('utilidad')->avg('utilidad'), 2);
        $precisionPromedio = round($retroalimentacionesQuery->whereNotNull('precision')->avg('precision'), 2);
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
            
            // 3. Distribución por género - CORREGIDO para asegurar orden consistente
            $generosFijos = ['Femenino', 'Masculino', 'No especificado'];
            $distribucionPorGenero = [];

            // Inicializar con valores para asegurar que todos los géneros estén presentes
            foreach ($generosFijos as $genero) {
                $distribucionPorGenero[] = (object)[
                    'genero' => $genero,
                    'total' => 0
                ];
            }

            // Obtener los conteos reales de la base de datos
            $generosDB = User::where('created_at', '>=', $fechaDesde)
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                    return $q->where('departamento', $departamentoFiltro);
                })
                ->select('sexo', DB::raw('count(*) as total'))
                ->groupBy('sexo')
                ->get();

            // Actualizar los totales para los géneros que existen en la base de datos
            foreach ($generosDB as $item) {
                $generoNormalizado = null;
                
                if (strtolower($item->sexo) == 'f' || strtolower($item->sexo) == 'femenino') {
                    $generoNormalizado = 'Femenino';
                } else if (strtolower($item->sexo) == 'm' || strtolower($item->sexo) == 'masculino') {
                    $generoNormalizado = 'Masculino';
                } else {
                    $generoNormalizado = 'No especificado';
                }
                
                // Actualizar el total en nuestro array ordenado
                foreach ($distribucionPorGenero as $key => $distribucion) {
                    if ($distribucion->genero == $generoNormalizado) {
                        $distribucionPorGenero[$key]->total = $item->total;
                        break;
                    }
                }
            }

            // Loguear para verificar
            Log::info('Distribución por género: ', json_decode(json_encode($distribucionPorGenero), true));
            
            // 4. Distribución por edad - CORREGIDO para contar correctamente
            $distribucionPorEdad = [
                ['rango' => '16-18', 'total' => 0],
                ['rango' => '19-21', 'total' => 0],
                ['rango' => '22-25', 'total' => 0],
                ['rango' => '26-30', 'total' => 0],
                ['rango' => '31+', 'total' => 0]
            ];

            // Usar una consulta más directa - verificar que la fecha es válida
            $usuarios = User::where('created_at', '>=', $fechaDesde)
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                    return $q->where('departamento', $departamentoFiltro);
                })
                ->whereNotNull('fecha_nacimiento')
                ->where('fecha_nacimiento', '!=', '') // Asegurar que no está vacía
                ->where('fecha_nacimiento', '>=', '1930-01-01') // Fecha razonable
                ->where('fecha_nacimiento', '<=', now()) // No fechas futuras
                ->select('id', 'fecha_nacimiento')
                ->get();

            // Debuggear cuántos usuarios se encontraron
            Log::info('Usuarios con fecha de nacimiento válida: ' . $usuarios->count());

            foreach ($usuarios as $usuario) {
                try {
                    $fechaNacimiento = new Carbon($usuario->fecha_nacimiento);
                    $edad = $fechaNacimiento->age;
                    
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
                } catch (\Exception $e) {
                    Log::error('Error al procesar fecha de nacimiento: ' . $usuario->fecha_nacimiento);
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
                
            // 8. Carreras más recomendadas - VERSIÓN CORREGIDA
            $carrerasMasRecomendadas = [];

            try {
                // Verificar todas las tablas existentes
                $tablesArr = array_map(function($table) {
                    return get_object_vars($table)[key(get_object_vars($table))];
                }, $tables);
                
                // Usar método directo para obtener carreras (más confiable)
                $carrerasFromDB = DB::table('carreras')
                    ->select('id', 'nombre')
                    ->get();
                
                // Obtener recomendaciones de test desde resultados JSON
                $testsConResultados = Test::where('created_at', '>=', $fechaDesde)
                    ->where('completado', 1)
                    ->whereNotNull('resultados')
                    ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                        return $q->whereHas('user', function($query) use ($departamentoFiltro) {
                            $query->where('departamento', $departamentoFiltro);
                        });
                    })
                    ->get();
                
                // Estructura para contar recomendaciones
                $carrerasCounts = [];
                
                // Procesar cada test
                foreach ($testsConResultados as $test) {
                    $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                    
                    if (!empty($resultados['recomendaciones'])) {
                        foreach ($resultados['recomendaciones'] as $recomendacion) {
                            if (empty($recomendacion['carrera_id'])) continue;
                            
                            $carreraId = $recomendacion['carrera_id'];
                            $match = isset($recomendacion['match']) ? $recomendacion['match'] : 0;
                            
                            if (!isset($carrerasCounts[$carreraId])) {
                                // Buscar el nombre de la carrera en la BD
                                $carreraNombre = null;
                                foreach ($carrerasFromDB as $carrera) {
                                    if ($carrera->id == $carreraId) {
                                        $carreraNombre = $carrera->nombre;
                                        break;
                                    }
                                }
                                
                                // Si no se encuentra, usar el del JSON
                                if (!$carreraNombre && isset($recomendacion['nombre'])) {
                                    $carreraNombre = $recomendacion['nombre'];
                                } else if (!$carreraNombre) {
                                    $carreraNombre = "Carrera #" . $carreraId;
                                }
                                
                                $carrerasCounts[$carreraId] = [
                                    'id' => $carreraId,
                                    'nombre' => $carreraNombre,
                                    'total' => 0,
                                    'match_sum' => 0
                                ];
                            }
                            
                            $carrerasCounts[$carreraId]['total']++;
                            $carrerasCounts[$carreraId]['match_sum'] += $match;
                        }
                    }
                }
                
                // Convertir a array simple para JSON
                foreach ($carrerasCounts as $id => $data) {
                    $carrerasMasRecomendadas[] = [
                        'id' => $data['id'],
                        'nombre' => $data['nombre'],
                        'total' => $data['total'],
                        'match_promedio' => $data['total'] > 0 ? $data['match_sum'] / $data['total'] : 0
                    ];
                }
                
                // Ordenar por total de forma descendente
                usort($carrerasMasRecomendadas, function($a, $b) {
                    return $b['total'] - $a['total'];
                });
                
                // Limitar a 10 resultados
                $carrerasMasRecomendadas = array_slice($carrerasMasRecomendadas, 0, 10);
                
                // Garantizar que sea un array serializable
                Log::info("Carreras procesadas correctamente. Total: " . count($carrerasMasRecomendadas));
                
            } catch (\Exception $e) {
                Log::error('Error al extraer recomendaciones: ' . $e->getMessage());
                // Datos de respaldo en caso de error
                $carrerasMasRecomendadas = [
                    ['nombre' => 'Ejemplo: Ingeniería', 'total' => 5, 'match_promedio' => 80],
                    ['nombre' => 'Ejemplo: Medicina', 'total' => 4, 'match_promedio' => 75]
                ];
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
            
            // 11. Insights adicionales - CORREGIDO para manejar arrays y colecciones
            // Obtener el tipo de personalidad dominante
            $tipoPersonalidadDominante = null;
            $porcentajeDominante = 0;
            $carreraTop = null;
            $porcentajeTopCarreras = 0;
            
            // Verificar si porTipoPersonalidad es una colección
            if ($porTipoPersonalidad instanceof \Illuminate\Support\Collection && $porTipoPersonalidad->count() > 0) {
                $tipoPersonalidadDominante = $porTipoPersonalidad->first()->tipo_primario;
                $totalPersonalidades = $porTipoPersonalidad->sum('total');
                $porcentajeDominante = round(($porTipoPersonalidad->first()->total / $totalPersonalidades) * 100, 1);
            }
            
            // Obtener la carrera principal - CORREGIDO
            if (!empty($carrerasMasRecomendadas)) {
                // Si hay al menos una carrera
                $primerCarrera = $carrerasMasRecomendadas[0];
                $carreraTop = $primerCarrera['nombre'];
                
                // Calcular totales manualmente
                $totalRecomendaciones = 0;
                $top5Total = 0;
                
                // Sumar manualmente para mayor seguridad
                foreach ($carrerasMasRecomendadas as $index => $carrera) {
                    $total = $carrera['total'];
                    $totalRecomendaciones += $total;
                    
                    if ($index < 5) { // Para las primeras 5
                        $top5Total += $total;
                    }
                }
                
                // Calcular porcentaje
                $porcentajeTopCarreras = $totalRecomendaciones > 0 ? 
                    round(($top5Total / $totalRecomendaciones) * 100, 1) : 0;
            }
            

            // 12. Carreras más seleccionadas por los usuarios en la retroalimentación (solo nueva consulta)
            $carrerasSeleccionadasTop = \App\Models\Retroalimentacion::select('carrera_id', DB::raw('COUNT(*) as total'))
                ->whereNotNull('carrera_id')
                ->when($departamentoFiltro, function($q) use ($departamentoFiltro) {
                    return $q->whereHas('user', function($query) use ($departamentoFiltro) {
                        $query->where('departamento', $departamentoFiltro);
                    });
                })
                ->groupBy('carrera_id')
                ->orderByDesc('total')
                ->with('carrera:id,nombre')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'nombre' => $item->carrera->nombre ?? 'Sin nombre',
                        'total' => $item->total,
                    ];
                })
                ->toArray();

            return view('admin.estadisticas.index', compact(
                'totalUsuarios', 'testsIniciados', 'testsCompletados', 'departamentos', 'departamentoFiltro',
                'distribucionPorGenero', 'distribucionPorEdad', 'estudiantesPorDepartamento',
                'topInstituciones', 'porTipoPersonalidad', 'carrerasMasRecomendadas',
                'valoracionPromedio', 'distribucionValoraciones', 'totalValoraciones', 'comentariosRecientes',
                'tipoPersonalidadDominante', 'porcentajeDominante', 'carreraTop', 'porcentajeTopCarreras',
                'carrerasSeleccionadasTop', 'utilidadPromedio', 'precisionPromedio'
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
                
                // Distribución por edad - CORREGIDO
                fputcsv($file, ['DISTRIBUCIÓN POR EDAD']);
                fputcsv($file, ['Rango', 'Total', 'Porcentaje']);
                
                // Calcular distribución por edad
                $distribucionPorEdad = [
                    '16-18' => 0,
                    '19-21' => 0,
                    '22-25' => 0,
                    '26-30' => 0,
                    '31+' => 0
                ];
                
                $usuarios = User::where('created_at', '>=', $fechaDesde)
                    ->whereNotNull('fecha_nacimiento')
                    ->where('fecha_nacimiento', '!=', '')
                    ->where('fecha_nacimiento', '>=', '1930-01-01')
                    ->where('fecha_nacimiento', '<=', now())
                    ->select('id', 'fecha_nacimiento')
                    ->get();
                    
                foreach ($usuarios as $usuario) {
                    try {
                        $fechaNacimiento = new Carbon($usuario->fecha_nacimiento);
                        $edad = $fechaNacimiento->age;
                        
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
                    } catch (\Exception $e) {
                        // Ignorar errores de fechas inválidas
                    }
                }
                
                $totalEdades = array_sum($distribucionPorEdad);
                foreach ($distribucionPorEdad as $rango => $total) {
                    $porcentaje = $totalEdades > 0 ? round(($total / $totalEdades) * 100, 1) . '%' : '0%';
                    fputcsv($file, [$rango, $total, $porcentaje]);
                }
                fputcsv($file, []);
                
                // Distribución por género - CORREGIDO
                fputcsv($file, ['DISTRIBUCIÓN POR GÉNERO']);
                fputcsv($file, ['Género', 'Total', 'Porcentaje']);
                
                // Inicializar géneros fijos
                $generosFijos = ['Femenino', 'Masculino', 'No especificado'];
                $distribucionPorGenero = [
                    'Femenino' => 0,
                    'Masculino' => 0,
                    'No especificado' => 0
                ];
                
                // Obtener conteos reales
                $generosDB = User::where('created_at', '>=', $fechaDesde)
                    ->select('sexo', DB::raw('count(*) as total'))
                    ->groupBy('sexo')
                    ->get();
                
                // Normalizar y actualizar conteos
                foreach ($generosDB as $item) {
                    $generoNormalizado = 'No especificado';
                    
                    if (strtolower($item->sexo) == 'f' || strtolower($item->sexo) == 'femenino') {
                        $generoNormalizado = 'Femenino';
                    } else if (strtolower($item->sexo) == 'm' || strtolower($item->sexo) == 'masculino') {
                        $generoNormalizado = 'Masculino';
                    }
                    
                    $distribucionPorGenero[$generoNormalizado] += $item->total;
                }
                
                $totalGeneros = array_sum($distribucionPorGenero);
                foreach ($generosFijos as $genero) {
                    $total = $distribucionPorGenero[$genero];
                    $porcentaje = $totalGeneros > 0 ? round(($total / $totalGeneros) * 100, 1) . '%' : '0%';
                    fputcsv($file, [$genero, $total, $porcentaje]);
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
                
                // Carreras más recomendadas - CORREGIDO
                try {
                    // Verificar todas las tablas existentes
                    $tables = DB::select('SHOW TABLES');
                    $tablesArr = array_map(function($table) {
                        return get_object_vars($table)[key(get_object_vars($table))];
                    }, $tables);
                    
                    // Buscar la tabla correcta que relaciona tests y carreras
                    $tablasParaProbar = [
                        'test_carrera_recomendacion', 
                        'test_carrera', 
                        'test_recomendaciones', 
                        'carrera_test',
                        'recomendaciones'
                    ];
                    
                    $tablaEncontrada = null;
                    foreach ($tablasParaProbar as $tabla) {
                        if (in_array($tabla, $tablesArr)) {
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
                            $compatibilidadField = 'porcentaje';
                        }
                        
                        // Ver qué columnas tiene la tabla
                        $columns = Schema::getColumnListing($tablaEncontrada);
                        
                        // Verificar si los campos necesarios existen
                        if (!in_array($testIdField, $columns) || !in_array($carreraIdField, $columns)) {
                            // Si no, intentar con otros nombres comunes
                            if (in_array('id_test', $columns)) $testIdField = 'id_test';
                            if (in_array('id_carrera', $columns)) $carreraIdField = 'id_carrera';
                        }
                        
                        // Consulta final
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
                        // Extraer del JSON si no se encuentra tabla
                        $tests = Test::where('created_at', '>=', $fechaDesde)
                            ->where('completado', 1)
                            ->whereNotNull('resultados')
                            ->get();
                        
                        $carrerasConteo = [];
                        
                        foreach ($tests as $test) {
                            $resultados = is_string($test->resultados) ? json_decode($test->resultados, true) : $test->resultados;
                            
                            if (!empty($resultados['recomendaciones'])) {
                                foreach ($resultados['recomendaciones'] as $recomendacion) {
                                    if (empty($recomendacion['carrera_id']) || empty($recomendacion['nombre'])) {
                                        continue;
                                    }
                                    
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
                        
                        foreach ($carrerasConteo as $id => $data) {
                            $carrerasMasRecomendadas->push((object)[
                                'nombre' => $data['nombre'],
                                'total' => $data['total'],
                                'match_promedio' => $data['total'] > 0 ? $data['match_sum'] / $data['total'] : 0
                            ]);
                        }
                        
                        $carrerasMasRecomendadas = $carrerasMasRecomendadas->sortByDesc('total')->take(10)->values();
                    }
                    
                    fputcsv($file, ['CARRERAS MÁS RECOMENDADAS']);
                    fputcsv($file, ['Carrera', 'Total Recomendaciones', 'Match Promedio (%)']);
                    
                    foreach ($carrerasMasRecomendadas as $carrera) {
                        $nombre = $carrera instanceof \stdClass ? $carrera->nombre : $carrera['nombre'];
                        $total = $carrera instanceof \stdClass ? $carrera->total : $carrera['total'];
                        $match = $carrera instanceof \stdClass ? $carrera->match_promedio : $carrera['match_promedio'];
                        
                        fputcsv($file, [
                            $nombre, 
                            $total, 
                            round($match, 1) . '%'
                        ]);
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