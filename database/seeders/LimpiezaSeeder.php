<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class LimpiezaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('calificaciones')->truncate();
        DB::table('avances')->truncate();
        DB::table('proyectos')->truncate();
        DB::table('equipo_participante')->truncate();
        DB::table('equipo_rol_limites')->truncate();
        DB::table('equipos')->truncate();
        DB::table('evento_user')->truncate();
        DB::table('participantes')->truncate();
        DB::table('user_rol')->truncate();
        DB::table('users')->truncate();
        DB::table('criterio_evaluacion')->truncate();
        DB::table('eventos')->truncate();
        DB::table('perfiles')->truncate();
        DB::table('carreras')->truncate();
        DB::table('roles')->truncate();
        
        // Truncate other tables if necessary
        DB::table('solicitudes_equipo')->truncate();
        DB::table('evaluacion_comentarios')->truncate();
        DB::table('constancias')->truncate();

        Schema::enableForeignKeyConstraints();
    }
}