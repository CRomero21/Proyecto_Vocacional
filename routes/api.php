<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\InformeAvanzadoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes para informes avanzados - filtros dinámicos (sin autenticación requerida)
Route::get('/informes-avanzados/ciudades/{departamento}', [InformeAvanzadoController::class, 'getCiudadesByDepartamento']);
Route::get('/informes-avanzados/unidades-educativas/{ciudad}', [InformeAvanzadoController::class, 'getUnidadesEducativasByCiudad']);

Route::get('/ciudades/{departamento}', function (string $departamento) {
    $ciudades = User::where('departamento', $departamento)
        ->whereNotNull('ciudad')
        ->where('ciudad', '!=', '')
        ->distinct('ciudad')
        ->pluck('ciudad')
        ->sort()
        ->values();

    return response()->json($ciudades);
});

// Ruta para obtener instituciones por ciudad
Route::get('/instituciones/{ciudad}', function (string $ciudad) {
    $instituciones = User::where('ciudad', $ciudad)
        ->whereNotNull('unidad_educativa')
        ->where('unidad_educativa', '!=', '')
        ->distinct('unidad_educativa')
        ->pluck('unidad_educativa')
        ->sort()
        ->values();

    return response()->json($instituciones);
});

// Ruta para obtener instituciones por departamento
Route::get('/instituciones-departamento/{departamento}', function (string $departamento) {
    $instituciones = User::where('departamento', $departamento)
        ->whereNotNull('unidad_educativa')
        ->where('unidad_educativa', '!=', '')
        ->distinct('unidad_educativa')
        ->pluck('unidad_educativa')
        ->sort()
        ->values();

    return response()->json($instituciones);
});