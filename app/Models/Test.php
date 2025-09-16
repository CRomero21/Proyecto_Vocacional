<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fecha',
        'tipo_primario',
        'tipo_secundario',
        'resultados',
        'completado',
        'fecha_completado'
    ];

    protected $casts = [
        'resultados' => 'array',
        'completado' => 'boolean',
        'fecha' => 'datetime',
        'fecha_completado' => 'datetime'
    ];

    // Reglas de validación para evitar guardar datos null o inválidos
    public static $rules = [
        'user_id' => 'required|exists:users,id',
        'tipo_primario' => 'required|string|in:R,I,A,S,E,C',  // No null y debe ser un tipo RIASEC válido
        'tipo_secundario' => 'nullable|string|in:R,I,A,S,E,C',  // Opcional, pero si se asigna, debe ser válido
        'resultados' => 'required|array',  // No null y debe ser array
        'completado' => 'required|boolean',  // No null
        'fecha_completado' => 'required|date',  // No null cuando se completa
        'fecha' => 'required|date',  // No null
    ];

    /**
     * Obtiene el usuario que realizó el test
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene las carreras recomendadas para este test
     */
    public function carrerasRecomendadas()
    {
        return $this->hasMany(TestCarreraRecomendacion::class);
    }
    
    /**
     * @deprecated Esta relación se mantiene por compatibilidad con código existente
     * pero ya no guardamos respuestas individuales en la base de datos.
     */
    public function respuestas()
    {
        // Devuelve una colección vacía para mantener compatibilidad
        return $this->hasMany(Respuesta::class)->whereRaw('1 = 0');
    }
}