<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $rolAdmin = Rol::where('nombre', 'Admin')->first();
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->roles()->attach($rolAdmin->id);

        // Create Jueces
        $rolJuez = Rol::where('nombre', 'Juez')->first();
        $jueces = User::factory(5)->create();
        foreach ($jueces as $juez) {
            $juez->roles()->attach($rolJuez->id);
        }

        // Create Participantes Users
        $rolParticipante = Rol::where('nombre', 'Participante')->first();
        User::factory(50)->create()->each(function ($user) use ($rolParticipante) {
            $user->roles()->attach($rolParticipante->id);
        });
    }
}