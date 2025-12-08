<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Participante;
use App\Models\User;
use App\Models\Carrera;
use App\Models\Rol;

class ParticipantesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolParticipante = Rol::where('nombre', 'Participante')->first();
        $usersParticipantes = $rolParticipante->users;
        $carreras = Carrera::all();

        if ($carreras->isEmpty()) {
            $this->command->info('No se encontraron carreras, no se pueden crear participantes.');
            return;
        }

        foreach($usersParticipantes as $user) {
            Participante::factory()->create([
                'user_id' => $user->id,
                'carrera_id' => $carreras->random()->id,
            ]);
        }
    }
}