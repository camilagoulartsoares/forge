<?php

declare(strict_types=1);

namespace Forge\Pedidos;

use DomainException;
use InvalidArgumentException;

final class Pedido
{
    private string $id;
    private array $itens;

    public function __construct(string $id, array $itens)
    {
        if (trim($id) === '' || $itens === []) {
            throw new InvalidArgumentException('Pedido exige identificador e itens.');
        }

        $normalizados = [];
        foreach ($itens as $item) {
            if (!is_array($item) || !isset($item['codigo'], $item['quantidade'])
                || !is_string($item['codigo']) || trim($item['codigo']) === ''
                || !is_int($item['quantidade']) || $item['quantidade'] <= 0) {
                throw new InvalidArgumentException('Item invalido.');
            }
            $normalizados[] = ['codigo' => trim($item['codigo']), 'quantidade' => $item['quantidade']];
        }
        $this->id = trim($id);
        $this->itens = $normalizados;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function itens(): array
    {
        return $this->itens;
    }
}

interface ArmazenamentoPedidos
{
    public function existe(string $id): bool;
    public function salvar(Pedido $pedido): void;
}

final class RegistrarPedido
{
    private ArmazenamentoPedidos $armazenamento;

    public function __construct(ArmazenamentoPedidos $armazenamento)
    {
        $this->armazenamento = $armazenamento;
    }

    public function executar(string $id, array $itens): Pedido
    {
        $pedido = new Pedido($id, $itens);
        if ($this->armazenamento->existe($pedido->id())) {
            throw new DomainException('Pedido ja existe.');
        }
        $this->armazenamento->salvar($pedido);

        return $pedido;
    }
}

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
