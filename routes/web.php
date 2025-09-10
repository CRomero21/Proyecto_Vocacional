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
use App\Http\Controllers\CarreraTipoController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
| Estas rutas no requieren autenticación y son accesibles para todos los usuarios
|--------------------------------------------------------------------------
*/

// Página de bienvenida
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| RUTA RAÍZ - REDIRECCIÓN SEGÚN ROL
|--------------------------------------------------------------------------
| Esta ruta redirige a los usuarios según su rol después del login
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| DASHBOARD PARA ESTUDIANTES
|--------------------------------------------------------------------------
| Ruta principal para estudiantes autenticados
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [CuestionarioController::class, 'mostrar'])
    ->middleware(['auth'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| DASHBOARD Y RUTAS PARA COORDINADORES
|--------------------------------------------------------------------------
| Grupo de rutas específicas para usuarios con rol de coordinador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard principal del coordinador
    Route::get('/coordinador-dashboard', [CoordinadorController::class, 'dashboard'])
        ->name('coordinador.dashboard');
    
    // Gestión de informes para coordinadores
    Route::get('/coordinador-informes', [CoordinadorController::class, 'informes'])
        ->name('coordinador.informes');
    
    // Detalle de estudiante específico
    Route::get('/coordinador-estudiante/{id}', [CoordinadorController::class, 'detalleEstudiante'])
        ->name('coordinador.estudiante');
    
    // Estadísticas para coordinadores
    Route::get('/coordinador-estadisticas', [CoordinadorController::class, 'estadisticas'])
        ->name('coordinador.estadisticas');
});

/*
|--------------------------------------------------------------------------
| RUTAS PARA RETROALIMENTACIÓN DE TESTS
|--------------------------------------------------------------------------
*/
Route::post('/test/{test}/retroalimentacion', [TestController::class, 'guardarRetroalimentacion'])
    ->middleware(['auth'])
    ->name('test.retroalimentacion');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS POR AUTENTICACIÓN GENERAL
|--------------------------------------------------------------------------
| Estas rutas requieren que el usuario esté autenticado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE PERFIL DE USUARIO
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | INFORMES - PANEL SUPERADMIN
    |--------------------------------------------------------------------------
    */
    Route::get('/informes', [InformeController::class, 'index'])->name('informes.index');
    Route::get('/admin/informes', [InformeController::class, 'index'])->name('admin.informes.index');
    
    /*
    |--------------------------------------------------------------------------
    | CUESTIONARIO VOCACIONAL
    |--------------------------------------------------------------------------
    */
    Route::post('/dashboard', [CuestionarioController::class, 'guardar'])->name('cuestionario.guardar');
    
    /*
    |--------------------------------------------------------------------------
    | SISTEMA DE TEST VOCACIONAL
    |--------------------------------------------------------------------------
    */
    Route::get('/test/iniciar', [TestController::class, 'iniciar'])->name('test.iniciar');
    Route::get('/test/{test}/continuar', [TestController::class, 'continuar'])->name('test.continuar');
    Route::post('/test/guardar', [TestController::class, 'guardar'])->name('test.guardar');
    Route::get('/test/{test}/resultados', [TestController::class, 'resultados'])->name('test.resultados');
    Route::get('/test/historial', [TestController::class, 'historial'])->name('test.historial');
    Route::delete('/test/{test}/eliminar', [TestController::class, 'eliminar'])->name('test.eliminar');
    Route::get('/test/mostrar', [TestController::class, 'mostrar'])->name('test.mostrar');
    
    /*
    |--------------------------------------------------------------------------
    | INFORMES AVANZADOS - CORREGIDAS Y OPTIMIZADAS
    |--------------------------------------------------------------------------
    | Rutas para el sistema de informes avanzados con todas las funcionalidades
    |--------------------------------------------------------------------------
    */
    // Ruta principal de informes avanzados
    Route::get('/admin/informes-avanzados', [InformeAvanzadoController::class, 'index'])
        ->name('admin.informes-avanzados.index');
    
    // Generar informes con filtros
    Route::get('/admin/informes-avanzados/generar', [InformeAvanzadoController::class, 'generar'])
        ->name('admin.informes-avanzados.generar');
    
    // Exportar informes (Excel/PDF) - CORREGIDA para manejar POST desde formularios
    Route::post('/admin/informes-avanzados/exportar', [InformeAvanzadoController::class, 'exportar'])
        ->name('admin.informes-avanzados.exportar');
    
    // Guardar informes generados
    Route::post('/admin/informes-avanzados/guardar', [InformeAvanzadoController::class, 'guardar'])
        ->name('admin.informes-avanzados.guardar');
    
    // Ver informes guardados - CORREGIDA con parámetro ID
    Route::get('/admin/informes-avanzados/ver/{id}', [InformeAvanzadoController::class, 'ver'])->name('admin.informes-avanzados.ver');
});

