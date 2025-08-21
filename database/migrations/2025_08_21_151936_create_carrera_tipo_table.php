<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carrera_tipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carreras')->onDelete('cascade');
            $table->string('tipo_primario', 1)->comment('Código tipo primario (R,I,A,S,E,C)');
            $table->string('tipo_secundario', 1)->nullable()->comment('Código tipo secundario');
            $table->string('tipo_terciario', 1)->nullable()->comment('Código tipo terciario');
            $table->timestamps();
            
            $table->index(['tipo_primario', 'tipo_secundario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera_tipo');
    }
};