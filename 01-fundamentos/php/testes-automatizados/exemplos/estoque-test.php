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

// A deterministic sequence checks conservation beyond isolated examples.
$a = new Estoque();
$b = new Estoque();
$a->adicionar(100);
$b->adicionar(50);
$saldoA = 100;
$saldoB = 50;
foreach ([1, 7, 13, 0, 200, 5, -3, 10] as $quantidade) {
    if ($quantidade <= 0 || $quantidade > $saldoA) {
        $erro = $quantidade <= 0 ? InvalidArgumentException::class : UnderflowException::class;
        verificarExcecao($erro, function () use ($a, $b, $quantidade): void {
            $a->transferirPara($b, $quantidade);
        });
    } else {
        $a->transferirPara($b, $quantidade);
        $saldoA -= $quantidade;
        $saldoB += $quantidade;
    }
    verificarIgual($saldoA, $a->saldo());
    verificarIgual($saldoB, $b->saldo());
    verificarIgual(150, $a->saldo() + $b->saldo());
}
$b->transferirPara($a, $b->saldo());
verificarIgual(150, $a->saldo());
verificarIgual(0, $b->saldo());
echo 'OK: estoque' . PHP_EOL;
