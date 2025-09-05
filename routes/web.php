<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\CarreraUniversidadController;
use App\Http\Controllers\TipoPersonalidadController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\InformeAvanzadoController;
// Importar el nuevo controlador para tipos RIASEC
use App\Http\Controllers\Admin\CarreraTipoController;
use App\Models\CarreraTipo;

// Página de bienvenida
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Ruta raíz - redirección según rol
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        
        if ($user->role === 'superadmin') {
            return redirect('/informes');
        } elseif ($user->role === 'coordinador') {
            return redirect('/coordinador-dashboard');
        } else {
            return redirect('/dashboard');
        }
    }
    
    return redirect('/login');
})->name('home');

// Dashboard para estudiantes
Route::get('/dashboard', [CuestionarioController::class, 'mostrar'])
    ->middleware(['auth'])
    ->name('dashboard');

// DASHBOARD PARA COORDINADOR
Route::get('/coordinador-dashboard', [CoordinadorController::class, 'dashboard'])
    ->middleware(['auth'])
    ->name('coordinador.dashboard');

// OTRAS RUTAS DE COORDINADOR
Route::middleware(['auth'])->group(function () {
    Route::get('/coordinador-informes', [CoordinadorController::class, 'informes'])
        ->name('coordinador.informes');
    
    Route::get('/coordinador-estudiante/{id}', [CoordinadorController::class, 'detalleEstudiante'])
        ->name('coordinador.estudiante');
    
    Route::get('/coordinador-estadisticas', [CoordinadorController::class, 'estadisticas'])
        ->name('coordinador.estadisticas');
});

// Ruta para retroalimentación
Route::post('/test/{test}/retroalimentacion', [TestController::class, 'guardarRetroalimentacion'])
    ->middleware(['auth'])
    ->name('test.retroalimentacion');

// RESTO DE RUTAS PROTEGIDAS
Route::middleware(['auth'])->group(function () {
    // Gestión de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // IMPORTANTE: Ruta para informes (panel superadmin)
    Route::get('/informes', [InformeController::class, 'index'])->name('informes.index');
    
    // NUEVO: Ruta para admin.informes.index que estaba faltando
    Route::get('/admin/informes', [InformeController::class, 'index'])->name('admin.informes.index');
    
    // Cuestionario
    Route::post('/dashboard', [CuestionarioController::class, 'guardar'])->name('cuestionario.guardar');
    
    // SISTEMA DE TEST VOCACIONAL
    Route::get('/test/iniciar', [TestController::class, 'iniciar'])->name('test.iniciar');
    Route::get('/test/{test}/continuar', [TestController::class, 'continuar'])->name('test.continuar');
    Route::post('/test/guardar', [TestController::class, 'guardar'])->name('test.guardar');
    Route::get('/test/{test}/resultados', [TestController::class, 'resultados'])->name('test.resultados');
    Route::get('/test/historial', [TestController::class, 'historial'])->name('test.historial');
    Route::delete('/test/{test}/eliminar', [TestController::class, 'eliminar'])->name('test.eliminar');
    Route::get('/test/mostrar', [TestController::class, 'mostrar'])->name('test.mostrar');
    
    // INFORMES AVANZADOS - Esta es la ruta correcta fuera del prefijo 's'
    Route::get('/admin/informes-avanzados', [InformeAvanzadoController::class, 'index'])->name('admin.informes-avanzados.index');
    Route::get('/admin/informes-avanzados/generar', [InformeAvanzadoController::class, 'generar'])->name('admin.informes-avanzados.generar');
    Route::get('/admin/informes-avanzados/exportar/{formato}', [InformeAvanzadoController::class, 'exportar'])->name('admin.informes-avanzados.exportar');
    Route::post('/admin/informes-avanzados/guardar', [InformeAvanzadoController::class, 'guardar'])->name('admin.informes-avanzados.guardar');
    
    // PUEDES AGREGAR AQUÍ MÁS RUTAS PROTEGIDAS POR AUTENTICACIÓN GENERAL
});

// Ruta para estadísticas (accesible para admin y coordinador)
Route::get('/admin/estadisticas', [EstadisticasController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.estadisticas.index');

// SISTEMA DE ADMINISTRACIÓN - CON RESTRICCIÓN DE ROL SUPERADMIN
Route::prefix('s')->name('admin.')->middleware(['auth'])->group(function () {
    // Ruta principal de estadísticas
    Route::get('estadisticas', [EstadisticasController::class, 'index'])
        ->name('estadisticas.index');
        
    // Ruta para estadísticas en iframe (sin barras de navegación)
    Route::get('estadisticas-iframe', [EstadisticasController::class, 'iframe'])
        ->name('estadisticas.iframe');
        
    // Administración general
    Route::resource('preguntas', PreguntaController::class);  
    Route::resource('usuarios', UsuarioController::class);
    
    // Rutas para gestión de carreras
    Route::resource('carreras', CarreraController::class);
    
    // NUEVAS RUTAS: Gestión de tipos RIASEC para carreras
    Route::get('carreras/{carrera}/tipos', [CarreraTipo::class, 'edit'])
        ->name('carreras.tipos.edit');
    Route::post('carreras/{carrera}/tipos', [CarreraTipo::class, 'store'])
        ->name('carreras.tipos.store');
    Route::delete('carreras/{carrera}/tipos/{tipo}', [CarreraTipo::class, 'destroy'])
        ->name('carreras.tipos.destroy');
    
    // Rutas para gestión de universidades
    Route::resource('universidades', UniversidadController::class)->parameters([
        'universidades' => 'universidad'
    ]);
    
    // Rutas para gestión de tipos de personalidad RIASEC
    Route::resource('tipos-personalidad', TipoPersonalidadController::class);
    
    // Rutas para gestión de asociaciones carrera-universidad
    Route::get('carrera-universidad', [CarreraUniversidadController::class, 'index'])
        ->name('carrera-universidad.index');
        
    Route::get('carrera-universidad/create', [CarreraUniversidadController::class, 'create'])
        ->name('carrera-universidad.create');
        
    Route::post('carrera-universidad', [CarreraUniversidadController::class, 'store'])
        ->name('carrera-universidad.store');
    
    Route::get('carrera-universidad/{carrera}/{universidad}/edit', [CarreraUniversidadController::class, 'edit'])
        ->name('carrera-universidad.edit');
        
    Route::put('carrera-universidad/{carrera}/{universidad}', [CarreraUniversidadController::class, 'update'])
        ->name('carrera-universidad.update');
        
    Route::delete('carrera-universidad/{carrera}/{universidad}', [CarreraUniversidadController::class, 'destroy'])
        ->name('carrera-universidad.destroy');
    
    // PUEDES AGREGAR AQUÍ MÁS RUTAS DE ADMINISTRACIÓN CON PREFIJO 's'
});

// PUEDES AGREGAR AQUÍ RUTAS PÚBLICAS (SIN AUTENTICACIÓN)

require __DIR__.'/auth.php';