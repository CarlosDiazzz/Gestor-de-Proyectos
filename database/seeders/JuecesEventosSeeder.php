<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Evento;
use App\Models\User;
use App\Models\Rol;

class JuecesEventosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventos = Evento::all();
        $jueces = User::whereHas('roles', function ($query) {
            $query->where('nombre', 'Juez');
        })->get();

        if ($jueces->isEmpty() || $eventos->isEmpty()) {
            $this->command->info('No se encontraron jueces o eventos, no se asignarán jueces a eventos.');
            return;
        }

        $maxJueces = min(3, $jueces->count());

        $eventos->each(function ($evento) use ($jueces, $maxJueces) {
            $juecesAsignados = $jueces->random(rand(1, $maxJueces));
            $evento->jueces()->attach($juecesAsignados);
        });
    }
}