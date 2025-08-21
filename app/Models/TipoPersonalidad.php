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
    public function carrerasPrimarias()
    {
        return $this->hasMany(CarreraTipo::class, 'tipo_primario', 'codigo');
    }
    
    /**
     * Obtiene las carreras asociadas a este tipo de personalidad como tipo secundario
     */
    public function carrerasSecundarias()
    {
        return $this->hasMany(CarreraTipo::class, 'tipo_secundario', 'codigo');
    }
}