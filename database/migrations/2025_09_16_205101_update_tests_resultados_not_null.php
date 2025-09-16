<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->json('resultados')->nullable(false)->change();  // Ya estaba
            $table->string('tipo_primario')->nullable(false)->change();  // Nuevo: No permitir null
            $table->string('tipo_secundario')->nullable(false)->change();  // Nuevo: No permitir null
            $table->timestamp('fecha_completado')->nullable(false)->change();  // Nuevo: No permitir null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->json('resultados')->nullable()->change();
            $table->string('tipo_primario')->nullable()->change();
            $table->string('tipo_secundario')->nullable()->change();
            $table->timestamp('fecha_completado')->nullable()->change();
        });
    }
};