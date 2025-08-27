<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->string('duracion')->nullable();
            $table->text('perfil_ingreso')->nullable();
            $table->text('perfil_egreso')->nullable();
            $table->string('tipo_primario', 10)->nullable();
            $table->string('tipo_secundario', 10)->nullable();
            $table->string('tipo_terciario', 10)->nullable();
        });
    }

    public function down()
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->dropColumn([
                'duracion',
                'perfil_ingreso',
                'perfil_egreso',
                'tipo_primario',
                'tipo_secundario',
                'tipo_terciario'
            ]);
        });
    }
};
