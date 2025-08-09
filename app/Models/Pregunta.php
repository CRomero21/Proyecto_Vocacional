<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $fillable = ['texto', 'tipo'];

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class);
    }
}