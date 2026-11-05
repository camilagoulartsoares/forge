<?php

declare(strict_types=1);

require_once __DIR__ . '/verificacoes.php';
require_once __DIR__ . '/../../introducao/exemplos/calcular-total.php';

verificarIgual(60, calcularTotal(20, 3));
verificarIgual(0, calcularTotal(20, 0));
verificarIgual(0, calcularTotal(0, 3));
verificarIgual(0, calcularTotal(0, 0));
verificarIgual(PHP_INT_MAX, calcularTotal(PHP_INT_MAX, 1));
verificarIgual(0, calcularTotal(PHP_INT_MAX, 0));
verificarIgual(0, calcularTotal(0, PHP_INT_MAX));
verificarExcecao(OverflowException::class, function (): void {
    calcularTotal(PHP_INT_MAX, 2);
});
verificarExcecao(InvalidArgumentException::class, function (): void {
    calcularTotal(-1, 3);
});
verificarExcecao(InvalidArgumentException::class, function (): void {
    calcularTotal(20, -1);
});
verificarExcecao(TypeError::class, function (): void {
    calcularTotal('20', 3);
});

echo 'OK: total' . PHP_EOL;
