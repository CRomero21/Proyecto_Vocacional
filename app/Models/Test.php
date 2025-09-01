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