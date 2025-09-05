<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarreraTipo extends Model
{
    use HasFactory;

    protected $table = 'carrera_tipo';
    
    protected $fillable = [
        'carrera_id',
        'tipo_primario',
        'tipo_secundario',
        'tipo_terciario'
    ];

    /**
     * Relación con Carrera
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }
}