<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraUniversidad extends Model
{
    protected $table = 'carrera_universidad';

    protected $fillable = [
        'carrera_id',
        'universidad_id',
        'modalidad',
        'duracion',
        'costo_semestre',
        'requisitos',
        'disponible',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function universidad()
    {
        return $this->belongsTo(Universidad::class);
    }
}