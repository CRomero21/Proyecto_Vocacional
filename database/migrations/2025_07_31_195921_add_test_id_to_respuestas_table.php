<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('respuestas', function (Blueprint $table) {
            $table->foreignId('test_id')->after('id')->constrained('tests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respuestas', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
            $table->dropColumn('test_id');
        });
    }
};
