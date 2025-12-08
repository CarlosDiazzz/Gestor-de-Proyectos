<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate tables that are not handled in the new seeders
        // This is for full cleanup. Individual seeders also truncate their own tables.
        DB::table('solicitudes_equipo')->truncate();
        DB::table('evaluacion_comentarios')->truncate();
        DB::table('constancias')->truncate();


        $this->call([
            // Paso 1: Catálogos
            RolesSeeder::class,
            CarrerasSeeder::class,
            PerfilesSeeder::class,
            EventosSeeder::class,

            // Paso 2: Usuarios y sus detalles
            CriteriosEvaluacionSeeder::class,
            UsersSeeder::class,          // Crea usuarios y asigna roles
            ParticipantesSeeder::class,  // Completa el perfil del participante

            // Paso 3: Relaciones complejas
            JuecesEventosSeeder::class,  // Asigna jueces a eventos
            EquiposProyectosSeeder::class, // Crea equipos, proyectos y les asigna miembros

            // Paso 4: Datos de actividad
            CalificacionesAvancesSeeder::class, // Crea avances y calificaciones
        ]);

        Schema::enableForeignKeyConstraints();
    }
}