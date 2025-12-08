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
        Schema::table('solicitudes_equipo', function (Blueprint $table) {
            $table->foreignId('perfil_solicitado_id')
                ->nullable()
                ->after('participante_id')
                ->constrained('perfiles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_equipo', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['perfil_solicitado_id']);
            $table->dropColumn('perfil_solicitado_id');
        });
    }
};
