<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_carrera_recomendacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->foreignId('carrera_id')->constrained();
            $table->integer('match_porcentaje');
            $table->integer('orden');
            $table->boolean('es_primaria')->default(true);
            $table->string('area_conocimiento')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_carrera_recomendacion');
    }
};