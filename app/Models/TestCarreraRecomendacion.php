<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCarreraRecomendacion extends Model
{
    protected $table = 'test_carrera_recomendacion';
    
    protected $fillable = [
        'test_id',
        'carrera_id',
        'match_porcentaje',
        'orden',
        'es_primaria',
        'area_conocimiento'
    ];
    
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
    
    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }
}