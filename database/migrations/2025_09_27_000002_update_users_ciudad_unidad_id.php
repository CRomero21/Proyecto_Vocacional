<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ciudad');
            $table->foreignId('ciudad_id')->nullable()->after('departamento_id')->constrained('ciudades')->onDelete('set null');
            $table->foreignId('unidad_educativa_id')->nullable()->after('ciudad_id')->constrained('unidades_educativas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ciudad_id']);
            $table->dropForeign(['unidad_educativa_id']);
            $table->dropColumn(['ciudad_id', 'unidad_educativa_id']);
            $table->string('ciudad')->nullable()->after('departamento_id');
        });
    }
};
