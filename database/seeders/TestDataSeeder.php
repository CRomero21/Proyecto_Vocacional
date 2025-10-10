<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Test;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Crear algunos estudiantes de prueba
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'Estudiante ' . $i,
                'email' => 'estudiante' . $i . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'estudiante',
            ]);
        }

        // Crear algunos tests de prueba
        $estudiantes = User::where('role', 'estudiante')->get();
        foreach ($estudiantes as $estudiante) {
            $completado = rand(0, 1);
            Test::create([
                'user_id' => $estudiante->id,
                'completado' => $completado,
                'tipo_primario' => $completado ? ['Realista', 'Investigador', 'Artístico', 'Social', 'Emprendedor', 'Convencional'][rand(0, 5)] : null,
                'resultados' => json_encode(['tipo' => 'Realista', 'puntuacion' => rand(50, 100)]),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}