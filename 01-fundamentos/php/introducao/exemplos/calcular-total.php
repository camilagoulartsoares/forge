<?php

declare(strict_types=1);

function calcularTotal(int $precoUnitario, int $quantidade): int
{
    if ($precoUnitario < 0 || $quantidade < 0) {
        throw new InvalidArgumentException('Valores devem ser nao negativos.');
    }

    return $precoUnitario * $quantidade;
}
