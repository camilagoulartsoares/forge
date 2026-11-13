<?php

declare(strict_types=1);

namespace Forge\Pedidos;

require_once __DIR__ . '/Pedido.php';

interface ArmazenamentoPedidos
{
    public function existe(string $id): bool;
    public function salvar(Pedido $pedido): void;
}
