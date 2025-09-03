<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EstadisticasController extends Controller
{
    /**
     * Método principal para ver estadísticas con layout completo
     */
    public function index()
    {
        // Verificación de roles
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        
        // Obtener los datos para las estadísticas
        $data = $this->getEstadisticasData();
        
        // Retornar la vista con layout completo
        return view('admin.estadisticas.index', $data);
    }
    
    /**
     * Método para ver estadísticas en iframe sin layout
     */
    public function iframe()
    {
        // Verificación de roles
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        
        // Obtener los datos para las estadísticas
        $data = $this->getEstadisticasData();
        
        // Retornar la vista standalone sin layout
        return view('admin.estadisticas.iframe', $data);
    }
    
    /**
     * Método para calcular y obtener todos los datos de estadísticas
     */
    private function getEstadisticasData()
    {
        // Datos básicos para las estadísticas
        $totalTests = Test::where('completado', true)->count();
        $totalUsuarios = User::where('role', 'user')->count();
        $testsUltimaSemana = Test::where('completado', true)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        
        $testsIniciados = Test::count();
        $testsCompletados = $totalTests;
        $tasaConversion = $testsIniciados > 0 
            ? round(($testsCompletados / $testsIniciados) * 100, 1) 
            : 0;
        
        // Preparar datos para los gráficos (implementar cuando sea necesario)
        $porTipoPersonalidad = [];
        $carrerasMasRecomendadas = collect();
        $porAreaConocimiento = collect();
        $tendenciaPorMes = [];
        
        // Retornar array con todos los datos
        return [
            'totalTests' => $totalTests,
            'totalUsuarios' => $totalUsuarios,
            'testsUltimaSemana' => $testsUltimaSemana,
            'testsIniciados' => $testsIniciados,
            'testsCompletados' => $testsCompletados,
            'tasaConversion' => $tasaConversion,
            'porTipoPersonalidad' => $porTipoPersonalidad,
            'carrerasMasRecomendadas' => $carrerasMasRecomendadas,
            'porAreaConocimiento' => $porAreaConocimiento,
            'tendenciaPorMes' => $tendenciaPorMes
        ];
    }
}