<?php

declare(strict_types=1);

require_once __DIR__ . '/verificacoes.php';
require_once __DIR__ . '/../../introducao/exemplos/calcular-total.php';

verificarIgual(60, calcularTotal(20, 3));
verificarIgual(0, calcularTotal(20, 0));
verificarIgual(0, calcularTotal(0, 3));
verificarIgual(0, calcularTotal(0, 0));
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
