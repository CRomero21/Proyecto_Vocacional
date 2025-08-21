<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected $fillable = [
        'texto',
        'tipo',
        'orden'
    ];

    /**
     * Obtiene las respuestas asociadas a esta pregunta
     */
    public function respuestas()
    {
        return $this->hasMany(Respuesta::class);
    }
}