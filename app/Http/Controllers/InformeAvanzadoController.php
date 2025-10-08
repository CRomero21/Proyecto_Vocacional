<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Informe;
use App\Models\User;
use App\Models\TestResult;
use App\Models\Carrera;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\UnidadEducativa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class InformeAvanzadoController extends Controller
{
    public function index(Request $request)
    {
        // Validación de parámetros
        $request->validate([
            'tipo_informe' => 'nullable|in:usuarios_datos,instituciones_educativas,distribucion_demografica,personalidades,carreras,carreras_mas_solicitadas',
            'departamento' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'genero' => 'nullable|in:m,f',
            'edad_min' => 'nullable|integer|min:13|max:100',
            'edad_max' => 'nullable|integer|min:13|max:100|gte:edad_min',
            'institucion' => 'nullable|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'area_conocimiento' => 'nullable|string|max:100',
            'tipos_personalidad' => 'nullable|array',
            'tipos_personalidad.*' => 'in:R,I,A,S,E,C',
            'estado_test' => 'nullable|in:completado,incompleto',
            'campos' => 'nullable|array',
            'campos.*' => 'in:telefono,email'
        ]);

        // Variables para la vista
        $departamentos = [];
        $instituciones = [];
        $tipoInforme = $request->get('tipo_informe');
        $datos = null;
        $datosGrafico = null;
        $insights = [];
        $filtrosAplicados = [];
        $informeCargado = null;
        
        // Si se está viendo un informe guardado
        if ($request->route('id')) {
            $informeCargado = Informe::find($request->route('id'));
            if ($informeCargado) {
                // Verificación de permisos: solo propietario o superadmin
                if (auth()->user()->role !== 'superadmin' && $informeCargado->user_id !== auth()->id()) {
                    abort(403, 'No tienes permiso para ver este informe.');
                }
                $datos = json_decode($informeCargado->datos, true);
                $tipoInforme = $informeCargado->tipo;
                $filtrosAplicados = json_decode($informeCargado->filtros, true) ?? [];
            }
        }

        // Filtrar informes recientes por usuario (solo superadmin ve todos)
        $query = Informe::latest()->take(10);
        if (auth()->user()->role !== 'superadmin') {
            $query->where('user_id', auth()->id());
        }
        $informesRecientes = $query->get();
        
        // Si se está generando un nuevo informe
        if ($request->has('tipo_informe') && !$informeCargado) {
            $startTime = microtime(true);
            
            $limit = $request->get('limit', 1000); // Máximo por defecto
            $page = $request->get('page', 1);
            
            $datos = $this->generarDatosInforme($request, $limit);
            $filtrosAplicados = $request->all();
            $datosGrafico = $this->prepararDatosGrafico($datos, $tipoInforme);
            $insights = $this->generarInsights($datos, $tipoInforme);
            
            $executionTime = microtime(true) - $startTime;
            
            // Logging mejorado con métricas
            Log::info('Informe generado', [
                'user_id' => auth()->id(),
                'tipo_informe' => $tipoInforme,
                'registros_generados' => is_array($datos) && isset($datos['data']) ? count($datos['data']) : count($datos),
                'tiempo_ejecucion' => round($executionTime, 3),
                'filtros_aplicados' => array_keys(array_filter($filtrosAplicados, function($value) {
                    return !empty($value) && !is_array($value);
                })),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Agregar información de paginación si es un array paginado
            if (is_array($datos) && isset($datos['data'])) {
                $filtrosAplicados['pagination'] = [
                    'current_page' => $datos['current_page'],
                    'per_page' => $datos['per_page'],
                    'total' => $datos['total'],
                    'last_page' => $datos['last_page']
                ];
                $datos = $datos['data']; // Extraer solo los datos para la vista
            }
        }
        
        // Obtener datos para filtros usando las relaciones correctas
        $departamentos = Departamento::whereHas('ciudades.unidadesEducativas.users')
            ->distinct()
            ->pluck('nombre')
            ->filter()
            ->sort()
            ->values();
        $instituciones = UnidadEducativa::whereHas('users')
            ->distinct()
            ->pluck('nombre')
            ->filter()
            ->sort()
            ->values();
        $areasConocimiento = $this->getCachedData('areas_conocimiento', function() {
            return Carrera::distinct()->pluck('area_conocimiento')->filter()->sort()->values();
        }, 60); // Cache por 1 hora
        
        return view('admin.informes-avanzados.index', compact(
            'informesRecientes',
            'departamentos',
            'instituciones',
            'tipoInforme',
            'datos',
            'datosGrafico',
            'insights',
            'filtrosAplicados',
            'informeCargado',
            'areasConocimiento'
        ));
    }
    
    public function generar(Request $request)
    {
        // Redirigir con los parámetros para que se procesen en index
        return redirect()->route('admin.informes-avanzados.index', $request->all());
    }
    
    public function guardar(Request $request)
    {
        $request->validate([
            'nombre_informe' => 'required|string|max:255',
            'datos_informe' => 'required',
            'tipo_informe' => 'required|string',
            'filtros' => 'nullable',
        ]);
        
        $informe = new Informe();
        $informe->nombre = $request->nombre_informe;
        $informe->tipo = $request->tipo_informe;
        $informe->datos = $request->datos_informe;
        $informe->filtros = $request->filtros ?? '{}';
        $informe->user_id = auth()->id() ?? 1; // Asignar usuario actual o por defecto
        $informe->save();
        
        return redirect()->route('admin.informes-avanzados.ver', $informe->id)->with('success', 'Informe guardado correctamente');
    }
    
    public function ver($id)
    {
        return $this->index(request()->merge(['id' => $id]));
    }
    
    public function exportar(Request $request)
    {
        Log::info('=== INICIO EXPORTACIÓN ===');
        Log::info('Método HTTP: ' . $request->method());
        Log::info('Todos los datos del request', ['request_data' => $request->all()]);
        
        try {
            // Aumentar límites de memoria y tiempo de ejecución
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300); // 5 minutos
            
            $formato = $request->formato ?? 'excel';
            $tipo = $request->tipo ?? 'general';
            $id = $request->id ?? null;
            $datosRaw = $request->datos ?? null;
            
            // Validar y sanitizar el parámetro id
            if (is_array($id)) {
                $id = reset($id); // Obtener el primer elemento si es array
            }
            
            Log::info("Parámetros: formato=$formato, tipo=$tipo, id=$id");
            Log::info("Datos raw recibidos", ['raw_data' => $datosRaw]);
            
            // Validar parámetros
            if (!in_array($formato, ['excel', 'pdf'])) {
                throw new \Exception("Formato de exportación no válido: $formato");
            }
            
            $datos = null;
            
            // Caso 1: Cargar desde informe guardado
            if ($id && !is_array($id)) {
                Log::info("Cargando desde informe guardado ID: $id");
                $informe = Informe::find($id);
                if (!$informe) {
                    throw new \Exception("El informe solicitado no existe");
                }
                // Verificación de permisos: solo propietario o superadmin
                if (auth()->user()->role !== 'superadmin' && $informe->user_id !== auth()->id()) {
                    throw new \Exception("No tienes permiso para exportar este informe.");
                }
                $datos = $informe->datos;
                $tipo = $informe->tipo;
                
                // Decodificar si es necesario
                if (is_string($datos)) {
                    $datos = json_decode($datos, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception("Error al decodificar los datos del informe guardado: " . json_last_error_msg());
                    }
                }
            } 
            // Caso 2: Datos enviados directamente
            elseif ($datosRaw && $datosRaw !== '' && $datosRaw !== '[]') {
                Log::info("Procesando datos enviados directamente");
                $datos = json_decode($datosRaw, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Error al decodificar los datos enviados: " . json_last_error_msg());
                }
            } 
            // Caso 3: No hay datos
            else {
                throw new \Exception("No se proporcionaron datos para exportar. Genere un informe primero.");
            }

            Log::info("Datos procesados correctamente, tipo: " . gettype($datos) . ", cantidad: " . (is_array($datos) ? count($datos) : 'N/A'));

            if (empty($datos)) {
                throw new \Exception("Los datos del informe están vacíos");
            }
            
            // Limitar datos si son muy grandes para evitar problemas de memoria
            if (is_array($datos) && count($datos) > 10000) {
                throw new \Exception("El informe contiene demasiados registros. Limite los filtros para exportar menos de 10,000 registros.");
            }
            
            // Generar archivo según formato
            if ($formato === 'excel') {
                Log::info("Generando Excel...");
                return $this->exportarExcel($datos, $tipo);
            } elseif ($formato === 'pdf') {
                Log::info("Generando PDF...");
                return $this->exportarPDF($datos, $tipo);
            } else {
                throw new \Exception("Formato de exportación no válido");
            }
            
        } catch (\Exception $e) {
            Log::error("Error en exportación: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Redirigir con mensaje de error
            return redirect()->back()->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }
    
    private function exportarExcel($datos, $tipo)
    {
        try {
            // Crear una nueva hoja de cálculo
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Definir encabezados según el tipo de informe
            $headers = $this->getHeadersForTipo($tipo);
            
            // Establecer encabezados
            foreach ($headers as $col => $header) {
                $sheet->setCellValue(chr(65 + $col) . '1', $header);
                $sheet->getStyle(chr(65 + $col) . '1')->getFont()->setBold(true);
            }
            
            // Establecer datos
            $row = 2;
            foreach ($datos as $item) {
                $col = 0;
                foreach ($headers as $header) {
                    $value = $this->getValueForHeader($item, $header, $tipo);
                    $sheet->setCellValue(chr(65 + $col) . $row, $value);
                    $col++;
                }
                $row++;
            }
            
            // Autoajustar columnas
            foreach (range('A', chr(65 + count($headers) - 1)) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Crear el escritor
            $writer = new Xlsx($spreadsheet);
            
            // Configurar headers para descarga
            $filename = 'informe_' . $tipo . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Guardar en la salida
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            Log::error("Error en exportarExcel: " . $e->getMessage());
            throw new \Exception("Error al generar el archivo Excel: " . $e->getMessage());
        }
    }
    
    private function exportarPDF($datos, $tipo)
    {
        try {
            // Preparar datos para la vista PDF
            $headers = $this->getHeadersForTipo($tipo);
            $titulo = $this->getTituloForTipo($tipo);
            
            // Crear vista temporal con los datos
            $pdf = Pdf::loadView('admin.informes-avanzados.pdf', compact('datos', 'headers', 'titulo', 'tipo'));
            
            // Configurar el PDF
            $pdf->setPaper('a4', 'landscape');
            
            // Descargar el PDF
            $filename = 'informe_' . $tipo . '_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error("Error en exportarPDF: " . $e->getMessage());
            throw new \Exception("Error al generar el archivo PDF: " . $e->getMessage());
        }
    }
    
    private function getHeadersForTipo($tipo)
    {
        switch ($tipo) {
            case 'usuarios_datos':
                return ['Nombre', 'Email', 'Teléfono', 'Género', 'Departamento', 'Ciudad', 'Institución'];
            case 'instituciones_educativas':
                // Los headers dependerán del nivel de agrupación, pero por simplicidad mantendremos los mismos
                return ['Institución', 'Departamento', 'Ciudad', 'Total Estudiantes', 'Tests Completados', 'Tests Incompletos'];
            case 'distribucion_demografica':
                return ['Departamento', 'Ciudad', 'Total Usuarios', 'Porcentaje'];
            case 'personalidades':
                return ['Tipo', 'Descripción', 'Total', 'Porcentaje'];
            case 'carreras':
                return ['Carrera', 'Área', 'Recomendaciones', 'Porcentaje', 'Match Promedio'];
            case 'carreras_mas_solicitadas':
                return ['Carrera', 'Área', 'Solicitudes', 'Porcentaje', 'Utilidad Promedio', 'Precisión Promedio'];
            default:
                return ['Dato'];
        }
    }
    
    private function getValueForHeader($item, $header, $tipo)
    {
        $isArray = is_array($item);
        $isObject = is_object($item);

        switch ($header) {
            case 'Nombre':
                return $isArray ? ($item['name'] ?? '') : ($isObject ? ($item->name ?? '') : '');
            case 'Email':
                return $isArray ? ($item['email'] ?? '') : ($isObject ? ($item->email ?? '') : '');
            case 'Teléfono':
                return $isArray ? ($item['phone'] ?? '') : ($isObject ? ($item->phone ?? '') : '');
            case 'Género':
                $sexo = $isArray ? ($item['sexo'] ?? '') : ($isObject ? ($item->sexo ?? '') : '');
                // Manejar diferentes formatos de valores de sexo
                if (strtolower($sexo) == 'm' || strtolower($sexo) == 'masculino') {
                    return 'Masculino';
                } elseif (strtolower($sexo) == 'f' || strtolower($sexo) == 'femenino') {
                    return 'Femenino';
                } elseif (strtolower($sexo) == 'otro' || strtolower($sexo) == 'o') {
                    return 'Otro';
                } else {
                    return $sexo ?: 'No especificado';
                }
            case 'Departamento':
                return $isArray ? ($item['departamento'] ?? $item['departamento_nombre'] ?? '') : ($isObject ? ($item->departamento ?? $item->departamento_nombre ?? '') : '');
            case 'Ciudad':
                return $isArray ? ($item['ciudad'] ?? $item['ciudad_nombre'] ?? '') : ($isObject ? ($item->ciudad ?? $item->ciudad_nombre ?? '') : '');
            case 'Institución':
                return $isArray ? ($item['nombre'] ?? $item['unidad_educativa'] ?? '') : ($isObject ? ($item->nombre ?? $item->unidad_educativa ?? '') : '');
            case 'Total Estudiantes':
                return $isObject ? ($item->total_estudiantes ?? 0) : ($isArray ? ($item['total_estudiantes'] ?? 0) : 0);
            case 'Tests Completados':
                return $isObject ? ($item->tests_completados ?? 0) : ($isArray ? ($item['tests_completados'] ?? 0) : 0);
            case 'Tests Incompletos':
                return $isObject ? ($item->tests_incompletos ?? 0) : ($isArray ? ($item['tests_incompletos'] ?? 0) : 0);
            case 'Total Usuarios':
                return $isObject ? ($item->total ?? $item->total_usuarios ?? 0) : ($isArray ? ($item['total'] ?? $item['total_usuarios'] ?? 0) : 0);
            case 'Porcentaje':
                $porcentaje = $isObject ? ($item->porcentaje ?? 0) : ($isArray ? ($item['porcentaje'] ?? 0) : 0);
                return $porcentaje . '%';
            case 'Estado':
                return $isArray ? ($item['estado'] ?? '') : '';
            case 'Cantidad':
                return $isArray ? ($item['cantidad'] ?? 0) : 0;
            case 'Tipo':
                return $isObject ? ($item->tipo_primario ?? '') : ($isArray ? ($item['tipo_primario'] ?? '') : '');
            case 'Descripción':
                return $isObject ? ($item->descripcion ?? '') : ($isArray ? ($item['descripcion'] ?? '') : '');
            case 'Total':
                return $isObject ? ($item->total ?? 0) : ($isArray ? ($item['total'] ?? 0) : 0);
            case 'Carrera':
                return $isObject ? ($item->nombre ?? '') : ($isArray ? ($item['nombre'] ?? '') : '');
            case 'Área':
                return $isObject ? ($item->area_conocimiento ?? '') : ($isArray ? ($item['area_conocimiento'] ?? '') : '');
            case 'Recomendaciones':
                return $isObject ? ($item->total ?? 0) : ($isArray ? ($item['total'] ?? 0) : 0);
            case 'Match Promedio':
                $match = $isObject ? ($item->match_promedio ?? 0) : ($isArray ? ($item['match_promedio'] ?? 0) : 0);
                return $match . '%';
            case 'Solicitudes':
                return $isObject ? ($item->total_solicitudes ?? 0) : ($isArray ? ($item['total_solicitudes'] ?? 0) : 0);
            case 'Utilidad Promedio':
                $utilidad = $isObject ? ($item->utilidad_promedio ?? 0) : ($isArray ? ($item['utilidad_promedio'] ?? 0) : 0);
                return $utilidad . '/5';
            case 'Precisión Promedio':
                $precision = $isObject ? ($item->precision_promedio ?? 0) : ($isArray ? ($item['precision_promedio'] ?? 0) : 0);
                return $precision . '/5';
            default:
                return '';
        }
    }
    
    private function getTituloForTipo($tipo)
    {
        $titulos = [
            'usuarios_datos' => 'Datos de Contacto de Usuarios',
            'instituciones_educativas' => 'Usuarios por Institución Educativa',
            'distribucion_demografica' => 'Distribución Geográfica',
            'personalidades' => 'Distribución de Tipos de Personalidad',
            'carreras' => 'Carreras Recomendadas',
            'carreras_mas_solicitadas' => 'Carreras Más Solicitadas',
        ];
        
        return $titulos[$tipo] ?? 'Informe';
    }
    
    private function generarDatosInforme(Request $request, int $limit = null)
    {
        $tipo = $request->tipo_informe;
        
        $datos = match($tipo) {
            'usuarios_datos' => $this->generarDatosUsuarios($request, $limit),
            'instituciones_educativas' => $this->generarDatosInstituciones($request, $limit),
            'distribucion_demografica' => $this->generarDatosDemograficos($request, $limit),
            'personalidades' => $this->generarDatosPersonalidades($request),
            'carreras' => $this->generarDatosCarreras($request),
            'carreras_mas_solicitadas' => $this->generarDatosCarrerasMasSolicitadas($request),
            default => []
        };
        
        return $datos;
    }
    
    private function generarDatosUsuarios(Request $request, int $limit = null)
    {
        $query = User::with(['departamento', 'ciudad', 'unidadEducativa']);
        
        // Aplicar filtros usando las relaciones
        if ($request->filled('departamento')) {
            $query->whereHas('departamento', function($q) use ($request) {
                $q->where('nombre', $request->departamento);
            });
        }
        if ($request->filled('ciudad')) {
            $query->whereHas('ciudad', function($q) use ($request) {
                $q->where('nombre', $request->ciudad);
            });
        }
        if ($request->filled('genero')) {
            // Convertir abreviaturas a palabras completas para coincidir con la base de datos
            $generoValue = $request->genero;
            if (strtolower($generoValue) == 'm') {
                $generoValue = 'Masculino';
            } elseif (strtolower($generoValue) == 'f') {
                $generoValue = 'Femenino';
            } elseif (strtolower($generoValue) == 'o') {
                $generoValue = 'Otro';
            }
            $query->where('sexo', $generoValue);
        }
        if ($request->filled('edad_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
        }
        if ($request->filled('edad_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
        }
        if ($request->filled('institucion')) {
            $query->whereHas('unidadEducativa', function($q) use ($request) {
                $q->where('nombre', $request->institucion);
            });
        }
        if ($request->filled('fecha_inicio')) {
            $query->where('created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }
        if ($request->filled('area_conocimiento')) {
            $query->whereHas('tests.carrerasRecomendadas', function($q) use ($request) {
                $q->where('area_conocimiento', $request->area_conocimiento);
            });
        }
        if ($request->filled('tipos_personalidad')) {
            $query->whereHas('tests', function($q) use ($request) {
                $q->whereIn('tipo_primario', $request->tipos_personalidad);
            });
        }
        if ($request->filled('estado_test')) {
            if ($request->estado_test === 'completado') {
                $query->whereHas('tests', function($q) {
                    $q->where('completado', 1);
                });
            } elseif ($request->estado_test === 'incompleto') {
                $query->whereDoesntHave('tests')
                      ->orWhereHas('tests', function($q) {
                          $q->where('completado', 0);
                      });
            }
        }
        
        // Aplicar paginación si se especifica un límite
        if ($limit && $limit > 0) {
            $usuarios = $query->paginate($limit);
            $datos = $usuarios->toArray();
            
            // Transformar los datos paginados
            $datos['data'] = array_map(function($usuario) {
                return [
                    'id' => $usuario['id'],
                    'name' => $usuario['name'],
                    'email' => $usuario['email'],
                    'phone' => $usuario['phone'],
                    'sexo' => $usuario['sexo'],
                    'departamento' => $usuario['departamento'] ? $usuario['departamento']['nombre'] : null,
                    'ciudad' => $usuario['ciudad'] ? $usuario['ciudad']['nombre'] : null,
                    'unidad_educativa' => $usuario['unidad_educativa'] ? $usuario['unidad_educativa']['nombre'] : null,
                    'fecha_nacimiento' => $usuario['fecha_nacimiento'],
                ];
            }, $datos['data']);
            
            return $datos;
        }
        
        $usuarios = $query->get();
        
        // Transformar los datos para incluir los nombres de las relaciones
        return $usuarios->map(function($usuario) {
            return [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'phone' => $usuario->phone,
                'sexo' => $usuario->sexo,
                'departamento' => $usuario->departamento ? $usuario->departamento->nombre : null,
                'ciudad' => $usuario->ciudad ? $usuario->ciudad->nombre : null,
                'unidad_educativa' => $usuario->unidadEducativa ? $usuario->unidadEducativa->nombre : null,
                'fecha_nacimiento' => $usuario->fecha_nacimiento,
            ];
        })->toArray();
    }
    
    private function generarDatosInstituciones(Request $request, int $limit = null)
    {
        // Determinar el nivel de agrupación basado en los filtros aplicados
        $departamentoSeleccionado = $request->filled('departamento');
        $ciudadSeleccionada = $request->filled('ciudad');
        $institucionSeleccionada = $request->filled('institucion');

        // Construir subconsulta para usuarios filtrados
        $subQueryUsuarios = DB::table('users')
            ->select('unidad_educativa_id')
            ->whereNotNull('unidad_educativa_id');

        // Aplicar filtros a los usuarios
        if ($request->filled('genero')) {
            // Convertir abreviaturas a palabras completas para coincidir con la base de datos
            $generoValue = $request->genero;
            if (strtolower($generoValue) == 'm') {
                $generoValue = 'Masculino';
            } elseif (strtolower($generoValue) == 'f') {
                $generoValue = 'Femenino';
            } elseif (strtolower($generoValue) == 'o') {
                $generoValue = 'Otro';
            }
            $subQueryUsuarios->where('sexo', $generoValue);
        }
        if ($request->filled('edad_min')) {
            $subQueryUsuarios->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
        }
        if ($request->filled('edad_max')) {
            $subQueryUsuarios->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
        }
        if ($request->filled('fecha_inicio')) {
            $subQueryUsuarios->where('created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $subQueryUsuarios->where('created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }
        if ($request->filled('area_conocimiento')) {
            $subQueryUsuarios->join('tests', 'users.id', '=', 'tests.user_id')
                ->join('test_carrera_recomendacion', 'tests.id', '=', 'test_carrera_recomendacion.test_id')
                ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                ->where('carreras.area_conocimiento', $request->area_conocimiento);
        }
        if ($request->filled('tipos_personalidad')) {
            $subQueryUsuarios->join('tests', 'users.id', '=', 'tests.user_id')
                ->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }
        if ($request->filled('estado_test')) {
            $subQueryUsuarios->join('tests', 'users.id', '=', 'tests.user_id');
            if ($request->estado_test === 'completado') {
                $subQueryUsuarios->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $subQueryUsuarios->where('tests.completado', 0);
            }
        }

        // Lógica condicional basada en los filtros aplicados
        if ($departamentoSeleccionado && !$ciudadSeleccionada && !$institucionSeleccionada) {
            // Nivel 1: Solo departamento seleccionado - mostrar ciudades
            return $this->generarDatosCiudadesPorDepartamento($request, $subQueryUsuarios, $limit);
        } elseif ($departamentoSeleccionado && $ciudadSeleccionada && !$institucionSeleccionada) {
            // Nivel 2: Departamento y ciudad seleccionados - mostrar instituciones
            return $this->generarDatosInstitucionesPorCiudad($request, $subQueryUsuarios, $limit);
        } else {
            // Nivel 3: Todos los filtros o ninguno - mostrar instituciones específicas
            return $this->generarDatosInstitucionEspecifica($request, $subQueryUsuarios, $limit);
        }
    }

    private function generarDatosCiudadesPorDepartamento(Request $request, $subQueryUsuarios, int $limit = null)
    {
        $query = DB::table('ciudades')
            ->selectRaw('
                ciudades.nombre as ciudad,
                departamentos.nombre as departamento,
                COUNT(DISTINCT users.id) as total_usuarios,
                COUNT(DISTINCT CASE WHEN tests.completado = 1 THEN tests.id END) as tests_completados,
                COUNT(DISTINCT CASE WHEN tests.completado = 0 OR tests.id IS NULL THEN users.id END) as tests_incompletos
            ')
            ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
            ->leftJoin('unidades_educativas', 'ciudades.id', '=', 'unidades_educativas.ciudad_id')
            ->leftJoin('users', 'unidades_educativas.id', '=', 'users.unidad_educativa_id')
            ->leftJoin('tests', 'users.id', '=', 'tests.user_id')
            ->where('departamentos.nombre', $request->departamento)
            ->whereIn('unidades_educativas.id', $subQueryUsuarios) // Solo instituciones con usuarios que cumplen filtros
            ->groupBy('ciudades.id', 'ciudades.nombre', 'departamentos.nombre')
            ->having('total_usuarios', '>', 0)
            ->orderBy('total_usuarios', 'desc');

        // Aplicar filtros adicionales que no están en la subconsulta
        if ($request->filled('fecha_inicio')) {
            $query->where('users.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('users.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }
        if ($request->filled('tipos_personalidad')) {
            $query->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }
        if ($request->filled('estado_test')) {
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where(function($q) {
                    $q->where('tests.completado', 0)->orWhereNull('tests.id');
                });
            }
        }

        if ($limit && $limit > 0) {
            $resultados = $query->paginate($limit);
            $datos = $resultados->toArray();

            $datos['data'] = array_map(function($item) {
                return [
                    'ciudad' => $item->ciudad,
                    'departamento' => $item->departamento,
                    'total_usuarios' => $item->total_usuarios,
                    'tests_completados' => $item->tests_completados,
                    'tests_incompletos' => $item->tests_incompletos,
                ];
            }, $datos['data']);

            return $datos;
        }

        $resultados = $query->get();

        return $resultados->map(function($item) {
            return [
                'ciudad' => $item->ciudad,
                'departamento' => $item->departamento,
                'total_usuarios' => $item->total_usuarios,
                'tests_completados' => $item->tests_completados,
                'tests_incompletos' => $item->tests_incompletos,
            ];
        })->toArray();
    }

    private function generarDatosInstitucionesPorCiudad(Request $request, $subQueryUsuarios, int $limit = null)
    {
        $query = UnidadEducativa::select('unidades_educativas.*')
            ->selectRaw('
                COUNT(DISTINCT users.id) as total_estudiantes,
                COUNT(DISTINCT CASE WHEN tests.completado = 1 THEN tests.id END) as tests_completados,
                COUNT(DISTINCT CASE WHEN tests.completado = 0 OR tests.id IS NULL THEN users.id END) as tests_incompletos,
                departamentos.nombre as departamento_nombre,
                ciudades.nombre as ciudad_nombre
            ')
            ->leftJoin('users', 'unidades_educativas.id', '=', 'users.unidad_educativa_id')
            ->leftJoin('tests', 'users.id', '=', 'tests.user_id')
            ->leftJoin('ciudades', 'unidades_educativas.ciudad_id', '=', 'ciudades.id')
            ->leftJoin('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
            ->where('departamentos.nombre', $request->departamento)
            ->where('ciudades.nombre', $request->ciudad)
            ->whereIn('unidades_educativas.id', $subQueryUsuarios) // Solo instituciones con usuarios que cumplen filtros
            ->groupBy('unidades_educativas.id', 'unidades_educativas.nombre', 'unidades_educativas.ciudad_id', 'departamentos.nombre', 'ciudades.nombre')
            ->having('total_estudiantes', '>', 0)
            ->orderBy('total_estudiantes', 'desc');

        // Aplicar filtros adicionales que no están en la subconsulta
        if ($request->filled('fecha_inicio')) {
            $query->where('users.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('users.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }
        if ($request->filled('tipos_personalidad')) {
            $query->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }
        if ($request->filled('estado_test')) {
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where(function($q) {
                    $q->where('tests.completado', 0)->orWhereNull('tests.id');
                });
            }
        }

        if ($limit && $limit > 0) {
            $instituciones = $query->paginate($limit);
            $datos = $instituciones->toArray();

            $datos['data'] = array_map(function($institucion) {
                return [
                    'id' => $institucion['id'],
                    'nombre' => $institucion['nombre'],
                    'departamento' => $institucion['departamento_nombre'] ?? null,
                    'ciudad' => $institucion['ciudad_nombre'] ?? null,
                    'total_estudiantes' => $institucion['total_estudiantes'] ?? 0,
                    'tests_completados' => $institucion['tests_completados'] ?? 0,
                    'tests_incompletos' => $institucion['tests_incompletos'] ?? 0,
                ];
            }, $datos['data']);

            return $datos;
        }

        $instituciones = $query->get();

        return $instituciones->map(function($institucion) {
            return [
                'id' => $institucion->id,
                'nombre' => $institucion->nombre,
                'departamento' => $institucion->departamento_nombre ?? null,
                'ciudad' => $institucion->ciudad_nombre ?? null,
                'total_estudiantes' => $institucion->total_estudiantes ?? 0,
                'tests_completados' => $institucion->tests_completados ?? 0,
                'tests_incompletos' => $institucion->tests_incompletos ?? 0,
            ];
        })->toArray();
    }

    private function generarDatosInstitucionEspecifica(Request $request, $subQueryUsuarios, int $limit = null)
    {
        $query = UnidadEducativa::select('unidades_educativas.*')
            ->selectRaw('
                COUNT(DISTINCT users.id) as total_estudiantes,
                COUNT(DISTINCT CASE WHEN tests.completado = 1 THEN tests.id END) as tests_completados,
                COUNT(DISTINCT CASE WHEN tests.completado = 0 OR tests.id IS NULL THEN users.id END) as tests_incompletos,
                departamentos.nombre as departamento_nombre,
                ciudades.nombre as ciudad_nombre
            ')
            ->leftJoin('users', 'unidades_educativas.id', '=', 'users.unidad_educativa_id')
            ->leftJoin('tests', 'users.id', '=', 'tests.user_id')
            ->leftJoin('ciudades', 'unidades_educativas.ciudad_id', '=', 'ciudades.id')
            ->leftJoin('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
            ->whereIn('unidades_educativas.id', $subQueryUsuarios) // Solo instituciones con usuarios que cumplen filtros
            ->groupBy('unidades_educativas.id', 'unidades_educativas.nombre', 'unidades_educativas.ciudad_id', 'departamentos.nombre', 'ciudades.nombre');

        // Aplicar filtros de ubicación
        if ($request->filled('departamento')) {
            $query->where('departamentos.nombre', $request->departamento);
        }
        if ($request->filled('ciudad')) {
            $query->where('ciudades.nombre', $request->ciudad);
        }
        if ($request->filled('institucion')) {
            $query->where('unidades_educativas.nombre', $request->institucion);
        }

        // Aplicar filtros adicionales que no están en la subconsulta
        if ($request->filled('fecha_inicio')) {
            $query->where('users.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('users.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }
        if ($request->filled('tipos_personalidad')) {
            $query->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }
        if ($request->filled('estado_test')) {
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where(function($q) {
                    $q->where('tests.completado', 0)->orWhereNull('tests.id');
                });
            }
        }

        $query->having('total_estudiantes', '>', 0)->orderBy('total_estudiantes', 'desc');

        if ($limit && $limit > 0) {
            $instituciones = $query->paginate($limit);
            $datos = $instituciones->toArray();

            $datos['data'] = array_map(function($institucion) {
                return [
                    'id' => $institucion['id'],
                    'nombre' => $institucion['nombre'],
                    'departamento' => $institucion['departamento_nombre'] ?? null,
                    'ciudad' => $institucion['ciudad_nombre'] ?? null,
                    'total_estudiantes' => $institucion['total_estudiantes'] ?? 0,
                    'tests_completados' => $institucion['tests_completados'] ?? 0,
                    'tests_incompletos' => $institucion['tests_incompletos'] ?? 0,
                ];
            }, $datos['data']);

            return $datos;
        }

        $instituciones = $query->get();

        return $instituciones->map(function($institucion) {
            return [
                'id' => $institucion->id,
                'nombre' => $institucion->nombre,
                'departamento' => $institucion->departamento_nombre ?? null,
                'ciudad' => $institucion->ciudad_nombre ?? null,
                'total_estudiantes' => $institucion->total_estudiantes ?? 0,
                'tests_completados' => $institucion->tests_completados ?? 0,
                'tests_incompletos' => $institucion->tests_incompletos ?? 0,
            ];
        })->toArray();
    }

    private function generarDatosDepartamentos(Request $request)
    {
        $query = User::selectRaw('departamentos.nombre as departamento, COUNT(*) as total')
            ->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id')
            ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
            ->whereNotNull('users.departamento_id')
            ->whereNotNull('users.ciudad_id')
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->orderBy('total', 'desc');

        // Aplicar filtros demográficos
        if ($request->filled('genero')) {
            $generoValue = $request->genero;
            if (strtolower($generoValue) == 'm') {
                $generoValue = 'Masculino';
            } elseif (strtolower($generoValue) == 'f') {
                $generoValue = 'Femenino';
            } elseif (strtolower($generoValue) == 'o') {
                $generoValue = 'Otro';
            }
            $query->where('users.sexo', $generoValue);
        }
        if ($request->filled('edad_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
        }
        if ($request->filled('edad_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
        }

        // Aplicar filtros de fecha
        if ($request->filled('fecha_inicio')) {
            $query->where('users.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('users.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }

        // Aplicar filtros adicionales
        if ($request->filled('institucion')) {
            $query->join('unidades_educativas', 'users.unidad_educativa_id', '=', 'unidades_educativas.id')
                  ->where('unidades_educativas.nombre', $request->institucion);
        }
        if ($request->filled('area_conocimiento')) {
            $query->join('tests', 'users.id', '=', 'tests.user_id')
                  ->join('test_carrera_recomendacion', 'tests.id', '=', 'test_carrera_recomendacion.test_id')
                  ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                  ->where('carreras.area_conocimiento', $request->area_conocimiento);
        }
        if ($request->filled('tipos_personalidad')) {
            if (!$request->filled('area_conocimiento')) {
                $query->join('tests', 'users.id', '=', 'tests.user_id');
            }
            $query->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }
        if ($request->filled('estado_test')) {
            if (!$request->filled('area_conocimiento') && !$request->filled('tipos_personalidad')) {
                $query->join('tests', 'users.id', '=', 'tests.user_id');
            }
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where('tests.completado', 0);
            }
        }

        $resultados = $query->get();
        $total = $resultados->sum('total');

        foreach ($resultados as $resultado) {
            $resultado->porcentaje = $total > 0 ? round(($resultado->total / $total) * 100, 2) : 0;
        }

        return $resultados->toArray();
    }

    private function generarDatosCiudadesPorDepartamentoDemografico(Request $request)
    {
        $query = User::selectRaw('ciudades.nombre as ciudad, departamentos.nombre as departamento, COUNT(*) as total')
            ->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id')
            ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
            ->where('departamentos.nombre', $request->departamento)
            ->whereNotNull('users.ciudad_id')
            ->groupBy('ciudades.id', 'ciudades.nombre', 'departamentos.nombre')
            ->orderBy('total', 'desc');

        // Aplicar filtros demográficos
        if ($request->filled('genero')) {
            $generoValue = $request->genero;
            if (strtolower($generoValue) == 'm') {
                $generoValue = 'Masculino';
            } elseif (strtolower($generoValue) == 'f') {
                $generoValue = 'Femenino';
            } elseif (strtolower($generoValue) == 'o') {
                $generoValue = 'Otro';
            }
            $query->where('users.sexo', $generoValue);
        }
        if ($request->filled('edad_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
        }
        if ($request->filled('edad_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
        }

        // Aplicar filtros de fecha
        if ($request->filled('fecha_inicio')) {
            $query->where('users.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('users.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }

        // Aplicar filtros adicionales
        if ($request->filled('institucion')) {
            $query->join('unidades_educativas', 'users.unidad_educativa_id', '=', 'unidades_educativas.id')
                  ->where('unidades_educativas.nombre', $request->institucion);
        }
        if ($request->filled('area_conocimiento')) {
            $query->join('tests', 'users.id', '=', 'tests.user_id')
                  ->join('test_carrera_recomendacion', 'tests.id', '=', 'test_carrera_recomendacion.test_id')
                  ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                  ->where('carreras.area_conocimiento', $request->area_conocimiento);
        }
        if ($request->filled('tipos_personalidad')) {
            if (!$request->filled('area_conocimiento')) {
                $query->join('tests', 'users.id', '=', 'tests.user_id');
            }
            $query->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }
        if ($request->filled('estado_test')) {
            if (!$request->filled('area_conocimiento') && !$request->filled('tipos_personalidad')) {
                $query->join('tests', 'users.id', '=', 'tests.user_id');
            }
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where('tests.completado', 0);
            }
        }

        $resultados = $query->get();
        $total = $resultados->sum('total');

        foreach ($resultados as $resultado) {
            $resultado->porcentaje = $total > 0 ? round(($resultado->total / $total) * 100, 2) : 0;
        }

        return $resultados->toArray();
    }

    private function generarDatosCiudadEspecifica(Request $request)
    {
        // Primero obtener los nombres de ciudad y departamento
        $ciudadInfo = DB::table('ciudades')
            ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
            ->where('departamentos.nombre', $request->departamento)
            ->where('ciudades.nombre', $request->ciudad)
            ->select('ciudades.nombre as ciudad', 'departamentos.nombre as departamento')
            ->first();

        if (!$ciudadInfo) {
            return [];
        }

        // Ahora contar los usuarios en esa ciudad aplicando todos los filtros
        $query = User::where('departamentos.nombre', $request->departamento)
            ->where('ciudades.nombre', $request->ciudad)
            ->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id')
            ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
            ->whereNotNull('users.ciudad_id');

        // Aplicar filtros demográficos
        if ($request->filled('genero')) {
            $generoValue = $request->genero;
            if (strtolower($generoValue) == 'm') {
                $generoValue = 'Masculino';
            } elseif (strtolower($generoValue) == 'f') {
                $generoValue = 'Femenino';
            } elseif (strtolower($generoValue) == 'o') {
                $generoValue = 'Otro';
            }
            $query->where('users.sexo', $generoValue);
        }
        if ($request->filled('edad_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
        }
        if ($request->filled('edad_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
        }

        // Aplicar filtros de fecha
        if ($request->filled('fecha_inicio')) {
            $query->where('users.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('users.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }

        // Aplicar filtros adicionales
        if ($request->filled('institucion')) {
            $query->join('unidades_educativas', 'users.unidad_educativa_id', '=', 'unidades_educativas.id')
                  ->where('unidades_educativas.nombre', $request->institucion);
        }
        if ($request->filled('area_conocimiento')) {
            $query->join('tests', 'users.id', '=', 'tests.user_id')
                  ->join('test_carrera_recomendacion', 'tests.id', '=', 'test_carrera_recomendacion.test_id')
                  ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                  ->where('carreras.area_conocimiento', $request->area_conocimiento);
        }
        if ($request->filled('tipos_personalidad')) {
            if (!$request->filled('area_conocimiento')) {
                $query->join('tests', 'users.id', '=', 'tests.user_id');
            }
            $query->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }
        if ($request->filled('estado_test')) {
            if (!$request->filled('area_conocimiento') && !$request->filled('tipos_personalidad')) {
                $query->join('tests', 'users.id', '=', 'tests.user_id');
            }
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where('tests.completado', 0);
            }
        }

        $totalUsuarios = $query->count();

        if ($totalUsuarios > 0) {
            return [[
                'departamento' => $ciudadInfo->departamento,
                'ciudad' => $ciudadInfo->ciudad,
                'total' => $totalUsuarios,
                'porcentaje' => 100 // Siempre 100% ya que es solo una ciudad
            ]];
        }

        return [];
    }

    private function generarDatosDemograficos(Request $request)
    {
        // Determinar el nivel de agrupación basado en los filtros aplicados
        $departamentoSeleccionado = $request->filled('departamento');
        $ciudadSeleccionada = $request->filled('ciudad');

        // Lógica condicional basada en los filtros aplicados
        if (!$departamentoSeleccionado && !$ciudadSeleccionada) {
            // Nivel 1: No se seleccionó departamento ni ciudad - mostrar departamentos
            return $this->generarDatosDepartamentos($request);
        } elseif ($departamentoSeleccionado && !$ciudadSeleccionada) {
            // Nivel 2: Solo departamento seleccionado - mostrar ciudades del departamento
            return $this->generarDatosCiudadesPorDepartamentoDemografico($request);
        } else {
            // Nivel 3: Departamento y ciudad seleccionados - mostrar solo esa ciudad
            return $this->generarDatosCiudadEspecifica($request);
        }
    }
    
    private function generarDatosPersonalidades(Request $request)
    {
        return $this->getCachedData('personalidades_' . md5(serialize($request->all())), function() use ($request) {
            $query = DB::table('tests')
                ->select('tipo_primario', DB::raw('COUNT(*) as total'))
                ->whereNotNull('tipo_primario')
                ->groupBy('tipo_primario');

            // Aplicar filtros de ubicación
            if ($request->filled('departamento') || $request->filled('ciudad') || $request->filled('institucion')) {
                $query->join('users', 'tests.user_id', '=', 'users.id');

                if ($request->filled('departamento')) {
                    $query->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id')
                          ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
                          ->where('departamentos.nombre', $request->departamento);
                }
                if ($request->filled('ciudad')) {
                    if (!$request->filled('departamento')) {
                        $query->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id');
                    }
                    $query->where('ciudades.nombre', $request->ciudad);
                }
                if ($request->filled('institucion')) {
                    $query->join('unidades_educativas', 'users.unidad_educativa_id', '=', 'unidades_educativas.id')
                          ->where('unidades_educativas.nombre', $request->institucion);
                }
            }

            // Aplicar filtros demográficos
            if ($request->filled('genero') || $request->filled('edad_min') || $request->filled('edad_max')) {
                if (!$request->filled('departamento') && !$request->filled('ciudad') && !$request->filled('institucion')) {
                    $query->join('users', 'tests.user_id', '=', 'users.id');
                }

                if ($request->filled('genero')) {
                    $generoValue = $request->genero;
                    if (strtolower($generoValue) == 'm') {
                        $generoValue = 'Masculino';
                    } elseif (strtolower($generoValue) == 'f') {
                        $generoValue = 'Femenino';
                    } elseif (strtolower($generoValue) == 'o') {
                        $generoValue = 'Otro';
                    }
                    $query->where('users.sexo', $generoValue);
                }
                if ($request->filled('edad_min')) {
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
                }
                if ($request->filled('edad_max')) {
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
                }
            }

            // Aplicar filtros de fecha
            if ($request->filled('fecha_inicio')) {
                $query->where('tests.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
            }
            if ($request->filled('fecha_fin')) {
                $query->where('tests.created_at', '<=', $request->fecha_fin . ' 23:59:59');
            }

            // Aplicar filtro de área de conocimiento
            if ($request->filled('area_conocimiento')) {
                $query->join('test_carrera_recomendacion', 'tests.id', '=', 'test_carrera_recomendacion.test_id')
                      ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
                      ->where('carreras.area_conocimiento', $request->area_conocimiento);
            }

            // Aplicar filtro de estado del test
            if ($request->filled('estado_test')) {
                if ($request->estado_test === 'completado') {
                    $query->where('tests.completado', 1);
                } elseif ($request->estado_test === 'incompleto') {
                    $query->where('tests.completado', 0);
                }
            }

            $resultados = $query->get();
            $total = $resultados->sum('total');

            foreach ($resultados as $resultado) {
                $resultado->porcentaje = $total > 0 ? round(($resultado->total / $total) * 100, 2) : 0;
                $resultado->descripcion = $this->getDescripcionTipoPersonalidad($resultado->tipo_primario);
            }

            return $resultados->toArray();
        }, 30); // Cache por 30 minutos
    }
    
    private function generarDatosCarreras(Request $request)
    {
        $query = DB::table('test_carrera_recomendacion')
            ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
            ->join('tests', 'test_carrera_recomendacion.test_id', '=', 'tests.id')
            ->select('carreras.nombre', 'carreras.area_conocimiento', 
                    DB::raw('COUNT(*) as total'), 
                    DB::raw('AVG(test_carrera_recomendacion.match_porcentaje) as match_promedio'))
            ->groupBy('carreras.id', 'carreras.nombre', 'carreras.area_conocimiento');

        // Aplicar filtros de ubicación
        if ($request->filled('departamento') || $request->filled('ciudad') || $request->filled('institucion')) {
            $query->join('users', 'tests.user_id', '=', 'users.id');

            if ($request->filled('departamento')) {
                $query->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id')
                      ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
                      ->where('departamentos.nombre', $request->departamento);
            }
            if ($request->filled('ciudad')) {
                if (!$request->filled('departamento')) {
                    $query->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id');
                }
                $query->where('ciudades.nombre', $request->ciudad);
            }
            if ($request->filled('institucion')) {
                $query->join('unidades_educativas', 'users.unidad_educativa_id', '=', 'unidades_educativas.id')
                      ->where('unidades_educativas.nombre', $request->institucion);
            }
        }

        // Aplicar filtros demográficos
        if ($request->filled('genero') || $request->filled('edad_min') || $request->filled('edad_max')) {
            if (!$request->filled('departamento') && !$request->filled('ciudad') && !$request->filled('institucion')) {
                $query->join('users', 'tests.user_id', '=', 'users.id');
            }

            if ($request->filled('genero')) {
                $generoValue = $request->genero;
                if (strtolower($generoValue) == 'm') {
                    $generoValue = 'Masculino';
                } elseif (strtolower($generoValue) == 'f') {
                    $generoValue = 'Femenino';
                } elseif (strtolower($generoValue) == 'o') {
                    $generoValue = 'Otro';
                }
                $query->where('users.sexo', $generoValue);
            }
            if ($request->filled('edad_min')) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
            }
            if ($request->filled('edad_max')) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
            }
        }

        // Aplicar filtros de fecha
        if ($request->filled('fecha_inicio')) {
            $query->where('tests.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('tests.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }

        // Aplicar filtro de área de conocimiento
        if ($request->filled('area_conocimiento')) {
            $query->where('carreras.area_conocimiento', $request->area_conocimiento);
        }

        // Aplicar filtros de tipos de personalidad
        if ($request->filled('tipos_personalidad')) {
            $query->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }

        // Aplicar filtro de estado del test
        if ($request->filled('estado_test')) {
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where('tests.completado', 0);
            }
        }

        $resultados = $query->get();
        $total = $resultados->sum('total');
        
        foreach ($resultados as $resultado) {
            $resultado->porcentaje = $total > 0 ? round(($resultado->total / $total) * 100, 2) : 0;
        }
        
        return $resultados->toArray();
    }
    
    private function generarDatosCarrerasMasSolicitadas(Request $request)
    {
        $query = DB::table('retroalimentaciones')
            ->join('carreras', 'retroalimentaciones.carrera_id', '=', 'carreras.id')
            ->join('users', 'retroalimentaciones.user_id', '=', 'users.id')
            ->select('carreras.nombre', 'carreras.area_conocimiento', 
                    DB::raw('COUNT(*) as total_solicitudes'),
                    DB::raw('AVG(retroalimentaciones.utilidad) as utilidad_promedio'),
                    DB::raw('AVG(retroalimentaciones.precision) as precision_promedio'))
            ->whereNotNull('retroalimentaciones.carrera_id')
            ->groupBy('carreras.id', 'carreras.nombre', 'carreras.area_conocimiento')
            ->orderBy('total_solicitudes', 'desc');

        // Aplicar filtros de ubicación
        if ($request->filled('departamento')) {
            $query->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id')
                  ->join('departamentos', 'ciudades.departamento_id', '=', 'departamentos.id')
                  ->where('departamentos.nombre', $request->departamento);
        }
        if ($request->filled('ciudad')) {
            if (!$request->filled('departamento')) {
                $query->join('ciudades', 'users.ciudad_id', '=', 'ciudades.id');
            }
            $query->where('ciudades.nombre', $request->ciudad);
        }
        if ($request->filled('institucion')) {
            $query->join('unidades_educativas', 'users.unidad_educativa_id', '=', 'unidades_educativas.id')
                  ->where('unidades_educativas.nombre', $request->institucion);
        }

        // Aplicar filtros demográficos
        if ($request->filled('genero')) {
            $generoValue = $request->genero;
            if (strtolower($generoValue) == 'm') {
                $generoValue = 'Masculino';
            } elseif (strtolower($generoValue) == 'f') {
                $generoValue = 'Femenino';
            } elseif (strtolower($generoValue) == 'o') {
                $generoValue = 'Otro';
            }
            $query->where('users.sexo', $generoValue);
        }
        if ($request->filled('edad_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
        }
        if ($request->filled('edad_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, users.fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
        }

        // Aplicar filtros de fecha
        if ($request->filled('fecha_inicio')) {
            $query->where('retroalimentaciones.created_at', '>=', $request->fecha_inicio . ' 00:00:00');
        }
        if ($request->filled('fecha_fin')) {
            $query->where('retroalimentaciones.created_at', '<=', $request->fecha_fin . ' 23:59:59');
        }

        // Aplicar filtro de área de conocimiento
        if ($request->filled('area_conocimiento')) {
            $query->where('carreras.area_conocimiento', $request->area_conocimiento);
        }

        // Aplicar filtros de tipos de personalidad
        if ($request->filled('tipos_personalidad')) {
            $query->join('tests', 'users.id', '=', 'tests.user_id')
                  ->whereIn('tests.tipo_primario', $request->tipos_personalidad);
        }

        // Aplicar filtro de estado del test
        if ($request->filled('estado_test')) {
            $query->join('tests', 'users.id', '=', 'tests.user_id');
            if ($request->estado_test === 'completado') {
                $query->where('tests.completado', 1);
            } elseif ($request->estado_test === 'incompleto') {
                $query->where('tests.completado', 0);
            }
        }

        $resultados = $query->get();
        $total = $resultados->sum('total_solicitudes');
        
        foreach ($resultados as $resultado) {
            $resultado->porcentaje = $total > 0 ? round(($resultado->total_solicitudes / $total) * 100, 2) : 0;
            $resultado->utilidad_promedio = round($resultado->utilidad_promedio, 1);
            $resultado->precision_promedio = round($resultado->precision_promedio, 1);
        }
        
        return $resultados->toArray();
    }
    
    private function prepararDatosGrafico($datos, $tipo)
    {
        if (!is_array($datos) || empty($datos)) return null;

        switch ($tipo) {
            case 'distribucion_demografica':
                // Determinar el tipo de datos basado en las claves disponibles
                if (isset($datos[0]['departamento']) && !isset($datos[0]['ciudad'])) {
                    // Datos de departamentos
                    return [
                        'labels' => array_column($datos, 'departamento'),
                        'datos' => array_column($datos, 'total'),
                        'porcentajes' => array_column($datos, 'porcentaje'),
                        'titulo' => 'Distribución por Departamentos',
                        'colores' => ['#6366f1', '#10b981', '#f472b6', '#fbbf24', '#a78bfa', '#06b6d4']
                    ];
                } elseif (isset($datos[0]['ciudad']) && isset($datos[0]['departamento'])) {
                    // Datos de ciudades
                    return [
                        'labels' => array_column($datos, 'ciudad'),
                        'datos' => array_column($datos, 'total'),
                        'porcentajes' => array_column($datos, 'porcentaje'),
                        'titulo' => 'Distribución por Ciudad',
                        'colores' => ['#6366f1', '#10b981', '#f472b6', '#fbbf24', '#a78bfa', '#06b6d4']
                    ];
                }
                return null;
            case 'instituciones_educativas':
                // Determinar qué tipo de datos tenemos basado en las claves disponibles
                if (isset($datos[0]['ciudad']) && !isset($datos[0]['nombre'])) {
                    // Datos de ciudades
                    return [
                        'labels' => array_column($datos, 'ciudad'),
                        'datos' => array_column($datos, 'total_usuarios'),
                        'titulo' => 'Usuarios por Ciudad',
                        'colores' => ['#6366f1', '#10b981', '#f472b6', '#fbbf24', '#a78bfa', '#06b6d4']
                    ];
                } elseif (isset($datos[0]['nombre'])) {
                    // Datos de instituciones
                    return [
                        'labels' => array_column($datos, 'nombre'),
                        'datos' => array_column($datos, 'total_estudiantes'),
                        'titulo' => 'Estudiantes por Institución',
                        'colores' => ['#6366f1', '#10b981', '#f472b6', '#fbbf24', '#a78bfa', '#06b6d4']
                    ];
                }
                return null;
            case 'personalidades':
                return [
                    'labels' => array_column($datos, 'tipo_primario'),
                    'datos' => array_column($datos, 'total'),
                    'porcentajes' => array_column($datos, 'porcentaje'),
                    'titulo' => 'Distribución de Personalidades',
                    'colores' => ['#6366f1', '#10b981', '#f472b6', '#fbbf24', '#a78bfa', '#06b6d4']
                ];
            default:
                return null;
        }
    }
    
    private function generarInsights($datos, $tipo)
    {
        if (!is_array($datos) || empty($datos)) return [];

        $insights = [];

        switch ($tipo) {
            case 'usuarios_datos':
                $total = count($datos);
                $insights['total_usuarios'] = "Total de usuarios registrados: $total";
                break;
            case 'instituciones_educativas':
                // Determinar el tipo de datos basado en las claves disponibles
                if (isset($datos[0]['ciudad']) && !isset($datos[0]['nombre'])) {
                    // Insights para datos de ciudades
                    $totalCiudades = count($datos);
                    $totalUsuarios = array_sum(array_column($datos, 'total_usuarios'));
                    $ciudadMax = collect($datos)->sortByDesc('total_usuarios')->first();
                    $insights['total_ciudades'] = "Total de ciudades con usuarios: $totalCiudades";
                    $insights['total_usuarios'] = "Total de usuarios en estas ciudades: $totalUsuarios";
                    if ($ciudadMax) {
                        $insights['ciudad_mas_usuarios'] = "La ciudad con más usuarios es {$ciudadMax['ciudad']} con {$ciudadMax['total_usuarios']} usuarios";
                    }
                } elseif (isset($datos[0]['nombre'])) {
                    // Insights para datos de instituciones
                    $totalInstituciones = count($datos);
                    $totalEstudiantes = array_sum(array_column($datos, 'total_estudiantes'));
                    $institucionMax = collect($datos)->sortByDesc('total_estudiantes')->first();
                    $insights['total_instituciones'] = "Total de instituciones educativas: $totalInstituciones";
                    $insights['total_estudiantes'] = "Total de estudiantes: $totalEstudiantes";
                    if ($institucionMax) {
                        $insights['institucion_mas_estudiantes'] = "La institución con más estudiantes es {$institucionMax['nombre']} con {$institucionMax['total_estudiantes']} estudiantes";
                    }
                }
                break;
            case 'distribucion_demografica':
                // Determinar el tipo de datos basado en las claves disponibles
                if (isset($datos[0]['departamento']) && !isset($datos[0]['ciudad'])) {
                    // Insights para datos de departamentos
                    $totalDepartamentos = count($datos);
                    $totalUsuarios = array_sum(array_column($datos, 'total'));
                    $departamentoMax = collect($datos)->sortByDesc('total')->first();
                    $insights['total_departamentos'] = "Total de departamentos con usuarios: $totalDepartamentos";
                    $insights['total_usuarios'] = "Total de usuarios: $totalUsuarios";
                    if ($departamentoMax) {
                        $insights['departamento_mas_poblado'] = "El departamento con más usuarios es {$departamentoMax['departamento']} con {$departamentoMax['total']} usuarios ({$departamentoMax['porcentaje']}%)";
                    }
                } elseif (isset($datos[0]['ciudad']) && isset($datos[0]['departamento'])) {
                    // Insights para datos de ciudades
                    $totalCiudades = count($datos);
                    $totalUsuarios = array_sum(array_column($datos, 'total'));
                    $ciudadMax = collect($datos)->sortByDesc('total')->first();
                    $insights['total_ciudades'] = "Total de ciudades: $totalCiudades";
                    $insights['total_usuarios'] = "Total de usuarios en estas ciudades: $totalUsuarios";
                    if ($ciudadMax) {
                        $insights['ciudad_mas_poblada'] = "La ciudad con más usuarios es {$ciudadMax['ciudad']} con {$ciudadMax['total']} usuarios ({$ciudadMax['porcentaje']}%)";
                    }
                }
                break;
            case 'personalidades':
                $maxTipo = collect($datos)->sortByDesc('total')->first();
                if ($maxTipo) {
                    $insights['tipo_mas_comun'] = "El tipo de personalidad más común es {$maxTipo['tipo_primario']} con {$maxTipo['total']} usuarios ({$maxTipo['porcentaje']}%)";
                }
                break;
        }

        return $insights;
    }
    
    private function getDescripcionTipoPersonalidad($tipo)
    {
        $descripciones = [
            'R' => 'Realista - Prefiere trabajos prácticos y concretos',
            'I' => 'Investigador - Le gusta investigar y resolver problemas',
            'A' => 'Artístico - Creativo y expresivo',
            'S' => 'Social - Ayuda a otros y enseña',
            'E' => 'Emprendedor - Líder y persuasivo',
            'C' => 'Convencional - Organizado y detallista'
        ];
        
        return $descripciones[$tipo] ?? 'Tipo no especificado';
    }
    
    // API Methods para filtros dinámicos
    public function getCiudadesByDepartamento($departamentoNombre)
    {
        $ciudades = Ciudad::whereHas('departamento', function($query) use ($departamentoNombre) {
            $query->where('nombre', $departamentoNombre);
        })
        ->whereHas('unidadesEducativas.users') // Solo ciudades que tienen unidades educativas con usuarios
        ->orderBy('nombre')
        ->get(['id', 'nombre']);
        
        return response()->json($ciudades);
    }
    
    public function getUnidadesEducativasByCiudad($ciudadNombre)
    {
        $unidadesEducativas = UnidadEducativa::whereHas('ciudad', function($query) use ($ciudadNombre) {
            $query->where('nombre', $ciudadNombre);
        })
        ->whereHas('users') // Solo unidades educativas que tienen usuarios registrados
        ->orderBy('nombre')
        ->get(['id', 'nombre']);
        
        return response()->json($unidadesEducativas);
    }
    
    // Método helper para cache inteligente
    private function getCachedData(string $key, callable $callback, int $minutes = 30)
    {
        // Solo usar cache si no hay filtros específicos que cambien frecuentemente
        $cacheKey = 'informe_' . md5($key);
        
        return Cache::remember($cacheKey, $minutes * 60, function() use ($callback, $cacheKey) {
            Log::info("Generando datos para cache: $cacheKey");
            return $callback();
        });
    }
    
    // Método para medir tiempo de ejecución
    private function measureExecutionTime(callable $callback, string $operation = 'operacion')
    {
        $startTime = microtime(true);
        $result = $callback();
        $executionTime = microtime(true) - $startTime;
        
        Log::info("$operation completada en " . round($executionTime, 3) . " segundos");
        
        return $result;
    }
}