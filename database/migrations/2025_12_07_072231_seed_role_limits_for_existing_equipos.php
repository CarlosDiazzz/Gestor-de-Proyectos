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
        // Migración de datos: Asignar límites de roles a equipos existentes
        // que tienen max_programadores, max_disenadores y max_testers en 0
        
        $equipos = DB::table('equipos')
            ->where('max_programadores', 0)
            ->where('max_disenadores', 0)
            ->where('max_testers', 0)
            ->get();

        foreach ($equipos as $equipo) {
            // Contar miembros actuales por rol
            $programadores = DB::table('equipo_participante')
                ->where('equipo_id', $equipo->id)
                ->where('perfil_id', 1) // Programador
                ->count();
            
            $disenadores = DB::table('equipo_participante')
                ->where('equipo_id', $equipo->id)
                ->where('perfil_id', 2) // Diseñador
                ->count();
            
            $testers = DB::table('equipo_participante')
                ->where('equipo_id', $equipo->id)
                ->where('perfil_id', 4) // Tester
                ->count();
            
            $totalMiembros = DB::table('equipo_participante')
                ->where('equipo_id', $equipo->id)
                ->count();

            // Calcular límites basados en composición actual
            // Si el equipo tiene miembros de un rol, asignar al menos ese número
            // Si no tiene miembros, asignar distribución balanceada
            
            if ($totalMiembros == 0) {
                // Equipo vacío: asignar distribución balanceada por defecto
                $maxProgramadores = 2;
                $maxDisenadores = 1;
                $maxTesters = 1;
            } else {
                // Equipo con miembros: usar composición actual + espacio para crecer
                $espacioRestante = 5 - $totalMiembros;
                
                // Asignar al menos lo que ya tienen, más distribución del espacio restante
                $maxProgramadores = max($programadores, 1);
                $maxDisenadores = max($disenadores, 1);
                $maxTesters = max($testers, 1);
                
                // Distribuir espacio restante proporcionalmente
                if ($espacioRestante > 0) {
                    // Si tienen programadores, darles más espacio
                    if ($programadores > 0) {
                        $maxProgramadores += min(2, $espacioRestante);
                        $espacioRestante -= min(2, $espacioRestante);
                    }
                    // Distribuir resto equitativamente
                    if ($espacioRestante > 0 && $disenadores > 0) {
                        $maxDisenadores += 1;
                        $espacioRestante -= 1;
                    }
                    if ($espacioRestante > 0 && $testers > 0) {
                        $maxTesters += 1;
                    }
                }
                
                // Asegurar que no excedemos 4 total (más el líder = 5)
                $totalLimites = $maxProgramadores + $maxDisenadores + $maxTesters;
                if ($totalLimites > 4) {
                    // Reducir proporcionalmente
                    $factor = 4 / $totalLimites;
                    $maxProgramadores = max(1, floor($maxProgramadores * $factor));
                    $maxDisenadores = max(1, floor($maxDisenadores * $factor));
                    $maxTesters = max(1, floor($maxTesters * $factor));
                }
            }

            // Actualizar equipo con los límites calculados
            DB::table('equipos')
                ->where('id', $equipo->id)
                ->update([
                    'max_programadores' => $maxProgramadores,
                    'max_disenadores' => $maxDisenadores,
                    'max_testers' => $maxTesters,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir: poner todos los límites en 0
        DB::table('equipos')->update([
            'max_programadores' => 0,
            'max_disenadores' => 0,
            'max_testers' => 0,
        ]);
    }
};
