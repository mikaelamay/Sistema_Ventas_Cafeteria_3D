<?php

namespace App\Models;

use Database\Factories\InsumoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'unidad_medida', 'stock_actual', 'stock_minimo', 'costo_unitario'])]
class Insumo extends Model
{
    /** @use HasFactory<InsumoFactory> */
    use HasFactory;

    /**
     * Get the inventory movements registered for the supply.
     *
     * @return HasMany<MovimientoInventario, $this>
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stock_actual' => 'decimal:3',
            'stock_minimo' => 'decimal:3',
            'costo_unitario' => 'decimal:2',
        ];
    }
}
