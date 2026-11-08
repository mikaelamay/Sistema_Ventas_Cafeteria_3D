<?php

namespace App\Models;

use App\Enums\TipoMovimiento;
use Database\Factories\MovimientoInventarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['insumo_id', 'tipo', 'cantidad', 'motivo', 'user_id'])]
class MovimientoInventario extends Model
{
    /** @use HasFactory<MovimientoInventarioFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movimientos_inventario';

    /**
     * Get the supply affected by the movement.
     *
     * @return BelongsTo<Insumo, $this>
     */
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    /**
     * Get the user that registered the movement.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo' => TipoMovimiento::class,
            'cantidad' => 'decimal:3',
        ];
    }
}
