<?php

namespace App\Models;

use Database\Factories\ProductoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'descripcion', 'precio', 'stock', 'categoria', 'imagen_path', 'modelo_3d_path', 'activo'])]
class Producto extends Model
{
    /** @use HasFactory<ProductoFactory> */
    use HasFactory;

    /**
     * Get the order lines that reference the product.
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
            'precio' => 'decimal:2',
            'stock' => 'integer',
            'activo' => 'boolean',
        ];
    }
}
