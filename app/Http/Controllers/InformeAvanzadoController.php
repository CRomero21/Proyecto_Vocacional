<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\User;
use App\Exports\InformeExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class InformeAvanzadoController extends Controller
{
    public function index()
    {
        // Cargar informes guardados
        $informesGuardados = []; // Implementar consulta a DB
        
        // Corregido para usar la ruta correcta a la vista
        return view('admin.informes-avanzados.index', compact('informesGuardados'));
    }
    
    public function generar(Request $request)
    {
        // Validar request
        $validated = $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'departamento' => 'nullable|string',
            'ciudad' => 'nullable|string',
            'tipo_personalidad' => 'nullable|string',
            'genero' => 'nullable|string',
            'edad_min' => 'nullable|numeric|min:0',
            'edad_max' => 'nullable|numeric|min:0',
            'tipo_informe' => 'required|string'
        ]);
        
        // Construir consulta según filtros
        $query = User::query()->where('role', 'estudiante');
        
        if ($request->filled('departamento')) {
            $query->where('departamento', $request->departamento);
        }
        
        if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->ciudad);
        }
        
        if ($request->filled('genero')) {
            $query->where('genero', $request->genero);
        }
        
        if ($request->filled('edad_min')) {
            $query->where('edad', '>=', $request->edad_min);
        }
        
        if ($request->filled('edad_max')) {
            $query->where('edad', '<=', $request->edad_max);
        }
        
        // Filtro de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereHas('tests', function($q) use ($request) {
                $q->where('created_at', '>=', $request->fecha_inicio);
            });
        }
        
        if ($request->filled('fecha_fin')) {
            $query->whereHas('tests', function($q) use ($request) {
                $q->where('created_at', '<=', $request->fecha_fin);
            });
        }
        
        // Filtro por tipo de personalidad
        if ($request->filled('tipo_personalidad')) {
            $query->whereHas('tests', function($q) use ($request) {
                $q->where('tipo_primario', $request->tipo_personalidad);
            });
        }
        
        // Obtener datos según tipo de informe
        $datos = [];
        
        switch ($request->tipo_informe) {
            case 'demografico':
                $datos = $this->generarInformeDemografico($query);
                break;
            case 'carreras':
                $datos = $this->generarInformeCarreras($query);
                break;
            case 'personalidad':
                $datos = $this->generarInformePersonalidad($query);
                break;
            case 'conversion':
                $datos = $this->generarInformeConversion($query);
                break;
            case 'instituciones':
                $datos = $this->generarInformeInstituciones($query);
                break;
            case 'tendencias':
                $datos = $this->generarInformeTendencias($query);
                break;
            default:
                $datos = $this->generarInformeDemografico($query);
        }
        
        // Guardar configuración temporal del informe en sesión
        session(['informe_actual' => [
            'filtros' => $validated,
            'datos' => $datos
        ]]);
        
        // Cargar informes guardados para mostrar en la vista
        $informesGuardados = []; // Implementar consulta a DB
        
        // Corregido para usar la ruta correcta a la vista
        return view('admin.informes-avanzados.index', [
            'datos' => $datos,
            'filtros' => $validated,
            'informesGuardados' => $informesGuardados
        ]);
    }
    
    public function exportar(Request $request, $formato)
    {
        // Recuperar datos de la sesión
        $informe = session('informe_actual');
        
        if (!$informe) {
            return back()->with('error', 'No hay datos para exportar. Genera un informe primero.');
        }
        
        $nombreArchivo = 'informe-' . date('Y-m-d') . '.' . $formato;
        
        if ($formato === 'excel') {
            return Excel::download(new InformeExport($informe['datos']), $nombreArchivo);
        } elseif ($formato === 'pdf') {
            // También actualizado para usar la ruta correcta
            $pdf = PDF::loadView('admin.informes-avanzados.pdf', [
                'datos' => $informe['datos'],
                'filtros' => $informe['filtros']
            ]);
            
            return $pdf->download($nombreArchivo);
        }
        
        return back()->with('info', 'Exportación a ' . $formato . ' en desarrollo');

    }
    
    public function guardar(Request $request)
    {
        // Validar el request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        
        // Recuperar datos de la sesión
        $informe = session('informe_actual');
        
        if (!$informe) {
            return back()->with('error', 'No hay datos para guardar. Genera un informe primero.');
        }
        
        // Aquí implementarías la lógica para guardar el informe en la base de datos
        // Por ejemplo:
        /*
        InformeGuardado::create([
            'nombre' => $validated['nombre'],
            'filtros' => json_encode($informe['filtros']),
            'tipo_informe' => $informe['filtros']['tipo_informe'],
            'user_id' => auth()->id()
        ]);
        */
        
        return back()->with('success', 'Informe guardado correctamente');
    }
    
    // Métodos privados para generación de informes específicos
    private function generarInformeDemografico($query)
    {
        // Obtener usuarios según la consulta
        $usuarios = $query->get();
        
        // Datos por departamento y ciudad
        $porDepartamento = $usuarios->groupBy('departamento')
            ->map(function($grupo) {
                return [
                    'total' => $grupo->count(),
                    'porcentaje' => round(($grupo->count() / max(1, $usuarios->count())) * 100, 1)
                ];
            });
            
        $porCiudad = $usuarios->groupBy('ciudad')
            ->map(function($grupo) {
                return [
                    'total' => $grupo->count(),
                    'porcentaje' => round(($grupo->count() / max(1, $usuarios->count())) * 100, 1)
                ];
            });
            
        // Distribución por género
        $porGenero = $usuarios->groupBy('genero')
            ->map(function($grupo) use ($usuarios) {
                return [
                    'total' => $grupo->count(),
                    'porcentaje' => round(($grupo->count() / max(1, $usuarios->count())) * 100, 1)
                ];
            });
            
        // Distribucion por rango de edad
        $porEdad = [
            '14-16' => $usuarios->whereBetween('edad', [14, 16])->count(),
            '17-19' => $usuarios->whereBetween('edad', [17, 19])->count(),
            '20-22' => $usuarios->whereBetween('edad', [20, 22])->count(),
            '23-25' => $usuarios->whereBetween('edad', [23, 25])->count(),
            '26+' => $usuarios->where('edad', '>=', 26)->count(),
        ];
        
        // Devolver datos para el informe
        return [
            'total_estudiantes' => $usuarios->count(),
            'por_departamento' => $porDepartamento,
            'por_ciudad' => $porCiudad,
            'por_genero' => $porGenero,
            'por_edad' => $porEdad,
            'tabla' => $this->generarTablaDemografica($usuarios)
        ];
    }
    
    private function generarTablaDemografica($usuarios)
    {
        // Genera los datos para la tabla en el informe
        $tabla = [];
        
        foreach ($usuarios->groupBy('ciudad') as $ciudad => $grupo) {
            $tests = Test::whereIn('user_id', $grupo->pluck('id'))->get();
            $testsCompletados = $tests->where('completado', true)->count();
            $testsIniciados = $tests->count();
            
            $tabla[] = [
                'ciudad' => $ciudad,
                'total_estudiantes' => $grupo->count(),
                'tests_completados' => $testsCompletados,
                'tests_incompletos' => $testsIniciados - $testsCompletados,
                'tasa_conversion' => $testsIniciados > 0 ? 
                    round(($testsCompletados / $testsIniciados) * 100, 1) : 0,
                'tipo_primario_dominante' => $this->obtenerTipoDominante($tests->where('completado', true))
            ];
        }
        
        return $tabla;
    }
    
    private function obtenerTipoDominante($tests)
    {
        if ($tests->isEmpty()) {
            return 'N/A';
        }
        
        $tipos = ['R', 'I', 'A', 'S', 'E', 'C'];
        $conteo = [];
        
        foreach ($tipos as $tipo) {
            $conteo[$tipo] = $tests->where('tipo_primario', $tipo)->count();
        }
        
        arsort($conteo);
        
        $tipoDominante = key($conteo);
        
        $nombres = [
            'R' => 'Realista',
            'I' => 'Investigador',
            'A' => 'Artístico',
            'S' => 'Social',
            'E' => 'Emprendedor',
            'C' => 'Convencional'
        ];
        
        return $nombres[$tipoDominante] . ' (' . $tipoDominante . ')';
    }
    
    private function generarInformeCarreras($query)
    {
        // Obtener usuarios según la consulta
        $usuarios = $query->get();
        $userIds = $usuarios->pluck('id');
        
        // Obtener tests completados de esos usuarios
        $tests = Test::whereIn('user_id', $userIds)
            ->where('completado', true)
            ->get();
            
        // Obtener carreras recomendadas
        $carrerasRecomendadas = [];
        
        // Aquí implementarías la lógica para obtener las carreras recomendadas
        // Este es un ejemplo simplificado
        /*
        foreach ($tests as $test) {
            $recomendaciones = $test->recomendaciones;
            
            foreach ($recomendaciones as $recomendacion) {
                $carreraId = $recomendacion->carrera_id;
                
                if (!isset($carrerasRecomendadas[$carreraId])) {
                    $carrerasRecomendadas[$carreraId] = [
                        'nombre' => $recomendacion->carrera->nombre,
                        'total' => 0,
                        'match_promedio' => 0,
                        'total_match' => 0
                    ];
                }
                
                $carrerasRecomendadas[$carreraId]['total']++;
                $carrerasRecomendadas[$carreraId]['total_match'] += $recomendacion->porcentaje_match;
            }
        }
        
        // Calcular match promedio
        foreach ($carrerasRecomendadas as $id => $datos) {
            $carrerasRecomendadas[$id]['match_promedio'] = 
                $datos['total'] > 0 ? ($datos['total_match'] / $datos['total']) : 0;
        }
        */
        
        // Devolver datos para el informe
        return [
            'total_tests' => $tests->count(),
            'carreras_recomendadas' => $carrerasRecomendadas,
            // Otros datos específicos para el informe de carreras
        ];
    }
    
    private function generarInformePersonalidad($query)
    {
        // Implementación para informe de personalidad
        return [
            // Datos para el informe
        ];
    }
    
    private function generarInformeConversion($query)
    {
        // Implementación para informe de tasas de conversión
        return [
            // Datos para el informe
        ];
    }
    
    private function generarInformeInstituciones($query)
    {
        // Implementación para informe de instituciones
        return [
            // Datos para el informe
        ];
    }
    
    private function generarInformeTendencias($query)
    {
        // Implementación para informe de tendencias temporales
        return [
            // Datos para el informe
        ];
    }
}