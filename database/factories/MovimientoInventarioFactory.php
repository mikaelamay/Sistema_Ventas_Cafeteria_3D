<?php

namespace Database\Factories;

use App\Enums\TipoMovimiento;
use App\Models\Insumo;
use App\Models\MovimientoInventario;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovimientoInventario>
 */
class MovimientoInventarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'insumo_id' => Insumo::factory(),
            'tipo' => fake()->randomElement(TipoMovimiento::cases()),
            'cantidad' => fake()->randomFloat(3, 0.1, 50),
            'motivo' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
