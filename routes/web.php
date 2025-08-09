<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UsuarioController;

// Página de bienvenida
Route::get('/welcome', function () {
    return view('welcome');
});

// Dashboard para usuarios autenticados
Route::get('/dashboard', [CuestionarioController::class, 'mostrar'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupo de rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ruta para informes (solo superadmin si tienes middleware especial)
    Route::get('/informes', [InformeController::class, 'index'])->name('informes');

    // Cuestionario RIASEC
    //Route::get('/cuestionario', [CuestionarioController::class, 'mostrar'])->name('cuestionario');
    //Route::post('/cuestionario', [CuestionarioController::class, 'guardar'])->name('cuestionario.guardar');}

    Route::get('/dashboard', [CuestionarioController::class, 'mostrar'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::post('/dashboard', [CuestionarioController::class, 'guardar'])->name('cuestionario.guardar');

    Route::resource('admin/preguntas', PreguntaController::class)->names('admin.preguntas');  

    Route::get('/test/iniciar', [TestController::class, 'iniciar'])->name('test.iniciar');
    Route::post('/test/guardar', [TestController::class, 'guardar'])->name('test.guardar');

    Route::resource('admin/usuarios', UsuarioController::class)->names('admin.usuarios');
    Route::get('/informes', [App\Http\Controllers\InformeController::class, 'index'])->name('informes.index');
});

require __DIR__.'/auth.php';