<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Informe;
use App\Models\User;
use App\Models\TestResult;
use App\Models\Carrera;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class InformeAvanzadoController extends Controller
{
    public function index(Request $request)
    {
        // Obtener informes recientes
        $informesRecientes = Informe::latest()->take(10)->get();
        
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
                $datos = json_decode($informeCargado->datos, true);
                $tipoInforme = $informeCargado->tipo;
                $filtrosAplicados = json_decode($informeCargado->filtros, true) ?? [];
            }
        }
        
        // Si se está generando un nuevo informe
        if ($request->has('tipo_informe') && !$informeCargado) {
            $datos = $this->generarDatosInforme($request);
            $filtrosAplicados = $request->all();
            $datosGrafico = $this->prepararDatosGrafico($datos, $tipoInforme);
            $insights = $this->generarInsights($datos, $tipoInforme);
        }
        
        // Obtener datos para filtros
        $departamentos = User::distinct()->pluck('departamento')->filter()->sort()->values();
        $instituciones = User::distinct()->pluck('unidad_educativa')->filter()->sort()->values();
        $areasConocimiento = Carrera::distinct()->pluck('area_conocimiento')->filter()->sort()->values();
        
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
        \Log::info('=== INICIO EXPORTACIÓN ===');
        \Log::info('Método HTTP: ' . $request->method());
        \Log::info('Todos los datos del request', ['request_data' => $request->all()]);
        
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
            
            \Log::info("Parámetros: formato=$formato, tipo=$tipo, id=$id");
            \Log::info("Datos raw recibidos", ['raw_data' => $datosRaw]);
            
            // Validar parámetros
            if (!in_array($formato, ['excel', 'pdf'])) {
                throw new \Exception("Formato de exportación no válido: $formato");
            }
            
            $datos = null;
            
            // Caso 1: Cargar desde informe guardado
            if ($id && !is_array($id)) {
                \Log::info("Cargando desde informe guardado ID: $id");
                $informe = \App\Models\Informe::find($id);
                if (!$informe) {
                    throw new \Exception("El informe solicitado no existe");
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
                \Log::info("Procesando datos enviados directamente");
                $datos = json_decode($datosRaw, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Error al decodificar los datos enviados: " . json_last_error_msg());
                }
            } 
            // Caso 3: No hay datos
            else {
                throw new \Exception("No se proporcionaron datos para exportar. Genere un informe primero.");
            }

            \Log::info("Datos procesados correctamente, tipo: " . gettype($datos) . ", cantidad: " . (is_array($datos) ? count($datos) : 'N/A'));

            if (empty($datos)) {
                throw new \Exception("Los datos del informe están vacíos");
            }
            
            // Limitar datos si son muy grandes para evitar problemas de memoria
            if (is_array($datos) && count($datos) > 10000) {
                throw new \Exception("El informe contiene demasiados registros. Limite los filtros para exportar menos de 10,000 registros.");
            }
            
            // Generar archivo según formato
            if ($formato === 'excel') {
                \Log::info("Generando Excel...");
                return $this->exportarExcel($datos, $tipo);
            } else if ($formato === 'pdf') {
                \Log::info("Generando PDF...");
                return $this->exportarPDF($datos, $tipo);
            } else {
                throw new \Exception("Formato de exportación no válido");
            }
            
        } catch (\Exception $e) {
            \Log::error("Error en exportación: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
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
            \Log::error("Error en exportarExcel: " . $e->getMessage());
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
            \Log::error("Error en exportarPDF: " . $e->getMessage());
            throw new \Exception("Error al generar el archivo PDF: " . $e->getMessage());
        }
    }
    
    private function getHeadersForTipo($tipo)
    {
        switch ($tipo) {
            case 'usuarios_datos':
                return ['Nombre', 'Email', 'Teléfono', 'Género', 'Departamento', 'Ciudad', 'Institución'];
            case 'instituciones_educativas':
                return ['Institución', 'Departamento', 'Ciudad', 'Total Estudiantes', 'Tests Completados', 'Tests Incompletos'];
            case 'distribucion_demografica':
                return ['Departamento', 'Ciudad', 'Total Usuarios', 'Porcentaje'];
            case 'tests_completados':
                return ['Estado', 'Cantidad', 'Porcentaje'];
            case 'personalidades':
                return ['Tipo', 'Descripción', 'Total', 'Porcentaje'];
            case 'carreras':
                return ['Carrera', 'Área', 'Recomendaciones', 'Porcentaje', 'Match Promedio'];
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
                return strtolower($sexo) == 'm' ? 'Masculino' : (strtolower($sexo) == 'f' ? 'Femenino' : 'No especificado');
            case 'Departamento':
                return $isArray ? ($item['departamento'] ?? '') : ($isObject ? ($item->departamento ?? '') : '');
            case 'Ciudad':
                return $isArray ? ($item['ciudad'] ?? '') : ($isObject ? ($item->ciudad ?? '') : '');
            case 'Institución':
                return $isArray ? ($item['unidad_educativa'] ?? '') : ($isObject ? ($item->unidad_educativa ?? '') : '');
            case 'Total Estudiantes':
                return $isObject ? ($item->total_estudiantes ?? 0) : ($isArray ? ($item['total_estudiantes'] ?? 0) : 0);
            case 'Tests Completados':
                return $isObject ? ($item->tests_completados ?? 0) : ($isArray ? ($item['tests_completados'] ?? 0) : 0);
            case 'Tests Incompletos':
                return $isObject ? ($item->tests_incompletos ?? 0) : ($isArray ? ($item['tests_incompletos'] ?? 0) : 0);
            case 'Total Usuarios':
                return $isObject ? ($item->total ?? 0) : ($isArray ? ($item['total'] ?? 0) : 0);
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
            'tests_completados' => 'Tests Completados vs Incompletos',
            'personalidades' => 'Distribución de Tipos de Personalidad',
            'carreras' => 'Carreras Recomendadas',
        ];
        
        return $titulos[$tipo] ?? 'Informe';
    }
    
    private function generarDatosInforme(Request $request)
    {
        $tipo = $request->tipo_informe;
        
        switch ($tipo) {
            case 'usuarios_datos':
                return $this->generarDatosUsuarios($request);
            case 'instituciones_educativas':
                return $this->generarDatosInstituciones($request);
            case 'distribucion_demografica':
                return $this->generarDatosDemograficos($request);
            case 'tests_completados':
                return $this->generarDatosTests($request);
            case 'personalidades':
                return $this->generarDatosPersonalidades($request);
            case 'carreras':
                return $this->generarDatosCarreras($request);
            default:
                return [];
        }
    }
    
    private function generarDatosUsuarios(Request $request)
    {
        $query = User::query();
        
        // Aplicar filtros
        if ($request->filled('departamento')) {
            $query->where('departamento', $request->departamento);
        }if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->ciudad);
        }
        if ($request->filled('genero')) {
            $query->where('sexo', $request->genero);
        }
        if ($request->filled('edad_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= ?', [$request->edad_min]);
        }
        if ($request->filled('edad_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) <= ?', [$request->edad_max]);
        }
        if ($request->filled('institucion')) {
            $query->where('unidad_educativa', $request->institucion);
        }
        if ($request->filled('area_conocimiento')) {
            $query->whereHas('test.carrerasRecomendadas', function($q) use ($request) {
                $q->where('area_conocimiento', $request->area_conocimiento);
            });
        }  
        
        return $query->get()->toArray();
    }
    
    private function generarDatosInstituciones(Request $request)
    {
        $query = DB::table('users')
            ->select('unidad_educativa', 'departamento', 'ciudad', DB::raw('COUNT(*) as total_estudiantes'))
            ->whereNotNull('unidad_educativa')
            ->groupBy('unidad_educativa', 'departamento', 'ciudad');
        
        // Aplicar filtros
        if ($request->filled('departamento')) {
            $query->where('departamento', $request->departamento);
        }
        if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->ciudad);
        }
        
        $instituciones = $query->get();
        
        // Agregar conteo de tests completados/incompletos
        foreach ($instituciones as $institucion) {
            $completados = DB::table('users')
                ->join('test_results', 'users.id', '=', 'test_results.user_id')
                ->where('users.unidad_educativa', $institucion->unidad_educativa)
                ->where('test_results.completado', 1)
                ->count();
                
            $incompletos = DB::table('users')
                ->leftJoin('test_results', 'users.id', '=', 'test_results.user_id')
                ->where('users.unidad_educativa', $institucion->unidad_educativa)
                ->where(function($q) {
                    $q->whereNull('test_results.id')
                      ->orWhere('test_results.completado', 0);
                })
                ->count();
                
            $institucion->tests_completados = $completados;
            $institucion->tests_incompletos = $incompletos;
        }
        
        return $instituciones->toArray();
    }
    
    private function generarDatosDemograficos(Request $request)
    {
        $query = DB::table('users')
            ->select('departamento', 'ciudad', DB::raw('COUNT(*) as total'))
            ->groupBy('departamento', 'ciudad');
        
        // Aplicar filtros
        if ($request->filled('departamento')) {
            $query->where('departamento', $request->departamento);
        }
        if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->ciudad);
        }
        
        $resultados = $query->get();
        $total = $resultados->sum('total');
        
        foreach ($resultados as $resultado) {
            $resultado->porcentaje = $total > 0 ? round(($resultado->total / $total) * 100, 2) : 0;
        }
        
        return $resultados->toArray();
    }
    
    private function generarDatosTests(Request $request)
    {
        $completados = DB::table('test_results')->where('completado', 1)->count();
        $incompletos = DB::table('test_results')->where('completado', 0)->count();
        $total = $completados + $incompletos;
        
        return [
            'completados' => $completados,
            'incompletos' => $incompletos,
            'porcentaje_completados' => $total > 0 ? round(($completados / $total) * 100, 2) : 0,
            'porcentaje_incompletos' => $total > 0 ? round(($incompletos / $total) * 100, 2) : 0,
        ];
    }
    
    private function generarDatosPersonalidades(Request $request)
    {
        $query = DB::table('test_results')
            ->select('tipo_primario', DB::raw('COUNT(*) as total'))
            ->whereNotNull('tipo_primario')
            ->groupBy('tipo_primario');
        
        $resultados = $query->get();
        $total = $resultados->sum('total');
        
        foreach ($resultados as $resultado) {
            $resultado->porcentaje = $total > 0 ? round(($resultado->total / $total) * 100, 2) : 0;
            $resultado->descripcion = $this->getDescripcionTipoPersonalidad($resultado->tipo_primario);
        }
        
        return $resultados->toArray();
    }
    
    private function generarDatosCarreras(Request $request)
    {
        $query = DB::table('test_carrera_recomendacion')
            ->join('carreras', 'test_carrera_recomendacion.carrera_id', '=', 'carreras.id')
            ->select('carreras.nombre', 'carreras.area_conocimiento', 
                    DB::raw('COUNT(*) as total'), 
                    DB::raw('AVG(test_carrera_recomendacion.match_percentage) as match_promedio'))
            ->groupBy('carreras.id', 'carreras.nombre', 'carreras.area_conocimiento');
        
        $resultados = $query->get();
        $total = $resultados->sum('total');
        
        foreach ($resultados as $resultado) {
            $resultado->porcentaje = $total > 0 ? round(($resultado->total / $total) * 100, 2) : 0;
        }
        
        return $resultados->toArray();
    }
    
    private function prepararDatosGrafico($datos, $tipo)
    {
        if (empty($datos)) return null;
        
        switch ($tipo) {
            case 'distribucion_demografica':
                return [
                    'labels' => array_column($datos, 'ciudad'),
                    'datos' => array_column($datos, 'total'),
                    'porcentajes' => array_column($datos, 'porcentaje'),
                    'titulo' => 'Distribución por Ciudad',
                    'colores' => ['#6366f1', '#10b981', '#f472b6', '#fbbf24', '#a78bfa', '#06b6d4']
                ];
            case 'personalidades':
                return [
                    'labels' => array_column($datos, 'tipo_primario'),
                    'datos' => array_column($datos, 'total'),
                    'porcentajes' => array_column($datos, 'porcentaje'),
                    'titulo' => 'Distribución de Personalidades',
                    'colores' => ['#6366f1', '#10b981', '#f472b6', '#fbbf24', '#a78bfa', '#06b6d4']
                ];
            case 'tests_completados':
                return [
                    'labels' => ['Completados', 'Incompletos'],
                    'datos' => [$datos['completados'], $datos['incompletos']],
                    'porcentajes' => [$datos['porcentaje_completados'], $datos['porcentaje_incompletos']],
                    'titulo' => 'Estado de Tests',
                    'colores' => ['#10b981', '#ef4444']
                ];
            default:
                return null;
        }
    }
    
    private function generarInsights($datos, $tipo)
    {
        if (empty($datos)) return [];
        
        $insights = [];
        
        switch ($tipo) {
            case 'usuarios_datos':
                $total = count($datos);
                $insights['total_usuarios'] = "Total de usuarios registrados: $total";
                break;
            case 'distribucion_demografica':
                $maxCiudad = collect($datos)->sortByDesc('total')->first();
                if ($maxCiudad) {
                    $insights['ciudad_mas_poblada'] = "La ciudad con más usuarios es {$maxCiudad['ciudad']} con {$maxCiudad['total']} usuarios ({$maxCiudad['porcentaje']}%)";
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
}