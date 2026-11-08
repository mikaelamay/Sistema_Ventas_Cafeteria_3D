<?php

namespace App\Enums;

enum EstadoPedido: string
{
    case Pendiente = 'pendiente';
    case EnPreparacion = 'en_preparacion';
    case Listo = 'listo';
    case Entregado = 'entregado';
    case Cancelado = 'cancelado';
}
