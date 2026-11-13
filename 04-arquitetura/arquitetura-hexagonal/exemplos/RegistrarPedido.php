<?php

declare(strict_types=1);

namespace Forge\Pedidos;

use DomainException;

require_once __DIR__ . '/ArmazenamentoPedidos.php';

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
