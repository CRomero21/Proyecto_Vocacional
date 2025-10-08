<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadEducativa extends Model
{
    protected $table = 'unidades_educativas'; // <-- Corrige el nombre de la tabla
    protected $fillable = ['nombre', 'ciudad_id'];

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'unidad_educativa_id');
    }
}