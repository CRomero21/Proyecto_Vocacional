<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'filtros',
        'datos',
        'user_id',
    ];

    protected $casts = [
        'filtros' => 'array',
        'datos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}