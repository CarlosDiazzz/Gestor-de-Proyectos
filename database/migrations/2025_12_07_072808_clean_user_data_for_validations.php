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
        // Migración de datos: Limpiar datos de usuarios para cumplir con nuevas validaciones
        
        // 1. Limpiar nombres con números
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            // Remover números del nombre, mantener solo letras, espacios y acentos
            $cleanName = preg_replace('/[0-9]/', '', $user->name);
            // Limpiar espacios múltiples
            $cleanName = preg_replace('/\s+/', ' ', $cleanName);
            $cleanName = trim($cleanName);
            
            // Solo actualizar si cambió
            if ($cleanName !== $user->name && !empty($cleanName)) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'name' => $cleanName,
                        'updated_at' => now(),
                    ]);
            }
        }

        // 2. Limpiar teléfonos (truncar a 10 dígitos)
        $participantes = DB::table('participantes')
            ->whereNotNull('telefono')
            ->get();
        
        foreach ($participantes as $participante) {
            // Extraer solo dígitos
            $telefono = preg_replace('/[^0-9]/', '', $participante->telefono);
            
            // Si tiene más de 10 dígitos, tomar los primeros 10
            if (strlen($telefono) > 10) {
                $telefono = substr($telefono, 0, 10);
            }
            
            // Solo actualizar si cambió
            if ($telefono !== $participante->telefono) {
                DB::table('participantes')
                    ->where('id', $participante->id)
                    ->update([
                        'telefono' => $telefono,
                        'updated_at' => now(),
                    ]);
            }
        }

        // 3. Limpiar matrículas (truncar a 8 caracteres alfanuméricos)
        $participantes = DB::table('participantes')
            ->whereNotNull('no_control')
            ->get();
        
        foreach ($participantes as $participante) {
            // Extraer solo alfanuméricos
            $noControl = preg_replace('/[^a-zA-Z0-9]/', '', $participante->no_control);
            
            // Si tiene más de 8 caracteres, tomar los primeros 8
            if (strlen($noControl) > 8) {
                $noControl = substr($noControl, 0, 8);
            }
            
            // Solo actualizar si cambió
            if ($noControl !== $participante->no_control) {
                DB::table('participantes')
                    ->where('id', $participante->id)
                    ->update([
                        'no_control' => $noControl,
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
        // No es posible revertir esta migración de datos
        // ya que no guardamos los valores originales
        // Esta migración es de limpieza de datos, no de estructura
    }
};
