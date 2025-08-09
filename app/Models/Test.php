<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'user_id',
        'fecha',
        // otros campos...
    ];

    public function respuestas()
    {
        return $this->hasMany(\App\Models\Respuesta::class);
    }
}