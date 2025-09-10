<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraRecommendation extends Model
{
    protected $table = 'test_carrera_recomendacion';

    protected $fillable = ['test_id', 'carrera_id', 'match_porcentaje', 'orden', 'es_primaria', 'area_conocimiento'];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class, 'test_id');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }
}