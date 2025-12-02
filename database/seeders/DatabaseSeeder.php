<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use App\Models\Perfil;
use App\Models\Carrera;
use App\Models\Evento;
use App\Models\CriterioEvaluacion;
use App\Models\Participante;
use App\Models\Equipo;
use App\Models\Proyecto;
use App\Models\Avance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        // Truncate tables
        User::truncate();
        Rol::truncate();
        Perfil::truncate();
        Carrera::truncate();
        Evento::truncate();
        CriterioEvaluacion::truncate();
        Participante::truncate();
        Equipo::truncate();
        Proyecto::truncate();
        Avance::truncate();
        DB::table('user_rol')->truncate();
        DB::table('equipo_participante')->truncate();
        DB::table('calificaciones')->truncate();
        DB::table('evento_user')->truncate();

        Schema::enableForeignKeyConstraints();

        // Create Roles
        $rolAdmin = Rol::create(['nombre' => 'Admin']);
        $rolJuez = Rol::create(['nombre' => 'Juez']);
        $rolParticipante = Rol::create(['nombre' => 'Participante']);

        // Create Perfiles
        $perfilProg = Perfil::create(['nombre' => 'Programador']);
        $perfilDisenador = Perfil::create(['nombre' => 'Diseñador']);
        Perfil::create(['nombre' => 'Líder de Proyecto']);
        Perfil::create(['nombre' => 'Tester']);


        // Create Carreras and Eventos
        $carreras = Carrera::factory(5)->create();
        $eventos = Evento::factory(3)->create();

        // Create Criterios for Eventos
        foreach ($eventos as $evento) {
            CriterioEvaluacion::factory(5)->create(['evento_id' => $evento->id]);
        }

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->roles()->attach($rolAdmin->id);

        // Create Jueces
        $jueces = User::factory(5)->create();
        foreach ($jueces as $juez) {
            $juez->roles()->attach($rolJuez->id);
        }

        // <<< NEW: Assign Jueces to Eventos >>>
        $this->call(EventoUserSeeder::class);

        // Create Participantes
        $participantes = User::factory(50)->create()->each(function ($user) use ($rolParticipante, $carreras) {
            $user->roles()->attach($rolParticipante->id);
            Participante::factory()->create([
                'user_id' => $user->id,
                'carrera_id' => $carreras->random()->id,
            ]);
        });
        
        $participantesFromDb = Participante::all();
        $perfilesFromDb = Perfil::all();

        // Create Equipos
        $equipos = Equipo::factory(10)->create();

        foreach ($equipos as $equipo) {
            // Attach 3 random participants to each team with a random profile
            $randomParticipantes = $participantesFromDb->random(3);
            foreach($randomParticipantes as $participante) {
                $equipo->participantes()->attach($participante->id, ['perfil_id' => $perfilesFromDb->random()->id]);
            }

            // Create a project for the team
            $proyecto = Proyecto::factory()->create([
                'equipo_id' => $equipo->id,
                'evento_id' => $eventos->random()->id,
            ]);

            // Create advances for the project
            Avance::factory(3)->create(['proyecto_id' => $proyecto->id]);

            // <<< MODIFIED: Create calificaciones for the project from assigned jueces >>>
            $criterios = CriterioEvaluacion::where('evento_id', $proyecto->evento_id)->get();
            $juecesDelEvento = $proyecto->evento->jueces; // Get jueces assigned to this specific event

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