<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SolicitudEquipo;
use App\Models\Equipo;

class VerificaSolicitudes extends Command
{
    protected $signature = 'solicitudes:verificar {--equipo=11}';
    protected $description = 'Verifica las solicitudes en la base de datos';

    public function handle()
    {
        $this->info('=== TODAS LAS SOLICITUDES EN BD ===\n');
        
        $todas = SolicitudEquipo::with(['equipo', 'participante.user'])->orderBy('equipo_id')->get();
        
        foreach ($todas as $s) {
            $badge = match($s->estado) {
                'pendiente' => '[PENDIENTE]',
                'aceptada' => '[ACEPTADA]',
                'rechazada' => '[RECHAZADA]',
                default => '[UNKNOWN]'
            };
            $this->line("{$badge} Equipo {$s->equipo_id}: {$s->participante->user->name} → {$s->estado}");
        }
        
        $this->info("\n=== ESTADÍSTICAS ===");
        $this->line("Total: " . $todas->count());
        $this->line("Pendientes: " . $todas->where('estado', 'pendiente')->count());
        $this->line("Aceptadas: " . $todas->where('estado', 'aceptada')->count());
        $this->line("Rechazadas: " . $todas->where('estado', 'rechazada')->count());
    }
}
