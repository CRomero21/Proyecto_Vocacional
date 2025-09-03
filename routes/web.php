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
Route::get('/coordinador-informes', [CoordinadorController::class, 'informes'])
    ->middleware(['auth'])
    ->name('coordinador.informes');

Route::get('/coordinador-estudiante/{id}', [CoordinadorController::class, 'detalleEstudiante'])
    ->middleware(['auth'])
    ->name('coordinador.estudiante');

Route::get('/coordinador-estadisticas', [CoordinadorController::class, 'estadisticas'])
    ->middleware(['auth'])
    ->name('coordinador.estadisticas');

Route::post('/test/{test}/retroalimentacion', [TestController::class, 'guardarRetroalimentacion'])
    ->name('test.retroalimentacion');

// RESTO DE RUTAS PROTEGIDAS
Route::middleware(['auth'])->group(function () {
    // Gestión de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // IMPORTANTE: Ruta para informes (panel superadmin)
    Route::get('/informes', [InformeController::class, 'index'])->name('informes.index');

    // Ruta para estadísticas en iframe (sin barras de navegación)
    Route::get('/admin/estadisticas-iframe', [EstadisticasController::class, 'iframe'])
        ->name('admin.estadisticas.iframe');
    
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
});

// SISTEMA DE ADMINISTRACIÓN - CON RESTRICCIÓN DE ROL SUPERADMIN
Route::prefix('s')->name('admin.')->middleware(['auth'])->group(function () {
    // Administración general
    Route::resource('preguntas', PreguntaController::class);  
    Route::resource('usuarios', UsuarioController::class);
    
    // Rutas para gestión de carreras
    Route::resource('carreras', CarreraController::class);
    
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
    
    // Añadidas rutas de edición y actualización dentro del grupo correcto
    Route::get('carrera-universidad/{carrera}/{universidad}/edit', [CarreraUniversidadController::class, 'edit'])
        ->name('carrera-universidad.edit');
        
    Route::put('carrera-universidad/{carrera}/{universidad}', [CarreraUniversidadController::class, 'update'])
        ->name('carrera-universidad.update');
        
    Route::delete('carrera-universidad/{carrera}/{universidad}', [CarreraUniversidadController::class, 'destroy'])
        ->name('carrera-universidad.destroy');
    
    // Eliminada ruta duplicada con prefijo 'admin/carrera-universidad'
});

require __DIR__.'/auth.php';