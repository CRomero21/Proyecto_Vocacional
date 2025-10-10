<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CoordinadorSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Coordinador Test',
            'email' => 'coordinador@test.com',
            'password' => Hash::make('password'),
            'role' => 'coordinador',
        ]);
    }
}