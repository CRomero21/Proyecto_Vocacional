<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_users_for_fecha_nacimiento_y_ciudad.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('fecha_nacimiento')->nullable()->after('name');
            $table->string('ciudad')->nullable()->after('departamento');
            $table->dropColumn('edad'); // si existe
            $table->dropColumn('direccion'); // si existe
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('edad')->nullable();
            $table->string('direccion')->nullable();
            $table->dropColumn('fecha_nacimiento');
            $table->dropColumn('ciudad');
        });
    }
};