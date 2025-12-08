<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Avance;
use App\Models\Proyecto;
use App\Models\CriterioEvaluacion;
use Illuminate\Support\Facades\DB;

class CalificacionesAvancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proyectos = Proyecto::with('evento.jueces')->get();

        if ($proyectos->isEmpty()) {
            $this->command->info('No se encontraron proyectos para crear avances y calificaciones.');
            return;
        }

        foreach ($proyectos as $proyecto) {
            // Create advances for the project
            Avance::factory(3)->create(['proyecto_id' => $proyecto->id]);

            // Create calificaciones for the project from assigned jueces
            $criterios = CriterioEvaluacion::where('evento_id', $proyecto->evento_id)->get();
            $juecesDelEvento = $proyecto->evento->jueces;

            if ($criterios->isEmpty() || $juecesDelEvento->isEmpty()) {
                continue; // Skip if no criteria or judges for this event
            }

            foreach ($juecesDelEvento as $juez) {
                foreach ($criterios as $criterio) {
                    DB::table('calificaciones')->insert([
                        'proyecto_id' => $proyecto->id,
                        'juez_user_id' => $juez->id,
                        'criterio_id' => $criterio->id,
                        'puntuacion' => rand(0, 100),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}