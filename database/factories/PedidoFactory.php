<?php

namespace Database\Factories;

use App\Enums\EstadoPedido;
use App\Enums\MetodoPago;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'estado' => EstadoPedido::Pendiente,
            'total' => fake()->randomFloat(2, 5, 200),
            'notas' => null,
            'metodo_pago' => MetodoPago::Efectivo,
        ];
    }

    /**
     * Indicate that the order belongs to a guest (no registered user).
     */
    public function invitado(): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => null,
        ]);
    }
}
