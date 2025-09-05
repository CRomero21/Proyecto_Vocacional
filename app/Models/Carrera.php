<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'area_conocimiento',
        'es_institucional',
        'imagen',
        'duracion',
        'perfil_egreso',
        'perfil_ingreso'
    ];

    /**
     * Relación con CarreraTipo (uno a muchos)
     */
    public function carreraTipos()
    {
        return $this->hasMany(CarreraTipo::class, 'carrera_id');
    }

    /**
     * Relación con CarreraTipo (uno a uno - para compatibilidad)
     */
    public function carreraTipo()
    {
        return $this->hasOne(CarreraTipo::class, 'carrera_id');
    }

    // Otras relaciones que tengas...
}