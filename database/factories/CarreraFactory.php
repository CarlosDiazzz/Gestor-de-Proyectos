<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Carrera>
 */
class CarreraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Lista ampliada de carreras (TecNM y otras populares)
        $carreras = [
            // --- Carreras TecNM  ---
            'Ingeniería en Sistemas Computacionales',
            'Ingeniería Informática',
            'Ingeniería Industrial',
            'Ingeniería Mecatrónica',
            'Ingeniería Electrónica',
            'Ingeniería Eléctrica',
            'Ingeniería Mecánica',
            'Ingeniería Química',
            'Ingeniería Bioquímica',
            'Ingeniería en Gestión Empresarial',
            'Ingeniería en Logística',
            'Ingeniería Civil',
            'Licenciatura en Administración',
            'Licenciatura en Contaduría Pública',
            // --- Otras carreras universitarias comunes ---
            'Arquitectura',
            'Diseño Gráfico',
            'Derecho',
            'Medicina',
            'Nutrición',
            'Psicología',
            'Mercadotecnia',
            'Ciencias de la Comunicación',
            'Gastronomía',
            'Turismo',
            'Relaciones Internacionales',
            'Economía',
            'Finanzas',
            'Pedagogía',
            'Biología',
        ];

        return [
            'nombre' => $this->faker->unique()->randomElement($carreras),
            //clave tipo "ISC123", "ARQ456"
            'clave' => $this->faker->unique()->bothify('??###'),
        ];
    }
}