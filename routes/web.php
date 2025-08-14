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

// Página de bienvenida
Route::get('/welcome', function () {
    return view('welcome');
});

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

// DASHBOARD PARA COORDINADOR - SIN PREFIJOS NI MIDDLEWARE PERSONALIZADO
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
    // Añade esta línea a tu archivo web.php junto con las demás rutas de coordinador
Route::get('/coordinador-estadisticas', [CoordinadorController::class, 'estadisticas'])
    ->middleware(['auth'])
    ->name('coordinador.estadisticas');

// RESTO DE RUTAS PROTEGIDAS
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/informes', [InformeController::class, 'index'])->name('informes.index');
    Route::post('/dashboard', [CuestionarioController::class, 'guardar'])->name('cuestionario.guardar');
    Route::resource('admin/preguntas', PreguntaController::class)->names('admin.preguntas');  
    Route::get('/test/iniciar', [TestController::class, 'iniciar'])->name('test.iniciar');
    Route::post('/test/guardar', [TestController::class, 'guardar'])->name('test.guardar');
    Route::resource('admin/usuarios', UsuarioController::class)->names('admin.usuarios');
});

require __DIR__.'/auth.php';