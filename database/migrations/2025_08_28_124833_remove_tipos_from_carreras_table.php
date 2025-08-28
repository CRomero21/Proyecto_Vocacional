<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->dropColumn(['tipo_primario', 'tipo_secundario', 'tipo_terciario']);
        });
    }

    public function down(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->string('tipo_primario', 10)->nullable();
            $table->string('tipo_secundario', 10)->nullable();
            $table->string('tipo_terciario', 10)->nullable();
        });
    }
};