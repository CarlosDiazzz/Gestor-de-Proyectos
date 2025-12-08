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
        // Migración de datos: Poblar equipo_rol_limites para equipos que no tienen registros
        
        $equipos = DB::table('equipos')->get();
        
        foreach ($equipos as $equipo) {
            // Verificar si el equipo ya tiene límites dinámicos
            $tieneLimitesDinamicos = DB::table('equipo_rol_limites')
                ->where('equipo_id', $equipo->id)
                ->exists();
            
            if (!$tieneLimitesDinamicos) {
                // El equipo NO tiene límites dinámicos, crearlos
                
                // Contar miembros actuales
                $totalMiembros = DB::table('equipo_participante')
                    ->where('equipo_id', $equipo->id)
                    ->count();
                
                $espacioDisponible = 5 - $totalMiembros;
                
                if ($espacioDisponible > 0) {
                    // Hay espacio disponible, crear límites para roles básicos
                    
                    // Contar miembros por rol
                    $programadores = DB::table('equipo_participante')
                        ->where('equipo_id', $equipo->id)
                        ->where('perfil_id', 1)
                        ->count();
                    
                    $disenadores = DB::table('equipo_participante')
                        ->where('equipo_id', $equipo->id)
                        ->where('perfil_id', 2)
                        ->count();
                    
                    $testers = DB::table('equipo_participante')
                        ->where('equipo_id', $equipo->id)
                        ->where('perfil_id', 4)
                        ->count();
                    
                    // Calcular límites: actual + espacio distribuido
                    $maxProgramadores = $programadores + max(1, floor($espacioDisponible / 3));
                    $maxDisenadores = $disenadores + max(1, floor($espacioDisponible / 3));
                    $maxTesters = $testers + max(1, ceil($espacioDisponible / 3));
                    
                    // Asegurar que el total no exceda 4
                    $totalLimites = $maxProgramadores + $maxDisenadores + $maxTesters;
                    if ($totalLimites > 4) {
                        $exceso = $totalLimites - 4;
                        $maxProgramadores = max($programadores, $maxProgramadores - ceil($exceso / 3));
                        $maxDisenadores = max($disenadores, $maxDisenadores - floor($exceso / 3));
                        $maxTesters = max($testers, $maxTesters - floor($exceso / 3));
                    }
                    
                    // Crear registros en equipo_rol_limites
                    if ($maxProgramadores > 0) {
                        DB::table('equipo_rol_limites')->insert([
                            'equipo_id' => $equipo->id,
                            'perfil_id' => 1, // Programador
                            'max_vacantes' => $maxProgramadores,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    if ($maxDisenadores > 0) {
                        DB::table('equipo_rol_limites')->insert([
                            'equipo_id' => $equipo->id,
                            'perfil_id' => 2, // Diseñador
                            'max_vacantes' => $maxDisenadores,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    if ($maxTesters > 0) {
                        DB::table('equipo_rol_limites')->insert([
                            'equipo_id' => $equipo->id,
                            'perfil_id' => 4, // Tester
                            'max_vacantes' => $maxTesters,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    echo "✓ Equipo '{$equipo->nombre}' (ID: {$equipo->id}): Límites creados - Prog: {$maxProgramadores}, Dis: {$maxDisenadores}, Test: {$maxTesters}\n";
                } else {
                    echo "⚠ Equipo '{$equipo->nombre}' (ID: {$equipo->id}): Completo (5/5), no se crearon límites\n";
                }
            } else {
                echo "✓ Equipo '{$equipo->nombre}' (ID: {$equipo->id}): Ya tiene límites dinámicos\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar todos los registros de equipo_rol_limites
        DB::table('equipo_rol_limites')->truncate();
    }
};
