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
        Schema::create('tipos_personalidad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 1)->unique()->comment('R, I, A, S, E, C');
            $table->string('nombre');
            $table->text('descripcion');
            $table->text('caracteristicas')->nullable();
            $table->string('color_hex', 7)->default('#3498db');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_personalidad');
    }
};