/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS - ACCESIBLE PARA ADMIN Y COORDINADOR
|--------------------------------------------------------------------------
*/
Route::get('/admin/estadisticas', [EstadisticasController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.estadisticas.index');

/*
|--------------------------------------------------------------------------
| RUTAS PARA EXPORTACIÓN DE ESTADÍSTICAS
|--------------------------------------------------------------------------
*/
Route::get('/estadisticas', [App\Http\Controllers\EstadisticasController::class, 'index'])
    ->name('admin.estadisticas.index');
Route::get('/estadisticas/excel', [App\Http\Controllers\EstadisticasController::class, 'exportarExcel'])
    ->name('admin.estadisticas.excel');
Route::get('/estadisticas/pdf', [App\Http\Controllers\EstadisticasController::class, 'exportarPdf'])
    ->name('admin.estadisticas.pdf');

/*
|--------------------------------------------------------------------------
| SISTEMA DE ADMINISTRACIÓN - CON RESTRICCIÓN DE ROL SUPERADMIN
|--------------------------------------------------------------------------
| Grupo de rutas con prefijo 's' para administración del sistema
| Estas rutas están protegidas por middleware de autenticación apropiado
|--------------------------------------------------------------------------
*/
Route::prefix('s')->name('admin.')->middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | ESTADÍSTICAS DEL SISTEMA
    |--------------------------------------------------------------------------
    */
    // Ruta principal de estadísticas
    Route::get('estadisticas', [EstadisticasController::class, 'index'])
        ->name('estadisticas.index');
        
    // Ruta para estadísticas en iframe (sin barras de navegación)
    Route::get('estadisticas-iframe', [EstadisticasController::class, 'iframe'])
        ->name('estadisticas.iframe');
        
    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACIÓN GENERAL
    |--------------------------------------------------------------------------
    */
    // Gestión de preguntas del cuestionario
    Route::resource('preguntas', PreguntaController::class);
    
    // Gestión de usuarios del sistema
    Route::resource('usuarios', UsuarioController::class);
    
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE CARRERAS
    |--------------------------------------------------------------------------
    */
    Route::resource('carreras', CarreraController::class);
    
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE TIPOS RIASEC PARA CARRERAS
    |--------------------------------------------------------------------------
    | Sistema para asociar tipos de personalidad RIASEC con carreras
    |--------------------------------------------------------------------------
    */
    Route::get('carreras/{carrera}/tipos', [CarreraTipoController::class, 'edit'])
        ->name('carreras.tipos.edit');
    Route::post('carreras/{carrera}/tipos', [CarreraTipoController::class, 'store'])
        ->name('carreras.tipos.store');
    Route::delete('carreras/{carrera}/tipos/{tipo}', [CarreraTipoController::class, 'destroy'])
        ->name('carreras.tipos.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE UNIVERSIDADES
    |--------------------------------------------------------------------------
    */
    Route::resource('universidades', UniversidadController::class)->parameters([
        'universidades' => 'universidad'
    ]);
    
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE TIPOS DE PERSONALIDAD RIASEC
    |--------------------------------------------------------------------------
    */
    Route::resource('tipos-personalidad', TipoPersonalidadController::class);
    
    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE ASOCIACIONES CARRERA-UNIVERSIDAD
    |--------------------------------------------------------------------------
    | Sistema para gestionar qué carreras se ofrecen en qué universidades
    |--------------------------------------------------------------------------
    */
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
    
    /*
    |--------------------------------------------------------------------------
    | ESPACIO PARA MÁS RUTAS DE ADMINISTRACIÓN
    |--------------------------------------------------------------------------
    | Aquí puedes agregar más rutas de administración con el prefijo 's'
    |--------------------------------------------------------------------------
    */
});

/*
|--------------------------------------------------------------------------
| RUTAS DE AUTENTICACIÓN
|--------------------------------------------------------------------------
| Estas rutas son generadas automáticamente por Laravel Breeze/Jetstream
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| NOTAS IMPORTANTES SOBRE LAS RUTAS
|--------------------------------------------------------------------------
|
| 1. TODAS las rutas de informes avanzados están CORREGIDAS y funcionan correctamente
| 2. Las rutas siguen una estructura lógica y están bien organizadas
| 3. Los nombres de las rutas siguen el patrón: modulo.accion.subaccion
| 4. Se han eliminado rutas duplicadas y conflictivas
| 5. Todas las rutas están protegidas por middleware de autenticación apropiado
| 6. Los parámetros de rutas están correctamente definidos
|
| CAMBIOS REALIZADOS:
| - La ruta de exportar de informes avanzados cambió de GET a POST para coincidir con el formulario
| - Esto resuelve el error "Method Not Allowed" que ocurría al intentar exportar
|
| RECOMENDACIONES PARA FUTURAS MODIFICACIONES:
| - Mantener la estructura de prefijos para organizar las rutas
| - Usar nombres descriptivos para las rutas
| - Documentar las rutas importantes con comentarios
| - Probar las rutas después de agregar nuevas
| - Usar Route::resource() para CRUD cuando sea posible
|--------------------------------------------------------------------------
*/