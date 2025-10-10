<?php
// filepath: c:\Users\USUARIO\Desktop\laravel\Proyecto_Vocacional\app\Http\Controllers\CoordinadorController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CoordinadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Muestra el dashboard principal del coordinador
     */
    public function dashboard()
    {
        // Verificar manualmente si el usuario es coordinador
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        try {
            // Datos básicos para el dashboard
            $totalEstudiantes = User::where('role', 'estudiante')->count();
            
            // Tests completados
            $totalTests = Test::where('completado', true)->count();
            
            // Tests iniciados (todos)
            $testsIniciados = Test::count();
            
            // Calcular tasa de conversión
            $tasaConversion = $testsIniciados > 0 
                ? round(($totalTests / $testsIniciados) * 100) . '%'
                : '0%';
            
            // Últimos tests para mostrar actividad reciente
            $ultimosTests = Test::with('user')
                ->latest()
                ->take(5)
                ->get();
            
            // Tendencias recientes - datos para gráfico de línea
            $fechaHaceUnMes = Carbon::now()->subDays(30);
            $tendenciasTests = DB::table('tests')
                ->select(DB::raw('DATE(created_at) as fecha, COUNT(*) as total'))
                ->where('created_at', '>=', $fechaHaceUnMes)
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get()
                ->map(function($item) {
                    return [
                        Carbon::parse($item->fecha)->format('d/m'),
                        (int)$item->total
                    ];
                })
                ->values()
                ->toArray();
            
            // Actividad de la última semana
            $fechaUltimaSemana = Carbon::now()->subDays(7);
            $testsUltimaSemana = Test::where('created_at', '>=', $fechaUltimaSemana)->count();
            
            // Estudiantes por departamento (para posibles gráficos)
            $estudiantesPorDepartamento = User::where('role', 'estudiante')
                ->select('departamento', DB::raw('count(*) as total'))
                ->whereNotNull('departamento')
                ->groupBy('departamento')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get();
            
            return view('coordinador.dashboard', compact(
                'totalEstudiantes',
                'totalTests',
                'testsIniciados',
                'tasaConversion',
                'ultimosTests',
                'testsUltimaSemana',
                'estudiantesPorDepartamento',
                'tendenciasTests'
            ));
            
        } catch (\Exception $e) {
            // Si hay un error, registrar el error y cargar la vista con datos mínimos
            \Log::error('Error en dashboard del coordinador: ' . $e->getMessage());
            
            // Intentar obtener al menos algunos datos básicos si es posible
            $totalEstudiantes = 0;
            $totalTests = 0;
            $testsIniciados = 0;
            $tasaConversion = '0%';
            $ultimosTests = collect();
            $testsUltimaSemana = 0;
            $estudiantesPorDepartamento = collect();
            $tendenciasTests = [];
            
            // Intentar obtener datos básicos uno por uno
            try {
                $totalEstudiantes = User::where('role', 'estudiante')->count();
            } catch (\Exception $e2) {
                \Log::error('Error obteniendo total estudiantes: ' . $e2->getMessage());
            }
            
            try {
                $totalTests = Test::where('completado', true)->count();
            } catch (\Exception $e2) {
                \Log::error('Error obteniendo total tests: ' . $e2->getMessage());
            }
            
            try {
                $testsIniciados = Test::count();
            } catch (\Exception $e2) {
                \Log::error('Error obteniendo tests iniciados: ' . $e2->getMessage());
            }
            
            try {
                $ultimosTests = Test::with('user')->latest()->take(5)->get();
            } catch (\Exception $e2) {
                \Log::error('Error obteniendo últimos tests: ' . $e2->getMessage());
                $ultimosTests = collect();
            }
            
            return view('coordinador.dashboard', compact(
                'totalEstudiantes',
                'totalTests',
                'testsIniciados',
                'tasaConversion',
                'ultimosTests',
                'testsUltimaSemana',
                'estudiantesPorDepartamento',
                'tendenciasTests'
            ));
        }
    }
    
    /**
     * Muestra la lista de estudiantes para informes
     */
    public function informes()
    {
        // Verificar permisos
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        try {
            $estudiantes = User::where('role', 'estudiante')
                ->withCount(['tests' => function($query) {
                    $query->where('completado', true);
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                
            return view('coordinador.informes', compact('estudiantes'));
        } catch (\Exception $e) {
            \Log::error('Error en informes: ' . $e->getMessage());
            return view('coordinador.informes', [
                'estudiantes' => collect(),
                'error' => 'Ocurrió un error al cargar los datos de estudiantes.'
            ]);
        }
    }
    
    /**
     * Muestra el detalle de un estudiante específico
     */
    public function detalleEstudiante($id)
    {
        // Verificar permisos
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        try {
            $estudiante = User::findOrFail($id);
            
            // Verificar que sea un estudiante
            if ($estudiante->role !== 'estudiante') {
                return redirect()->route('coordinador.informes')
                    ->with('error', 'El usuario seleccionado no es un estudiante');
            }
            
            // Obtener los tests del estudiante
            $tests = Test::where('user_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('coordinador.detalle-estudiante', compact('estudiante', 'tests'));
        } catch (\Exception $e) {
            \Log::error('Error en detalle estudiante: ' . $e->getMessage());
            return redirect()->route('coordinador.informes')
                ->with('error', 'Estudiante no encontrado o error al cargar sus datos');
        }
    }
   
    /**
     * Muestra estadísticas generales
     */
    public function estadisticas()
    {
        // Verificar permisos
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        try {
            // Estadísticas básicas
            $totalEstudiantes = User::where('role', 'estudiante')->count();
            $completadosTests = Test::where('completado', true)->count();
            $promedioResultados = Test::avg('puntuacion') ?? 0;
            
            // Estudiantes por departamento
            $estudiantesPorDepartamento = User::where('role', 'estudiante')
                ->select('departamento', DB::raw('count(*) as total'))
                ->whereNotNull('departamento')
                ->groupBy('departamento')
                ->orderBy('total', 'desc')
                ->get();
                
            // Tests por mes (para gráfico de tendencia)
            $testsPorMes = DB::table('tests')
                ->select(DB::raw('DATE_FORMAT(created_at, "%b %Y") as mes, COUNT(*) as total'))
                ->where('completado', true)
                ->groupBy('mes')
                ->orderBy(DB::raw('MIN(created_at)'))
                ->get();
                
            // Tipos de personalidad más comunes (si existe ese campo)
            $tiposPersonalidad = DB::table('tests')
                ->select('tipo_primario', DB::raw('COUNT(*) as total'))
                ->where('completado', true)
                ->whereNotNull('tipo_primario')
                ->groupBy('tipo_primario')
                ->orderBy('total', 'desc')
                ->get();
            
            return view('coordinador.estadisticas', compact(
                'totalEstudiantes', 
                'completadosTests', 
                'promedioResultados',
                'estudiantesPorDepartamento',
                'testsPorMes',
                'tiposPersonalidad'
            ));
        } catch (\Exception $e) {
            \Log::error('Error en estadísticas: ' . $e->getMessage());
            return view('coordinador.estadisticas', [
                'error' => 'No se pudieron cargar las estadísticas. Por favor, inténtelo de nuevo.'
            ]);
        }
    }
    
    /**
     * Redirecciona a la vista de estadísticas avanzadas del admin
     */
    public function estadisticasAvanzadas()
    {
        // Verificar permisos
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        // Simplemente redirigir a la ruta de estadísticas del admin
        return redirect()->route('admin.estadisticas.index');
    }
    
    /**
     * Redirecciona a la vista de informes avanzados del admin
     */
    public function informesAvanzados()
    {
        // Verificar permisos
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        // Simplemente redirigir a la ruta de informes avanzados del admin
        return redirect()->route('admin.informes-avanzados.index');
    }
}