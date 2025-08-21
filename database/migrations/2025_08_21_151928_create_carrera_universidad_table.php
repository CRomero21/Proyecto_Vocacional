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
        Schema::create('carrera_universidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carreras')->onDelete('cascade');
            $table->foreignId('universidad_id')->constrained('universidades')->onDelete('cascade');
            $table->string('modalidad')->nullable()->comment('Presencial, Virtual, Mixta');
            $table->string('duracion')->nullable()->comment('Duración de la carrera');
            $table->decimal('costo_semestre', 12, 2)->nullable();
            $table->text('requisitos')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
            
            $table->unique(['carrera_id', 'universidad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera_universidad');
    }
};