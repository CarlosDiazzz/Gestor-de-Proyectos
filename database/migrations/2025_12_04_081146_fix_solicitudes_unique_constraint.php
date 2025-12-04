<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_equipo', function (Blueprint $table) {
            // Remover la restricción UNIQUE antigua
            $table->dropUnique('solicitudes_equipo_equipo_id_participante_id_unique');
            
            // Agregar única restricción para PENDING requests únicamente
            // Esto permite múltiples solicitudes (aceptada, rechazada) pero solo 1 pendiente por (equipo, participante)
        });
        
        // Crear índice único condicional si la BD lo soporta (PostgreSQL sí)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX unique_pending_solicitud 
                          ON solicitudes_equipo(equipo_id, participante_id) 
                          WHERE estado = \'pendiente\'');
        }
    }

    public function down(): void
    {
        Schema::table('solicitudes_equipo', function (Blueprint $table) {
            // Restaurar la restricción UNIQUE original
            $table->unique(['equipo_id', 'participante_id']);
        });
        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS unique_pending_solicitud');
        }
    }
};
