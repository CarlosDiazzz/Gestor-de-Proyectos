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
        Schema::table('equipos', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_programadores')->default(0)->after('nombre');
            $table->unsignedTinyInteger('max_disenadores')->default(0)->after('max_programadores');
            $table->unsignedTinyInteger('max_testers')->default(0)->after('max_disenadores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn(['max_programadores', 'max_disenadores', 'max_testers']);
        });
    }
};
