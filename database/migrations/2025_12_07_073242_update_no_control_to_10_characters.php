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
        // Primero, cambiar el tamaño de la columna en la base de datos
        Schema::table('participantes', function (Blueprint $table) {
            $table->string('no_control', 20)->change();
        });

        // Migración de datos: Ajustar matrículas a 10 caracteres
        $participantes = DB::table('participantes')
            ->whereNotNull('no_control')
            ->get();
        
        foreach ($participantes as $participante) {
            $noControl = $participante->no_control;
            
            // Extraer solo alfanuméricos
            $noControl = preg_replace('/[^a-zA-Z0-9]/', '', $noControl);
            
            // Si tiene menos de 10 caracteres, rellenar con ceros a la izquierda
            if (strlen($noControl) < 10) {
                // Si es solo números, rellenar con ceros a la izquierda
                if (preg_match('/^[0-9]+$/', $noControl)) {
                    $noControl = str_pad($noControl, 10, '0', STR_PAD_LEFT);
                } else {
                    // Si tiene letras, rellenar con ceros a la derecha
                    $noControl = str_pad($noControl, 10, '0', STR_PAD_RIGHT);
                }
            }
            // Si tiene más de 10 caracteres, truncar a 10
            elseif (strlen($noControl) > 10) {
                $noControl = substr($noControl, 0, 10);
            }
            
            // Verificar que tenga al menos un número
            if (!preg_match('/[0-9]/', $noControl)) {
                // Si no tiene números, agregar un 0 al final
                $noControl = substr($noControl, 0, 9) . '0';
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
        // No es posible revertir completamente esta migración
        // ya que no guardamos los valores originales
        
        // Podemos revertir el cambio de tamaño de columna
        Schema::table('participantes', function (Blueprint $table) {
            $table->string('no_control', 20)->change();
        });
    }
};
