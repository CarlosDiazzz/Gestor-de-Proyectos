<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LimpiezaSeeder::class,

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
            EquiposProyectosSeeder::class, // Crea equipos con límites de roles y proyectos

            // Paso 4: Datos de actividad
            CalificacionesAvancesSeeder::class, // Crea avances y calificaciones
        ]);
    }
}