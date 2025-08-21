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
        'imagen'
    ];
    
    /**
     * Obtiene las universidades que ofrecen esta carrera
     */
    public function universidades()
    {
        return $this->belongsToMany(Universidad::class, 'carrera_universidad')
                    ->withPivot('modalidad', 'duracion', 'costo_semestre', 'requisitos', 'disponible')
                    ->withTimestamps();
    }
    
    /**
     * Obtiene los tipos de personalidad RIASEC asociados
     */
    public function tiposRiasec()
    {
        return $this->hasMany(CarreraTipo::class);
    }
    
    /**
     * Obtiene el tipo RIASEC primario de la carrera
     */
    public function tipoPrimario()
    {
        return $this->hasOne(CarreraTipo::class)->orderBy('id', 'asc');
    }
}