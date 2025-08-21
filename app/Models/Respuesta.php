<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'pregunta_id',
        'valor',
        'user_id'
    ];

    /**
     * Obtiene la pregunta asociada a esta respuesta
     */
    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }

    /**
     * Obtiene el test asociado a esta respuesta
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Obtiene el usuario que dio esta respuesta
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}