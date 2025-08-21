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
     * Obtiene la carrera asociada
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }
    
    /**
     * Obtiene el tipo de personalidad primario
     */
    public function tipoPrimarioRiasec()
    {
        return $this->belongsTo(TipoPersonalidad::class, 'tipo_primario', 'codigo');
    }
    
    /**
     * Obtiene el tipo de personalidad secundario
     */
    public function tipoSecundarioRiasec()
    {
        return $this->belongsTo(TipoPersonalidad::class, 'tipo_secundario', 'codigo');
    }
}