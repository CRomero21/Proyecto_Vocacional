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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('edad')->nullable();
            $table->string('sexo', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('unidad_educativa', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['edad', 'sexo', 'phone', 'unidad_educativa']);
        });
    }
};
