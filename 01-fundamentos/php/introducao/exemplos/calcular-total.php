<?php

declare(strict_types=1);

function calcularTotal(int $precoUnitario, int $quantidade): int
{
    if ($precoUnitario < 0 || $quantidade < 0) {
        throw new InvalidArgumentException('Valores devem ser nao negativos.');
    }

    if ($quantidade !== 0 && $precoUnitario > intdiv(PHP_INT_MAX, $quantidade)) {
        throw new OverflowException('Total excede o limite de inteiros.');
    }

    return $precoUnitario * $quantidade;
}
