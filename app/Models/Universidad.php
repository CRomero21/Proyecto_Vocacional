<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Universidad extends Model
{
    use HasFactory;
    
    protected $table = 'universidades';
    
    protected $fillable = [
        'nombre',
        'departamento',
        'municipio',
        'direccion',
        'tipo',
        'telefono',
        'sitio_web',
        'logo',
        'acreditada'
    ];
    
    /**
     * Obtiene las carreras ofrecidas por esta universidad
     */
    protected $casts = [
        'acreditada' => 'boolean',
    ];

    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_universidad')
                    ->withPivot('modalidad', 'duracion', 'costo_semestre', 'requisitos', 'disponible')
                    ->withTimestamps();
    }
}