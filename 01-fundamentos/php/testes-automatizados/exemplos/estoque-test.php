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

$retirada = new Estoque();
verificarExcecao(UnderflowException::class, function () use ($retirada): void {
    $retirada->retirar(1);
});
$retirada->adicionar(5);
$retirada->retirar(2);
verificarIgual(3, $retirada->saldo());
foreach ([0, -1] as $quantidade) {
    verificarExcecao(InvalidArgumentException::class, function () use ($retirada, $quantidade): void {
        $retirada->retirar($quantidade);
    });
    verificarIgual(3, $retirada->saldo());
}
verificarExcecao(UnderflowException::class, function () use ($retirada): void {
    $retirada->retirar(4);
});
verificarIgual(3, $retirada->saldo());
$retirada->retirar(3);
verificarIgual(0, $retirada->saldo());
$limite->retirar(PHP_INT_MAX);
verificarIgual(0, $limite->saldo());

$origem = new Estoque();
$destino = new Estoque();
$origem->adicionar(5);
$origem->transferirPara($destino, 2);
verificarIgual(3, $origem->saldo());
verificarIgual(2, $destino->saldo());
foreach ([0, -1] as $quantidade) {
    verificarExcecao(InvalidArgumentException::class, function () use ($origem, $destino, $quantidade): void {
        $origem->transferirPara($destino, $quantidade);
    });
}
verificarExcecao(InvalidArgumentException::class, function () use ($origem): void {
    $origem->transferirPara($origem, 1);
});
verificarExcecao(UnderflowException::class, function () use ($origem, $destino): void {
    $origem->transferirPara($destino, 4);
});
$cheio = new Estoque();
$cheio->adicionar(PHP_INT_MAX);
verificarExcecao(OverflowException::class, function () use ($origem, $cheio): void {
    $origem->transferirPara($cheio, 1);
});
verificarIgual(3, $origem->saldo());
verificarIgual(2, $destino->saldo());
verificarIgual(PHP_INT_MAX, $cheio->saldo());
$origem->transferirPara($destino, 3);
verificarIgual(0, $origem->saldo());
verificarIgual(5, $destino->saldo());

echo 'OK: estoque' . PHP_EOL;
