<?php
// Archivo de migración para añadir la columna datos


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->json('datos')->after('filtros');
        });
    }

    public function down()
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->dropColumn('datos');
        });
    }
};