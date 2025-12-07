<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear tabla para límites dinámicos de roles por equipo
        Schema::create('equipo_rol_limites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('perfil_id')->constrained('perfiles')->onDelete('cascade');
            $table->unsignedTinyInteger('max_vacantes')->default(0);
            $table->timestamps();
            
            // Un equipo no puede tener el mismo perfil dos veces
            $table->unique(['equipo_id', 'perfil_id']);
        });

        // Migrar datos existentes de las columnas antiguas a la nueva tabla
        $equipos = DB::table('equipos')->get();
        
        foreach ($equipos as $equipo) {
            // Solo migrar si tiene límites definidos
            if ($equipo->max_programadores > 0) {
                DB::table('equipo_rol_limites')->insert([
                    'equipo_id' => $equipo->id,
                    'perfil_id' => 1, // Programador
                    'max_vacantes' => $equipo->max_programadores,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            if ($equipo->max_disenadores > 0) {
                DB::table('equipo_rol_limites')->insert([
                    'equipo_id' => $equipo->id,
                    'perfil_id' => 2, // Diseñador
                    'max_vacantes' => $equipo->max_disenadores,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            if ($equipo->max_testers > 0) {
                DB::table('equipo_rol_limites')->insert([
                    'equipo_id' => $equipo->id,
                    'perfil_id' => 4, // Tester
                    'max_vacantes' => $equipo->max_testers,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipo_rol_limites');
    }
};
