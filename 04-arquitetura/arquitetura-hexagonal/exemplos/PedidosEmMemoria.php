<?php

declare(strict_types=1);

namespace Forge\Pedidos;

use DomainException;

require_once __DIR__ . '/ArmazenamentoPedidos.php';

final class PedidosEmMemoria implements ArmazenamentoPedidos
{
    private array $pedidos = [];

    public function existe(string $id): bool
    {
        return isset($this->pedidos[$id]);
    }

    public function salvar(Pedido $pedido): void
    {
        if ($this->existe($pedido->id())) {
            throw new DomainException('Pedido ja existe.');
        }
        $this->pedidos[$pedido->id()] = $pedido;
    }

    public function buscar(string $id): ?Pedido
    {
        return $this->pedidos[$id] ?? null;
    }
}
