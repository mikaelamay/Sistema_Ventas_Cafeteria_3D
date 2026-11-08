<?php

namespace App\Models;

use App\Enums\EstadoPedido;
use App\Enums\MetodoPago;
use Database\Factories\PedidoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'estado', 'total', 'notas', 'metodo_pago'])]
class Pedido extends Model
{
    /** @use HasFactory<PedidoFactory> */
    use HasFactory;

    /**
     * Get the user that placed the order.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lines that belong to the order.
     *
     * @return HasMany<DetallePedido, $this>
     */
    public function detallesPedido(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estado' => EstadoPedido::class,
            'metodo_pago' => MetodoPago::class,
            'total' => 'decimal:2',
        ];
    }
}
