<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    protected $fillable = [
        'test_id',
        'pregunta_id',
        'valor',
        'user_id', 
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}