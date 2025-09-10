<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Carrera;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'unidad_educativa',
        'fecha_nacimiento',
        'sexo',
        'email',
        'password',
        'phone',
        'departamento',
        'ciudad',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */ 
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function test()
    {
        return $this->hasMany(Test::class);
    }

    // AGREGAR AQUÍ: Constantes para roles
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_COORDINADOR = 'coordinador'; // En lugar de 'admin'
    public const ROLE_USER = 'estudiante';

    // AGREGAR AQUÍ: Métodos helper para verificar roles
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isCoordinador() 
    {
        return $this->role === self::ROLE_COORDINADOR;
    }

    public function isCoordinadorOrHigher()
    {
        return $this->isCoordinador() || $this->isSuperAdmin();
    }

    public function tests()
    {
        return $this->hasMany(\App\Models\Test::class);
    }
}