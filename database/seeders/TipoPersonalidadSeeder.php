<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoPersonalidad;
use Illuminate\Support\Facades\DB;

class TipoPersonalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'codigo' => 'R',
                'nombre' => 'Realista',
                'descripcion' => 'Personas prácticas y orientadas a la acción. Prefieren trabajar con objetos, máquinas, herramientas, plantas o animales.',
                'caracteristicas' => 'Práctico, mecánico, realista, orientado a objetos, prefiere actividades concretas',
                'color_hex' => '#e74c3c',
            ],
            [
                'codigo' => 'I',
                'nombre' => 'Investigativo',
                'descripcion' => 'Personas analíticas, intelectuales y curiosas. Prefieren actividades que impliquen pensar, observar, investigar y resolver problemas.',
                'caracteristicas' => 'Analítico, intelectual, científico, preciso, orientado a la investigación',
                'color_hex' => '#3498db',
            ],
            [
                'codigo' => 'A',
                'nombre' => 'Artístico',
                'descripcion' => 'Personas creativas, intuitivas y sensibles. Disfrutan de la auto-expresión, la innovación y actividades sin una estructura clara.',
                'caracteristicas' => 'Creativo, expresivo, original, independiente, imaginativo',
                'color_hex' => '#9b59b6',
            ],
            [
                'codigo' => 'S',
                'nombre' => 'Social',
                'descripcion' => 'Personas amigables, colaborativas y empáticas. Disfrutan trabajando con otras personas, ayudando, enseñando o brindando asistencia.',
                'caracteristicas' => 'Servicial, amigable, cooperativo, comprensivo, orientado a personas',
                'color_hex' => '#2ecc71',
            ],
            [
                'codigo' => 'E',
                'nombre' => 'Emprendedor',
                'descripcion' => 'Personas persuasivas, ambiciosas y seguras. Prefieren liderar, convencer a otros y tomar riesgos para lograr objetivos.',
                'caracteristicas' => 'Persuasivo, líder, dominante, enérgico, ambicioso',
                'color_hex' => '#f39c12',
            ],
            [
                'codigo' => 'C',
                'nombre' => 'Convencional',
                'descripcion' => 'Personas organizadas, detallistas y precisas. Prefieren seguir procedimientos establecidos y trabajar con datos de manera ordenada.',
                'caracteristicas' => 'Ordenado, sistemático, eficiente, detallista, metódico',
                'color_hex' => '#1abc9c',
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_personalidad')->insert($tipo);
        }
    }
}