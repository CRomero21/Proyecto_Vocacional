<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Obtén todas las carreras con tipos definidos
        $carreras = DB::table('carreras')
            ->select('id', 'tipo_primario', 'tipo_secundario', 'tipo_terciario')
            ->get();

        foreach ($carreras as $carrera) {
            // Solo migra si tiene tipo_primario
            if ($carrera->tipo_primario) {
                DB::table('carrera_tipo')->updateOrInsert(
                    ['carrera_id' => $carrera->id],
                    [
                        'tipo_primario' => $carrera->tipo_primario,
                        'tipo_secundario' => $carrera->tipo_secundario,
                        'tipo_terciario' => $carrera->tipo_terciario,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        // Si quieres revertir, elimina los registros migrados
        $carreras = DB::table('carreras')->pluck('id');
        DB::table('carrera_tipo')->whereIn('carrera_id', $carreras)->delete();
    }
};