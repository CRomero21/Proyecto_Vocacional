<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades'; // <-- Añade esta línea

    protected $fillable = ['nombre', 'departamento_id'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function unidadesEducativas()
    {
        return $this->hasMany(UnidadEducativa::class);
    }
}