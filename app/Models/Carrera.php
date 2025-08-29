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
        'tipo_primario',
        'tipo_secundario',
        'tipo_terciario',
        // agrega otros campos si los tienes
    ];

    /**
     * Relación con universidades (muchos a muchos)
     */
    public function universidades()
    {
        return $this->belongsToMany(Universidad::class, 'carrera_universidad')
            ->withPivot('modalidad', 'duracion', 'costo_semestre', 'requisitos', 'disponible')
            ->withTimestamps();
    }
    public function carreraTipo()
    {
        return $this->hasOne(\App\Models\CarreraTipo::class, 'carrera_id');
    }

    /**
     * Relación con el tipo RIASEC primario
     */
    public function tipoPrimario()
    {
        return $this->belongsTo(\App\Models\TipoPersonalidad::class, 'tipo_primario', 'codigo');
    }

    /**
     * Relación con el tipo RIASEC secundario
     */
    public function tipoSecundario()
    {
        return $this->belongsTo(\App\Models\TipoPersonalidad::class, 'tipo_secundario', 'codigo');
    }

    /**
     * Relación con el tipo RIASEC terciario
     */
    public function tipoTerciario()
    {
        return $this->belongsTo(\App\Models\TipoPersonalidad::class, 'tipo_terciario', 'codigo');
    }
}