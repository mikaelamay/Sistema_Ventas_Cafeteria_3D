<?php

namespace Database\Seeders;

use App\Models\Insumo;
use Illuminate\Database\Seeder;

class InsumoSeeder extends Seeder
{
    /**
     * Seed the inventory supplies used for coffee preparation.
     */
    public function run(): void
    {
        $insumos = [
            [
                'nombre' => 'Café en grano',
                'unidad_medida' => 'kg',
                'stock_actual' => 10.000,
                'stock_minimo' => 2.000,
                'costo_unitario' => 18.50,
            ],
            [
                'nombre' => 'Leche',
                'unidad_medida' => 'lt',
                'stock_actual' => 20.000,
                'stock_minimo' => 5.000,
                'costo_unitario' => 2.80,
            ],
            [
                'nombre' => 'Vasos',
                'unidad_medida' => 'unidad',
                'stock_actual' => 100.000,
                'stock_minimo' => 20.000,
                'costo_unitario' => 0.15,
            ],
        ];

        foreach ($insumos as $insumo) {
            Insumo::updateOrCreate(
                ['nombre' => $insumo['nombre']],
                $insumo,
            );
        }
    }
}
