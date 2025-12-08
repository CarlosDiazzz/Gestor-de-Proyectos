<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perfil;

class PerfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Perfil::truncate();

        Perfil::create(['nombre' => 'Programador']);
        Perfil::create(['nombre' => 'Diseñador']);
        Perfil::create(['nombre' => 'Líder de Proyecto']);
        Perfil::create(['nombre' => 'Tester']);
    }
}
