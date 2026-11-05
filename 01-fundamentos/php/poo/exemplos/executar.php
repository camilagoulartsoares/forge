<?php

declare(strict_types=1);

require_once __DIR__ . '/Estoque.php';

$estoque = new Estoque();
$estoque->adicionar(3);
echo 'Saldo: ' . $estoque->saldo() . PHP_EOL;
