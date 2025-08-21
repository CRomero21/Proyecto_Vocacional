<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Obtiene las respuestas asociadas a este test
     */
    public function respuestas()
    {
        return $this->hasMany(Respuesta::class);
    }

    /**
     * Obtiene el usuario que realizó el test
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}