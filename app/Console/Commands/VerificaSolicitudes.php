<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SolicitudEquipo;
use App\Models\Equipo;

class VerificaSolicitudes extends Command
{
    protected $signature = 'solicitudes:verificar';
    protected $description = 'Verifica las solicitudes en la base de datos';

    public function handle()
    {
        $this->info('=== VERIFICACIÓN DE SOLICITUDES ===\n');
        
        // 1. Ver equipo DevcITO
        $equipo = Equipo::find(12);
        if (!$equipo) {
            $this->error('No existe Equipo 12');
            return;
        }
        
        $this->info("Equipo: {$equipo->nombre}");
        $lider = $equipo->getLider();
        $this->info("Líder: " . ($lider ? $lider->user->name . " (Participante ID: {$lider->id}, User ID: {$lider->user_id})" : "SIN LÍDER"));
        
        // 2. Ver solicitudes pendientes del equipo
        $solicitudes = $equipo->solicitudesPendientes()->with('participante.user')->get();
        $this->info("Solicitudes pendientes: {$solicitudes->count()}");
        foreach ($solicitudes as $s) {
            $this->line("  - {$s->participante->user->name} (Participante ID: {$s->participante_id})");
        }
        
        // 3. Verificar si el líder está en el dashboard
        if ($lider && $lider->user) {
            $this->info("\n=== VERIFICACIÓN DEL DASHBOARD DEL LÍDER ===");
            $user = $lider->user;
            $this->line("Usuario: {$user->name} (ID: {$user->id})");
            $participante = $user->participante;
            $this->line("Participante: " . ($participante ? "SÍ (ID: {$participante->id})" : "NO"));
            
            if ($participante) {
                $equipo_del_lider = $participante->equipos->first();
                $this->line("Equipo del participante: " . ($equipo_del_lider ? $equipo_del_lider->nombre . " (ID: {$equipo_del_lider->id})" : "NO TIENE"));
                
                if ($equipo_del_lider && $equipo_del_lider->id === 12) {
                    $solicitudes_visibles = $equipo_del_lider->solicitudesPendientes()->get();
                    $this->line("Solicitudes pendientes visibles: {$solicitudes_visibles->count()}");
                    foreach ($solicitudes_visibles as $s) {
                        $this->line("  - {$s->participante->user->name}");
                    }
                }
            }
        }
    }
}
