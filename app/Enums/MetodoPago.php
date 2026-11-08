<?php

namespace App\Enums;

enum MetodoPago: string
{
    case Efectivo = 'efectivo';
    case Tarjeta = 'tarjeta';
    case Transferencia = 'transferencia';
}
