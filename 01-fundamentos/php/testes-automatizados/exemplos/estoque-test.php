<?php

declare(strict_types=1);

require_once __DIR__ . '/verificacoes.php';
require_once __DIR__ . '/../../poo/exemplos/Estoque.php';

$estoque = new Estoque();
verificarIgual(0, $estoque->saldo());
$estoque->adicionar(2);
$estoque->adicionar(3);
verificarIgual(5, $estoque->saldo());

foreach ([0, -1] as $quantidade) {
    verificarExcecao(InvalidArgumentException::class, function () use ($estoque, $quantidade): void {
        $estoque->adicionar($quantidade);
    });
    verificarIgual(5, $estoque->saldo());
}

$outro = new Estoque();
verificarIgual(0, $outro->saldo());
$outro->adicionar(1);
verificarIgual(5, $estoque->saldo());
verificarIgual(1, $outro->saldo());

$limite = new Estoque();
$limite->adicionar(PHP_INT_MAX);
verificarIgual(PHP_INT_MAX, $limite->saldo());
verificarExcecao(OverflowException::class, function () use ($limite): void {
    $limite->adicionar(1);
});
verificarIgual(PHP_INT_MAX, $limite->saldo());

echo 'OK: estoque' . PHP_EOL;
