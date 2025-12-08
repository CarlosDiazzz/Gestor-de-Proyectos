<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipo;
use App\Models\Proyecto;
use App\Models\Participante;
use App\Models\Perfil;
use App\Models\Evento;
use App\Models\EquipoRolLimite;
use Illuminate\Support\Facades\DB;

class EquiposProyectosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate all related tables for a clean slate
        Equipo::truncate();
        Proyecto::truncate();
        DB::table('equipo_participante')->truncate();
        DB::table('equipo_rol_limites')->truncate();

        // Get all necessary master data
        $participantesDisponibles = Participante::all()->shuffle();
        $perfiles = Perfil::all()->keyBy('nombre');
        $eventos = Evento::all();

        // Ensure we have the required data to proceed
        if ($participantesDisponibles->isEmpty() || $perfiles->isEmpty() || $eventos->isEmpty()) {
            $this->command->info('No hay suficientes participantes, perfiles o eventos para crear equipos y proyectos.');
            return;
        }

        $idPerfilLider = $perfiles['Líder de Proyecto']->id;
        // Exclude leader profile from random assignment
        $perfilesParaMiembros = $perfiles->except(['Líder de Proyecto']);


        // Create 10 teams with detailed role limits and members
        for ($i = 0; $i < 10; $i++) {
            $equipo = Equipo::factory()->create();

            // --- Define Role Limits for the Team ---
            $limites = [];
            // 1. Every team needs 1 leader
            $limites[$idPerfilLider] = 1;

            // 2. Define random limits for other roles
            $maxMiembros = rand(4, 5); // Total team size will be 4 or 5
            $miembrosAsignar = $maxMiembros - 1; // Subtract the leader

            for ($j = 0; $j < $miembrosAsignar; $j++) {
                $perfilAleatorio = $perfilesParaMiembros->random();
                if (!isset($limites[$perfilAleatorio->id])) {
                    $limites[$perfilAleatorio->id] = 0;
                }
                $limites[$perfilAleatorio->id]++;
            }

            // 3. Create the EquipoRolLimite records
            foreach ($limites as $perfilId => $max) {
                EquipoRolLimite::create([
                    'equipo_id' => $equipo->id,
                    'perfil_id' => $perfilId,
                    'max_vacantes' => $max,
                ]);
            }


            // --- Partially Populate the Team ---
            $participantesAsignados = collect();

            // 1. Assign the Leader
            if ($participantesDisponibles->isNotEmpty()) {
                $lider = $participantesDisponibles->pop();
                $equipo->participantes()->attach($lider->id, ['perfil_id' => $idPerfilLider]);
                $participantesAsignados->push($lider);
            }

            // 2. Assign some other members, but not all, to leave empty slots
            foreach ($limites as $perfilId => $max) {
                if ($perfilId == $idPerfilLider) continue; // Skip leader, already assigned

                // Fill about half the vacancies
                $vacantesA_Llenar = floor($max / 2) + rand(0, $max % 2);

                for ($k = 0; $k < $vacantesA_Llenar; $k++) {
                    if ($participantesDisponibles->isNotEmpty()) {
                        $miembro = $participantesDisponibles->pop();
                        $equipo->participantes()->attach($miembro->id, ['perfil_id' => $perfilId]);
                        $participantesAsignados->push($miembro);
                    }
                }
            }
            
            // --- Create a Project for the Team ---
            Proyecto::factory()->create([
                'equipo_id' => $equipo->id,
                'evento_id' => $eventos->random()->id,
            ]);

            // Make the assigned participants unavailable for the next team
            $participantesDisponibles = $participantesDisponibles->diff($participantesAsignados);
        }
    }
}