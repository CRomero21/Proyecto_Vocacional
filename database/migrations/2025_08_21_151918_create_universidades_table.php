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
        Schema::create('universidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('departamento');
            $table->text('direccion')->nullable();
            $table->string('tipo')->nullable()->comment('Pública, Privada, etc.');
            $table->string('telefono')->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('acreditada')->default(false);
            $table->timestamps();
            
            $table->index(['nombre', 'departamento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universidades');
    }
};