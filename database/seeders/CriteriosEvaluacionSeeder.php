<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CriterioEvaluacion;
use App\Models\Evento;

class CriteriosEvaluacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CriterioEvaluacion::truncate();
        $eventos = Evento::all();
        foreach ($eventos as $evento) {
            CriterioEvaluacion::factory(5)->create(['evento_id' => $evento->id]);
        }
    }
}
