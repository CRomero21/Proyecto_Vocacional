<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Test;

class CoordinadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function dashboard()
    {
        // Verificar manualmente si el usuario es coordinador
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        try {
            $totalEstudiantes = User::where('role', 'estudiante')->count();
            $totalTests = Test::count();
            $ultimosTests = Test::with('user')->latest()->take(5)->get();
            
            return view('coordinador.dashboard', compact('totalEstudiantes', 'totalTests', 'ultimosTests'));
        } catch (\Exception $e) {
            // Si hay un error, cargar la vista sin datos
            return view('coordinador.dashboard');
        }
    }
    
    public function informes()
    {
        try {
            $estudiantes = User::where('role', 'estudiante')->get();
            return view('coordinador.informes', compact('estudiantes'));
        } catch (\Exception $e) {
            return view('coordinador.informes');
        }
    }
    
    public function detalleEstudiante($id)
    {
        try {
            $estudiante = User::findOrFail($id);
            return view('coordinador.detalle-estudiante', compact('estudiante'));
        } catch (\Exception $e) {
            return redirect()->route('coordinador.informes')
                ->with('error', 'Estudiante no encontrado');
        }
    }
   
    public function estadisticas()
    {
        // Verificar manualmente si el usuario es coordinador
        if (Auth::user()->role !== 'coordinador') {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        try {
            // Aquí puedes agregar la lógica para generar estadísticas
            $totalEstudiantes = User::where('role', 'estudiante')->count();
            $completadosTests = Test::count();
            $promedioResultados = Test::avg('puntuacion') ?? 0;
            
            return view('coordinador.estadisticas', compact(
                'totalEstudiantes', 
                'completadosTests', 
                'promedioResultados'
            ));
        } catch (\Exception $e) {
            return view('coordinador.estadisticas');
        }
    }
}