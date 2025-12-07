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
        // Migración de datos: Asegurar que todos los equipos tengan un líder
        
        // Obtener ID del perfil de Líder de Proyecto
        $perfilLider = DB::table('perfiles')->where('nombre', 'Líder de Proyecto')->first();
        $idPerfilLider = $perfilLider ? $perfilLider->id : 3; // Fallback a ID 3
        
        // Obtener todos los equipos
        $equipos = DB::table('equipos')->get();
        
        foreach ($equipos as $equipo) {
            // Verificar si el equipo ya tiene un líder
            $tieneLider = DB::table('equipo_participante')
                ->where('equipo_id', $equipo->id)
                ->where('perfil_id', $idPerfilLider)
                ->exists();
            
            if (!$tieneLider) {
                // El equipo NO tiene líder, buscar el miembro más antiguo
                $miembroMasAntiguo = DB::table('equipo_participante')
                    ->where('equipo_id', $equipo->id)
                    ->orderBy('created_at', 'asc')
                    ->first();
                
                if ($miembroMasAntiguo) {
                    // Asignar al miembro más antiguo como líder
                    DB::table('equipo_participante')
                        ->where('equipo_id', $equipo->id)
                        ->where('participante_id', $miembroMasAntiguo->participante_id)
                        ->update([
                            'perfil_id' => $idPerfilLider,
                            'updated_at' => now(),
                        ]);
                    
                    echo "✓ Equipo '{$equipo->nombre}' (ID: {$equipo->id}): Líder asignado al participante {$miembroMasAntiguo->participante_id}\n";
                } else {
                    // El equipo no tiene miembros (equipo vacío)
                    echo "⚠ Equipo '{$equipo->nombre}' (ID: {$equipo->id}): Sin miembros, no se puede asignar líder\n";
                }
            } else {
                echo "✓ Equipo '{$equipo->nombre}' (ID: {$equipo->id}): Ya tiene líder\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es posible revertir esta migración de datos
        // ya que no sabemos qué equipos tenían líder originalmente
    }
};
