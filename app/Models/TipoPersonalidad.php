<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPersonalidad extends Model
{
    use HasFactory;
    
    protected $table = 'tipos_personalidad';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'caracteristicas',
        'color_hex'
    ];
    
    /**
     * Obtiene las carreras asociadas a este tipo de personalidad como tipo primario
     */
    public function carrerasPrimario()
    {
        return $this->hasMany(\App\Models\Carrera::class, 'tipo_primario', 'codigo');
    }
    public function carrerasSecundario()
    {
        return $this->hasMany(\App\Models\Carrera::class, 'tipo_secundario', 'codigo');
    }
    public function carrerasTerciario()
    {
        return $this->hasMany(\App\Models\Carrera::class, 'tipo_terciario', 'codigo');
    }
}