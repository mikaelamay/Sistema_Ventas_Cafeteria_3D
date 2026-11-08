<?php

namespace Database\Factories;

use App\Models\Insumo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Insumo>
 */
class InsumoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'unidad_medida' => fake()->randomElement(['gramos', 'mililitros', 'unidades']),
            'stock_actual' => fake()->randomFloat(3, 0, 1000),
            'stock_minimo' => fake()->randomFloat(3, 0, 100),
            'costo_unitario' => fake()->randomFloat(2, 0.1, 20),
        ];
    }
}
